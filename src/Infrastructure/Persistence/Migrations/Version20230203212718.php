<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230203212718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create wallets table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE `wallets`
            (
                `id`            varchar(36)             not null,
                `user_id`       varchar(36)             not null,
                `name`          varchar(255)           not null,
                `start_balance` int                    not null,
                `currency`      varchar(3)             not null,
                `created_at`    datetime default NOW() not null,
                `updated_at`    datetime               null,
                constraint wallets_pk primary key (`id`),
                constraint wallets_user_id_name_unique unique (`user_id`, `name`)
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS `wallets`');
    }
}
