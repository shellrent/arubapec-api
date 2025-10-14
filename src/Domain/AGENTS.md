# Domain Module Guidelines

## Scope
- Applies to files within `src/Domain` and its subdirectories.

## Structure
- Expose domain operations through `DomainClient` with one public method per OpenAPI operation.
- Keep request DTOs in the `Dto` namespace with `toArray()` serialization helpers.
- Response DTOs must provide `fromArray()` named constructors and surface typed accessors for nested models.

## Data Handling
- Parse date and time strings with `CarbonImmutable` and propagate parsing issues using `UnexpectedResponseException`.
- Reuse shared DTOs from other modules when the OpenAPI schemas match instead of duplicating logic.

## Testing
- Cover success paths, request serialization, and API error handling using PHPUnit with Guzzle's `MockHandler`.
