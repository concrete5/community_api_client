<?php

/**
 * @project:  Community Api Client
 *
 * @copyright  (C) 2021 Portland Labs (https://www.portlandlabs.com)
 * @author     Fabian Bitter (fabian@bitter.de)
 */

namespace PortlandLabs\CommunityApiClient\Models;

use Concrete\Core\User\User;
use PortlandLabs\CommunityApiClient\ApiClient;
use PortlandLabs\CommunityApiClient\Exceptions\CommunicatorError;
use PortlandLabs\CommunityApiClient\Exceptions\InvalidConfiguration;

class Achievements
{
    protected $client;
    protected $user;

    public function __construct(
        User $user,
        ApiClient $client
    )
    {
        $this->client = $client;
        $this->user = $user;
    }

    public function assign(
        string $handle
    ): bool
    {
        $payload = [
            "user" => [
                "email" => $this->user->getUserInfoObject()->getUserEmail(),
            ],
            "achievement" => [
                "handle" => $handle
            ]
        ];

        try {
            $response = $this->client->doRequest("/api/v1/achievements/assign", $payload);

            if (isset($response["error"]) && (int)$response["error"] === 1) {
                return false;
            } else {
                return true;
            }
        } catch (CommunicatorError $e) {
            return false;
        } catch (InvalidConfiguration $e) {
            return false;
        }
    }
}