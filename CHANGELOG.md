# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Authentication client covering `/public/auth/v2/token` and `/public/auth/v2/refresh` endpoints with typed DTOs and error handling.
- Domain-specific exceptions for transport, API, and decoding errors.
- PHPUnit tests for the authentication workflow using Guzzle mock handlers.
- Documentation describing how to request tokens via the new client.
- Account client encapsulating `/public/partner/pec/v3/accounts` operations with typed request/response DTOs and pagination models.
- Extensive PHPUnit coverage for account creation, lifecycle actions, search filters, and error handling.
- Additional service client covering `/public/partner/pec/v2/additionalServices` operations with dedicated DTOs and error handling.
- Country client mapping `/public/partner/v2/countries` to expose the ArubaPEC country catalogue with typed DTOs and error handling.
- Domain client encapsulating `/public/partner/pec/v2/domains` endpoints including certification, lifecycle, search, and mailbox listing workflows.
- Shared pagination request options reused by account and domain searches.

### Changed
- Promoted contract and renewal value objects to the shared namespace for reuse across modules.
