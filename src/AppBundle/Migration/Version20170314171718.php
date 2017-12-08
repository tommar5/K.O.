<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170314171718 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE languages (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, language VARCHAR(20) DEFAULT NULL, INDEX IDX_A0D1537967B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_languages (user_id INT NOT NULL, language_id INT NOT NULL, INDEX IDX_A031DE9DA76ED395 (user_id), INDEX IDX_A031DE9D82F1BAF4 (language_id), PRIMARY KEY(user_id, language_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE licence_languages (licence_id INT NOT NULL, language_id INT NOT NULL, INDEX IDX_14884B1B26EF07C9 (licence_id), INDEX IDX_14884B1B82F1BAF4 (language_id), PRIMARY KEY(licence_id, language_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE languages ADD CONSTRAINT FK_A0D1537967B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_languages ADD CONSTRAINT FK_A031DE9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE user_languages ADD CONSTRAINT FK_A031DE9D82F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id)');
        $this->addSql('ALTER TABLE licence_languages ADD CONSTRAINT FK_14884B1B26EF07C9 FOREIGN KEY (licence_id) REFERENCES licences (id)');
        $this->addSql('ALTER TABLE licence_languages ADD CONSTRAINT FK_14884B1B82F1BAF4 FOREIGN KEY (language_id) REFERENCES languages (id)');
        $this->addSql('ALTER TABLE licences ADD second_driver TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE licences ADD first_driver TINYINT(1) DEFAULT NULL');
        $this->addSql("ALTER TABLE licences ADD city VARCHAR(255) DEFAULT NULL, ADD gender TINYINT(1) DEFAULT '1'");
        $this->addSql("ALTER TABLE users ADD city VARCHAR(255) DEFAULT NULL, ADD gender  TINYINT(1) DEFAULT '1'");
        $this->addSql('ALTER TABLE licences ADD secondary_language VARCHAR(20) DEFAULT NULL, CHANGE phone phone VARCHAR(36) DEFAULT NULL, CHANGE gender gender SMALLINT DEFAULT 1');
        $this->addSql('ALTER TABLE users ADD secondary_language VARCHAR(20) DEFAULT NULL, CHANGE gender gender SMALLINT DEFAULT 1');
        $this->addSql('ALTER TABLE licences CHANGE email email VARCHAR(80) DEFAULT NULL, CHANGE phone phone VARCHAR(18) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(80) DEFAULT NULL, CHANGE phone phone VARCHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE applications ADD lasf_email VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE licences DROP driver_type');
        $this->addSql('ALTER TABLE users DROP city, DROP gender, DROP language, DROP other_language');
        $this->addSql('ALTER TABLE licences DROP city, DROP gender, DROP language, DROP other_language');
        $this->addSql('ALTER TABLE licences DROP second_driver, DROP first_driver');
        $this->addSql('ALTER TABLE licences CHANGE email email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE phone phone VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE phone phone VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE applications DROP lasf_email');
    }
}
