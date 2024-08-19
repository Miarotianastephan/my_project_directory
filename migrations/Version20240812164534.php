<?php 

declare(strict_type=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240812164534 extends AbstractMigration
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
                // $this->addSql('DROP INDEX sys_c007010');
        // $this->addSql('ALTER TABLE UTILISATEUR MODIFY (dt_ajout DATE DEFAULT NULL)');
        // $this->addSql('ALTER INDEX idx_901ff15b1dab1036 RENAME TO IDX_1D1C63B3D51E9150');
    }
    public function down(Schema $schema): void{
                $this->addSql('ALTER TABLE utilisateur MODIFY (DT_AJOUT DATE DEFAULT \'SYSDATE\')');
        $this->addSql('CREATE UNIQUE INDEX sys_c007010 ON utilisateur (USER_MATRICULE)');
        $this->addSql('ALTER INDEX idx_1d1c63b3d51e9150 RENAME TO IDX_901FF15B1DAB1036');
    }

}
