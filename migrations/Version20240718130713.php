<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240718130713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE MESSENGER_MESSAGES_SEQ');
        $this->addSql('ALTER TABLE GROUPE_UTILISATEUR MODIFY (grp_libelle VARCHAR2(255) DEFAULT NULL)');
        $this->addSql('ALTER INDEX sys_c007009 RENAME TO UNIQ_92C1107DA5CB9D6A');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE MESSENGER_MESSAGES_SEQ START WITH 1 MINVALUE 1 INCREMENT BY 1');
        $this->addSql('ALTER TABLE groupe_utilisateur MODIFY (GRP_LIBELLE VARCHAR2(1000) DEFAULT NULL)');
        $this->addSql('ALTER INDEX uniq_92c1107da5cb9d6a RENAME TO sys_c007009');
    }
}
