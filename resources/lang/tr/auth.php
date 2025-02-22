<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kimlik Doğrulama Dil Satırları
    |--------------------------------------------------------------------------
    |
    | Aşağıdaki dil satırları kimlik doğrulama sırasında kullanıcılara
    | gösterilebilecek mesajlar için kullanılır. Bu metinleri uygulamanızın
    | gereksinimlerine göre düzenlemekte özgürsünüz.
    |
    */

    'failed' => 'Bu kimlik bilgileri kayıtlarımızla eşleşmiyor.',
    'password' => 'Girilen parola yanlış.',
    'throttle' => 'Çok fazla giriş denemesi. Lütfen :seconds saniye sonra tekrar deneyin.',

    // Özel kimlik doğrulama mesajları
    'permanent_account' => [
        'login_required' => 'Bu hesap kalıcı bir hesaptır. Lütfen e-posta ve şifre ile giriş yapın.',
        'already_permanent' => 'Bu hesap zaten kalıcı bir hesaptır. Lütfen e-posta ve şifre ile giriş yapın.',
    ],
    'user_not_found' => 'Kullanıcı bulunamadı',
    'user_found' => 'Kullanıcı bulundu',
    'user_updated' => 'Kullanıcı başarıyla güncellendi',
    'user_registered' => 'Kullanıcı başarıyla kaydedildi',
    'login_success' => 'Giriş başarılı',
    'logout_success' => 'Başarıyla çıkış yapıldı',
    'token_refreshed' => 'Token yenilendi',
    'invalid_credentials' => 'Geçersiz kimlik bilgileri',
    'current_password_incorrect' => 'Mevcut şifre yanlış',

    // Google kimlik doğrulama mesajları
    'redirect_to_google' => 'Google giriş sayfasına yönlendiriliyorsunuz',
    'google_login_success' => 'Google ile başarıyla giriş yapıldı',
]; 