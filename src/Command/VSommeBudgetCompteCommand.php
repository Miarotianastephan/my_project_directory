<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'VSommeBudgetCompte',
    description: 'Add a short description for your command',
)]
class VSommeBudgetCompteCommand extends Command
{
    private EntityManagerInterface  $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        /*$this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;*/

        $this
            ->setDescription('Création de vues sommes budgets par classe de compte')
            ->setHelp('Exemple classe 6, 7,..');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->entityManager->getConnection();
        $sql = "CREATE VIEW ce_v_somme_budget_compte AS
                SELECT 
                    SUBSTR(cm.cpt_numero, 1, 1) as premier_chiffre,
                    SUM(det_b.budget_montant) as total_budget,
                    det_b.exercice_id
                FROM 
                    ce_detail_budget det_b 
                    INNER JOIN ce_compte_mere cm ON det_b.compte_mere_id = cm.id
                WHERE 
                    cm.cpt_numero LIKE '1%'
                or cm.cpt_numero LIKE '2%' 
                or cm.cpt_numero LIKE '3%' 
                or cm.cpt_numero LIKE '4%'
                or cm.cpt_numero LIKE '5%'
                or cm.cpt_numero LIKE '6%'
                or cm.cpt_numero LIKE '7%'
                or cm.cpt_numero LIKE '8%'
                or cm.cpt_numero LIKE '9%'
                GROUP BY 
                    SUBSTR(cm.cpt_numero, 1, 1), det_b.exercice_id";
        $connection->beginTransaction();
        try {
            $stmt = $connection->prepare($sql);
            // Afficher un message d'information avant l'exécution
            $output->writeln('<info>Création de la vue VSommeBudgetCompte en cours...</info>');
            // Afficher la requête et le paramètre avant exécution (pour debug)
            //$output->writeln($stmt->getSQL());

            // Exécuter la requête SQL
            $stmt->executeStatement();
            $connection->commit();

            $output->writeln('<info>La vue VSommeBudgetCompte a été créée avec succès !</info>');
            return Command::SUCCESS;
        }catch (\Exception $e){
            $connection->rollback();
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
