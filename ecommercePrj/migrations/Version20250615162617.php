<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250615162617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD vendor_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27F603EE73 FOREIGN KEY (vendor_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_29A5EC27F603EE73 ON produit (vendor_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27F603EE73
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_29A5EC27F603EE73 ON produit
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE produit DROP vendor_id
        SQL);
    }
}
