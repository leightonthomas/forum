<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221210002554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds SubForum table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
CREATE TABLE sub_forum (
    id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)', 
    name VARCHAR(255) NOT NULL, 
    UNIQUE INDEX UNIQ_696FDA745E237E06 (name), 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sub_forum');
    }
}
