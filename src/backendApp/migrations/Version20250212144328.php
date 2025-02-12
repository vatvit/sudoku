<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250212144328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_instance (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', started_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', finished_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', game_instance_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_instance_action (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_player_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7B193C0EE404301F (game_instance_id), INDEX IDX_7B193C0E4B4034DD (game_player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_instance_action_affect (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_action_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_type VARCHAR(255) NOT NULL, INDEX IDX_268B5A827B47397E (game_instance_action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_player (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', player_id_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E52CD7ADC036E511 (player_id_id), INDEX IDX_E52CD7ADE404301F (game_instance_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', display_name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_98197A65A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_game_instance (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', game_instance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', initial_state_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', solved TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_B1206F72E404301F (game_instance_id), INDEX IDX_B1206F72DD616571 (initial_state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_grid (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', grid LONGTEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sudoku_initial_state (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', grid_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', hidden_cells LONGTEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F3E0AB782CF16895 (grid_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_instance_action ADD CONSTRAINT FK_7B193C0EE404301F FOREIGN KEY (game_instance_id) REFERENCES game_instance (id)');
        $this->addSql('ALTER TABLE game_instance_action ADD CONSTRAINT FK_7B193C0E4B4034DD FOREIGN KEY (game_player_id) REFERENCES game_player (id)');
        $this->addSql('ALTER TABLE game_instance_action_affect ADD CONSTRAINT FK_268B5A827B47397E FOREIGN KEY (game_instance_action_id) REFERENCES game_instance_action (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADC036E511 FOREIGN KEY (player_id_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_player ADD CONSTRAINT FK_E52CD7ADE404301F FOREIGN KEY (game_instance_id) REFERENCES game_instance (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD CONSTRAINT FK_B1206F72E404301F FOREIGN KEY (game_instance_id) REFERENCES game_instance (id)');
        $this->addSql('ALTER TABLE sudoku_game_instance ADD CONSTRAINT FK_B1206F72DD616571 FOREIGN KEY (initial_state_id) REFERENCES sudoku_initial_state (id)');
        $this->addSql('ALTER TABLE sudoku_initial_state ADD CONSTRAINT FK_F3E0AB782CF16895 FOREIGN KEY (grid_id) REFERENCES sudoku_grid (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_instance_action DROP FOREIGN KEY FK_7B193C0EE404301F');
        $this->addSql('ALTER TABLE game_instance_action DROP FOREIGN KEY FK_7B193C0E4B4034DD');
        $this->addSql('ALTER TABLE game_instance_action_affect DROP FOREIGN KEY FK_268B5A827B47397E');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADC036E511');
        $this->addSql('ALTER TABLE game_player DROP FOREIGN KEY FK_E52CD7ADE404301F');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65A76ED395');
        $this->addSql('ALTER TABLE sudoku_game_instance DROP FOREIGN KEY FK_B1206F72E404301F');
        $this->addSql('ALTER TABLE sudoku_game_instance DROP FOREIGN KEY FK_B1206F72DD616571');
        $this->addSql('ALTER TABLE sudoku_initial_state DROP FOREIGN KEY FK_F3E0AB782CF16895');
        $this->addSql('DROP TABLE game_instance');
        $this->addSql('DROP TABLE game_instance_action');
        $this->addSql('DROP TABLE game_instance_action_affect');
        $this->addSql('DROP TABLE game_player');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE sudoku_game_instance');
        $this->addSql('DROP TABLE sudoku_grid');
        $this->addSql('DROP TABLE sudoku_initial_state');
        $this->addSql('DROP TABLE user');
    }
}
