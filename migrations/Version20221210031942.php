<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221210031942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Swap username to encrypted field, add blind index';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_7D3656A4F85E0677 ON account');
        $this->addSql(
            <<<SQL
ALTER TABLE account 
    ADD username_bidx VARCHAR(255) NOT NULL, 
    CHANGE username username VARCHAR(255) NOT NULL
SQL
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A421D93C81 ON account (username_bidx)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_7D3656A421D93C81 ON account');
        $this->addSql('ALTER TABLE account DROP username_bidx, CHANGE username username VARCHAR(30) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4F85E0677 ON account (username)');
    }
}
