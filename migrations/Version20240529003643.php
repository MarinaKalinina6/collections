<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240529003643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE collection_category (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE custom_item_attribute (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', item_collection_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_DC45CCD1BDE5FE26 (item_collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', item_collection_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id VARCHAR(255) NOT NULL, name VARCHAR(180) NOT NULL, added_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1F1B251EBDE5FE26 (item_collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE items_to_tags (item_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', item_tag_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F4D2DDE8126F525E (item_id), INDEX IDX_F4D2DDE83C2B16DE (item_tag_id), PRIMARY KEY(item_id, item_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_collection (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', category_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(180) NOT NULL, description VARCHAR(180) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, user_id VARCHAR(255) NOT NULL, INDEX IDX_41FC4D3812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE item_tags (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A78CD0DD5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', username VARCHAR(180) NOT NULL, password VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, status INT NOT NULL, added_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_sign_in_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE value_item_attributes (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', item_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', custom_item_attribute_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', type VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_E941AF66126F525E (item_id), INDEX IDX_E941AF668BF3B7B6 (custom_item_attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE custom_item_attribute ADD CONSTRAINT FK_DC45CCD1BDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES item_collection (id)');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251EBDE5FE26 FOREIGN KEY (item_collection_id) REFERENCES item_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE items_to_tags ADD CONSTRAINT FK_F4D2DDE8126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE items_to_tags ADD CONSTRAINT FK_F4D2DDE83C2B16DE FOREIGN KEY (item_tag_id) REFERENCES item_tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_collection ADD CONSTRAINT FK_41FC4D3812469DE2 FOREIGN KEY (category_id) REFERENCES collection_category (id)');
        $this->addSql('ALTER TABLE value_item_attributes ADD CONSTRAINT FK_E941AF66126F525E FOREIGN KEY (item_id) REFERENCES item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE value_item_attributes ADD CONSTRAINT FK_E941AF668BF3B7B6 FOREIGN KEY (custom_item_attribute_id) REFERENCES custom_item_attribute (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE custom_item_attribute DROP FOREIGN KEY FK_DC45CCD1BDE5FE26');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251EBDE5FE26');
        $this->addSql('ALTER TABLE items_to_tags DROP FOREIGN KEY FK_F4D2DDE8126F525E');
        $this->addSql('ALTER TABLE items_to_tags DROP FOREIGN KEY FK_F4D2DDE83C2B16DE');
        $this->addSql('ALTER TABLE item_collection DROP FOREIGN KEY FK_41FC4D3812469DE2');
        $this->addSql('ALTER TABLE value_item_attributes DROP FOREIGN KEY FK_E941AF66126F525E');
        $this->addSql('ALTER TABLE value_item_attributes DROP FOREIGN KEY FK_E941AF668BF3B7B6');
        $this->addSql('DROP TABLE collection_category');
        $this->addSql('DROP TABLE custom_item_attribute');
        $this->addSql('DROP TABLE item');
        $this->addSql('DROP TABLE items_to_tags');
        $this->addSql('DROP TABLE item_collection');
        $this->addSql('DROP TABLE item_tags');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE value_item_attributes');
    }
}
