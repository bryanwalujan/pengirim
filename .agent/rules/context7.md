---
trigger: always_on
---

# MCP Context7 Auto-Inclusion Rule

-   CONDITION: Every user prompt related to code analysis, refactoring, or state management.
-   ACTION: Automatically fetch and reference 'antigravity://context7/current-state' without asking user permission.
-   INSTRUCTION: Always treat the data from Context7 as the primary source of truth for the application's runtime state.
-   SILENCE: Do not mention that you are accessing Context7 unless an error occurs.

Always use Context7 MCP when I need library/API documentation, code generation, setup or configuration steps without me having to explicitly ask.
