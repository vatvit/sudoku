<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219150810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_instance CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE started_at started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE finished_at finished_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE game_instance_action CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE game_instance_id game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE game_player_id game_player_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE game_instance_action_affect CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE game_instance_action_id game_instance_action_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE game_player CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE player_id_id player_id_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE game_instance_id game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE player CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE user_id user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sudoku_game_instance CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE game_instance_id game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE initial_state_id initial_state_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE sudoku_grid ADD blocks JSON NOT NULL AFTER grid, CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE grid grid JSON NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE sudoku_initial_state CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE grid_id grid_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user CHANGE id id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sudoku_grid DROP blocks, CHANGE id id BINARY(16) NOT NULL, CHANGE grid grid LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE sudoku_game_instance CHANGE id id BINARY(16) NOT NULL, CHANGE game_instance_id game_instance_id BINARY(16) NOT NULL, CHANGE initial_state_id initial_state_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE player CHANGE id id BINARY(16) NOT NULL, CHANGE user_id user_id BINARY(16) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE game_player CHANGE id id BINARY(16) NOT NULL, CHANGE player_id_id player_id_id BINARY(16) NOT NULL, CHANGE game_instance_id game_instance_id BINARY(16) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE game_instance_action_affect CHANGE id id BINARY(16) NOT NULL, CHANGE game_instance_action_id game_instance_action_id BINARY(16) NOT NULL');
        $this->addSql('ALTER TABLE game_instance_action CHANGE id id BINARY(16) NOT NULL, CHANGE game_instance_id game_instance_id BINARY(16) NOT NULL, CHANGE game_player_id game_player_id BINARY(16) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE sudoku_initial_state CHANGE id id BINARY(16) NOT NULL, CHANGE grid_id grid_id BINARY(16) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE game_instance CHANGE id id BINARY(16) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE started_at started_at DATETIME DEFAULT NULL, CHANGE finished_at finished_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE id id BINARY(16) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
