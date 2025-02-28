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

## API Methods

```getLastEmailId()```
```getEmailById($id)```
```getEmailBySubject($subject)```

## Assertions
- **assertEmailTextContains($messageId, $expected)**
- **assertEmailSubjectEquals($messageId, $expectedSubject)**




