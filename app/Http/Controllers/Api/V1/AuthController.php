<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends BaseController
{
    /**
     * Check if user is permanent and return error response if true
     * @param User|null $user
     * @param string $messageKey
     * @return JsonResponse|null
     */
    private function checkPermanentAccount(?User $user, string $messageKey = 'login_required'): ?JsonResponse
    {
        if ($user && $user->is_permanent) {
            return $this->sendError(
                'Unauthorized',
                ['error' => __('auth.permanent_account.' . $messageKey)],
                401
            );
        }
        return null;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
        } else {
            $validator = Validator::make($request->all(), [
                'fingerprint' => 'required|string',
            ]);
            if ($validator->fails()) {
                return $this->sendError(
                    "Validation Error",
                    $validator->errors()->toArray()
                );
            }

            $user = User::query()
                ->where('fingerprint', $request->get('fingerprint'))
                ->first();
        }

        if (!$user) {
            return $this->sendError(
                __('auth.user_not_found')
            );
        }

        return $this->sendResponse(
            new UserResource($user),
            __('auth.user_found')
        );
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): JsonResponse
    {
        // JWT ile giriş yapmış kullanıcı kontrolü
        if (Auth::guard('api')->check()) {
            $user = Auth::guard('api')->user();
            
            $validator = Validator::make($request->all(), [
                'username' => 'string|unique:users,username,'.$user->id,
                'email' => 'email|unique:users,email,'.$user->id,
                'password' => 'string|min:6|nullable',
                'current_password' => 'required_with:password|string'
            ]);
            
            if ($validator->fails()) {
                return $this->sendError(
                    "Validation Error",
                    $validator->errors()->toArray()
                );
            }

            // Şifre değişikliği varsa, mevcut şifreyi kontrol et
            if ($request->has('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return $this->sendError('Validation Error', ['current_password' => [__('auth.current_password_incorrect')]]);
                }
                $user->password = Hash::make($request->password);
            }

            // Kullanıcı bilgilerini güncelle
            $user->update([
                'username' => $request->get('username', $user->username),
                'email' => $request->get('email', $user->email)
            ]);

        } else {
            // Fingerprint ile gelen kullanıcı için
            $fingerprint = $request->get('fingerprint');
            $username = $request->get('username');
            
            if (!$username) {
                $username = 'gamer' . random_int(1000, 9999);
            }
            
            $validator = Validator::make($request->all(), [
                'username' => 'string|unique:users,username'.($fingerprint ? ','.$fingerprint.',fingerprint' : ''),
                'fingerprint' => 'required|string',
            ]);
            
            if ($validator->fails()) {
                return $this->sendError(
                    "Validation Error",
                    $validator->errors()->toArray()
                );
            }

            $user = User::query()
                ->where('fingerprint', $fingerprint)
                ->first();

            if ($response = $this->checkPermanentAccount($user)) {
                return $response;
            }
                
            if (!$user) {
                $user = User::query()->create([
                    'username' => $username,
                    'fingerprint' => $fingerprint,
                    'password' => bcrypt($fingerprint . random_int(1000, 9999)),
                    'email' => $fingerprint . '@example.com',
                    'is_permanent' => false
                ]);
            } else {
                $user->update([
                    'username' => $username
                ]);
            }
        }

        return $this->sendResponse(
            new UserResource($user),
            __('auth.user_updated')
        );
    }

    /**
     * Register a permanent user account
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fingerprint' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'username' => 'required|string|unique:users,username'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray());
        }

        // Check if temporary user exists
        $tempUser = User::where('fingerprint', $request->fingerprint)->first();
        
        if ($response = $this->checkPermanentAccount($tempUser, 'already_permanent')) {
            return $response;
        }
        
        $user = $tempUser ?? new User();
        $user->email = $request->email;
        $user->username = $request->username;
        $user->password = Hash::make($request->password);
        $user->fingerprint = $request->fingerprint;
        $user->is_permanent = true;
        $user->save();

        $token = Auth::guard('api')->login($user);

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], __('auth.user_registered'));
    }

    /**
     * Login user and create token
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors()->toArray());
        }

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return $this->sendError('Unauthorized', ['error' => __('auth.invalid_credentials')], 401);
        }

        $user = Auth::guard('api')->user();

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], __('auth.login_success'));
    }

    /**
     * Logout user (Invalidate the token)
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::guard('api')->logout();
        return $this->sendResponse([], __('auth.logout_success'));
    }

    /**
     * Refresh a token
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->sendResponse([
            'token' => Auth::guard('api')->refresh(),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ], __('auth.token_refreshed'));
    }

    /**
     * Handle the Google callback with code
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handleGoogleCallback(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'fingerprint' => 'required|string',
                'state' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors()->toArray());
            }

            $googleUser = Socialite::driver('google')
                ->stateless()
                ->with([
                    'state' => $request->state,
                    'redirect_uri' => config('services.google.redirect')
                ])
                ->user($request->code);

            // Kullanıcıyı email'e göre bul veya oluştur
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Eğer aynı fingerprint ile geçici hesap varsa, onu güncelle
                if ($request->has('fingerprint')) {
                    $user = User::where('fingerprint', $request->fingerprint)
                        ->where('is_permanent', false)
                        ->first();
                }

                if (!$user) {
                    $user = new User();
                }

                $user->fill([
                    'username' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => bcrypt(random_int(100000, 999999)),
                    'is_permanent' => true,
                    'google_id' => $googleUser->id,
                ]);

                if ($request->has('fingerprint')) {
                    $user->fingerprint = $request->fingerprint;
                }

                $user->save();
            } else {
                // Google ID'yi güncelle
                $user->update([
                    'google_id' => $googleUser->id,
                    'is_permanent' => true,
                ]);

                // Eğer fingerprint varsa ve kullanıcının fingerprintti yoksa ekle
                if ($request->has('fingerprint') && !$user->fingerprint) {
                    $user->update(['fingerprint' => $request->fingerprint]);
                }
            }

            // JWT token oluştur
            $token = Auth::guard('api')->login($user);

            return $this->sendResponse([
                'user' => new UserResource($user),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ], __('auth.google_login_success'));

        } catch (Exception $e) {
            return $this->sendError('Google Auth Error', ['error' => $e->getMessage()], 401);
        }
    }
}
