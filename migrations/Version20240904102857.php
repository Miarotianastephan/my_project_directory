<?php 

//declare(strict_type=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240904102857 extends AbstractMigration
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
                $this->addSql('CREATE SEQUENCE demande_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE demande_type_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE detail_dm_type_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE exercice_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE grp_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE log_etat_demande_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE cpt_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE user_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE demande (dm_id NUMBER(10) NOT NULL, libelle VARCHAR2(255) NOT NULL, PRIMARY KEY(dm_id))');
        $this->addSql('CREATE TABLE demande_type (dm_type_id NUMBER(10) NOT NULL, entity_code_id NUMBER(10) NOT NULL, utilisateur_id NUMBER(10) NOT NULL, plan_compte_id NUMBER(10) NOT NULL, exercice_id NUMBER(10) NOT NULL, dm_id NUMBER(10) NOT NULL, dm_date DATE NOT NULL, dm_montant DOUBLE PRECISION NOT NULL, dm_mode_paiement VARCHAR2(255) NOT NULL, ref_demande VARCHAR2(255) NOT NULL, dm_etat NUMBER(10) NOT NULL, montant_reel DOUBLE PRECISION DEFAULT \'0\' NOT NULL, mere_id NUMBER(10) DEFAULT NULL NULL, PRIMARY KEY(dm_type_id))');
        $this->addSql('CREATE INDEX IDX_EB7B25FCB417CC34 ON demande_type (entity_code_id)');
        $this->addSql('CREATE INDEX IDX_EB7B25FCFB88E14F ON demande_type (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_EB7B25FC421B9B35 ON demande_type (plan_compte_id)');
        $this->addSql('CREATE INDEX IDX_EB7B25FC89D40298 ON demande_type (exercice_id)');
        $this->addSql('CREATE INDEX IDX_EB7B25FCFADC156C ON demande_type (dm_id)');
        $this->addSql('CREATE TABLE detail_demande_piece (detail_dm_type_id NUMBER(10) NOT NULL, demande_type_id NUMBER(10) NOT NULL, det_dm_piece_url CLOB NOT NULL, det_dm_type_url VARCHAR2(255) NOT NULL, det_dm_date DATE NOT NULL, PRIMARY KEY(detail_dm_type_id))');
        $this->addSql('CREATE INDEX IDX_22B796F1F5BB373C ON detail_demande_piece (demande_type_id)');
        $this->addSql('CREATE TABLE exercice (exercice_id NUMBER(10) NOT NULL, exercice_date_debut DATE NOT NULL, exercice_date_fin DATE DEFAULT NULL NULL, PRIMARY KEY(exercice_id))');
        $this->addSql('CREATE TABLE groupe_utilisateur (grp_id NUMBER(10) NOT NULL, grp_libelle VARCHAR2(255) NOT NULL, grp_niveau NUMBER(10) NOT NULL, PRIMARY KEY(grp_id))');
        $this->addSql('CREATE TABLE log_demande_type (log_dm_id NUMBER(10) NOT NULL, demande_type_id NUMBER(10) NOT NULL, log_dm_date TIMESTAMP(0) NOT NULL, dm_etat NUMBER(10) NOT NULL, log_dm_observation VARCHAR2(255) DEFAULT NULL NULL, user_matricule VARCHAR2(255) NOT NULL, PRIMARY KEY(log_dm_id))');
        $this->addSql('CREATE INDEX IDX_332AB75AF5BB373C ON log_demande_type (demande_type_id)');
        $this->addSql('CREATE TABLE plan_compte (cpt_id NUMBER(10) NOT NULL, cpt_numero VARCHAR2(255) NOT NULL, cpt_libelle VARCHAR2(255) NOT NULL, PRIMARY KEY(cpt_id))');
        $this->addSql('CREATE TABLE utilisateur (user_id NUMBER(10) NOT NULL, grp_id NUMBER(10) NOT NULL, user_matricule VARCHAR2(255) NOT NULL, dt_ajout DATE NOT NULL, PRIMARY KEY(user_id))');
        $this->addSql('CREATE INDEX IDX_1D1C63B3D51E9150 ON utilisateur (grp_id)');
        $this->addSql('CREATE TABLE messenger_messages (id NUMBER(20) NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR2(190) NOT NULL, created_at TIMESTAMP(0) NOT NULL, available_at TIMESTAMP(0) NOT NULL, delivered_at TIMESTAMP(0) DEFAULT NULL NULL, PRIMARY KEY(id))');
        $this->addSql('DECLARE
          constraints_Count NUMBER;
        BEGIN
          SELECT COUNT(CONSTRAINT_NAME) INTO constraints_Count
            FROM USER_CONSTRAINTS
           WHERE TABLE_NAME = \'MESSENGER_MESSAGES\'
             AND CONSTRAINT_TYPE = \'P\';
          IF constraints_Count = 0 OR constraints_Count = \'\' THEN
            EXECUTE IMMEDIATE \'ALTER TABLE MESSENGER_MESSAGES ADD CONSTRAINT MESSENGER_MESSAGES_AI_PK PRIMARY KEY (ID)\';
          END IF;
        END;');
        $this->addSql('CREATE SEQUENCE MESSENGER_MESSAGES_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TRIGGER MESSENGER_MESSAGES_AI_PK
           BEFORE INSERT
           ON MESSENGER_MESSAGES
           FOR EACH ROW
        DECLARE
           last_Sequence NUMBER;
           last_InsertID NUMBER;
        BEGIN
           IF (:NEW.ID IS NULL OR :NEW.ID = 0) THEN
              SELECT MESSENGER_MESSAGES_SEQ.NEXTVAL INTO :NEW.ID FROM DUAL;
           ELSE
              SELECT NVL(Last_Number, 0) INTO last_Sequence
                FROM User_Sequences
               WHERE Sequence_Name = \'MESSENGER_MESSAGES_SEQ\';
              SELECT :NEW.ID INTO last_InsertID FROM DUAL;
              WHILE (last_InsertID > last_Sequence) LOOP
                 SELECT MESSENGER_MESSAGES_SEQ.NEXTVAL INTO last_Sequence FROM DUAL;
              END LOOP;
              SELECT MESSENGER_MESSAGES_SEQ.NEXTVAL INTO last_Sequence FROM DUAL;
           END IF;
        END;');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FCB417CC34 FOREIGN KEY (entity_code_id) REFERENCES plan_compte (cpt_id)');
        $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FCFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (user_id)');
        $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FC421B9B35 FOREIGN KEY (plan_compte_id) REFERENCES plan_compte (cpt_id)');
        $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FC89D40298 FOREIGN KEY (exercice_id) REFERENCES exercice (exercice_id)');
        $this->addSql('ALTER TABLE demande_type ADD CONSTRAINT FK_EB7B25FCFADC156C FOREIGN KEY (dm_id) REFERENCES demande (dm_id)');
        $this->addSql('ALTER TABLE detail_demande_piece ADD CONSTRAINT FK_22B796F1F5BB373C FOREIGN KEY (demande_type_id) REFERENCES demande_type (dm_type_id)');
        $this->addSql('ALTER TABLE log_demande_type ADD CONSTRAINT FK_332AB75AF5BB373C FOREIGN KEY (demande_type_id) REFERENCES demande_type (dm_type_id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D51E9150 FOREIGN KEY (grp_id) REFERENCES groupe_utilisateur (grp_id)');
    }
    public function down(Schema $schema): void{
                $this->addSql('DROP SEQUENCE demande_seq');
        $this->addSql('DROP SEQUENCE demande_type_seq');
        $this->addSql('DROP SEQUENCE detail_dm_type_seq');
        $this->addSql('DROP SEQUENCE exercice_seq');
        $this->addSql('DROP SEQUENCE grp_seq');
        $this->addSql('DROP SEQUENCE log_etat_demande_seq');
        $this->addSql('DROP SEQUENCE cpt_seq');
        $this->addSql('DROP SEQUENCE user_seq');
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
        $this->addSql('DROP TABLE groupe_utilisateur');
        $this->addSql('DROP TABLE log_demande_type');
        $this->addSql('DROP TABLE plan_compte');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }

}
