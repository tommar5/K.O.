<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214091014 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE committe_sports (committe_id INT NOT NULL, sport_id INT NOT NULL, INDEX IDX_5712B0DC73A8D64C (committe_id), INDEX IDX_5712B0DCAC78BCF8 (sport_id), PRIMARY KEY(committe_id, sport_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE committe_sports ADD CONSTRAINT FK_5712B0DC73A8D64C FOREIGN KEY (committe_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE committe_sports ADD CONSTRAINT FK_5712B0DCAC78BCF8 FOREIGN KEY (sport_id) REFERENCES sports (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE committe_sports');
    }
}
