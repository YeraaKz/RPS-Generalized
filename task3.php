<?php

class  Security
{
    private $key;
    public function __construct()
    {
        $this->key = random_bytes(32);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function generateHMAC($move)
    {
        return hash_hmac('sha256', $move, $this->key);
    }
}


