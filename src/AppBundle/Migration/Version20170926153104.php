<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170926153104 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('Update licences set gender=2 where gender=1');
        $this->addSql('Update licences set gender=1 where gender=0');
        $this->addSql('Update users set gender=2 where gender=1');
        $this->addSql('Update licences set gender=1 where gender=0');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('Update licences set gender=0 where gender=1');
        $this->addSql('Update licences set gender=1 where gender=2');
        $this->addSql('Update users set gender=0 where gender=1');
        $this->addSql('Update licences set gender=1 where gender=2');
    }
}
