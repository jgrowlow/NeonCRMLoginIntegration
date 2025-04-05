<?php

namespace CustomApi\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class NeonCRMService
{
    protected $client;
    protected $apiUrl;
    protected $apiKey;
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->client = new Client();
        $this->apiUrl = 'https://api.neoncrm.com/v2/accounts/{id}';
        $this->apiKey = getenv('NEONCRM_API_KEY');
        $this->logger = $logger;
    }

    /**
     * Get the user's email from NeonCRM by user ID.
     */
    public function getUserEmail($userId)
    {
        try {
            $response = $this->client->request('GET', "{$this->apiUrl}{$userId}", [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['individualAccount'])) {
                return $data['individualAccount']['email1'] ?? 
                       $data['individualAccount']['email2'] ?? 
                       $data['individualAccount']['contact']['email'] ?? 
                       null;
            }

            $this->logger->warning("NeonCRM response missing 'individualAccount' for user {$userId}");
            return null;
        } catch (RequestException $e) {
            $this->logger->error("NeonCRM API error for user {$userId}: " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            $this->logger->error("Unexpected error for user {$userId}: " . $e->getMessage());
            return null;
        }
    }
}
