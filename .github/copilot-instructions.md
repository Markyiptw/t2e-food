# Copilot Instructions

## Coding Style

Favour functional programming, e.g.

- declarative over imperative code style
- immutable functions / methods returning new values over mutating existing values
- separation of pure logic from side effects
- apply functional programming techniques, such as piping, folding / reducing, mapping, compose etc.

## Workflows

### Testing

Write tests where they add value — the goal is confidence when refactoring, not coverage metrics. Test behaviour and outcomes, not implementation details or internal structure.

### Commits

Group changes into **logical, self-contained commits** that are easy to review. A commit should represent one coherent idea — not necessarily one file, but not a dump of everything either.

- Write clear, imperative commit messages (e.g. `Add user authentication middleware`)
- Never commit commented-out code or debug statements
