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
    name: 'Scripts',
    description: 'Les scripts à exécuter pour le bon fonctionnement du projet',
)]
class ScriptsCommand extends Command
{
    private EntityManagerInterface  $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }



    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->entityManager->getConnection();
        $sql_alter_user = 'ALTER TABLE ce_utilisateur MODIFY DT_AJOUT DEFAULT SYSDATE';
        $sql_log_demande_type = 'ALTER TABLE ce_log_demande_type MODIFY LOG_DM_DATE DEFAULT SYSDATE';
        $sql_detail_demande_piece = 'ALTER TABLE ce_detail_demande_piece MODIFY det_dm_date DEFAULT SYSDATE';

        $sql_ajout_user_admin = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Admin\', 0)';
        $sql_ajout_user_resp_commission = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Responsable Commission\', 10)';
        $sql_ajout_user_sg = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Secrétaire Générale\', 20)';
        $sql_ajout_user_tresorier = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Trésorier\', 30)';
        $sql_ajout_user_comptable = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Comptable\', 40)';
        $sql_ajout_user_commissaire_compte = 'insert into ce_groupe_utilisateur (grp_id, grp_libelle, grp_niveau) values (grp_seq.NEXTVAL, \'Commissaire aux Comptes\', 50)';


        return Command::SUCCESS;
    }
}
