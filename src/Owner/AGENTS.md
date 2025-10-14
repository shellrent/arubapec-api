# Owner Module Guidelines

## Scope
- Applies to files within `src/Owner` and its subdirectories.

## Structure
- Expose owner operations through `OwnerClient` with one public method per OpenAPI operation defined in `openapi/owner-api.json`.
- Keep request DTOs inside the `Dto` namespace with explicit `toArray()` serializers.
- Map API responses with immutable DTOs that rely on `fromArray()` named constructors.

## Data Handling
- Reuse shared DTOs from `src/Shared` when the schema matches (e.g. `OwnerModel`, `OwnerId`).
- Validate mandatory fields in responses and throw `Shellrent\\Arubapec\\Exception\\UnexpectedResponseException` when payloads are inconsistent.

## Testing
- Cover success, validation, and error scenarios for each public method using PHPUnit with Guzzle's `MockHandler`.
