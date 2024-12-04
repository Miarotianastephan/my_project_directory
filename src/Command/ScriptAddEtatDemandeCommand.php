<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ScriptAddEtatDemande',
    description: 'Add a short description for your command',
)]
class ScriptAddEtatDemandeCommand extends Command
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
        $sql_ajout_initie = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,100, \'Initié\')';
        $sql_ajout_modifier = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,101, \'Modifié\')';
        $sql_ajout_attente_fonds = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,200, \'Attente fonds\')';
        $sql_ajout_attente_modification = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,201, \'Attente modification\')';
        $sql_ajout_attente_versement = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,202, \'Attente versement\')';
        $sql_ajout_comptabiliser = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,300, \'Comptabilisé\')';
        $sql_ajout_justifier = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,301, \'Refusé\')';
        $sql_ajout_refuser = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,400, \'Justifié\')';
        $sql_ajout_reverser = 'insert into ce_etat_demande(etat_id, etat_code, etat_libelle) VALUES (etat_dm_seq.NEXTVAL ,401, \'Reversé\')';

        $connection->beginTransaction();
        try {
            $output->writeln('<info>éxécution de sql_ajout_initie</info>');
            $stmt = $connection->prepare($sql_ajout_initie);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_modifier</info>');
            $stmt = $connection->prepare($sql_ajout_modifier);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_attente_fonds</info>');
            $stmt = $connection->prepare($sql_ajout_attente_fonds);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_attente_modification</info>');
            $stmt = $connection->prepare($sql_ajout_attente_modification);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_attente_versement</info>');
            $stmt = $connection->prepare($sql_ajout_attente_versement);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_comptabiliser</info>');
            $stmt = $connection->prepare($sql_ajout_comptabiliser);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_justifier</info>');
            $stmt = $connection->prepare($sql_ajout_justifier);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_refuser</info>');
            $stmt = $connection->prepare($sql_ajout_refuser);
            $stmt->executeStatement();

            $output->writeln('<info>éxécution de sql_ajout_reverser</info>');
            $stmt = $connection->prepare($sql_ajout_reverser);
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
