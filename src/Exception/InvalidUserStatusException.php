<?php
namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class InvalidUserStatusException extends CustomUserMessageAuthenticationException
{
    public function __construct($defaultUserMatricule = "")
    {
        $message = sprintf('L\'utilisateur "%s" n\'est pas actif.', $defaultUserMatricule);
        parent::__construct($message);
    }
}