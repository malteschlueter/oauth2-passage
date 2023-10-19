<?php

declare(strict_types=1);

namespace League\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Passage extends AbstractProvider
{
    use BearerAuthorizationTrait;

    protected const BASE_PASSAGE_DOMAIN = 'withpassage.com';

    protected string $subDomain;

    /**
     * @param mixed[] $options
     * @param mixed[] $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        $options['subDomain'] ?? throw new \InvalidArgumentException('The "subDomain" option not set. Please set a sub domain.');
    }

    public function getBaseAuthorizationUrl(): string
    {
        return $this->getBasePassageUrl() . '/authorize';
    }

    /**
     * @param mixed[] $params
     */
    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->getBasePassageUrl() . '/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->getBasePassageUrl() . '/userinfo';
    }

    public function getLogoutUrl(): string
    {
        return $this->getBasePassageUrl() . '/logout';
    }

    /**
     * @return string[]
     */
    protected function getDefaultScopes(): array
    {
        return ['openid email phone'];
    }

    /**
     * Checks a provider response for errors.
     *
     * @param mixed[]|string $data Parsed response data
     *
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if ($response->getStatusCode() === 200) {
            return;
        }

        $message = match (true) {
            \is_string($data) => $data,
            isset($data['error'], $data['error_description']) => $data['error'] . ': ' . $data['error_description'],
            isset($data['error']) => $data['error'],
            default => 'Unhandled error response',
        };

        throw new IdentityProviderException($message, $response->getStatusCode(), $response);
    }

    /**
     * @param mixed[] $response
     */
    protected function createResourceOwner(array $response, AccessToken $token): ResourceOwnerInterface
    {
        return new PassageUser($response);
    }

    protected function getBasePassageUrl(): string
    {
        return sprintf('https://%s.%s', $this->subDomain, static::BASE_PASSAGE_DOMAIN);
    }
}
