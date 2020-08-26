<?php
/**
 * Description:
 * create database and tables
 *
 * @package App\Services
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20200826195033
 *
 * @package DoctrineMigrations
 */
final class Version20200826195033 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create database and tables';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE processed_file (
                        id INT AUTO_INCREMENT NOT NULL,
                        file_name VARCHAR(255) NOT NULL, 
                        md5_checksum VARCHAR(45) NOT NULL, 
                        date_created DATETIME NOT NULL, 
                        date_updated DATETIME DEFAULT NULL, 
                        UNIQUE INDEX unique_file_name_and_checksum (file_name, md5_checksum),
                        PRIMARY KEY(id)
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE postcode_info (
                        id INT AUTO_INCREMENT NOT NULL, 
                        postcode VARCHAR(45) NOT NULL, 
                        latitude NUMERIC(10, 8) NOT NULL, 
                        longitude NUMERIC(11, 8) NOT NULL, 
                        date_created DATETIME NOT NULL, 
                        date_updated DATETIME DEFAULT NULL,
                        UNIQUE INDEX UNIQ_6C4F97576339A411 (postcode),
                        PRIMARY KEY(id)
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE processed_file');
        $this->addSql('DROP TABLE postcode_info');
    }
}
