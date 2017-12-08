<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170331095520 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_competition_documents (sub_competition_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_DDBA86FB1A9D8106 (sub_competition_id), UNIQUE INDEX UNIQ_DDBA86FBC33F7837 (document_id), PRIMARY KEY(sub_competition_id, document_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_competition_documents ADD CONSTRAINT FK_DDBA86FB1A9D8106 FOREIGN KEY (sub_competition_id) REFERENCES sub_competition (id)');
        $this->addSql('ALTER TABLE sub_competition_documents ADD CONSTRAINT FK_DDBA86FBC33F7837 FOREIGN KEY (document_id) REFERENCES file_upload (id)');
        $this->addSql('CREATE TABLE sub_competition_judges (sub_competition_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2E1B32531A9D8106 (sub_competition_id), INDEX IDX_2E1B3253A76ED395 (user_id), PRIMARY KEY(sub_competition_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_competition_stewards (sub_competition_id INT NOT NULL, steward_id INT NOT NULL, INDEX IDX_309A75251A9D8106 (sub_competition_id), INDEX IDX_309A75258F819768 (steward_id), PRIMARY KEY(sub_competition_id, steward_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_competition_judges ADD CONSTRAINT FK_2E1B32531A9D8106 FOREIGN KEY (sub_competition_id) REFERENCES sub_competition (id)');
        $this->addSql('ALTER TABLE sub_competition_judges ADD CONSTRAINT FK_2E1B3253A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE sub_competition_stewards ADD CONSTRAINT FK_309A75251A9D8106 FOREIGN KEY (sub_competition_id) REFERENCES sub_competition (id)');
        $this->addSql('ALTER TABLE sub_competition_stewards ADD CONSTRAINT FK_309A75258F819768 FOREIGN KEY (steward_id) REFERENCES stewards (id)');
        $this->addSql('ALTER TABLE sub_competition ADD safety_chief_id INT DEFAULT NULL, ADD observer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_competition ADD CONSTRAINT FK_50F3592F39D275FA FOREIGN KEY (safety_chief_id) REFERENCES safety_chief (id)');
        $this->addSql('ALTER TABLE sub_competition ADD CONSTRAINT FK_50F3592F83F4F546 FOREIGN KEY (observer_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_50F3592F39D275FA ON sub_competition (safety_chief_id)');
        $this->addSql('CREATE INDEX IDX_50F3592F83F4F546 ON sub_competition (observer_id)');
        $this->addSql('ALTER TABLE sub_competition ADD competition_chief_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_competition ADD CONSTRAINT FK_50F3592F4B0A53CE FOREIGN KEY (competition_chief_id) REFERENCES competition_chiefs (id)');
        $this->addSql('CREATE INDEX IDX_50F3592F4B0A53CE ON sub_competition (competition_chief_id)');
        $this->addSql('ALTER TABLE sub_competition ADD skk_head_id INT DEFAULT NULL, ADD technical_delegate VARCHAR(255) DEFAULT NULL, ADD svo_delegate VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sub_competition ADD CONSTRAINT FK_50F3592F1E063A6A FOREIGN KEY (skk_head_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_50F3592F1E063A6A ON sub_competition (skk_head_id)');
        $this->addSql('ALTER TABLE sub_competition ADD competition_chief_confirmed TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE applications ADD member_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD competitionId INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sub_competition_file_upload');
        $this->addSql('DROP TABLE sub_competition_judges');
        $this->addSql('DROP TABLE sub_competition_stewards');
        $this->addSql('ALTER TABLE sub_competition DROP FOREIGN KEY FK_50F3592F39D275FA');
        $this->addSql('ALTER TABLE sub_competition DROP FOREIGN KEY FK_50F3592F83F4F546');
        $this->addSql('DROP INDEX IDX_50F3592F39D275FA ON sub_competition');
        $this->addSql('DROP INDEX IDX_50F3592F83F4F546 ON sub_competition');
        $this->addSql('ALTER TABLE sub_competition DROP safety_chief_id, DROP observer_id');
        $this->addSql('DROP INDEX IDX_50F3592F4B0A53CE ON sub_competition');
        $this->addSql('ALTER TABLE sub_competition DROP competition_chief_id');
        $this->addSql('ALTER TABLE sub_competition DROP FOREIGN KEY FK_50F3592F1E063A6A');
        $this->addSql('DROP INDEX IDX_50F3592F1E063A6A ON sub_competition');
        $this->addSql('ALTER TABLE sub_competition DROP skk_head_id, DROP technical_delegate, DROP svo_delegate');
        $this->addSql('ALTER TABLE sub_competition DROP competition_chief_confirmed');
        $this->addSql('DROP TABLE sub_competition_documents');
        $this->addSql('CREATE TABLE application_sub_competition (application_id INT NOT NULL, sub_competition_id INT NOT NULL, INDEX IDX_764DA6DD3E030ACD (application_id), INDEX IDX_764DA6DD1A9D8106 (sub_competition_id), PRIMARY KEY(application_id, sub_competition_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application_sub_competition ADD CONSTRAINT FK_764DA6DD1A9D8106 FOREIGN KEY (sub_competition_id) REFERENCES sub_competition (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE application_sub_competition ADD CONSTRAINT FK_764DA6DD3E030ACD FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE languages ADD users_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE languages ADD CONSTRAINT FK_A0D1537967B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_A0D1537967B3B43D ON languages (users_id)');
        $this->addSql('ALTER TABLE licences DROP FOREIGN KEY FK_6314AC4FA76ED395');
        $this->addSql('ALTER TABLE applications DROP member_code');
    }
}
