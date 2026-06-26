# Security Policy

## Reporting a vulnerability

If you discover a security vulnerability in t2e-food, please **do not** open a
public GitHub issue. Instead, report it privately so it can be addressed before
any public disclosure.

Email: **security@t2e.food**

Please include:

- A description of the vulnerability and its potential impact.
- Steps to reproduce (proof of concept, if possible).
- The affected version(s) / commit.

We will acknowledge receipt as soon as possible and work with you on a fix and
coordinated disclosure timeline.

## Scope

This policy covers security vulnerabilities in the t2e-food codebase itself
(e.g. authentication/authorization flaws, injection, unsafe deserialization,
secrets exposure in code).

It does **not** cover:

- Vulnerabilities in third-party dependencies — report these to the upstream
  project. Run `composer audit` and `npm audit` to check your own install.
- The legal/compliance aspects of scraping OpenRice — that is a usage question,
  not a security vulnerability. See the disclaimer in the
  [README](README.md).

## Supported versions

Security fixes are applied only to the latest `main` branch. There are no
separate LTS release lines.
