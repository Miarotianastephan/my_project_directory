<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718125318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE GRP_SEQ');
        $this->addSql('DROP SEQUENCE USRS_SEQ');
        $this->addSql('CREATE SEQUENCE groupe_utilisateur_grp_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
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
        $this->addSql('ALTER TABLE UTILISATEUR_SYSTEM DROP CONSTRAINT SYS_C007014');
        $this->addSql('DROP TABLE UTILISATEUR_SYSTEM');
        $this->addSql('DROP INDEX sys_c007009');
        $this->addSql('ALTER TABLE GROUPE_UTILISATEUR MODIFY (grp_libelle VARCHAR2(255) DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE groupe_utilisateur_grp_id_seq');
        $this->addSql('CREATE SEQUENCE GRP_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE USRS_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE UTILISATEUR_SYSTEM (USRS_ID NUMBER(10) NOT NULL, MATRICULE VARCHAR2(6) NOT NULL, DATE_CREATION DATE DEFAULT \'SYSDATE\' NULL, GRP_ID NUMBER(10) NOT NULL, PRIMARY KEY(USRS_ID))');
        $this->addSql('CREATE INDEX IDX_606AA7881DAB1036 ON UTILISATEUR_SYSTEM (GRP_ID)');
        $this->addSql('ALTER TABLE UTILISATEUR_SYSTEM ADD CONSTRAINT SYS_C007014 FOREIGN KEY (GRP_ID) REFERENCES GROUPE_UTILISATEUR (GRP_ID)');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE groupe_utilisateur MODIFY (GRP_LIBELLE VARCHAR2(1000) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX sys_c007009 ON groupe_utilisateur (GRP_LIBELLE)');
    }
}
