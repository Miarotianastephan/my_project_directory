<?php 

//declare(strict_type=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240909080840 extends AbstractMigration
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
                $this->addSql('CREATE SEQUENCE compte_mere_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE compte_mere (id NUMBER(10) NOT NULL, cpt_numero VARCHAR2(255) NOT NULL, cpt_libelle VARCHAR2(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE PLAN_COMPTE ADD (compte_mere_id NUMBER(10) NOT NULL)');
        $this->addSql('ALTER TABLE PLAN_COMPTE ADD CONSTRAINT FK_7BAE4A1281B9F858 FOREIGN KEY (compte_mere_id) REFERENCES compte_mere (id)');
        $this->addSql('CREATE INDEX IDX_7BAE4A1281B9F858 ON PLAN_COMPTE (compte_mere_id)');
        // $this->addSql('ALTER TABLE UTILISATEUR MODIFY (dt_ajout DATE DEFAULT NULL)');
        $this->addSql('ALTER TABLE MESSENGER_MESSAGES MODIFY (created_at TIMESTAMP(0) DEFAULT NULL, available_at TIMESTAMP(0) DEFAULT NULL, delivered_at TIMESTAMP(0) DEFAULT NULL)');
    }
    public function down(Schema $schema): void{
                $this->addSql('ALTER TABLE plan_compte DROP CONSTRAINT FK_7BAE4A1281B9F858');
        $this->addSql('DROP SEQUENCE compte_mere_id_seq');
        $this->addSql('DROP TABLE compte_mere');
        $this->addSql('ALTER TABLE messenger_messages MODIFY (CREATED_AT TIMESTAMP(0) DEFAULT NULL, AVAILABLE_AT TIMESTAMP(0) DEFAULT NULL, DELIVERED_AT TIMESTAMP(0) DEFAULT NULL)');
        $this->addSql('DROP INDEX IDX_7BAE4A1281B9F858');
        $this->addSql('ALTER TABLE plan_compte DROP (compte_mere_id)');
        $this->addSql('ALTER TABLE utilisateur MODIFY (DT_AJOUT DATE DEFAULT \'SYSDATE\')');
    }

}
