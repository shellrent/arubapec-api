# Agent Guidelines

## Repository Scope
- This file applies to the entire repository.
- Keep this document up to date as the project evolves so future tasks have clear context.

## Coding Standards
- All PHP files must declare `strict_types=1` and use PSR-12 formatting.
- Prefer immutable value objects for DTOs; expose data through typed getters, avoid public properties.
- Use camelCase naming for methods and properties while aligning class names with the OpenAPI schema when it improves clarity.
- Always type-hint parameters and return types. Use nullable types instead of untyped mixed values.
- Handle API date and time fields with `CarbonImmutable` from the Carbon library.

## HTTP Client
- Use `guzzlehttp/guzzle` as the HTTP layer. Disable automatic HTTP error exceptions and handle status codes explicitly.
- Wrap transport and decoding issues in domain-specific exceptions located under `Shellrent\\Arubapec\\Exception`.

## Testing
- Add unit tests for new features using PHPUnit. Prefer MockHandler from Guzzle to avoid real HTTP calls.

## Documentation
- Update `README.md` and `CHANGELOG.md` whenever the public surface or behaviour changes.
- Provide practical code samples in the README for newly added features.

## Tooling
- Keep `composer.json` dependencies current and run `composer update` when requirements change so that `composer.lock` stays in sync.

## Future Notes
- Organise API clients by specification (e.g., `Auth`, `Account`) and expose them via `ArubapecClient`.
- Share reusable DTOs across modules via a `Shared` namespace.
