<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503094105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE players (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, encrypted_email VARCHAR(255) DEFAULT NULL, tracking_ids JSON DEFAULT NULL, UNIQUE INDEX UNIQ_264E43A6D17F50A6 (uuid), INDEX uuid_idx (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE game_sessions ADD player_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_sessions ADD CONSTRAINT FK_3124623599E6F5DF FOREIGN KEY (player_id) REFERENCES players (id)');
        $this->addSql('CREATE INDEX IDX_3124623599E6F5DF ON game_sessions (player_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE players');
        $this->addSql('ALTER TABLE game_sessions DROP FOREIGN KEY FK_3124623599E6F5DF');
        $this->addSql('DROP INDEX IDX_3124623599E6F5DF ON game_sessions');
        $this->addSql('ALTER TABLE game_sessions DROP player_id');
    }
}
