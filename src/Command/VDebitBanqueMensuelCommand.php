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
    name: 'VDebitBanqueMensuel',
    description: 'Liste des débit mensuel de banque par exercice',
)]
class VDebitBanqueMensuelCommand extends Command
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
            ->setDescription('Création de vues liste de débit mensuel de banque par exercice')
            ->setHelp('à filtrer selon l\' exercice à rechercher');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->entityManager->getConnection();
        /* réccuppération de la valeur brute */
        $sql = 'CREATE VIEW v_debit_banque_mensuel AS
                SELECT 
                    COALESCE(SUM(vmdb.MVT_MONTANT), 0) AS total,
                    TO_CHAR(e.evn_date_operation, \'YYYY-MM\') AS mois_operation,
                    e.evn_exercice_id
                FROM 
                    v_mouvement_debit_banque vmdb
                LEFT JOIN 
                    evenement e ON vmdb.mvt_evenement_id = e.evn_id
                GROUP BY 
                    TO_CHAR(e.evn_date_operation, \'YYYY-MM\'),
                    e.evn_exercice_id
                ORDER BY 
                    mois_operation';


        /* réccuppération par mois
        $sql = 'CREATE VIEW v_debit_banque_mensuel AS
                WITH calendrier AS (
                    SELECT
                        ADD_MONTHS(TRUNC(ex.exercice_date_debut, \'YYYY\'), LEVEL - 1) AS mois,
                        ex.exercice_id
                    FROM exercice ex
                    CONNECT BY LEVEL <= MONTHS_BETWEEN(
                        TRUNC(ADD_MONTHS(ex.exercice_date_debut, 12), \'YYYY\') - 1,
                        TRUNC(ex.exercice_date_debut, \'YYYY\')
                    ) + 1
                        AND ADD_MONTHS(TRUNC(ex.exercice_date_debut, \'YYYY\'), LEVEL - 1) < TRUNC(ADD_MONTHS(ex.exercice_date_debut, 12), \'YYYY\')
                )
                SELECT
                    c.mois,
                    TO_CHAR(c.mois, \'YYYY-MM\') AS mois_operation,
                    COALESCE(SUM(vmdb.MVT_MONTANT), 0) AS total,
                    c.exercice_id
                FROM
                    calendrier c
                LEFT JOIN
                    evenement e ON TRUNC(e.evn_date_operation, \'MM\') = c.mois
                    AND e.evn_exercice_id = c.exercice_id
                LEFT JOIN
                    v_mouvement_debit_banque vmdb ON vmdb.mvt_evenement_id = e.evn_id
                GROUP BY
                    c.mois,
                    c.exercice_id
                ORDER BY
                    c.mois';
        */

        try {
            $stmt = $connection->prepare($sql);
            //$stmt->bindValue('libelle_compte', 'banque',\PDO::PARAM_STR);

            // Afficher un message d'information avant l'exécution
            $output->writeln('<info>Création de la vue VDebitBanqueMensuel en cours...</info>');

            // Afficher la requête et le paramètre avant exécution (pour debug)
            //$output->writeln($stmt->getSQL());

            // Exécuter la requête SQL
            $stmt->executeStatement();

            $output->writeln('<info>La vue VDebitBanqueMensuel a été créée avec succès !</info>');
            return Command::SUCCESS;
        }catch (\Exception $e){
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
