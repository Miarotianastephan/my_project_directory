<?php 

//declare(strict_type=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240905083347 extends AbstractMigration
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
                $this->addSql('ALTER TABLE TRANSACTION_TYPE DROP CONSTRAINT FK_6E9D69886A8C56BC');
        $this->addSql('DROP INDEX idx_6e9d69886a8c56bc');
        $this->addSql('ALTER TABLE TRANSACTION_TYPE DROP (DETAIL_TRANSACTION_COMPTE_ID)');
    }
    public function down(Schema $schema): void{
                $this->addSql('ALTER TABLE transaction_type ADD (DETAIL_TRANSACTION_COMPTE_ID NUMBER(10) NOT NULL)');
        $this->addSql('ALTER TABLE transaction_type ADD CONSTRAINT FK_6E9D69886A8C56BC FOREIGN KEY (DETAIL_TRANSACTION_COMPTE_ID) REFERENCES DETAIL_TRANSACTION_COMPTE (DET_TRS_CPT_ID)');
        $this->addSql('CREATE INDEX idx_6e9d69886a8c56bc ON transaction_type (DETAIL_TRANSACTION_COMPTE_ID)');
    }

}
