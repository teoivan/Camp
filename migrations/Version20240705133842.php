<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240705133842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workout ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE workout ADD CONSTRAINT FK_649FFB72C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_649FFB72C54C8C93 ON workout (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE workout DROP FOREIGN KEY FK_649FFB72C54C8C93');
        $this->addSql('DROP INDEX IDX_649FFB72C54C8C93 ON workout');
        $this->addSql('ALTER TABLE workout DROP type_id');
    }
}
