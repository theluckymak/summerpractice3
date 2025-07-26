<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250726105340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, user_id, filename, visibility, extracted_text, keywords, created_at FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, filename VARCHAR(255) NOT NULL, visibility VARCHAR(10) NOT NULL, extracted_text CLOB DEFAULT NULL, keywords CLOB DEFAULT NULL --(DC2Type:json)
        , created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_C53D045FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO image (id, user_id, filename, visibility, extracted_text, keywords, created_at) SELECT id, user_id, filename, visibility, extracted_text, keywords, created_at FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE INDEX IDX_C53D045FA76ED395 ON image (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__image AS SELECT id, user_id, filename, visibility, extracted_text, keywords, created_at FROM image');
        $this->addSql('DROP TABLE image');
        $this->addSql('CREATE TABLE image (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, filename VARCHAR(255) NOT NULL, visibility VARCHAR(10) NOT NULL, extracted_text CLOB DEFAULT NULL, keywords CLOB DEFAULT NULL --(DC2Type:json)
        , created_at DATETIME DEFAULT NULL, CONSTRAINT FK_C53D045FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO image (id, user_id, filename, visibility, extracted_text, keywords, created_at) SELECT id, user_id, filename, visibility, extracted_text, keywords, created_at FROM __temp__image');
        $this->addSql('DROP TABLE __temp__image');
        $this->addSql('CREATE INDEX IDX_C53D045FA76ED395 ON image (user_id)');
    }
}
