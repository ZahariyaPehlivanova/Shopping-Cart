<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170420134208 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category_promotions (category_id INT NOT NULL, promotion_id INT NOT NULL, INDEX IDX_CC05604A12469DE2 (category_id), INDEX IDX_CC05604A139DF194 (promotion_id), PRIMARY KEY(category_id, promotion_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_promotions ADD CONSTRAINT FK_CC05604A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_promotions ADD CONSTRAINT FK_CC05604A139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product CHANGE createdOn createdOn DATETIME NOT NULL, CHANGE updatedOn updatedOn DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE category_promotions');
        $this->addSql('ALTER TABLE product CHANGE createdOn createdOn DATETIME NOT NULL, CHANGE updatedOn updatedOn DATETIME NOT NULL');
    }
}
