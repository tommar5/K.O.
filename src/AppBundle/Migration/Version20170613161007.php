<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170613161007 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sQ = <<<SQ
INSERT INTO mail_templates (alias, subject, content, created_at, updated_at) VALUES
("inform_about_assigned_judges", "Pranešimas apie teisėjo paskyrimą",
"Sveiki,
<p>Pranešame, kad buvo paskirtas teisėjas.</p>
Teisėjai: {{ judges }}</br>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),
("inform_about_confirmed_organisator_licence", "Pranešimas apie patvirtintą organizatoriaus licenziją",
"Sveiki,
<p>Pranešame, kad Jūsų pateikta organizatoriaus licenzija buvo patvirtinta.</p>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),

("inform_added_competition_result", "Pranešimas apie įkeltus varžybų rezultatus",
"Sveiki,
<p>Pranešame, kad buvo įkelti varžybų rezultatai.</p>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),

("inform_added_competition_report", "Pranešimas apie įkeltą varžybų ataskaitą",
"Sveiki,
<p>Pranešame, kad buvo įkelta varžybų ataskaitą.</p>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),

("inform_added_competition_bulletin", "Pranešimas apie įkeltas varžybų organizatoriaus biuletenis",
"Sveiki,
<p>Pranešame, kad buvo įkeltas organizatoriaus biuletenis.</p>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00"),

("inform_added_competition_skk_report", "Pranešimas apie įkeltą SKK pirmininko ataskaita",
"Sveiki,
<p>Pranešame, kad buvo įkelta SKK pirmininko ataskaita.</p>
Paraiškos pavadinimas: {{ application }}</br>", "2017-05-22 00:00:00", "2017-05-22 00:00:00")

SQ;
        $this->addSql($sQ);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_about_assigned_judges'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_about_confirmed_organisator_licence'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_added_competition_result'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_added_competition_report'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_added_competition_bulletin'");
        $this->addSql("DELETE FROM `mail_templates` WHERE `alias` = 'inform_added_competition_skk_report'");
    }
}
