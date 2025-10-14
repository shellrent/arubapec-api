# Additional Service Module Guidelines

## Scope
- Applies to files within `src/AdditionalService` and its subdirectories.

## Structure
- Implement an `AdditionalServiceClient` matching the operations defined in `openapi/additional-services-api.json`.
- Keep all request and response DTOs inside the `Dto` namespace with immutable constructors.
- Request DTOs must expose `toArray()` for serialisation; response DTOs must provide `fromArray()` named constructors.

## Data Handling
- Parse API date/time fields with `CarbonImmutable` and serialise outbound payloads using ISO 8601 format.
- Reuse shared value objects from `src/Shared/Dto` when modelling common data structures (e.g. renewal or contract data).
- Validate mandatory payload fields and throw `Shellrent\\Arubapec\\Exception\\UnexpectedResponseException` when responses are inconsistent with the specification.

## Testing
- Cover happy-path and failure scenarios for each public method on `AdditionalServiceClient` with PHPUnit tests using Guzzle's `MockHandler`.
