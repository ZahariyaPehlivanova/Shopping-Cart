<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170428115832 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE createdOn createdOn DATETIME NOT NULL, CHANGE updatedOn updatedOn DATETIME NOT NULL');
        $this->addSql('ALTER TABLE promotion ADD is_category_promo TINYINT(1) NOT NULL, ADD is_all_products_promo TINYINT(1) NOT NULL, ADD is_product_promo TINYINT(1) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE createdOn createdOn DATETIME NOT NULL, CHANGE updatedOn updatedOn DATETIME NOT NULL');
        $this->addSql('ALTER TABLE promotion DROP is_category_promo, DROP is_all_products_promo, DROP is_product_promo');
    }
}
