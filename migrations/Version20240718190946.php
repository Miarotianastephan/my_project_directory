<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718190946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE GROUPE_UTILISATEUR_GRP_ID_SEQ');
        $this->addSql('DROP SEQUENCE MESSENGER_MESSAGES_SEQ');
        $this->addSql('CREATE SEQUENCE grp_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE groupe_utilisateur_2 (grp_id NUMBER(10) NOT NULL, grp_libelle VARCHAR2(255) NOT NULL, niveau NUMBER(10) NOT NULL, PRIMARY KEY(grp_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_330B87FAA5CB9D6A ON groupe_utilisateur_2 (grp_libelle)');
        $this->addSql('ALTER TABLE UTILISATEUR_SYSTEM DROP CONSTRAINT SYS_C007030');
        $this->addSql('DROP TABLE UTILISATEUR_SYSTEM');
        $this->addSql('DROP TABLE GROUPE_UTILISATEUR');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE grp_seq');
        $this->addSql('CREATE SEQUENCE GROUPE_UTILISATEUR_GRP_ID_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE SEQUENCE MESSENGER_MESSAGES_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE UTILISATEUR_SYSTEM (USRS_ID NUMBER(10) NOT NULL, MATRICULE VARCHAR2(6) NOT NULL, DATE_CREATION DATE DEFAULT \'SYSDATE\' NULL, GRP_ID NUMBER(10) NOT NULL, PRIMARY KEY(USRS_ID))');
        $this->addSql('CREATE INDEX IDX_606AA7881DAB1036 ON UTILISATEUR_SYSTEM (GRP_ID)');
        $this->addSql('CREATE TABLE GROUPE_UTILISATEUR (GRP_ID NUMBER(10) NOT NULL, GRP_LIBELLE VARCHAR2(1000) NOT NULL, NIVEAU NUMBER(10) NOT NULL, PRIMARY KEY(GRP_ID))');
        $this->addSql('CREATE UNIQUE INDEX sys_c007009 ON GROUPE_UTILISATEUR (GRP_LIBELLE)');
        $this->addSql('ALTER TABLE UTILISATEUR_SYSTEM ADD CONSTRAINT SYS_C007030 FOREIGN KEY (GRP_ID) REFERENCES GROUPE_UTILISATEUR (GRP_ID)');
        $this->addSql('DROP TABLE groupe_utilisateur_2');
    }
}
