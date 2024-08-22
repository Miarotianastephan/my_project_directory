<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240822121652 extends AbstractMigration
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
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE test2_id_seq START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('CREATE TABLE test2 (id NUMBER(10) NOT NULL, labelle VARCHAR2(255) NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE test2_id_seq');
        $this->addSql('DROP TABLE test2');
    }
}
