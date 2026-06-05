<?php

namespace DigitalPolygon\PolymerDrupalContractsTest\phpunit\unit;

use DigitalPolygon\Polymer\Drupal\Contracts\Event\AlterSettingsFilesEvent;
use DigitalPolygon\Polymer\Drupal\Contracts\Event\CollectSettingsFilesEvent;
use DigitalPolygon\Polymer\Drupal\Contracts\Event\DrupalSettingsEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Exercises the contracts through a real event dispatcher — the shape in
 * which every plugin actually consumes them (DESIGN.md §6).
 */
class SettingsFilesDispatchTest extends TestCase
{
    public function testListenersContributeInPriorityOrder(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(
            DrupalSettingsEvents::COLLECT_SETTINGS_FILES,
            static function (CollectSettingsFilesEvent $event): void {
                $event->addSettingsFile('low', '// low priority');
            },
            -10
        );
        $dispatcher->addListener(
            DrupalSettingsEvents::COLLECT_SETTINGS_FILES,
            static function (CollectSettingsFilesEvent $event): void {
                $event->addSettingsFile('high', '// high priority');
            },
            10
        );

        $event = new CollectSettingsFilesEvent();
        $event->setSite('default');
        $dispatcher->dispatch($event, DrupalSettingsEvents::COLLECT_SETTINGS_FILES);

        $this->assertSame(['high', 'low'], array_keys($event->getSettingsFiles()));
    }

    public function testLaterListenerCanOverrideEarlierContributionById(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(
            DrupalSettingsEvents::COLLECT_SETTINGS_FILES,
            static function (CollectSettingsFilesEvent $event): void {
                $event->addSettingsFile('shared-id', 'original');
            },
            10
        );
        $dispatcher->addListener(
            DrupalSettingsEvents::COLLECT_SETTINGS_FILES,
            static function (CollectSettingsFilesEvent $event): void {
                $event->addSettingsFile('shared-id', 'replacement');
            },
            -10
        );

        $event = new CollectSettingsFilesEvent();
        $dispatcher->dispatch($event, DrupalSettingsEvents::COLLECT_SETTINGS_FILES);

        $this->assertSame(['shared-id' => 'replacement'], $event->getSettingsFiles());
    }

    public function testStopPropagationHaltsRemainingListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(
            DrupalSettingsEvents::COLLECT_SETTINGS_FILES,
            static function (CollectSettingsFilesEvent $event): void {
                $event->addSettingsFile('first', 'ran');
                $event->stopPropagation();
            },
            10
        );
        $dispatcher->addListener(
            DrupalSettingsEvents::COLLECT_SETTINGS_FILES,
            static function (CollectSettingsFilesEvent $event): void {
                $event->addSettingsFile('second', 'must not run');
            },
            -10
        );

        $event = new CollectSettingsFilesEvent();
        $dispatcher->dispatch($event, DrupalSettingsEvents::COLLECT_SETTINGS_FILES);

        $this->assertSame(['first' => 'ran'], $event->getSettingsFiles());
    }

    public function testCollectThenAlterPipeline(): void
    {
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(
            DrupalSettingsEvents::COLLECT_SETTINGS_FILES,
            static function (CollectSettingsFilesEvent $event): void {
                $event->addSettingsFile('keep', '// keep');
                $event->addSettingsFile('drop', '// drop');
            }
        );
        $dispatcher->addListener(
            DrupalSettingsEvents::ALTER_SETTINGS_FILES,
            static function (AlterSettingsFilesEvent $event): void {
                $files = $event->getSettingsFiles();
                unset($files['drop']);
                $event->setSettingsFiles($files);
            }
        );

        $collect = new CollectSettingsFilesEvent();
        $collect->setSite('default');
        $dispatcher->dispatch($collect, DrupalSettingsEvents::COLLECT_SETTINGS_FILES);

        $alter = new AlterSettingsFilesEvent();
        $alter->setSite($collect->getSite());
        $alter->setSettingsFiles($collect->getSettingsFiles());
        $dispatcher->dispatch($alter, DrupalSettingsEvents::ALTER_SETTINGS_FILES);

        $this->assertSame(['keep' => '// keep'], $alter->getSettingsFiles());
        $this->assertSame('default', $alter->getSite());
    }

    public function testEventsDispatchIndependently(): void
    {
        $dispatcher = new EventDispatcher();
        $collectCalls = 0;
        $alterCalls = 0;
        $dispatcher->addListener(
            DrupalSettingsEvents::COLLECT_SETTINGS_FILES,
            static function () use (&$collectCalls): void {
                $collectCalls++;
            }
        );
        $dispatcher->addListener(
            DrupalSettingsEvents::ALTER_SETTINGS_FILES,
            static function () use (&$alterCalls): void {
                $alterCalls++;
            }
        );

        $dispatcher->dispatch(new CollectSettingsFilesEvent(), DrupalSettingsEvents::COLLECT_SETTINGS_FILES);

        $this->assertSame(1, $collectCalls);
        $this->assertSame(0, $alterCalls);
    }
}
