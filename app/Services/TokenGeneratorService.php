<?php

namespace App\Services;

use \Exception;

class TokenGeneratorService
{

    public function getRandomToken() : String
    {
        $characters = 'BCFGJLQRSTUVXYZ23456789';
        $randomString = '';
        for ($i = 0; $i < 14; $i++)
        {
            $randomString .= $characters[rand(0, strlen($characters)-1)];
        }
        return $randomString;
    }

}
