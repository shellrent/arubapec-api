# Account Module Guidelines

## Scope
- Applies to files within `src/Account` and its subdirectories.

## Structure
- Implement an `AccountClient` exposing the operations defined in `openapi/account-api.json`.
- Place all request and response DTOs inside the `Dto` namespace.
- Request DTOs must expose `toArray()` while response DTOs must offer `fromArray()` named constructors.

## Data Handling
- Parse all temporal fields using `CarbonImmutable`. Convert them back to ISO 8601 strings when serialising request payloads.
- Validate required payload fields and throw `Shellrent\\Arubapec\\Exception\\UnexpectedResponseException` for malformed responses.
- Treat collections as immutable arrays; expose typed getters returning arrays of DTO instances.

## Testing
- Cover happy-path and error scenarios for each public method added to `AccountClient` using PHPUnit with Guzzle's `MockHandler`.
