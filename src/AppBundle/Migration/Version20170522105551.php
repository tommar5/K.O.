<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170522105551 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sQ = <<<SQ
INSERT INTO mail_templates (alias, subject, content, created_at, updated_at) VALUES
("inform_about_added_track_licence", "Pranešimas apie pridėtą trasos licenciją",
"Sveiki,
<p>Pranešame, kad buvo pateikta trasos licencija.</p>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),
("inform_about_added_organisator_licence", "Pranešimas apie pridėtą organizatoriaus licenciją",
"Sveiki,
<p>Pranešame, kad buvo pateikta organizatoriaus licencija.</p>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),
("inform_about_assigned_observer", "Pranešimas apie stebėtojo paskyrimą",
"Sveiki,
<p>Pranešame, kad buvo priskirtas stebėtojas.</p>
Stebėtojas: {{ observer }}</br>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),
("inform_about_assigned_skkhead", "Pranešimas apie SKK pirmininko paskyrimą",
"Sveiki,
<p>Pranešame, kad buvo paskirtas SKK pirmininkas.</p>
SKK pirmininkas: {{ skkHead }}</br>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),

("inform_about_assigned_steward", "Pranešimas apie komisaro paskyrimą",
"Sveiki,
<p>Pranešame, kad buvo paskirtas komisaras.</p>
Komisaras: {{ steward }}</br>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00")

SQ;
        $this->addSql($sQ);
                    
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_about_added_track_licence'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_about_added_organisator_licence'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_about_assigned_observer'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_about_assigned_skkhead'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_about_assigned_steward'");
    }
}
