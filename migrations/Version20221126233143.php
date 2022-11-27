<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221126233143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds table for Account entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
CREATE TABLE account (
    id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)', 
    username VARCHAR(30) NOT NULL, 
    email_address VARCHAR(255) NOT NULL, 
    email_address_bidx VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL, 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4F85E0677 ON account (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D3656A4807D712E ON account (email_address_bidx)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE account');
    }
}
