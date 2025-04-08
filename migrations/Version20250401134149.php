<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250401134149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE customer_property (customer_id UUID NOT NULL, property_id UUID NOT NULL, PRIMARY KEY(customer_id, property_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A7375FF19395C3F3 ON customer_property (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A7375FF1549213EC ON customer_property (property_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN customer_property.customer_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN customer_property.property_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer_property ADD CONSTRAINT FK_A7375FF19395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer_property ADD CONSTRAINT FK_A7375FF1549213EC FOREIGN KEY (property_id) REFERENCES properties (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE properties ADD agent_id UUID NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN properties.agent_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE properties ADD CONSTRAINT FK_87C331C73414710B FOREIGN KEY (agent_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_87C331C73414710B ON properties (agent_id)
        SQL);
        $this->addSql(<<<'SQL'
           ALTER TABLE "user" ADD discriminator VARCHAR(255) DEFAULT 'customer' NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer_property DROP CONSTRAINT FK_A7375FF19395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customer_property DROP CONSTRAINT FK_A7375FF1549213EC
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE customer_property
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE properties DROP CONSTRAINT FK_87C331C73414710B
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_87C331C73414710B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE properties DROP agent_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP discriminator
        SQL);
    }
}
