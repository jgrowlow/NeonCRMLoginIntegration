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
        $this->apiUrl = 'https://api.neoncrm.com/v2/accounts/';
        $this->apiKey = getenv('NEONCRM_API_KEY');
        $this->logger = $logger;
    }

    /**
     * Get the user's email from NeonCRM by user ID.
     */
    public function getUserEmail($accountId)
    {
        try {
            // Make the API call to NeonCRM to get account details
            $response = $this->client->request('GET', $this->apiUrl . $accountId, [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ],
                'timeout' => 10,  // Optional: Set a timeout to avoid hanging indefinitely
            ]);
            
            // Decode the response
            $data = json_decode($response->getBody(), true);
            
            // Check for the primary contact's email
            if (isset($data['primaryContact']['email1'])) {
                return $data['primaryContact']['email1'];
            } elseif (isset($data['primaryContact']['email2'])) {
                return $data['primaryContact']['email2'];
            } elseif (isset($data['primaryContact']['email3'])) {
                return $data['primaryContact']['email3'];
            }
            
            // If no email found, log and return null
            $this->logger->warning("No valid email found for account {$accountId}");
            return null;
        }
