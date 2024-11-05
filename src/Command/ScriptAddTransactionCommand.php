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
    name: 'ScriptAddTransaction',
    description: 'Add a short description for your command',
)]
class ScriptAddTransactionCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->entityManager->getConnection();
        $sql_ajout_ce001 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-001\',\'Encaissement Subvention BFM\')';
        $sql_ajout_ce002 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-002\',\'Approvisionnement petite caissse Siège\')';
        $sql_ajout_ce003 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-003\',\'Approvisionnement petite caissse RT\')';
        $sql_ajout_ce004 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-004\',\'Paiement facture par chèque\')';
        $sql_ajout_ce005 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-005\',\'Paiement facture en espèces sièges\')';
        $sql_ajout_ce006 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-006\',\'Paiement facture en espèces RT\')';
        $sql_ajout_ce007 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-007\',\'Dépense directe payée par BFM\')';
        $sql_ajout_ce010 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-010\',\'Encaissement interêt opération\')';
        $sql_ajout_ce011 = 'insert into ce_transaction_type (TRS_ID, TRS_CODE, TRS_LIBELLE) VALUES (trs_seq.NEXTVAL,\'CE-011\',\'Comptabilisation des frais bancaires\')';
        $connection->beginTransaction();
        try {
            $output->writeln('<info>éxécution de sql_ajout_ce001</info>');
            $stmt = $connection->prepare($sql_ajout_ce001);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_ce002</info>');
            $stmt = $connection->prepare($sql_ajout_ce002);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_ce003</info>');
            $stmt = $connection->prepare($sql_ajout_ce003);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_ce004</info>');
            $stmt = $connection->prepare($sql_ajout_ce004);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_ce005</info>');
            $stmt = $connection->prepare($sql_ajout_ce005);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_ce006</info>');
            $stmt = $connection->prepare($sql_ajout_ce006);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_ce007</info>');
            $stmt = $connection->prepare($sql_ajout_ce007);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_ce010</info>');
            $stmt = $connection->prepare($sql_ajout_ce010);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_ce011</info>');
            $stmt = $connection->prepare($sql_ajout_ce011);
            $stmt->executeStatement();

            $connection->commit();
            $output->writeln('<info>Succèes !</info>');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $connection->rollback();
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
