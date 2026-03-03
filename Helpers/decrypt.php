<?php
function decrypt_data(string $ciphertext, string $iv, string $tag)
{
    $key = hex2bin(getenv('SECRET_KEY'));

    // Decode exactly like encryption stored it
    $decodedCipher = $ciphertext;
    $decodedIv = $iv;
    $decodedTag = $tag;

    if ($decodedCipher === false || $decodedIv === false || $decodedTag === false) {
        return '';
    }

    $decrypted = openssl_decrypt(
        $decodedCipher,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $decodedIv,
        $decodedTag
    );

    return $decrypted !== false ? $decrypted : null;
}