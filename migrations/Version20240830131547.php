<?php 

//declare(strict_type=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240830131547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void
    {
        parent::preUp($schema);
        $this->addSql("ALTER session SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS' NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS' NLS_TIMESTAMP_TZ_FORMAT = 'YYYY-MM-DD HH24:MI:SS TZH:TZM'");
    }

    public function up(Schema $schema): void{
                // $this->addSql('CREATE SEQUENCE log_etat_demande_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        // $this->addSql('CREATE SEQUENCE cpt_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        // $this->addSql('CREATE TABLE demande (dm_id NUMBER(10) NOT NULL, libelle VARCHAR2(255) NOT NULL, PRIMARY KEY(dm_id))');
        // $this->addSql('CREATE TABLE demande_type (dm_type_id NUMBER(10) NOT NULL, entity_code_id NUMBER(10) NOT NULL, utilisateur_id NUMBER(10) NOT NULL, plan_compte_id NUMBER(10) NOT NULL, exercice_id NUMBER(10) NOT NULL, dm_id NUMBER(10) NOT NULL, dm_date DATE NOT NULL, dm_montant DOUBLE PRECISION NOT NULL, dm_mode_paiement VARCHAR2(255) NOT NULL, ref_demande VARCHAR2(255) NOT NULL, dm_etat NUMBER(10) NOT NULL, PRIMARY KEY(dm_type_id))');
        // $this->addSql('CREATE INDEX IDX_EB7B25FCB417CC34 ON demande_type (entity_code_id)');
        // $this->addSql('CREATE INDEX IDX_EB7B25FCFB88E14F ON demande_type (utilisateur_id)');
        // $this->addSql('CREATE INDEX IDX_EB7B25FC421B9B35 ON demande_type (plan_compte_id)');
        // $this->addSql('CREATE INDEX IDX_EB7B25FC89D40298 ON demande_type (exercice_id)');
        // $this->addSql('CREATE INDEX IDX_EB7B25FCFADC156C ON demande_type (dm_id)');
        // $this->addSql('CREATE TABLE detail_demande_piece (detail_dm_type_id NUMBER(10) NOT NULL, demande_type_id NUMBER(10) NOT NULL, det_dm_piece_url CLOB NOT NULL, det_dm_type_url VARCHAR2(255) NOT NULL, det_dm_date DATE NOT NULL, PRIMARY KEY(detail_dm_type_id))');
        // $this->addSql('CREATE INDEX IDX_22B796F1F5BB373C ON detail_demande_piece (demande_type_id)');
        // $this->addSql('CREATE TABLE exercice (exercice_id NUMBER(10) NOT NULL, exercice_date_debut DATE NOT NULL, exercice_date_fin DATE DEFAULT NULL NULL, PRIMARY KEY(exercice_id))');
        // $this->addSql('CREATE TABLE log_demande_type (log_dm_id NUMBER(10) NOT NULL, demande_type_id NUMBER(10) NOT NULL, log_dm_date TIMESTAMP(0) NOT NULL, dm_etat NUMBER(10) NOT NULL, log_dm_observation VARCHAR2(255) DEFAULT NULL NULL, user_matricule VARCHAR2(255) NOT NULL, PRIMARY KEY(log_dm_id))');
        // $this->addSql('CREATE INDEX IDX_332AB75AF5BB373C ON log_demande_type (demande_type_id)');
        // $this->addSql('CREATE TABLE plan_compte (cpt_id NUMBER(10) NOT NULL, cpt_numero VARCHAR2(255) NOT NULL, cpt_libelle VARCHAR2(255) NOT NULL, PRIMARY KEY(cpt_id))');
        // $this->addSql('CREATE TABLE utilisateur (user_id NUMBER(10) NOT NULL, grp_id NUMBER(10) NOT NULL, user_matricule VARCHAR2(255) NOT NULL, dt_ajout DATE NOT NULL, roles CLOB NOT NULL, PRIMARY KEY(user_id))');
        // $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B312525178 ON utilisateur (user_matricule)');
        // $this->addSql('CREATE INDEX IDX_1D1C63B3D51E9150 ON utilisateur (grp_id)');
        $this->addSql('COMMENT ON COLUMN utilisateur.roles IS \'(DC2Type:json)\'');
        // $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FCB417CC34 FOREIGN KEY (entity_code_id) REFERENCES plan_compte (cpt_id)');
        // $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FCFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (user_id)');
        // $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FC421B9B35 FOREIGN KEY (plan_compte_id) REFERENCES plan_compte (cpt_id)');
        // $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FC89D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (exercice_id)');
        // $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FCFADC156C FOREIGN KEY (dm_id) REFERENCES demande (dm_id)');
        // $this->addSql('ALTER TABLE detail_demande_piece ADD CONSTRAINT FK_22B796F1F5BB373C FOREIGN KEY (demande_type_id) REFERENCES demande_type (dm_type_id)');
        // $this->addSql('ALTER TABLE log_demande_type ADD CONSTRAINT FK_332AB75AF5BB373C FOREIGN KEY (demande_type_id) REFERENCES demande_type (dm_type_id)');
        // $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D51E9150 FOREIGN KEY (grp_id) REFERENCES groupe_utilisateur (grp_id)');
        $this->addSql('ALTER TABLE MESSENGER_MESSAGES MODIFY (created_at TIMESTAMP(0) DEFAULT NULL, available_at TIMESTAMP(0) DEFAULT NULL, delivered_at TIMESTAMP(0) DEFAULT NULL)');
    }
    public function down(Schema $schema): void{
                $this->addSql('DROP SEQUENCE log_etat_demande_seq');
        $this->addSql('DROP SEQUENCE cpt_seq');
        $this->addSql('ALTER TABLE demande_type DROP CONSTRAINT FK_EB7B25FCB417CC34');
        $this->addSql('ALTER TABLE demande_type DROP CONSTRAINT FK_EB7B25FCFB88E14F');
        $this->addSql('ALTER TABLE demande_type DROP CONSTRAINT FK_EB7B25FC421B9B35');
        $this->addSql('ALTER TABLE demande_type DROP CONSTRAINT FK_EB7B25FC89D40298');
        $this->addSql('ALTER TABLE demande_type DROP CONSTRAINT FK_EB7B25FCFADC156C');
        $this->addSql('ALTER TABLE detail_demande_piece DROP CONSTRAINT FK_22B796F1F5BB373C');
        $this->addSql('ALTER TABLE log_demande_type DROP CONSTRAINT FK_332AB75AF5BB373C');
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT FK_1D1C63B3D51E9150');
        $this->addSql('DROP TABLE demande');
        $this->addSql('DROP TABLE demande_type');
        $this->addSql('DROP TABLE detail_demande_piece');
        $this->addSql('DROP TABLE exercice');
        $this->addSql('DROP TABLE log_demande_type');
        $this->addSql('DROP TABLE plan_compte');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('ALTER TABLE messenger_messages MODIFY (CREATED_AT TIMESTAMP(0) DEFAULT NULL, AVAILABLE_AT TIMESTAMP(0) DEFAULT NULL, DELIVERED_AT TIMESTAMP(0) DEFAULT NULL)');
    }

}
