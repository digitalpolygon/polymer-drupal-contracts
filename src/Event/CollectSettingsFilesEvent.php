<?php

namespace DigitalPolygon\Polymer\Drupal\Contracts\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Collect event: listeners contribute settings-file snippets for a site.
 *
 * Each contribution is a complete PHP snippet keyed by a stable id, so later
 * listeners (and the alter event) can address individual contributions.
 */
final class CollectSettingsFilesEvent extends Event
{
    /**
     * @var array<string, string>
     */
    protected array $settingsFiles = [];

    protected string $site;

    /**
     * Contribute a settings-file snippet.
     *
     * @param string $id
     *   Stable identifier for this contribution.
     * @param string $file
     *   The complete PHP snippet to include in the site's settings.
     */
    public function addSettingsFile(string $id, string $file): void
    {
        $this->settingsFiles[$id] = $file;
    }

    /**
     * @return array<string, string>
     */
    public function getSettingsFiles(): array
    {
        return $this->settingsFiles;
    }

    public function setSite(string $site): void
    {
        $this->site = $site;
    }

    public function getSite(): string
    {
        return $this->site;
    }
}
