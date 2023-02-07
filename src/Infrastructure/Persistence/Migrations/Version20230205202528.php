<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230205202528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create transactions table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            CREATE TABLE `transactions` (
              `id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
              `user_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `type` enum('expense','income','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
              `source_wallet_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
              `target_wallet_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `category_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
              `date` date NOT NULL,
              `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `amount` int NOT NULL,
              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `transactions_date_index` (`date` DESC),
              KEY `transactions_categories_id_fk` (`category_id`),
              KEY `transactions_source_wallets_id_fk` (`source_wallet_id`),
              KEY `transactions_target_wallets_id_fk` (`target_wallet_id`),
              CONSTRAINT `transactions_categories_id_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
              CONSTRAINT `transactions_source_wallets_id_fk` FOREIGN KEY (`source_wallet_id`) REFERENCES `wallets` (`id`),
              CONSTRAINT `transactions_target_wallets_id_fk` FOREIGN KEY (`target_wallet_id`) REFERENCES `wallets` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS `transactions`');
    }
}
