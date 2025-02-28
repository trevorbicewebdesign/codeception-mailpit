# Codeception Mailpit Module

Provides test helpers for Codeception to facilitate email testing with [Mailpit](https://mailpit.axllent.org/). This module allows you to search for and retrieve email content from Mailpit during your tests, making it easy to verify email functionality, extract activation keys, and more.

## Overview

The Mailpit module integrates with Codeception using Guzzle to interact with Mailpit’s API. It provides methods to:

- **Search Emails:** Retrieve the most recent email(s) based on search criteria.
- **Fetch Full Email Content:** After searching, fetch the complete content of an email by its ID.
- **Extract Information:** Easily extract details such as activation keys for further assertions in your tests.
- **Email Assertions for Codeception:** 

## Installation

Install the module via Composer:

```bash
composer require dev trevorbicewebdesign/codeception-mailpit
```

## Configuration

Add the module to your Codeception suite configuration (for example, in your acceptance.suite.yml):

```yml
modules:
  enabled:
    - Mailpit
  config:
    Mailpit:
      base_uri: 'http://localhost:10006'
```
- **base_uri:**  The base URL for your Mailpit installation.

## Usage

Once installed and configured, you can use the module in your tests. For example:

```php
public function testActivationEmailContent(AcceptanceTester $I)
{
    // Get the Mailpit module instance.
    $mailpit = $I->getModule('Mailpit');
    
    // Fetch the full content of the most recent email.
    $fullContent = $mailpit->getLastEmailFullContent();
    
    // Extract the activation key using a regular expression.
    preg_match('/[?&]key=([^&]+)/', $fullContent, $matches);
    $activationKey = $matches[1] ?? null;
    
    // Assert that an activation key was found.
    $I->assertNotEmpty($activationKey, 'Activation key not found in email content.');
    
    // Optionally, output the key or perform additional assertions.
    $I->comment("Extracted activation key: {$activationKey}");
}
```

## API Methods

```getLastEmailId()```
```getEmailById($id)```
```getEmailBySubject($subject)```

## Assertions
- **assertEmailTextContains($messageId, $expected)**
- **assertEmailSubjectEquals($messageId, $expectedSubject)**




