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
 * Class Version20200825090230
 *
 * @package DoctrineMigrations
 */
final class Version20200825090230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'create database and tables';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $sql = 'CREATE TABLE processed_file (
                    id INT AUTO_INCREMENT NOT NULL,
                    file_name VARCHAR(255) NOT NULL, 
                    md5_checksum VARCHAR(45) NOT NULL, 
                    date_created DATETIME NOT NULL, 
                    date_updated DATETIME DEFAULT NULL, 
                    PRIMARY KEY(id)
                ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql($sql);

        $sql = 'CREATE TABLE postcode_info (
                    id INT AUTO_INCREMENT NOT NULL, 
                    postcode VARCHAR(45) NOT NULL, 
                    latitude NUMERIC(10, 8) NOT NULL, 
                    longitude NUMERIC(11, 8) NOT NULL, 
                    date_created DATETIME NOT NULL, 
                    date_updated DATETIME DEFAULT NULL, 
                    PRIMARY KEY(id)
                ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB';
        $this->addSql($sql);
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE processed_file');
        $this->addSql('DROP TABLE postcode_info');
    }
}
