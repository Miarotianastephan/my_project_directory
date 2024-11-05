<?php

namespace App\DataFixtures;

use App\Entity\GroupeUtilisateur;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UtilisateurFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création d'un groupe utilisateur
        $groupe = $manager->getRepository(GroupeUtilisateur::class)->findByLibelle('Admin');
        
        if (!$groupe) {
            throw new \Exception('GroupeUtilisateur avec ID 1 non trouvé dans la base de données.');
        }

        // Création d'un utilisateur
        $utilisateur = new Utilisateur();
        $utilisateur->setUserMatricule('galielelo'); // Assurez-vous que ce champ est unique
        $utilisateur->setDateAjout(new \DateTime()); // Format de date correspondant à votre configuration
        $utilisateur->setGroupUtilisateur($groupe); // Associer l'utilisateur au groupe créé
        $utilisateur->setRoles(['ROLE_0']); // Optionnel, car il est déjà défini dans le constructeur

        $manager->persist($utilisateur);
        
        // Exécution de toutes les requêtes
        $manager->flush();

    }
}
