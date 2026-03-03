<?php
function encrypt_sensitive_fields(array $data, array $fieldsToEncrypt) 
{
    $key = hex2bin($_ENV['SECRET_KEY']);
    $results = [];

    foreach ($data as $keyName => $value) {
        if (in_array($keyName, $fieldsToEncrypt)) {
            // Generate a unique 12-byte IV for EVERY field
            $iv = openssl_random_pseudo_bytes(12);
            
            $ciphertext = openssl_encrypt(
                $value,
                'aes-256-gcm',
                $key,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            // Store all three components for this specific field
            $results[$keyName] = [
                'cipher' => $ciphertext,
                'iv' => $iv,
                'tag' => $tag
            ];
        } else {
            // Leave non-sensitive fields as plain text
            $results[$keyName] = $value;
        }
    }

    return $results;
}