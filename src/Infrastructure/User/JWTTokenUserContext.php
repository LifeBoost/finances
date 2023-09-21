<?php

declare(strict_types=1);

namespace App\Infrastructure\User;

use App\Domain\User\UserContext;
use App\Domain\User\UserId;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\RequestStack;

final class JWTTokenUserContext implements UserContext
{
    private const AUTHORIZATION_HEADER_NAME = 'Authorization';
    private const AUTHORIZATION_HEADER_PREFIX = 'Bearer';

    public function __construct(
        private readonly string $jwtSecretKey,
        private readonly string $jwtAlgorithm,
        private readonly RequestStack $requestStack
    ) {}

    public function getUserId(): UserId
    {
        $jwtToken = $this->getJwtTokenFromHeader();

        $result = JWT::decode($jwtToken, new Key($this->jwtSecretKey, $this->jwtAlgorithm));

        return UserId::fromString($result->userId);
    }

    private function getJwtTokenFromHeader(): string
    {
        $header = $this->requestStack->getMainRequest()->headers->get(self::AUTHORIZATION_HEADER_NAME);

        return trim(str_replace(self::AUTHORIZATION_HEADER_PREFIX, '', $header));
    }
}
