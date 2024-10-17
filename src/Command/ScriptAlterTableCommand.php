<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ScriptAlterTable',
    description: 'Add a short description for your command',
)]
class ScriptAlterTableCommand extends Command
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
        /* réccuppération de la valeur brute */
        $sql_alter_user = 'ALTER TABLE ce_utilisateur MODIFY DT_AJOUT DEFAULT SYSDATE';
        $sql_log_demande_type = 'ALTER TABLE ce_log_demande_type MODIFY LOG_DM_DATE DEFAULT SYSDATE';
        $sql_detail_demande_piece = 'ALTER TABLE ce_detail_demande_piece MODIFY det_dm_date DEFAULT SYSDATE';
        $connection->beginTransaction();
        try {
            $output->writeln('<info>éxécution de sql_alter_user</info>');
            $stmt = $connection->prepare($sql_alter_user);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_log_demande_type</info>');
            $stmt = $connection->prepare($sql_log_demande_type);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_detail_demande_piece</info>');
            $stmt = $connection->prepare($sql_detail_demande_piece);
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
