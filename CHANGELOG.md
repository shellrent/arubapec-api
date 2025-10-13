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
