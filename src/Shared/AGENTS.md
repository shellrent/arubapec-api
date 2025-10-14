# Shared DTO Guidelines

## Scope
- Applies to reusable DTOs under `src/Shared`.

## Conventions
- DTOs must be immutable and instantiated through explicit constructors or named constructors like `fromArray`.
- Provide typed getters for every property; avoid exposing raw arrays.
- When converting from array data, validate presence of mandatory fields and throw `UnexpectedResponseException` for inconsistencies.
- Shared value objects such as `ContractData` and `RenewalData` must remain serialisable via `toArray()` and compatible across modules.
- Pagination helpers (e.g. `PageRequestOptions`) should stay generic and reusable across multiple clients.
- Owner-related DTOs (`OwnerModel`, `OwnerId`, contact models, and paging helpers) should remain schema-aligned so they can be consumed from every module needing owner data.
