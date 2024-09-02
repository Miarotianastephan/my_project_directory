<?php

namespace App\Service;


class CompteService
{

    public $numero;
    public $intitule;
    public  $enfants = [];

    public function __construct(string $numero, string $intitule) {
        $this->numero = $numero;
        $this->intitule = $intitule; 
    }

    function constructionhirarchie(){
        $comptes = [];
        $c1 = new CompteService('66', 'Activite Sportif');
        $c2 =  new CompteService('661', 'Activite Sportif collation');
        $c3 = new CompteService('661100', 'Activite Sportif collation: foot-ball');
        array_push($comptes, $c1);
        array_push($comptes, $c2);
        array_push($comptes, $c3);

        $hierarchie = [];
        $comptesIndexe = [];

        // Indexation des comptes par son numéro
        foreach($comptes as $cpt){
            $comptesIndexe[$cpt->numero] = $cpt;
        }

        // Construction de la hiérarchie
        foreach($comptes as $cpt){
            // rech du parent
            $isFindParent = false;
            foreach($comptesIndexe as $possibleParent){
                if($cpt->numero !== $possibleParent->numero && strpos($cpt->numero, $possibleParent->numero)==0){
                    // le comptes est enfant du possible parents
                    $possibleParent->enfants[] = $cpt;
                    $isFindParent = true;
                    break;
                }
            }

            // si aucun parent n'est trouvé, c'est un élément racine
            if(!$isFindParent){
                $hierarchie[] = $cpt;
            }
        }
        return $hierarchie;

    }

    function afficherHierarchie($comptes, $niveau = 0){
        foreach($comptes as $compte){
            $str = str_repeat(" ", $niveau).$compte->numero." ".$compte->intitule.PHP_EOL;
            dump($str);
            if(!empty($compte->enfants)){
                $this->afficherHierarchie($compte->enfants, $niveau+1);
            }
        }
    }

    

}