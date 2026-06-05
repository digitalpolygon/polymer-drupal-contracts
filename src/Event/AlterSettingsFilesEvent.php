<?php

namespace DigitalPolygon\Polymer\Drupal\Contracts\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Alter event: listeners may reorder, remove, or replace the resolved set of
 * settings-file snippets collected by CollectSettingsFilesEvent before it is
 * written to the site's settings.
 */
final class AlterSettingsFilesEvent extends Event
{
    /**
     * @var array<string, string>
     */
    protected array $settingsFiles = [];

    protected string $site;

    /**
     * @param array<string, string> $settingsFiles
     */
    public function setSettingsFiles(array $settingsFiles): void
    {
        $this->settingsFiles = $settingsFiles;
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
