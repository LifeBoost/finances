<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230204211526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create categories table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `categories`
            (
                `id` varchar(36) COLLATE utf8mb4_unicode_ci not null,
                `user_id` varchar(36) not null,
                `type` enum('expense', 'income') not null,
                `name` varchar(255) not null,
                `icon` varchar(255) not null,
                `created_at` datetime default NOW() not null,
                `updated_at` datetime null,
                constraint categories_pk primary key (`id`),
                constraint categories_user_id_name_type_unique unique (`user_id`, `name`, `type`)
            );
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS `categories`');
    }
}
