<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318130426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, animal_id INT NOT NULL, assistant_id INT DEFAULT NULL, veterinarian_id INT NOT NULL, creation_date DATETIME NOT NULL, consultation_date DATETIME NOT NULL, reason LONGTEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_964685A68E962C16 (animal_id), INDEX IDX_964685A6E05387EF (assistant_id), INDEX IDX_964685A6804C8213 (veterinarian_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultation_treatment (consultation_id INT NOT NULL, treatment_id INT NOT NULL, INDEX IDX_3EF32AD662FF6CDF (consultation_id), INDEX IDX_3EF32AD6471C0366 (treatment_id), PRIMARY KEY(consultation_id, treatment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE treatment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, duration INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A68E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6E05387EF FOREIGN KEY (assistant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6804C8213 FOREIGN KEY (veterinarian_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE consultation_treatment ADD CONSTRAINT FK_3EF32AD662FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE consultation_treatment ADD CONSTRAINT FK_3EF32AD6471C0366 FOREIGN KEY (treatment_id) REFERENCES treatment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal ADD photo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231F7E9E4C8C FOREIGN KEY (photo_id) REFERENCES media (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6AAB231F7E9E4C8C ON animal (photo_id)');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_UUID ON user');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(255) NOT NULL, DROP uuid');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A68E962C16');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6E05387EF');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6804C8213');
        $this->addSql('ALTER TABLE consultation_treatment DROP FOREIGN KEY FK_3EF32AD662FF6CDF');
        $this->addSql('ALTER TABLE consultation_treatment DROP FOREIGN KEY FK_3EF32AD6471C0366');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE consultation_treatment');
        $this->addSql('DROP TABLE treatment');
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231F7E9E4C8C');
        $this->addSql('DROP INDEX UNIQ_6AAB231F7E9E4C8C ON animal');
        $this->addSql('ALTER TABLE animal DROP photo_id');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD uuid VARCHAR(180) NOT NULL, DROP email');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_UUID ON user (uuid)');
    }
}
