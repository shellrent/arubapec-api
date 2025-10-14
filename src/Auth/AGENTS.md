# Auth Module Guidelines

## Scope
- Applies to files within `src/Auth` and its subdirectories.

## Structure
- Expose authentication operations via `AuthClient`.
- Keep request/response DTOs inside the `Dto` namespace. DTOs must provide `toArray()` (for requests) or `fromArray()` named constructors (for responses) to keep serialization consistent.

## Error Handling
- Authentication methods should throw `Shellrent\Arubapec\Exception\ApiException` for API-level errors (HTTP >= 400) and `Shellrent\Arubapec\Exception\UnexpectedResponseException` when payloads cannot be parsed.

## Testing
- Cover success and failure scenarios for each operation with PHPUnit tests using Guzzle's `MockHandler`.
