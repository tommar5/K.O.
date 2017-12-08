<?php

namespace AppBundle\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20170731164326 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("update licences set type='declarant_licence.a' where type='declarant_licence_a'");
        $this->addSql("update licences set type='declarant_licence.b' where type='declarant_licence_b'");
        $this->addSql("update licences set type='declarant_licence.k' where type='declarant_licence_k'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("update licences set type='declarant_licence_a' where type='declarant_licence.a'");
        $this->addSql("update licences set type='declarant_licence_b' where type='declarant_licence.b'");
        $this->addSql("update licences set type='declarant_licence_k' where type='declarant_licence.k'");
    }
}
