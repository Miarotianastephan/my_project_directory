<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)] 
#[ORM\Table(name: 'utilisateur')]
class Utilisateur implements UserInterface, \Serializable,PasswordAuthenticatedUserInterface
{
    
    public function __construct()
    {
        // $this->roles = ['ROLE_USER']; // S'assurer que roles a toujours un tableau
    }
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: 'user_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(name: 'user_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name:"user_matricule" ,length: 255,nullable:false,unique: true)]
    private ?string $user_matricule = null;

    #[ORM\Column(name:"dt_ajout" ,type: 'customdate')]
    private ?\DateTimeInterface $date_ajout = null;

    // #[MaxDepth(1)]
    #[ORM\ManyToOne(targetEntity:GroupeUtilisateur::class ,inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name:"grp_id",referencedColumnName:"grp_id",nullable: false)]
    private ?GroupeUtilisateur $group_utilisateur = null;

    #[ORM\Column]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserMatricule(): ?string
    {
        return $this->user_matricule;
    }

    public function setUserMatricule(string $user_matricule): static
    {
        $this->user_matricule = $user_matricule;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    // public function setDateAjout(string $date_ajout): static
    // {
    //     $dateString = $date_ajout;
    //     $date = \DateTime::createFromFormat('d-M-y', $dateString);

    //     if ($date) {
    //         $formattedDate = $date->format('Y-m-d H:i:s');
    //         // Use $formattedDate in your Doctrine operations
    //         $this->date_ajout = $formattedDate;
    //     }

    //     return $this;
    // }
    public function setDateAjout(\DateTimeInterface $date_ajout): static
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    public function getGroupUtilisateur(): ?GroupeUtilisateur
    {
        return $this->group_utilisateur;
    }

    public function setGroupUtilisateur(?GroupeUtilisateur $group_utilisateur): static
    {
        $this->group_utilisateur = $group_utilisateur;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->getUserMatricule();   
    }

    public function eraseCredentials():void{}    
    
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);  // Symfony requires unique roles
        // return [];
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->user_matricule,
            $this->group_utilisateur,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->id,
            $this->user_matricule,
            $this->group_utilisateur,
        ] = unserialize($serialized);
    }

    public function getPassword(): ?string
    {
        return null;
    }
    public function setPassword(string $password): static
    {
        // $this->password = $password;
        throw new \Exception("Le mot de passe doit être géré via Active Directory.");
        // return $this;
    }

    public function isAdmin(){
        return in_array('ROLE_ADMIN', $this->getRoles());
    }
}
