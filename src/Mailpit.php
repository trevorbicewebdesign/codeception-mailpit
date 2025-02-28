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
    public function getLastEmailContent()
    {
        // Mailpit API endpoint for searching messages.
        $endpoint = '/api/v1/search';
        $query = [
            'query' => "''",  // Provide literal two single quotes to satisfy the API.
            'limit' => 1,
            'sort'  => 'desc'
        ];

        $response = $this->client->request('GET', $endpoint, ['query' => $query]);
        $body = $response->getBody()->getContents();
        $result = json_decode($body, true);

        if (empty($result['data']) || !is_array($result['data'])) {
            throw new \Exception('No messages found in Mailpit.');
        }

        // Assume the first message is the most recent one.
        $lastMessage = $result['data'][0];

        // Mailpit API typically returns the email content in the 'body' field.
        $emailContent = isset($lastMessage['body']) ? $lastMessage['body'] : '';

        if (empty($emailContent)) {
            throw new \Exception('No email content found in the latest message.');
        }

        return $emailContent;
    }
}
