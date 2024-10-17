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
    name: 'ScriptAddBudgetType',
    description: 'Add a short description for your command',
)]
class ScriptAddBudgetTypeCommand extends Command
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
        $sql_ajout_depense = 'insert into ce_budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, \'Dépense\')';
        $sql_ajout_inverstissement = 'insert into ce_budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, \'Investissement\')';
        $sql_ajout_fonctionnement = 'insert into ce_budget_type (BUDGET_TYPE_ID, LIBELLE) values (budget_type_seq.NEXTVAL, \'Fonctionnement\')';
        $connection->beginTransaction();
        try {
            $output->writeln('<info>éxécution de sql_ajout_depense</info>');
            $stmt = $connection->prepare($sql_ajout_depense);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_inverstissement</info>');
            $stmt = $connection->prepare($sql_ajout_inverstissement);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_fonctionnement</info>');
            $stmt = $connection->prepare($sql_ajout_fonctionnement);
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
