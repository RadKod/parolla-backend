<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'password' => 'The provided password is incorrect.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    // Custom auth messages
    'permanent_account' => [
        'login_required' => 'This account is permanent. Please login with email and password.',
        'already_permanent' => 'This account is already permanent. Please login with email and password.',
    ],
    'user_not_found' => 'User not found',
    'user_found' => 'User found',
    'user_updated' => 'User updated successfully',
    'user_registered' => 'User registered successfully',
    'login_success' => 'Login successful',
    'logout_success' => 'Successfully logged out',
    'token_refreshed' => 'Token refreshed',
    'invalid_credentials' => 'Invalid credentials',
    'current_password_incorrect' => 'Current password is incorrect',

    // Google auth messages
    'redirect_to_google' => 'Redirecting to Google login page',
    'google_login_success' => 'Successfully logged in with Google',

];
