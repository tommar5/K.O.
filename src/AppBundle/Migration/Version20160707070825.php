<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160707070825 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE stewards (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, INDEX IDX_5063BD69A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stewards_sports (steward_id INT NOT NULL, sport_id INT NOT NULL, INDEX IDX_271F153D8F819768 (steward_id), INDEX IDX_271F153DAC78BCF8 (sport_id), PRIMARY KEY(steward_id, sport_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stewards ADD CONSTRAINT FK_5063BD69A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE stewards_sports ADD CONSTRAINT FK_271F153D8F819768 FOREIGN KEY (steward_id) REFERENCES stewards (id)');
        $this->addSql('ALTER TABLE stewards_sports ADD CONSTRAINT FK_271F153DAC78BCF8 FOREIGN KEY (sport_id) REFERENCES sports (id)');
        $this->addSql('ALTER TABLE applications ADD steward_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F08F819768 FOREIGN KEY (steward_id) REFERENCES stewards (id)');
        $this->addSql('CREATE INDEX IDX_F7C966F08F819768 ON applications (steward_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F08F819768');
        $this->addSql('ALTER TABLE stewards_sports DROP FOREIGN KEY FK_271F153D8F819768');
        $this->addSql('DROP TABLE stewards');
        $this->addSql('DROP TABLE stewards_sports');
        $this->addSql('DROP INDEX IDX_F7C966F08F819768 ON applications');
        $this->addSql('ALTER TABLE applications DROP steward_id');
    }
}
