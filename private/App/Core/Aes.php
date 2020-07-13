<?php

class Aes
{
    private $chiper = 'aes-256-cbc';
    public function encrypt($plaintext, $key)
    {
        $password = substr(hash('sha1', $key, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        return base64_encode(openssl_encrypt($plaintext, $this->chiper, $password, 0, $iv));
    }

    public function decrypt($chipertext, $key)
    {
        $password = substr(hash('sha1', $key, true), 0, 32);
        $iv = chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0) . chr(0x0);
        return openssl_decrypt(base64_decode($chipertext), $this->chiper, $password, 0, $iv);
    }
}