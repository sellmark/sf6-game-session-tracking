<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503204211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_sessions ADD previous_session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_sessions ADD CONSTRAINT FK_31246235163FAE66 FOREIGN KEY (previous_session_id) REFERENCES game_sessions (id)');
        $this->addSql('CREATE INDEX IDX_31246235163FAE66 ON game_sessions (previous_session_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_sessions DROP FOREIGN KEY FK_31246235163FAE66');
        $this->addSql('DROP INDEX IDX_31246235163FAE66 ON game_sessions');
        $this->addSql('ALTER TABLE game_sessions DROP previous_session_id');
    }
}
