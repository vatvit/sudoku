<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303160055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_instance (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', finished_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', game_instance_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_instance_action (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_player_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7B193C0EE404301F (game_instance_id), INDEX IDX_7B193C0E4B4034DD (game_player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_instance_action_affect (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_action_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_type VARCHAR(255) NOT NULL, INDEX IDX_268B5A827B47397E (game_instance_action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_player (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', player_id_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E52CD7ADC036E511 (player_id_id), INDEX IDX_E52CD7ADE404301F (game_instance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_state (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', last_game_instance_action_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', game_state_type VARCHAR(255) NOT NULL, INDEX IDX_91A0AB74E404301F (game_instance_id), UNIQUE INDEX UNIQ_91A0AB74A8E823B2 (last_game_instance_action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', display_name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_98197A65A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_game_instance (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', sudoku_puzzle_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', solved TINYINT(1) NOT NULL, INDEX IDX_B1206F72321EB00E (sudoku_puzzle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_game_state (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_state_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', filled_cells JSON NOT NULL, noted_cells JSON NOT NULL, UNIQUE INDEX UNIQ_854A68E3AE9CC3E7 (game_state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_grid (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', size INT NOT NULL, grid JSON NOT NULL, cell_groups JSON NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_puzzle (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', sudoku_grid_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', hidden_cells JSON NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_83D7DC4A6BE3ADB (sudoku_grid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_instance_action ADD CONSTRAINT FK_7B193C0EE404301F FOREIGN KEY (game_instance_id) REFERENCES game_instance (id)');
        $this->addSql('ALTER TABLE game_instance_action ADD CONSTRAINT FK_7B193C0E4B4034DD FOREIGN KEY (game_player_id) REFERENCES game_player (id)');
        $this->addSql('ALTER TABLE game_instance_action_affect ADD CONSTRAINT FK_268B5A827B47397E FOREIGN KEY (game_instance_action_id) REFERENCES game_instance_action (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADC036E511 FOREIGN KEY (player_id_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADE404301F FOREIGN KEY (game_instance_id) REFERENCES game_instance (id)');
        $this->addSql('ALTER TABLE game_state ADD CONSTRAINT FK_91A0AB74E404301F FOREIGN KEY (game_instance_id) REFERENCES game_instance (id)');
        $this->addSql('ALTER TABLE game_state ADD CONSTRAINT FK_91A0AB74A8E823B2 FOREIGN KEY (last_game_instance_action_id) REFERENCES game_instance_action (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD CONSTRAINT FK_B1206F72321EB00E FOREIGN KEY (sudoku_puzzle_id) REFERENCES sudoku_puzzle (id)');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD CONSTRAINT FK_B1206F72BF396750 FOREIGN KEY (id) REFERENCES game_instance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sudoku_game_state ADD CONSTRAINT FK_854A68E3AE9CC3E7 FOREIGN KEY (game_state_id) REFERENCES game_state (id)');
        $this->addSql('ALTER TABLE sudoku_puzzle ADD CONSTRAINT FK_83D7DC4A6BE3ADB FOREIGN KEY (sudoku_grid_id) REFERENCES sudoku_grid (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_instance_action DROP FOREIGN KEY FK_7B193C0EE404301F');
        $this->addSql('ALTER TABLE game_instance_action DROP FOREIGN KEY FK_7B193C0E4B4034DD');
        $this->addSql('ALTER TABLE game_instance_action_affect DROP FOREIGN KEY FK_268B5A827B47397E');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADC036E511');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADE404301F');
        $this->addSql('ALTER TABLE game_state DROP FOREIGN KEY FK_91A0AB74E404301F');
        $this->addSql('ALTER TABLE game_state DROP FOREIGN KEY FK_91A0AB74A8E823B2');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65A76ED395');
        $this->addSql('ALTER TABLE sudoku_game_instance DROP FOREIGN KEY FK_B1206F72321EB00E');
        $this->addSql('ALTER TABLE sudoku_game_instance DROP FOREIGN KEY FK_B1206F72BF396750');
        $this->addSql('ALTER TABLE sudoku_game_state DROP FOREIGN KEY FK_854A68E3AE9CC3E7');
        $this->addSql('ALTER TABLE sudoku_puzzle DROP FOREIGN KEY FK_83D7DC4A6BE3ADB');
        $this->addSql('DROP TABLE game_instance');
        $this->addSql('DROP TABLE game_instance_action');
        $this->addSql('DROP TABLE game_instance_action_affect');
        $this->addSql('DROP TABLE game_player');
        $this->addSql('DROP TABLE game_state');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE sudoku_game_instance');
        $this->addSql('DROP TABLE sudoku_game_state');
        $this->addSql('DROP TABLE sudoku_grid');
        $this->addSql('DROP TABLE sudoku_puzzle');
        $this->addSql('DROP TABLE user');
    }
}
