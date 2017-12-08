<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160829125406 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE application_stewards (application_id INT NOT NULL, steward_id INT NOT NULL, INDEX IDX_EF58627A3E030ACD (application_id), INDEX IDX_EF58627A8F819768 (steward_id), PRIMARY KEY(application_id, steward_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application_stewards ADD CONSTRAINT FK_EF58627A3E030ACD FOREIGN KEY (application_id) REFERENCES applications (id)');
        $this->addSql('ALTER TABLE application_stewards ADD CONSTRAINT FK_EF58627A8F819768 FOREIGN KEY (steward_id) REFERENCES stewards (id)');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F08F819768');
        $this->addSql('DROP INDEX IDX_F7C966F08F819768 ON applications');
        $this->addSql('ALTER TABLE applications DROP steward_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE application_stewards');
        $this->addSql('DROP INDEX UNIQ_F7C966F039D275FA ON applications');
        $this->addSql('ALTER TABLE applications ADD steward_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F08F819768 FOREIGN KEY (steward_id) REFERENCES stewards (id)');
        $this->addSql('CREATE INDEX IDX_F7C966F08F819768 ON applications (steward_id)');
    }
}
