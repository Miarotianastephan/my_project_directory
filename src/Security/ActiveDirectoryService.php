<?php

namespace App\Security;

/**
 * Service de gestion de l'authentification via Active Directory.
 *
 * Cette classe permet d'interagir avec un serveur LDAP pour authentifier un utilisateur
 * en utilisant ses identifiants Active Directory.
 */
class ActiveDirectoryService
{
    private $host;
    private $port;
    private $baseDn;

    public function __construct(string $host, int $port, string $baseDn)
    {
        $this->host = $host;
        $this->port = $port;
        $this->baseDn = $baseDn;
    }

    /**
     * Authentifie un utilisateur auprès du serveur LDAP.
     *
     * @param string $username Le nom d'utilisateur (ex. `user.matricule`).
     * @param string $password Le mot de passe de l'utilisateur.
     *
     * @return bool Retourne `true` si l'utilisateur est authentifié, `false` sinon.
     *
     * @throws \Exception Si la connexion au serveur LDAP échoue.
     */
    public function authenticate(string $username, string $password): bool
    {
        $ldapConn = ldap_connect($this->host, $this->port);
        if (!$ldapConn) {
            throw new \Exception('Impossible de se connecter au serveur LDAP');
        }

        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        $ldapRdn = $username.'@bcm.int';
        $ldapBind = @ldap_bind($ldapConn, $ldapRdn, $password);

        if ($ldapBind) {
            ldap_unbind($ldapConn);
            return true;
        }

        ldap_unbind($ldapConn);
        return false;
    }
}