# Repository Guidelines

## Project Structure & Module Organization
- `MultisiteTaxonomyWidget.php` is the bootstrap that wires the plugin into WordPress multisite.
- Reusable services live in `includes/` under the `lloc\Mtw` namespace (e.g., `Mtw.php`, `Posts.php`, `RelatedSites.php`).
- Unit doubles and fixtures stay in `tests/`, which mirrors the production classes (`TestPosts.php`, `TestPlugin.php`); bootstrap helpers live alongside in `tests/bootstrap.php`.
- Release and tooling scripts reside in `bin/` (`git-release.sh`, `githooks/`), while translations ship from `languages/`.
- Built artifacts land in `multisite-taxonomy-widget/` and the generated zip; keep them out of feature branches unless you are cutting a release.

## Build, Test, and Development Commands
- `composer install` — install PHP dependencies, including WordPress coding standards and PHPUnit stubs.
- `composer test` — run the PHPUnit suite defined by `phpunit.xml` with Brain Monkey for WordPress mocks.
- `composer phpstan` — execute static analysis with the bundled WordPress extensions; resolve introduced errors before opening a PR.
- `composer coverage` — produce HTML coverage under `tests/coverage/`; trim large reports from commits.
- `composer build` — invoke `bin/git-release.sh` to assemble the distributable plugin archive.
- `composer githooks` — refresh the optional pre-commit hook that runs the local checks.

## Coding Style & Naming Conventions
- Target PHP 7.4+ and follow WordPress Coding Standards (`vendor/bin/phpcs --standard=WordPress`); prefer four-space indentation and Yoda conditions where practical.
- Namespace code under `lloc\Mtw`; use PascalCase for classes (`FormatElements`) and snake_case for filter or action callbacks that integrate with WordPress.
- Keep widget-facing strings translatable, storing `.po` files in `languages/`; sync any new strings before release.

## Testing Guidelines
- Write PHPUnit tests alongside the corresponding class in `tests/`; name files `Test*.php` to match the production counterpart.
- Mock WordPress functions via Brain Monkey (autoloaded from `tests/bootstrap.php`) instead of stubbing globals manually.
- New features should include assertions for both network-aware behaviour and taxonomy edge cases; aim to keep overall coverage steady when running `composer coverage`.
- Record any manual multisite validation steps in the PR description when automated coverage is not feasible.

## Commit & Pull Request Guidelines
- Use short, imperative commit subjects (`Add widget cache flush`) similar to the existing history; group related edits into focused commits.
- Reference GitHub issues with `Fixes #123` when applicable, and note release-impacting changes in the body.
- Pull requests should summarise the change set, list validation (`composer test`, `composer phpstan`), and include screenshots for UI-facing tweaks.
- Ensure the zip artifacts are regenerated only via `composer build` during release preparation; omit them from routine PRs.
