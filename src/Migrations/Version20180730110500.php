<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180730110500 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE date_of_creation date_of_creation DATETIME NOT NULL, CHANGE date_of_last_modification date_of_last_modification DATETIME NOT NULL');
        $this->addSql('ALTER TABLE product_photo ADD position INT NOT NULL, CHANGE is_main is_main TINYINT(1) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product CHANGE date_of_creation date_of_creation DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL, CHANGE date_of_last_modification date_of_last_modification DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE product_photo DROP position, CHANGE is_main is_main TINYINT(1) DEFAULT \'0\'');
    }
}
