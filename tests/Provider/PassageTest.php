<?php

declare(strict_types=1);

namespace League\OAuth2\Client\Test\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Passage;
use League\OAuth2\Client\Provider\PassageUser;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Passage::class)]
class PassageTest extends TestCase
{
    protected Passage $provider;

    protected function setUp(): void
    {
        $this->provider = new Passage([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'subDomain' => 'mock_sub_domain',
        ]);
    }

    public function test_authorization_url(): void
    {
        $url = $this->provider->getAuthorizationUrl();

        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('approval_prompt', $query);
        $this->assertNotNull($this->provider->getState());

        $this->assertSame('https', $uri['scheme']);
        $this->assertSame('mock_sub_domain.withpassage.com', $uri['host']);
        $this->assertSame('/authorize', $uri['path']);
    }

    public function test_base_access_token_url(): void
    {
        $this->assertSame('https://mock_sub_domain.withpassage.com/token', $this->provider->getBaseAccessTokenUrl([]));
    }

    public function test_resource_owner_details_url(): void
    {
        $token = new AccessToken(['access_token' => 'mock_access_token']);

        $this->assertSame('https://mock_sub_domain.withpassage.com/userinfo', $this->provider->getResourceOwnerDetailsUrl($token));
    }

    public function test_logout_url(): void
    {
        $this->assertSame('https://mock_sub_domain.withpassage.com/logout', $this->provider->getLogoutUrl());
    }

    public function test_not_setting_a_sub_domain_will_throw(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Passage([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
        ]);
    }

    public function test_success_response(): void
    {
        $expectedData = [
            'access_token' => 'mock_access_token',
            'token_type' => 'bearer',
            'expires_in' => 3600,
            'refresh_token' => 'mock_refresh_token',
        ];

        $this->provider->setHttpClient($this->createMockClient([
            new Response(200, [], json_encode($expectedData, \JSON_THROW_ON_ERROR)),
        ]));

        $this->assertSame($expectedData, $this->provider->getParsedResponse(new Request('GET', '/')));
    }

    /**
     * @dataProvider provideErrorResponses
     */
    public function test_expect_exception(string $expectedErrorMessage, array $errorData): void
    {
        $this->expectException(IdentityProviderException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        $this->provider->setHttpClient($this->createMockClient([
            new Response(400, [], json_encode($errorData, \JSON_THROW_ON_ERROR)),
        ]));

        $this->provider->getParsedResponse(new Request('GET', '/'));
    }

    public static function provideErrorResponses(): iterable
    {
        yield 'Unhandled error response' => ['Unhandled error response', []];
        yield 'With error' => ['mock_error', ['error' => 'mock_error']];
        yield 'With error and description' => ['mock_error: mock_error_description', ['error' => 'mock_error', 'error_description' => 'mock_error_description']];
    }

    public function test_resource_owner(): void
    {
        $responseData = [
            'sub' => 'mock_sub',
            'email' => 'mock_email',
            'email_verified' => true,
        ];
        $this->provider->setHttpClient($this->createMockClient([
            new Response(200, [], json_encode($responseData, \JSON_THROW_ON_ERROR)),
        ]));

        $token = new AccessToken(['access_token' => 'mock_access_token']);

        $user = $this->provider->getResourceOwner($token);

        $this->assertInstanceOf(PassageUser::class, $user);
        $this->assertSame($responseData['sub'], $user->getId());
        $this->assertSame($responseData['email'], $user->getEmail());
        $this->assertNull($user->getPhoneNumber());
        $this->assertTrue($user->isEmailVerified());
        $this->assertFalse($user->isPhoneNumberVerified());
    }

    private function createMockClient(array $responses): ClientInterface
    {
        $mock = new MockHandler($responses);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }
}
