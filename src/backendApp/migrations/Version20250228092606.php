<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250228092606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sudoku_game_initial_state RENAME INDEX idx_f3e0ab782cf16895 TO IDX_FB5F27C12CF16895');
        $this->addSql('ALTER TABLE sudoku_grid ADD size INT NOT NULL AFTER id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sudoku_grid DROP size');
        $this->addSql('ALTER TABLE sudoku_game_initial_state RENAME INDEX idx_fb5f27c12cf16895 TO IDX_F3E0AB782CF16895');
    }
}
