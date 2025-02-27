<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250227181250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sudoku_initial_state TO sudoku_game_initial_state');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('RENAME TABLE sudoku_game_initial_state TO sudoku_initial_state');
    }
}
