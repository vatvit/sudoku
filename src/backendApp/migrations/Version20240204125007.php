<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204125007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add initial Users';
    }

    public function up(Schema $schema): void
    {
        $sql = '
            INSERT INTO user
            (email, password)
            VALUES 
                ("test1@test.com", "test1"),
                ("test2@test.com", "test2")
        ';
        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        $sql = 'TRUNCATE TABLE user';
        $this->addSql($sql);
    }
}
