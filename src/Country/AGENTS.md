# Country Module Guidelines

## Scope
- Applies to files within `src/Country` and its subdirectories.

## Structure
- Implement a `CountryClient` that maps the operations from `openapi/country-api.json`.
- Place DTOs within the `Dto` namespace. Keep them immutable, using constructors or named constructors.
- Response DTOs must offer `fromArray()` factory methods and validate mandatory fields, throwing `Shellrent\\Arubapec\\Exception\\UnexpectedResponseException` when data is inconsistent.

## Data Handling
- Represent country items with dedicated DTOs that expose typed getters.
- Reuse shared error DTOs from `src/Shared/Dto` when dealing with error payloads.

## Testing
- Cover the `CountryClient` with PHPUnit tests for both successful and error scenarios using Guzzle's `MockHandler`.
