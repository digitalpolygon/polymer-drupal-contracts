<?php

namespace DigitalPolygon\Polymer\Drupal\Contracts\Event;

/**
 * Catalog of Drupal settings-file events.
 *
 * Plugins couple to these named events (and their payload classes), never to
 * one another's implementations. Names are stable contracts: bump this package's
 * major version only when an event name or payload breaks.
 */
final class DrupalSettingsEvents
{
    /**
     * Listeners contribute keyed settings-file snippets.
     *
     * @Event("DigitalPolygon\Polymer\Drupal\Contracts\Event\CollectSettingsFilesEvent")
     */
    public const COLLECT_SETTINGS_FILES = 'polymer_drupal.settings.collect_files';

    /**
     * Listeners may reorder/remove/replace the resolved set before it is written.
     *
     * @Event("DigitalPolygon\Polymer\Drupal\Contracts\Event\AlterSettingsFilesEvent")
     */
    public const ALTER_SETTINGS_FILES = 'polymer_drupal.settings.alter_files';
}
