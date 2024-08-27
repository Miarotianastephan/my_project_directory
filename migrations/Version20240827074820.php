<?php 

declare(strict_type=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240827074820 extends AbstractMigration
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
        $this->addSql('CREATE TABLE utilisateur (user_id NUMBER(10) NOT NULL, grp_id NUMBER(10) NOT NULL, user_matricule VARCHAR2(255) NOT NULL, dt_ajout DATE NOT NULL, roles CLOB NOT NULL, PRIMARY KEY(user_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B312525178 ON utilisateur (user_matricule)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3D51E9150 ON utilisateur (grp_id)');
        $this->addSql('COMMENT ON COLUMN utilisateur.roles IS \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D51E9150 FOREIGN KEY (grp_id) REFERENCES groupe_utilisateur (grp_id)');
        $this->addSql('ALTER TABLE MESSENGER_MESSAGES MODIFY (created_at TIMESTAMP(0) DEFAULT NULL, available_at TIMESTAMP(0) DEFAULT NULL, delivered_at TIMESTAMP(0) DEFAULT NULL)');
    }
    public function down(Schema $schema): void{
                $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT FK_1D1C63B3D51E9150');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('ALTER TABLE messenger_messages MODIFY (CREATED_AT TIMESTAMP(0) DEFAULT NULL, AVAILABLE_AT TIMESTAMP(0) DEFAULT NULL, DELIVERED_AT TIMESTAMP(0) DEFAULT NULL)');
    }

}
