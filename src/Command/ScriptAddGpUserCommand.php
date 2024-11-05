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
    name: 'ScriptAddGpUser',
    description: 'Add a short description for your command',
)]
class ScriptAddGpUserCommand extends Command
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
        $sql_ajout_user_admin = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Admin\', 0)';
        $sql_ajout_user_resp_commission = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Responsable Commission\', 10)';
        $sql_ajout_user_sg = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Secrétaire Générale\', 20)';
        $sql_ajout_user_tresorier = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Trésorier\', 30)';
        $sql_ajout_user_comptable = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Comptable\', 40)';
        $sql_ajout_user_commissaire_compte = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Commissaire aux Comptes\', 50)';

        $connection->beginTransaction();
        try {
            $output->writeln('<info>éxécution de sql_ajout_user_admin</info>');
            $stmt = $connection->prepare($sql_ajout_user_admin);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_user_resp_commission</info>');
            $stmt = $connection->prepare($sql_ajout_user_resp_commission);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_user_sg</info>');
            $stmt = $connection->prepare($sql_ajout_user_sg);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_user_tresorier</info>');
            $stmt = $connection->prepare($sql_ajout_user_tresorier);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_user_comptable</info>');
            $stmt = $connection->prepare($sql_ajout_user_comptable);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_user_commissaire_compte</info>');
            $stmt = $connection->prepare($sql_ajout_user_commissaire_compte);
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
