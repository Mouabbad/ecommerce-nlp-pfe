<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250615123125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, produit_id INT DEFAULT NULL, vendeur_id INT DEFAULT NULL, statut VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_F5299398F347EFB (produit_id), INDEX IDX_F5299398858C065E (vendeur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` ADD CONSTRAINT FK_F5299398858C065E FOREIGN KEY (vendeur_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F347EFB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398858C065E
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `order`
        SQL);
    }
}
