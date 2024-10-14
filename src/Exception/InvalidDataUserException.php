<?php
namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class InvalidDataUserException extends CustomUserMessageAuthenticationException
{
    public function __construct($default = "")
    {
        $message = "Assurer que tous les champs soient remplis !";
        if( strlen($default) > 1 ){
            $message = $default;
        }
        parent::__construct($message);
    }
}
