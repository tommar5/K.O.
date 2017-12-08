<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160929103742 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO role_action (action, roles) VALUES ('application-view', 233174), ('application-modify', 233174), ('application-modify-others', 233168), 
          ('application-delete', 16), ('application-show-actions', 80), ('application-decline', 16), ('application-assigned-to-me', 2064), 
          ('competition-chief-confirm', 2064), ('competition-chief-view', 22), ('competition-member-view', 16), ('document-remove', 32784), ('date-restriction', 16),
          ('safety-chiefs-view', 16), ('additional-fields-view', 22), ('ajax-submit', 34320), ('pay-invoice-message-view', 6), ('admin-fields-view', 16), 
          ('delivery-modify', 22), ('insurance-upload', 6), ('other-documents-upload', 6), ('other-documents-view', 6), ('track-acceptance-upload', 6),
          ('track-acceptance-confirm', 1024), ('applicatiion-status-view', 22)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
