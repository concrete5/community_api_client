<?php /** @noinspection PhpUnused */
/** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:  Community Api Client
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\CommunityApiClient;

use Concrete\Core\Config\Repository\Repository;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Exception;
use PortlandLabs\CommunityApiClient\Exceptions\CommunicatorError;
use PortlandLabs\CommunityApiClient\Exceptions\InvalidConfiguration;

class ApiClient
{
    protected $config;

    public function __construct(
        Repository $config
    )
    {
        $this->config = $config;
    }

    public function getClientId(): string
    {
        return $this->config->get('community_api_client.client_id', '');
    }

    public function setClientId(
        string $clientId
    ): self
    {
        $this->config->save('community_api_client.client_id', $clientId);

        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->config->get('community_api_client.client_secret', '');
    }

    public function setClientSecret(
        string $clientSecret
    ): self
    {
        $this->config->save('community_api_client.client_secret', $clientSecret);

        return $this;
    }

    public function getEndpoint(): string
    {
        return $this->config->get('community_api_client.endpoint', '');
    }

    public function setEndpoint(
        string $endpoint
    ): self
    {
        $this->config->save('community_api_client.endpoint', $endpoint);

        return $this;
    }

    private function hasValidConfiguration()
    {
        return strlen($this->getEndpoint()) > 0 &&
            strlen($this->getClientId()) > 0 &&
            strlen($this->getClientSecret()) > 0;
    }

    /**
     * @return Client
     */
    private function getClient(): ?Client
    {
        if ($this->hasValidConfiguration()) {
            $stack = HandlerStack::create();

            $stack->push(
                new OAuth2Middleware(
                    new ClientCredentials(
                        new Client([
                            'base_uri' => $this->getBaseUrl()->withPath('/oauth/2.0/token')
                        ]),
                        [
                            "client_id" => $this->getClientId(),
                            "client_secret" => $this->getClientSecret()
                        ]
                    )
                )
            );

            return new Client([
                'handler' => $stack,
                'auth' => 'oauth',
            ]);
        } else {
            return null;
        }
    }

    private function getBaseUrl(): Uri
    {
        $uri = new Uri();
        return $uri->withHost($this->getEndpoint());
    }

    /**
     * @param string $path
     * @param array $payload
     * @return array
     * @throws InvalidConfiguration
     * @throws CommunicatorError
     */
    public function doRequest(
        string $path,
        array $payload = []
    ): array
    {
        if ($this->hasValidConfiguration()) {
            /** @noinspection PhpComposerExtensionStubsInspection */
            $request = new Request(
                "POST",
                $this->getBaseUrl()->withPath($path),
                [
                    "Content-Type" => "application/json"
                ],
                @json_encode($payload)
            );

            try {
                $response = $this->getClient()->send($request);

                $rawResponse = $response->getBody()->getContents();

                /** @noinspection PhpComposerExtensionStubsInspection */
                $jsonResponse = @json_decode($rawResponse, true);

                return $jsonResponse;

            } catch (Exception $e) {
                throw new CommunicatorError();
            } catch (GuzzleException $e) {
                throw new CommunicatorError();
            }
        } else {
            throw new InvalidConfiguration();
        }
    }
}