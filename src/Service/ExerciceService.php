<?php

namespace App\Service;

use App\Repository\ExerciceRepository;

class ExerciceService
{

    public $exerciceRepository;

    public function __construct(ExerciceRepository $exoRepository) {
        $this->exerciceRepository = $exoRepository;
    }

    // fonction pour avoir la dernière exercice en cours et non archivé
    public function getLastExercice() {
        return $this->exerciceRepository->findMostRecentOpenExercice();
    }
}