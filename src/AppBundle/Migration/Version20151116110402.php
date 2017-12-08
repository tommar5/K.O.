<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151116110402 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9296CD8AE');
        $this->addSql('DROP INDEX IDX_1483A5E9296CD8AE ON users');
        $this->addSql('ALTER TABLE users DROP team_id');
        $this->addSql('ALTER TABLE competition ADD licence_id INT DEFAULT NULL, CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB126EF07C9 FOREIGN KEY (licence_id) REFERENCES licences (id)');
        $this->addSql('CREATE INDEX IDX_B50A2CB126EF07C9 ON competition (licence_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competition DROP FOREIGN KEY FK_B50A2CB126EF07C9');
        $this->addSql('DROP INDEX IDX_B50A2CB126EF07C9 ON competition');
        $this->addSql('ALTER TABLE competition DROP licence_id, CHANGE description description LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users ADD team_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9296CD8AE FOREIGN KEY (team_id) REFERENCES licences (id)');
        $this->addSql('CREATE INDEX IDX_1483A5E9296CD8AE ON users (team_id)');
    }
}
