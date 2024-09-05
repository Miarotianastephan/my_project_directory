<?php 

//declare(strict_type=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240905082910 extends AbstractMigration
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
                $this->addSql('ALTER TABLE TRANSACTION_TYPE ADD (detail_transaction_compte_id NUMBER(10) NOT NULL)');
        $this->addSql('ALTER TABLE TRANSACTION_TYPE ADD CONSTRAINT FK_6E9D69886A8C56BC FOREIGN KEY (detail_transaction_compte_id) REFERENCES detail_transaction_compte (det_trs_cpt_id)');
        $this->addSql('CREATE INDEX IDX_6E9D69886A8C56BC ON TRANSACTION_TYPE (detail_transaction_compte_id)');
    }
    public function down(Schema $schema): void{
                $this->addSql('ALTER TABLE transaction_type DROP CONSTRAINT FK_6E9D69886A8C56BC');
        $this->addSql('DROP INDEX IDX_6E9D69886A8C56BC');
        $this->addSql('ALTER TABLE transaction_type DROP (detail_transaction_compte_id)');
    }

}
