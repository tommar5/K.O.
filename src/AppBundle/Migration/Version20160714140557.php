<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160714140557 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE licences_sports (licence_id INT NOT NULL, sport_id INT NOT NULL, INDEX IDX_EA94A33526EF07C9 (licence_id), INDEX IDX_EA94A335AC78BCF8 (sport_id), PRIMARY KEY(licence_id, sport_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE licences_sports ADD CONSTRAINT FK_EA94A33526EF07C9 FOREIGN KEY (licence_id) REFERENCES licences (id)');
        $this->addSql('ALTER TABLE licences_sports ADD CONSTRAINT FK_EA94A335AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sports (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE licences_sports');
    }
}
