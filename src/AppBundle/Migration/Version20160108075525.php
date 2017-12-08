<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160108075525 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE mail_templates SET content = "Sveiki,
                    <p>Norime pranešti apie naują licencijos prašymą LASF sistemoje.</p>

                    <p>
                        Prašymą pateikė: {{ customer }}<br>
                        Pageidaujama licencija: {{ (\"licences.type.\"~licence)|trans }}
                    </p>" where alias = "inform_about_licence"');
        $this->addSql('UPDATE mail_templates SET content = "Sveiki,
                    <p>Norime pranešti naujo dokumento įkėlimą LASF sistemoje.</p>
                    <p>
                        Vartotojas: {{ customer }}<br>
                        Licencija: {{ (\"licences.type.\"~licence)|trans }}<br>
                        Dokumentas: {{ (\"file_uploads.type.\"~file)|trans }}
                    </p>" where alias = "inform_rejected_file_update"');
        $this->addSql('UPDATE mail_templates SET content = "Sveiki,
                    <p>Norime Jums pranešti, kad vartotojo „{{ customer.fullName }}“ licencijos „{{ (\"licences.type.\"~licence.type)|trans }}“ dokumento „{{ (\'file_uploads.type.\'~file.type)|trans }}“ galiojimas baigiasi už mėnesio.</p>" where alias = "document_expiring_admin"');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
