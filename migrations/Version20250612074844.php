<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612074844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE act ADD chain_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE act ADD CONSTRAINT FK_AFECF544966C2F62 FOREIGN KEY (chain_id) REFERENCES chain (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_AFECF544966C2F62 ON act (chain_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE act DROP FOREIGN KEY FK_AFECF544966C2F62
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_AFECF544966C2F62 ON act
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE act DROP chain_id
        SQL);
    }
}
