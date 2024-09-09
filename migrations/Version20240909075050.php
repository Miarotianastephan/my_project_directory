<?php 

//declare(strict_type=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240909075050 extends AbstractMigration
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
                $this->addSql('ALTER TABLE DETAIL_TRANSACTION_COMPTE ADD (transaction_type_id NUMBER(10) NOT NULL, plan_compte_id NUMBER(10) NOT NULL)');
        $this->addSql('ALTER TABLE DETAIL_TRANSACTION_COMPTE ADD CONSTRAINT FK_9CD608E3B3E6B071 FOREIGN KEY (transaction_type_id) REFERENCES transaction_type (trs_id)');
        $this->addSql('ALTER TABLE DETAIL_TRANSACTION_COMPTE ADD CONSTRAINT FK_9CD608E3421B9B35 FOREIGN KEY (plan_compte_id) REFERENCES plan_compte (cpt_id)');
        $this->addSql('CREATE INDEX IDX_9CD608E3B3E6B071 ON DETAIL_TRANSACTION_COMPTE (transaction_type_id)');
        $this->addSql('CREATE INDEX IDX_9CD608E3421B9B35 ON DETAIL_TRANSACTION_COMPTE (plan_compte_id)');
    }
    public function down(Schema $schema): void{
                $this->addSql('ALTER TABLE detail_transaction_compte DROP CONSTRAINT FK_9CD608E3B3E6B071');
        $this->addSql('ALTER TABLE detail_transaction_compte DROP CONSTRAINT FK_9CD608E3421B9B35');
        $this->addSql('DROP INDEX IDX_9CD608E3B3E6B071');
        $this->addSql('DROP INDEX IDX_9CD608E3421B9B35');
        $this->addSql('ALTER TABLE detail_transaction_compte DROP (transaction_type_id, plan_compte_id)');
    }

}
