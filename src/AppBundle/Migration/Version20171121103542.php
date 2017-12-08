<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171121103542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE applications ADD COLUMN svo_delegate_id INT(11)');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_SvoDelegate FOREIGN KEY (svo_delegate_id) REFERENCES users(id)');
        $this->addSql('ALTER TABLE sub_competition ADD COLUMN svo_delegate_id INT(11)');
        $this->addSql('ALTER TABLE sub_competition ADD CONSTRAINT FK_SubCompetitionSvoDelegate FOREIGN KEY (svo_delegate_id) REFERENCES users(id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE applications ADD COLUMN svo_delegate VARCHAR(255)');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_SvoDelegate;');
        $this->addSql('ALTER TABLE applications DROP COLUMN svo_delegate_id');
        $this->addSql('ALTER TABLE sub_competition ADD COLUMN svo_delegate VARCHAR(255)');
        $this->addSql('ALTER TABLE sub_competition DROP FOREIGN KEY FK_SubCompetitionSvoDelegate;');
        $this->addSql('ALTER TABLE sub_competition DROP COLUMN svo_delegate_id');
    }
}
