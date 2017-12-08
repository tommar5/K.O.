<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151117080558 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE competition_participant (id INT AUTO_INCREMENT NOT NULL, competition_id INT DEFAULT NULL, user_id INT DEFAULT NULL, points NUMERIC(8, 2) NOT NULL, INDEX IDX_FF3EC46F7B39D312 (competition_id), INDEX IDX_FF3EC46FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE competition_participant ADD CONSTRAINT FK_FF3EC46F7B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id)');
        $this->addSql('ALTER TABLE competition_participant ADD CONSTRAINT FK_FF3EC46FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('DROP TABLE user_competition');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_competition (user_id INT NOT NULL, competition_id INT NOT NULL, INDEX IDX_1C11E524A76ED395 (user_id), INDEX IDX_1C11E5247B39D312 (competition_id), PRIMARY KEY(user_id, competition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_competition ADD CONSTRAINT FK_1C11E5247B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_competition ADD CONSTRAINT FK_1C11E524A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE competition_participant');
    }
}
