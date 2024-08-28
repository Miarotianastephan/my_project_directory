<?php

namespace App\Security;

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

    public function authenticate(string $username, string $password): bool
    {
        $ldapConn = ldap_connect($this->host, $this->port);
        if (!$ldapConn) {
            throw new \Exception('Impossible de se connecter au serveur LDAP');
        }

        ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

        // $ldapRdn = 'cn=read-only-admin,dc=example,dc=com';
        $ldapRdn = 'uid='.$username.','.$this->baseDn;

        dump($ldapRdn);

        $ldapBind = @ldap_bind($ldapConn, $ldapRdn, $password);

        if ($ldapBind) {
            ldap_unbind($ldapConn);
            return true;
        }

        ldap_unbind($ldapConn);
        return false;
    }
}