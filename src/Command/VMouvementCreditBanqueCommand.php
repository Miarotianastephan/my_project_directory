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
    name: 'VMouvementCreditBanque',
    description: 'Liste des mouvements de crédit en chèque = liste approvisionnement banque',
)]
class VMouvementCreditBanqueCommand extends Command
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
            ->setDescription('Création de vues liste de mouvement')
            ->setHelp('Liste des mouvements de crédit en banque = liste approvisionnement banque');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->entityManager->getConnection();

        $sql = 'CREATE VIEW v_mouvement_credit_banque AS 
                SELECT 
                    m.mvn_id , 
                    m.mvt_evenement_id , 
                    m.mvt_compte_id ,
                    m.mvt_montant , 
                    m.is_mvt_debit
                FROM mouvement m
                LEFT JOIN plan_compte p
                ON m.mvt_compte_id = p.cpt_id 
                WHERE 
                    LOWER(p.cpt_libelle) like \'banque\' 
                    AND m.is_mvt_debit = 0';

        try {
            $stmt = $connection->prepare($sql);
            //$stmt->bindValue('libelle_compte', 'banque',\PDO::PARAM_STR);

            // Afficher un message d'information avant l'exécution
            $output->writeln('<info>Création de la vue VMouvementCreditBanque en cours...</info>');

            // Afficher la requête et le paramètre avant exécution (pour debug)
            //$output->writeln($stmt->getSQL());

            // Exécuter la requête SQL
            $stmt->executeStatement();
            //$connection->commit();

            $output->writeln('<info>La vue VMouvementCreditBanque a été créée avec succès !</info>');
            return Command::SUCCESS;
        }catch (\Exception $e){
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
