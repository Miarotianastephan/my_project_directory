<?php 
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Constant\Roles;

/**
 * Extension Twig personnalisée pour récupérer les rôles d'utilisateurs.
 *
 * Cette classe permet d'ajouter des fonctions personnalisées dans Twig, notamment
 * une fonction pour obtenir la valeur correspondant à un rôle d'utilisateur à partir
 * de son nom symbolique.
 */
class AppExtension extends AbstractExtension
{

    /**
     * Retourne les fonctions Twig disponibles.
     *
     * Cette méthode enregistre la fonction Twig `get_role`, qui permet de récupérer
     * un rôle utilisateur à partir de son nom symbolique.
     *
     * @return array Un tableau contenant les fonctions Twig disponibles.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_role', [$this, 'getRole']),
        ];
    }

    /**
     * Récupère la constante associée à un rôle d'utilisateur.
     *
     * Cette méthode prend en paramètre un nom de rôle symbolique et retourne
     * la valeur associée à ce rôle dans la classe `Roles`. Si le rôle n'existe
     * pas, la fonction retourne 'UNKNOWN_ROLE'.
     *
     * @param string $roleName Le nom symbolique du rôle à récupérer.
     *
     * @return string Le constant correspondant au rôle, ou 'UNKNOWN_ROLE' si le rôle n'existe pas.
     */
    public function getRole(string $roleName): string
    {
        // Tableau associatif des rôles avec leurs valeurs correspondantes.
        $roles = [
            'admin' => Roles::ADMIN,
            'demandeur' => Roles::DEMANDEUR,
            'sg' => Roles::SG,
            'tresorier' => Roles::TRESORIER,
            'comptable' => Roles::COMPTABLE,
            'commissaire_compte' => Roles::COMMISSAIRE_COMPTE,
        ];

        // Retourne la valeur du rôle ou 'UNKNOWN_ROLE' si le rôle n'existe pas.
        return $roles[$roleName] ?? 'UNKNOWN_ROLE';
    }
}
