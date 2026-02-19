---
name: semantic-release
description: "Automates versioning, changelog generation, tagging, and publishing using conventional commits and CI. Activates when setting up CI/CD releases, generating changelogs, committing assets back to Git, creating GitHub releases, or when the user mentions semantic versioning, automated releases, changelog, GitHub Actions, commit conventions, npm releases, or release notes."
license: MIT
metadata:
  author: semantic-release
---

# Semantic Release (Automated Versioning & Publishing)

## When to Apply

Activate this skill when:

- Automating release workflows in CI/CD
- Versioning based on conventional commits (feat/fix/breaking)
- Generating release notes or CHANGELOG automatically
- Publishing tags and releases to GitHub
- Committing version bumps and changelog back to Git
- Integrating releases with npm, GitHub, or other registries
- Working with type‑safe TypeScript workflows and lint‑fixing flows

## Documentation

Use `search-docs` to open the official Semantic Release docs.  
Semantic Release automates the entire release process: version bumping, changelog generation, Git tagging, and publishing releases in a CI workflow.  [oai_citation:1‡GitHub](https://github.com/semantic-release/semantic-release?utm_source=chatgpt.com)

---

## What Is Semantic Release?

Semantic Release is a tool that **automatically determines the next version**, generates **release notes**, updates changelogs, **tags the release**, and can **publish artifacts or GitHub releases** — all from commit messages. It eliminates manual version bumps and encourages standardized commit conventions.  [oai_citation:2‡Semantic Release](https://semantic-release.gitbook.io/?utm_source=chatgpt.com)

---

## Core Principles

Semantic Release works by:

- **Analyzing commits** using conventional commit messages.  
- **Using a plugin pipeline** to determine version, generate notes, update changelog, commit assets, and publish releases.  
- Running after tests in CI to ensure quality and repeatability.  [oai_citation:3‡Semantic Release](https://semantic-release.gitbook.io/?utm_source=chatgpt.com)

---

## Installation

Install Semantic Release and plugins:

```bash
npm install \
  semantic-release \
  @semantic-release/changelog \
  @semantic-release/git \
  @semantic-release/github --save-dev

Also ensure you have a CI environment configured (GitHub Actions, GitLab CI, etc.) and set appropriate tokens (e.g., GH_TOKEN for GitHub).  ￼

⸻

Configuration

.releaserc.js Example

Semantic Release uses a configuration file or release key in package.json:

module.exports = {
  branches: ["main"],
  plugins: [
    "@semantic-release/commit-analyzer",
    "@semantic-release/release-notes-generator",
    "@semantic-release/changelog",
    ["@semantic-release/git", {
      "assets": ["CHANGELOG.md", "package.json"],
      "message": "chore(release): ${nextRelease.version} [skip ci]\n\n${nextRelease.notes}"
    }],
    "@semantic-release/github"
  ]
};

	•	commit-analyzer: analyzes commit messages to determine version bump.
	•	release-notes-generator: creates release notes for the changelog.
	•	changelog: updates a CHANGELOG.md file with new notes.
	•	git: commits the changelog and package version back to Git.
	•	github: publishes release artifacts and a GitHub Release.  ￼

⸻

How Semantic Release Works

In a CI environment, the typical workflow:
	1.	Build & test your code
	2.	Run semantic-release
	3.	Plugins analyze commits since the last release
	4.	Version number is determined
	5.	Release notes are generated
	6.	Changelog is updated (if configured)
	7.	Git tag and (optionally) GitHub release are created
	8.	Assets are published where needed  ￼

⸻

Plugins You Listed

@semantic-release/changelog
	•	Creates or updates a CHANGELOG.md file with release content.
	•	Must run after release-notes-generator and before git.
	•	Configurable via changelogFile and changelogTitle.  ￼

@semantic-release/git
	•	Commits generated assets (e.g., updated CHANGELOG, package.json) back to your repo.
	•	Allows specifying a custom commit message template.
	•	Uses environment Git credentials to push back.  ￼

@semantic-release/github
	•	Creates a GitHub Release with release notes under the correct tag and title.
	•	Includes assets uploaded using CI (e.g., build artifacts).  ￼

⸻

Conventional Commits

Semantic Release depends on commit message conventions like:

feat(scope): add new feature
fix(scope): fix a bug
perf(scope): improve performance
BREAKING CHANGE: major bump

These determine whether the next release is a patch, minor, or major bump.  ￼

⸻

CI Integration (Example: GitHub Actions)

A typical GitHub Actions workflow might include:

name: Release
on:
  push:
    branches:
      - main

jobs:
  release:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: 18
      - run: npm ci
      - run: npm test
      - run: npx semantic-release
        env:
          GH_TOKEN: ${{ secrets.GITHUB_TOKEN }}


⸻

Quality & Tooling Integration

To enforce commit conventions and support releases:
	•	ESLint/TypeScript: Lint and type‑check your code during CI.
	•	Husky: Run pre‑commit hooks to enforce code style and commit message conventions before push.
	•	Prettier: Format code consistently across your project.
	•	typescript‑eslint: Bridge ESLint and TypeScript linting.

These do not directly affect Semantic Release but help maintain a quality codebase that integrates well with automated release pipelines.

⸻

Best Practices
	•	Protect your release branch (e.g., main, master) in your VCS.
	•	Use conventional commit rules with commitlint or Husky hooks to ensure valid messages.
	•	Keep changelog and version bumping automated — no manual versioning.
	•	Run lint, tests, and type checks before releasing.
	•	Ensure release CI jobs have write access to Git tags and repository (tokens).  ￼

⸻

Summary

Concept	Purpose
semantic-release	Fully automates versioning & publishing
Plugins	Changelog, Git commit automation, GitHub Releases
Conventional Commits	Determines next SemVer release
CI/CD	Runs in CI to ensure consistent releases
ESLint/Prettier/Husky	Quality tooling around release pipeline

---