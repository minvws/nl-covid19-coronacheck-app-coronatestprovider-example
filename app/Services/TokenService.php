<?php

namespace App\Services;

use \Exception;

class TokenService
{

    protected string $tokenCharacters = 'BCFGJLQRSTUVXYZ23456789';

    public function getRandomToken() : String
    {
        $randomString = '';
        for ($i = 0; $i < 14; $i++)
        {
            $randomString .= $this->tokenCharacters[rand(0, strlen($this->tokenCharacters)-1)];
        }
        return $randomString;
    }

    public function generateChecksum($token) {

        $factor = 2;
        $sum = 0;
        $numberOfValidInputCharacters = strlen($this->tokenCharacters);

        for($i = strlen($token) - 1; $i >= 0; $i--){
            $codePoint = strpos($this->tokenCharacters, $token[$i]);

            $addend = $factor * $codePoint;

            // Alternate the "factor" that each "codePoint" is multiplied by
            $factor = ($factor == 2) ? 1 : 2;

            // Sum the digits of the "addend" as expressed in base "n"
            $addend = (floor($addend / $numberOfValidInputCharacters)) + ($addend % $numberOfValidInputCharacters);
            $sum += $addend;
        }

        $remainder = $sum % $numberOfValidInputCharacters;
        $checkCodePoint = ($numberOfValidInputCharacters - $remainder) % $numberOfValidInputCharacters;

        return  $this->tokenCharacters[$checkCodePoint];
    }


}
