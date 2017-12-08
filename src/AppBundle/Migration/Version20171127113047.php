<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171127113047 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE mail_templates SET content="Sveiki,
<p>Primename, kad paraiškoje „{{ competition }}“ nėra pateiktas dokumentas „{{ (\'file_uploads.type.\'~file)|trans }}“. Dokumentas turi būti pateiktas iki {{ date }}.</p>" WHERE alias="upload_document_to_competition"');
        $this->addSql('UPDATE mail_templates SET content="Sveiki,
<p>Primename, kad paraiškoje „{{ competition }}“ nėra pateiktas dokumentas „{{ (\'file_uploads.type.\'~file)|trans }}“. Prašom kaip įmanoma skubiau įkelti dokumentą.</p>" WHERE alias="upload_document_to_competition_without_date"');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
