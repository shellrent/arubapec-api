# Shared DTO Guidelines

## Scope
- Applies to reusable DTOs under `src/Shared`.

## Conventions
- DTOs must be immutable and instantiated through explicit constructors or named constructors like `fromArray`.
- Provide typed getters for every property; avoid exposing raw arrays.
- When converting from array data, validate presence of mandatory fields and throw `UnexpectedResponseException` for inconsistencies.
- Shared value objects such as `ContractData` and `RenewalData` must remain serialisable via `toArray()` and compatible across modules.
