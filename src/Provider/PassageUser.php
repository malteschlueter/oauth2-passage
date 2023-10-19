<?php

declare(strict_types=1);

namespace League\OAuth2\Client\Provider;

class PassageUser implements ResourceOwnerInterface
{
    /**
     * @var mixed[]
     */
    protected array $response;

    /**
     * @param mixed[] $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId(): ?string
    {
        return $this->response['sub'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->response['email'] ?? null;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->response['phone_number'] ?? null;
    }

    public function isEmailVerified(): bool
    {
        return $this->response['email_verified'] ?? false;
    }

    public function isPhoneNumberVerified(): bool
    {
        return $this->response['phone_number_verified'] ?? false;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return $this->response;
    }
}
