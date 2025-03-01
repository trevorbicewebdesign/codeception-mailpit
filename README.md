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

## Mailpit Module API Methods

- ```getLastEmailId()``` - Get the mailpit message id of the most recent email.
- ```getEmailById($id)``` - Retrieves an email by its ID.
- ```getEmailBySubject($subject)``` - Retrieves the ID of the most recent email that matches the given subject.

## Assertions
- **assertEmailTextContains($messageId, $expectedText)**
- **assertEmailTextEquals($messageId, $extectedText)**
- **assertEmailSubjectEquals($messageId, $expectedSubject)**
- **assertEmailSubjectContains($messageId, $expectedSubject)**
- **assertEmailHasHeaders($messageId,$expectedHeaders)**




