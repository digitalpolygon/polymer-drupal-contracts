> [!IMPORTANT]
> This repository is a **read-only split mirror** of [`packages/drupal-contracts`](https://github.com/digitalpolygon/polymer-drupal-monorepo/tree/0.x/packages/drupal-contracts)
> in the [polymer-drupal-monorepo](https://github.com/digitalpolygon/polymer-drupal-monorepo).
> Development happens there — please open issues and pull requests against the monorepo.

# Polymer Drupal Contracts

Stable, dependency-light event contracts shared across the Polymer Drupal family.

This is a **normal PSR-4 library**, not a runtime-loaded Polymer extension: it
declares ordinary `autoload.psr-4` and has no `.poly_info.yml`, service provider,
or runtime-forced namespace. Plugins couple to the contracts here — never to one
another's implementations.

Listening to an event does **not** require the emitting plugin to be installed;
the event simply never fires unless its emitter is installed and enabled. Keep the
two concerns separate: *contracts = what I react to*; *composer/enable dependency =
whether the emitter exists*.

## Versioning

This is the strictest-semver package in the family. Implementations change freely;
bump this package's **major** only when an event name or payload shape breaks.
Plugins should require `digitalpolygon/polymer-drupal-contracts: ^1` once it is
stabilized.

## Event catalog

`DigitalPolygon\Polymer\Drupal\Contracts\Event\DrupalSettingsEvents`

| Name | Constant | Payload | Shape |
| --- | --- | --- | --- |
| `polymer_drupal.settings.collect_files` | `COLLECT_SETTINGS_FILES` | `CollectSettingsFilesEvent` | collect |
| `polymer_drupal.settings.alter_files` | `ALTER_SETTINGS_FILES` | `AlterSettingsFilesEvent` | alter |

- **collect** — listeners contribute keyed PHP settings-file snippets for a site
  via `addSettingsFile($id, $snippet)`.
- **alter** — listeners may reorder/remove/replace the resolved set via
  `setSettingsFiles($snippets)` before it is written to the site's settings.

Emitted by `polymer-drupal` during site setup; `polymer-pantheon-drupal` listens
to contribute its Pantheon integration settings.
