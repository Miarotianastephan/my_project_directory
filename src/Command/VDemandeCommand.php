<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:demande:view', // Convention de nommage : `app:<nom>`
    description: 'Créer une vue pour les demandes',
)]
class VDemandeCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Exécute un script pour créer une vue SQL sans entité')
            ->setHelp('Cette commande crée une vue pour les demandes dans la base de données.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->entityManager->getConnection();
        $sql = "CREATE VIEW v_demande AS
                    SELECT 
                        e.exercice_id,
                        p.cpt_id,
                        TO_CHAR(e.exercice_date_debut, 'YYYY-MM') AS mois,
                        SUM(d.dm_montant) AS total_montant
                    FROM demande_type d
                    JOIN plan_compte p ON d.plan_compte_id = p.cpt_id
                    JOIN exercice e ON d.exercice_id = e.exercice_id
                    GROUP BY 
                        e.exercice_id,
                        p.cpt_id,
                        TO_CHAR(e.exercice_date_debut, 'YYYY-MM')
                ";

        try {
            $stmt = $connection->prepare($sql);
            //$output->writeln($stmt->getSlq());
            $stmt->executeQuery();
            $connection->commit();
            $output->writeln('<info>Vue v_demande créée avec succès.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Erreur lors de la création de la vue : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
