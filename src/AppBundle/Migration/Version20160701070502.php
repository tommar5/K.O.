<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160701070502 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition_chiefs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, INDEX IDX_B3A7DEDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competition_chiefs_sports (competition_chief_id INT NOT NULL, sport_id INT NOT NULL, INDEX IDX_63A1E0714B0A53CE (competition_chief_id), INDEX IDX_63A1E071AC78BCF8 (sport_id), PRIMARY KEY(competition_chief_id, sport_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competition_chiefs ADD CONSTRAINT FK_B3A7DEDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE competition_chiefs_sports ADD CONSTRAINT FK_63A1E0714B0A53CE FOREIGN KEY (competition_chief_id) REFERENCES competition_chiefs (id)');
        $this->addSql('ALTER TABLE competition_chiefs_sports ADD CONSTRAINT FK_63A1E071AC78BCF8 FOREIGN KEY (sport_id) REFERENCES sports (id)');
        $this->addSql('ALTER TABLE applications ADD competition_chief_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F04B0A53CE FOREIGN KEY (competition_chief_id) REFERENCES competition_chiefs (id)');
        $this->addSql('CREATE INDEX IDX_F7C966F04B0A53CE ON applications (competition_chief_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F04B0A53CE');
        $this->addSql('ALTER TABLE competition_chiefs_sports DROP FOREIGN KEY FK_63A1E0714B0A53CE');
        $this->addSql('DROP TABLE competition_chiefs');
        $this->addSql('DROP TABLE competition_chiefs_sports');
        $this->addSql('DROP INDEX IDX_F7C966F04B0A53CE ON applications');
        $this->addSql('ALTER TABLE applications DROP competition_chief_id');
    }
}
