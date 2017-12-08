<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170818093115 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE licences DROP send_notification_at');
        $this->addSql('UPDATE mail_templates SET content="Sveiki,
                    <p>Primename, kad Jūsų licencijos nr. {{ number }} „{{ (\"licences.type.\"~licence.type)|trans }}“ galiojimas baigiasi {{ date|date(\"Y-m-d\") }}. Prašome ją pratęsti.</p>" WHERE alias="licence_expiring"');
        $this->addSql('UPDATE mail_templates SET content="Sveiki,
                    <p>Primename, kad Jūsų licencijos nr. {{ number }} „{{ (\"licences.type.\"~licence.type)|trans }}“ dokumento „{{ (\'file_uploads.type.\'~file.type)|trans }}“ galiojimas baigiasi {{ date|date(\"Y-m-d\") }}.</p>" WHERE alias="document_expiring"');
        $this->addSql('UPDATE mail_templates SET content="Sveiki,
                    <p>Primename, kad vartotojo „{{ customer.fullName }}“ licencijos nr. {{ number }} „{{ (\"licences.type.\"~licence.type)|trans }}“ dokumento „{{ (\'file_uploads.type.\'~file.type)|trans }}“ galiojimas baigiasi {{ date|date(\"Y-m-d\") }}.</p>" WHERE alias="document_expiring_admin"');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE licences ADD send_notification_at DATE');
        $this->addSql('UPDATE mail_templates SET content="Sveiki,
                    <p>Norime Jums pranešti, kad Jūsų licencijos „{{ (\"licences.type.\"~licence.type)|trans }}“ galiojimas baigiasi {{ date|date(\"Y-m-d\") }}. Prašome ją pratęsti.</p>" WHERE alias="licence_expiring"');
        $this->addSql('UPDATE mail_templates SET content="Sveiki,
                    <p>Norime Jums pranešti, kad Jūsų licencijos „{{ (\"licences.type.\"~licence.type)|trans }}“ dokumento „{{ (\'file_uploads.type.\'~file.type)|trans }}“ galiojimas baigiasi {{ date|date(\"Y-m-d\") }}.</p>" WHERE alias="document_expiring"');
        $this->addSql('UPDATE mail_templates SET content="Sveiki,
                    <p>Norime Jums pranešti, kad vartotojo „{{ customer.fullName }}“ licencijos „{{ (\"licences.type.\"~licence.type)|trans }}“ dokumento „{{ (\'file_uploads.type.\'~file.type)|trans }}“ galiojimas baigiasi {{ date|date(\"Y-m-d\") }}.</p>" WHERE alias="document_expiring_admin"');
    }
}
