<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221210215345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add thread, thread event tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
CREATE TABLE thread (
    id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)', 
    author_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)', 
    forum_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)', 
    name VARCHAR(255) NOT NULL, 
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
    locked TINYINT(1) NOT NULL, 
    pinned TINYINT(1) NOT NULL, 
    INDEX IDX_31204C83F675F31B (author_id), 
    INDEX IDX_31204C8329CCBAD0 (forum_id), 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
        $this->addSql(
            <<<SQL
CREATE TABLE thread_event (
    id CHAR(36) NOT NULL COMMENT '(DC2Type:uuid)', 
    thread_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)', 
    author_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:uuid)', 
    time DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
    type VARCHAR(255) NOT NULL, 
    content VARCHAR(255) DEFAULT NULL, 
    INDEX IDX_312A159CE2904019 (thread_id), 
    INDEX IDX_312A159CF675F31B (author_id), 
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL
        );
        $this->addSql(
            'ALTER TABLE thread ADD CONSTRAINT FK_31204C8361220EA6 FOREIGN KEY (author_id) REFERENCES account (id)',
        );
        $this->addSql(
            'ALTER TABLE thread ADD CONSTRAINT FK_31204C8329CCBAD0 FOREIGN KEY (forum_id) REFERENCES sub_forum (id)',
        );
        $this->addSql(
             <<<SQL
ALTER TABLE thread_event ADD CONSTRAINT FK_312A159CE2904019 FOREIGN KEY (thread_id) REFERENCES thread (id)
SQL
        );
        $this->addSql(
            <<<SQL
ALTER TABLE thread_event ADD CONSTRAINT FK_312A159CF675F31B FOREIGN KEY (author_id) REFERENCES account (id)
SQL
        );
        $this->addSql('ALTER TABLE sub_forum ADD slug VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_696FDA74989D9B62 ON sub_forum (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_696FDA74989D9B62 ON sub_forum');
        $this->addSql('ALTER TABLE sub_forum DROP slug');
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C8361220EA6');
        $this->addSql('ALTER TABLE thread DROP FOREIGN KEY FK_31204C8329CCBAD0');
        $this->addSql('ALTER TABLE thread_event DROP FOREIGN KEY FK_312A159CE2904019');
        $this->addSql('ALTER TABLE thread_event DROP FOREIGN KEY FK_312A159CF675F31B');
        $this->addSql('DROP TABLE thread');
        $this->addSql('DROP TABLE thread_event');
    }
}
