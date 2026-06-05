<?php

namespace DigitalPolygon\PolymerDrupalContractsTest\phpunit\unit;

use DigitalPolygon\Polymer\Drupal\Contracts\Event\AlterSettingsFilesEvent;
use DigitalPolygon\Polymer\Drupal\Contracts\Event\CollectSettingsFilesEvent;
use DigitalPolygon\Polymer\Drupal\Contracts\Event\DrupalSettingsEvents;
use PHPUnit\Framework\TestCase;

/**
 * Asserts the contracts package's stability promise: event names and payload
 * semantics are what plugins couple to, so a change here is a BC break.
 */
class SettingsFilesEventsTest extends TestCase
{
    public function testEventNamesAreStableContracts(): void
    {
        $this->assertSame('polymer_drupal.settings.collect_files', DrupalSettingsEvents::COLLECT_SETTINGS_FILES);
        $this->assertSame('polymer_drupal.settings.alter_files', DrupalSettingsEvents::ALTER_SETTINGS_FILES);
    }

    public function testCollectEventKeysContributionsById(): void
    {
        $event = new CollectSettingsFilesEvent();
        $this->assertSame([], $event->getSettingsFiles());

        $event->addSettingsFile('alpha', '<?php // alpha');
        $event->addSettingsFile('beta', '<?php // beta');
        $this->assertSame(
            ['alpha' => '<?php // alpha', 'beta' => '<?php // beta'],
            $event->getSettingsFiles()
        );
    }

    public function testCollectEventLaterContributionWithSameIdWins(): void
    {
        $event = new CollectSettingsFilesEvent();
        $event->addSettingsFile('alpha', 'first');
        $event->addSettingsFile('alpha', 'second');
        $this->assertSame(['alpha' => 'second'], $event->getSettingsFiles());
    }

    public function testCollectEventCarriesSite(): void
    {
        $event = new CollectSettingsFilesEvent();
        $event->setSite('default');
        $this->assertSame('default', $event->getSite());
    }

    public function testAlterEventReplacesResolvedSetWholesale(): void
    {
        $event = new AlterSettingsFilesEvent();
        $event->setSettingsFiles(['alpha' => 'a', 'beta' => 'b']);
        $event->setSettingsFiles(['gamma' => 'c']);
        $this->assertSame(['gamma' => 'c'], $event->getSettingsFiles());
    }

    public function testAlterEventCarriesSite(): void
    {
        $event = new AlterSettingsFilesEvent();
        $event->setSite('multisite_a');
        $this->assertSame('multisite_a', $event->getSite());
    }
}
