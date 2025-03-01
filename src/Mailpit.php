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
     * Retrieves the ID of the most recent email that matches the given subject.
     *
     * @param string $subject The subject of the email to search for.
     * @return string The ID of the most recent email that matches the given subject.
     * @throws \Exception If no messages are found or if an error occurs during the search.
     */
    public function getEmailBySubject($subject)
    {
        $searchEndpoint = '/api/v1/search';
        $searchQuery = [
            'query' => $subject,
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

        // Return the ID of the most recent email.
        return $messageId;
    }

    /**
     * Retrieves an email by its ID.
     *
     * This function sends a GET request to the Mailpit API to fetch the full message
     * details for the specified email ID. It handles any HTTP request exceptions and
     * general exceptions that may occur during the process.
     *
     * @param string $id The ID of the email to retrieve.
     * @return array The full message details as an associative array.
     * @throws \Exception If the HTTP request fails or an error occurs while fetching the message.
     */
    public function getEmailById($id)
    {
        codecept_debug("Getting email for email id: {$id}");
        
        $messageEndpoint = "/api/v1/message/{$id}";
        try {
            $messageResponse = $this->client->request('GET', $messageEndpoint);
            $messageBody = $messageResponse->getBody()->getContents();
            $fullMessage = json_decode($messageBody, true);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception('HTTP request for full message failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('An error occurred while fetching full message: ' . $e->getMessage());
        }

        return $fullMessage;
    }

    /**
     * Get the mailpit message id of the most recent email.
     *
     * @return string
     * @throws \Exception if no messages are found or on API error.
     */
    public function getLastEmailId()
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

        // Return the ID of the most recent email.
        return $messageId;
    }

    
    /**
     * Asserts that the email text contains the expected value.
     *
     * @param string $messageId The ID of the email to retrieve.
     * @param string $expected The expected text to find in the email.
     * @throws \Exception If the email text does not contain the expected value.
     */
    public function assertEmailTextContains($messageId, $expected)
    {
        // Retrieve the email by its ID.
        $email = $this->getEmailById($messageId);

        // Extract the plain text content from the email.
        $actual = $email['Text'] ?? '';

        // Assert that the actual email text contains the expected value.
        $this->assertStringContainsString($expected, $actual, "Failed asserting that the email #{$messageId} text '{$actual}' contains '{$expected}'.");
    }

    /**
     * Asserts that the plain text content of an email matches the expected value.
     *
     * This method retrieves an email by its ID, extracts the plain text content,
     * and asserts that it matches the expected value.
     *
     * @param string $messageId The ID of the email to retrieve.
     * @param string $expected The expected plain text content of the email.
     *
     * @return void
     */
    public function assertEmailTextEquals($messageId, $expectedText)
    {
        // Retrieve the email by its ID.
        $email = $this->getEmailById($messageId);

        // Extract the plain text content from the email.
        $actual = $email['Text'] ?? '';

        // Assert that the actual email text matches the expected value.
        $this->assertEquals($expectedText, $actual);
    }

    /**
     * Asserts that the subject of the email with the given message ID matches the expected subject.
     *
     * @param string $messageId The ID of the email message to check.
     * @param string $expectedSubject The expected subject of the email.
     *
     * @throws \PHPUnit\Framework\AssertionFailedError If the actual subject does not match the expected subject.
     */
    public function assertEmailSubjectEquals($messageId, $expectedSubject)
    {
        // Retrieve the email by its ID.
        $email = $this->getEmailById($messageId);

        // Extract the subject from the email.
        $actualSubject = $email['Subject'] ?? '';

        // Assert that the actual email subject matches the expected value.
        $this->assertEquals($expectedSubject, $actualSubject);
    }

    /**
     * Asserts that the subject of an email contains the expected value.
     *
     * @param string $messageId The ID of the email to check.
     * @param string $expectedSubject The expected subject substring.
     *
     * @throws \PHPUnit\Framework\AssertionFailedError If the actual subject does not contain the expected value.
     */
    public function assertEmailSubjectContains($messageId, $expectedSubject)
    {
        // Retrieve the email by its ID.
        $email = $this->getEmailById($messageId);

        // Extract the subject from the email.
        $actualSubject = $email['Subject'] ?? '';

        // Assert that the actual email subject matches the expected value.
        $this->assertStringContainsString($expectedSubject, $actualSubject, "Failed asserting that the email #{$messageId} subject '{$actualSubject}' contains '{$expectedSubject}'.");
    }

    /**
     * Asserts that an email identified by its message ID contains the expected headers.
     *
     * @param string $messageId The ID of the email message to check.
     * @param array $expectedHeaders An associative array of expected headers to check against the email's actual headers.
     *
     * @throws \Exception If the email cannot be retrieved or if the headers do not match.
     */
    public function assertEmailHasHeaders($messageId, $expectedHeaders)
    {
        // Retrieve the email by its ID.
        $email = $this->getEmailById($messageId);

        // Extract the headers from the email.
        $actualHeaders = $email['Headers'] ?? [];

        // Assert that the actual email headers match the expected value.
        $this->assertArrayContainsArray($expectedHeaders, $actualHeaders);
    }

    public function assertEmailHtmlContains($messageId, $expectedHTML)
    {
        // Retrieve the email by its ID.
        $email = $this->getEmailById($messageId);

        // Extract the HTML content from the email.
        $actualHTML = $email['Html'] ?? '';

        // Assert that the actual email HTML contains the expected value.
        $this->assertStringContainsString($expectedHTML, $actualHTML, "Failed asserting that the email #{$messageId} HTML '{$actualHTML}' contains '{$expectedHTML}'.");
    }

    public function assertEmailHtmlEquals($messageId, $expectedHTML)
    {
        // Retrieve the email by its ID.
        $email = $this->getEmailById($messageId);

        // Extract the HTML content from the email.
        $actualHTML = $email['Html'] ?? '';

        // Assert that the actual email HTML matches the expected value.
        $this->assertEquals($expectedHTML, $actualHTML);
    }
   
}
