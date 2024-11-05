<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ScriptAddDemande',
    description: 'Add a short description for your command',
)]
class ScriptAddDemandeCommand extends Command
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
        $sql_ajout_decaissement = 'insert into ce_demande (dm_id, libelle, dm_code) values (demande_seq.NEXTVAL, \'Décaissement\', 10)';
        $sql_ajout_approvisionnement = 'insert into ce_demande (dm_id, libelle, dm_code) values (demande_seq.NEXTVAL, \'Approvisionnement\', 20)';
        $connection->beginTransaction();
        try {
            $output->writeln('<info>éxécution de sql_ajout_decaissement</info>');
            $stmt = $connection->prepare($sql_ajout_decaissement);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_approvisionnement</info>');
            $stmt = $connection->prepare($sql_ajout_approvisionnement);
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
