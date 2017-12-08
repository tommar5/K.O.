<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160609055153 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('CREATE TABLE applications (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, name VARCHAR(255) NOT NULL, location VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(255) DEFAULT NULL, street_number VARCHAR(255) DEFAULT NULL, date_from DATE NOT NULL, date_to DATE DEFAULT NULL, sport VARCHAR(255) NOT NULL, stage VARCHAR(255) NOT NULL, league VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, lasf_name VARCHAR(255) DEFAULT NULL, lasf_address VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, bank VARCHAR(255) DEFAULT NULL, bank_account VARCHAR(255) DEFAULT NULL, vat_number VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, deliver_to VARCHAR(255) DEFAULT NULL, terms_confirmed TINYINT(1) NOT NULL, INDEX IDX_F7C966F0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE application_documents (application_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_26B108893E030ACD (application_id), UNIQUE INDEX UNIQ_26B10889C33F7837 (document_id), PRIMARY KEY(application_id, document_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');}

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F0A76ED395');
        $this->addSql('DROP INDEX IDX_F7C966F0A76ED395 ON applications');
        $this->addSql('ALTER TABLE application_documents DROP FOREIGN KEY FK_26B108893E030ACD');
        $this->addSql('DROP TABLE applications');
        $this->addSql('DROP TABLE application_documents');
    }
}
