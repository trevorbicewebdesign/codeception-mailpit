<?php
namespace Codeception\Module;

use Codeception\Module;
use GuzzleHttp\Client;

class Mailpit extends Module
{
    /**
     * Module configuration.
     *
     * @var array
     */
    protected array $config;

    /**
     * @var Client
     */
    protected $client;

    public function _initialize()
    {
        if (empty($this->config['base_uri'])) {
            throw new \Exception('The base_uri configuration is required.');
        }

        $this->client = new Client([
            'base_uri' => $this->config['base_uri'],
            'timeout'  => 10,
        ]);
    }

    /**
     * Get the plain-text content of the most recent email.
     *
     * @return string
     * @throws \Exception if no messages are found or on API error.
     */
    <?php
namespace Codeception\Module;

use Codeception\Module;
use GuzzleHttp\Client;

class Mailpit extends Module
{
    protected $mailpit;
    /**
     * Module configuration.
     *
     * @var array
     */
    protected array $config = [
        'base_uri' => 'http://localhost:10006/',
    ];

    /**
     * @var Client
     */
    protected $client;

    protected array $requiredFields = array('base_uri');

    public function _initialize()
    {
        if (empty($this->config['base_uri'])) {
            throw new \Exception('The base_uri configuration is required.');
        }

        $this->client = new Client([
            'base_uri' => $this->config['base_uri'],
            'timeout'  => 10,
        ]);
    }

    /**
     * Get the plain-text content of the most recent email.
     *
     * @return string
     * @throws \Exception if no messages are found or on API error.
     */
    public function getLastEmailContent()
    {
        // Mailpit API endpoint for searching messages.
        $searchEndpoint = '/api/v1/search';
        $searchQuery = [
            'query' => "''",  // Literal two single quotes to satisfy the API.
            'limit' => 1,
            'sort'  => 'desc'
        ];

        try {
            $searchResponse = $this->client->request('GET', $searchEndpoint, ['query' => $searchQuery]);
            $searchBody = $searchResponse->getBody()->getContents();
            $searchResult = json_decode($searchBody, true);

            if (empty($searchResult['messages']) || !is_array($searchResult['messages'])) {
                throw new \Exception("No messages found in Mailpit. Response: {$searchBody}");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception('HTTP search request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('An error occurred during search: ' . $e->getMessage());
        }

        // Get the ID of the most recent email from the search results.
        $lastMessage = $searchResult['messages'][0];
        // Use the "ID" field (not MessageID) to fetch the full message.
        $messageId = $lastMessage['ID'];

        // Now fetch the full message by its ID.
        $messageEndpoint = "/api/v1/message/{$messageId}";
        try {
            $messageResponse = $this->client->request('GET', $messageEndpoint);
            $messageBody = $messageResponse->getBody()->getContents();
            $fullMessage = json_decode($messageBody, true);

            if (empty($fullMessage['Text'])) {
                throw new \Exception("No full message content found for message ID: {$messageId}");
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception('HTTP request for full message failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while fetching full message: ' . $e->getMessage());
        }

        return $fullMessage['Text'];
    }
}
