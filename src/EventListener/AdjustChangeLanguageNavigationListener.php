<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\EventListener;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Input;
use InspiredMinds\ContaoUnifiedEventAliases\UnifiedEventAliases;
use Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent;

#[AsHook('changelanguageNavigation', priority: -100)]
class AdjustChangeLanguageNavigationListener
{
    public function __construct(private readonly UnifiedEventAliases $unifiedAliases)
    {
    }

    public function __invoke(ChangelanguageNavigationEvent $event): void
    {
        if (!$alias = Input::get('auto_item', false, true)) {
            return;
        }

        global $objPage;

        // Check if any calendars have this page as its target page
        if (!$calendars = CalendarModel::findBy('jumpTo', $objPage->id)) {
            return;
        }

        // Check if unified aliases feature is enabled for this event
        if (!$currentEvent = CalendarEventsModel::findOneByAlias($alias)) {
            return;
        }

        if (!$this->unifiedAliases->isUnifiedAliasEnabled($currentEvent)) {
            return;
        }

        // Get the actual event for the current language
        if (!$actualEvent = $this->unifiedAliases->getEventForCurrentLanguage($currentEvent)) {
            return;
        }

        // Check if this event is actually allowed here
        if (!\in_array((int) $actualEvent->pid, array_map(intval(...), $calendars->fetchEach('id')), true)) {
            return;
        }

        // Get the main event for the current event
        $mainEvent = $this->unifiedAliases->getMainEvent($currentEvent);

        if (!$mainEvent) {
            if (!$this->unifiedAliases->isMainEvent($currentEvent)) {
                return;
            }

            $mainEvent = $currentEvent;
        }

        // Override the "items" URL attribute with the alias of the main event
        $event->getUrlParameterBag()->setUrlAttribute('events', $mainEvent->alias);
    }
}
