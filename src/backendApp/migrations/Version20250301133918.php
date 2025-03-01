<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301133918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sudoku_game_instance DROP FOREIGN KEY FK_B1206F72DD616571');
        $this->addSql('CREATE TABLE game_state (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', last_game_instance_action_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', game_state_type VARCHAR(255) NOT NULL, INDEX IDX_91A0AB74E404301F (game_instance_id), UNIQUE INDEX UNIQ_91A0AB74A8E823B2 (last_game_instance_action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_game_state (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_state_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', filled_cells JSON NOT NULL, noted_cells JSON NOT NULL, UNIQUE INDEX UNIQ_854A68E3AE9CC3E7 (game_state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_puzzle (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', sudoku_grid_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', hidden_cells JSON NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_83D7DC4A6BE3ADB (sudoku_grid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_state ADD CONSTRAINT FK_91A0AB74E404301F FOREIGN KEY (game_instance_id) REFERENCES game_instance (id)');
        $this->addSql('ALTER TABLE game_state ADD CONSTRAINT FK_91A0AB74A8E823B2 FOREIGN KEY (last_game_instance_action_id) REFERENCES game_instance_action (id)');
        $this->addSql('ALTER TABLE sudoku_game_state ADD CONSTRAINT FK_854A68E3AE9CC3E7 FOREIGN KEY (game_state_id) REFERENCES game_state (id)');
        $this->addSql('ALTER TABLE sudoku_puzzle ADD CONSTRAINT FK_83D7DC4A6BE3ADB FOREIGN KEY (sudoku_grid_id) REFERENCES sudoku_grid (id)');
        $this->addSql('ALTER TABLE sudoku_game_initial_state DROP FOREIGN KEY FK_F3E0AB782CF16895');
        $this->addSql('DROP TABLE sudoku_game_initial_state');
        $this->addSql('ALTER TABLE game_instance ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sudoku_game_instance DROP FOREIGN KEY FK_B1206F72E404301F');
        $this->addSql('DROP INDEX IDX_B1206F72DD616571 ON sudoku_game_instance');
        $this->addSql('DROP INDEX UNIQ_B1206F72E404301F ON sudoku_game_instance');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD sudoku_puzzle_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\' AFTER id, DROP game_instance_id, DROP initial_state_id');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD CONSTRAINT FK_B1206F72321EB00E FOREIGN KEY (sudoku_puzzle_id) REFERENCES sudoku_puzzle (id)');
        $this->addSql('CREATE INDEX IDX_B1206F72321EB00E ON sudoku_game_instance (sudoku_puzzle_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sudoku_game_instance DROP FOREIGN KEY FK_B1206F72321EB00E');
        $this->addSql('CREATE TABLE sudoku_game_initial_state (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', grid_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', hidden_cells LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FB5F27C12CF16895 (grid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sudoku_game_initial_state ADD CONSTRAINT FK_F3E0AB782CF16895 FOREIGN KEY (grid_id) REFERENCES sudoku_grid (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE game_state DROP FOREIGN KEY FK_91A0AB74E404301F');
        $this->addSql('ALTER TABLE game_state DROP FOREIGN KEY FK_91A0AB74A8E823B2');
        $this->addSql('ALTER TABLE sudoku_game_state DROP FOREIGN KEY FK_854A68E3AE9CC3E7');
        $this->addSql('ALTER TABLE sudoku_puzzle DROP FOREIGN KEY FK_83D7DC4A6BE3ADB');
        $this->addSql('DROP TABLE game_state');
        $this->addSql('DROP TABLE sudoku_game_state');
        $this->addSql('DROP TABLE sudoku_puzzle');
        $this->addSql('ALTER TABLE game_instance DROP updated_at');
        $this->addSql('DROP INDEX IDX_B1206F72321EB00E ON sudoku_game_instance');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD initial_state_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE sudoku_puzzle_id game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD CONSTRAINT FK_B1206F72DD616571 FOREIGN KEY (initial_state_id) REFERENCES sudoku_game_initial_state (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD CONSTRAINT FK_B1206F72E404301F FOREIGN KEY (game_instance_id) REFERENCES game_instance (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_B1206F72DD616571 ON sudoku_game_instance (initial_state_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1206F72E404301F ON sudoku_game_instance (game_instance_id)');
    }
}
