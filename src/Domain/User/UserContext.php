<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserContext
{
    public function getUserId(): UserId;
}
