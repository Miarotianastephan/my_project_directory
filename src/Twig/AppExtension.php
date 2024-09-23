<?php 
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Constant\Roles;

class AppExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_role', [$this, 'getRole']),
        ];
    }

    public function getRole(string $roleName): string
    {
        $roles = [
            'admin' => Roles::ADMIN,
            'demandeur' => Roles::DEMANDEUR,
            'sg' => Roles::SG,
            'tresorier' => Roles::TRESORIER,
            'comptable' => Roles::COMPTABLE,
        ];

        return $roles[$roleName] ?? 'UNKNOWN_ROLE';
    }
}
