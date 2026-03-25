---
apply: always
---

# Global Rules

ALWAYS: English for all code, class names, method names, variables, and interfaces. German for commit messages. Unix paths only. No inline comments — write self-explanatory code.

## Language
- All code, naming, and documentation in English.
- Commit messages in German.
- Follow Conventional Commits format (feat:, fix:, chore: ...) only when mandated by the repository.

## Filesystem
- Always use Unix-style path separators (`/`). Never use Windows paths or Windows-specific functions.

## Code Quality
- Enforce PSR-12 via Rector and PHP-CS-Fixer using rules from https://github.com/perspeqtive/dev-dependencies when installed. Otherwise follow PSR-12 and match the existing project style.
- New and changed code must be covered by unit tests. Container/configuration wiring should have at least smoke tests.

## Comments
- Never add inline comments. Write self-explanatory code through clear naming instead.
- PHPDoc only when types or intent cannot be expressed through the type system alone.