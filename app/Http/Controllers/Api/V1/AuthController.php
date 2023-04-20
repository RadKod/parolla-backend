<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
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
        if (!$user) {
            return $this->sendError(
                'User not found'
            );
        }

        return $this->sendResponse(
            new UserResource($user),
            'User found'
        );
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): JsonResponse
    {
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
        if (!$user) {
            $user = User::query()->create([
                'username' => $username,
                'fingerprint' => $fingerprint,
                'password' => bcrypt($fingerprint) . random_int(1000, 9999),
                'email' => $fingerprint . '@example.com',
            ]);
        }

        $user->update([
            'username' => $username,
        ]);

        return $this->sendResponse(
            new UserResource($user),
            'User updated'
        );
    }
}
