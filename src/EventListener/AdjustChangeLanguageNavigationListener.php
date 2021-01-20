<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified Event Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\EventListener;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Input;
use InspiredMinds\ContaoUnifiedEventAliases\UnifiedEventAliases;
use Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent;

/**
 * @Hook("changelanguageNavigation", priority=-100)
 */
class AdjustChangeLanguageNavigationListener
{
    private $unifiedAliases;

    public function __construct(UnifiedEventAliases $unifiedAliases)
    {
        $this->unifiedAliases = $unifiedAliases;
    }

    public function __invoke(ChangelanguageNavigationEvent $event): void
    {
        $alias = Input::get('auto_item', false, true);

        if (empty($alias)) {
            return;
        }

        global $objPage;

        $calendars = CalendarModel::findBy('jumpTo', $objPage->id);

        // Check if any calendars have this page as its target page
        if (null === $calendars) {
            return;
        }

        // Check if unified aliases feature is enabled for this event
        $currentEvent = CalendarEventsModel::findOneByAlias($alias);

        if (null === $currentEvent) {
            return;
        }

        if (!$this->unifiedAliases->isUnifiedAliasEnabled($currentEvent)) {
            return;
        }

        // Get the actual event for the current language
        $actualEvent = $this->unifiedAliases->getEventForCurrentLanguage($currentEvent);

        if (null === $actualEvent) {
            return;
        }

        // Check if this event is actually allowed here
        if (!\in_array((int) $actualEvent->pid, array_map('intval', $calendars->fetchEach('id')), true)) {
            return;
        }

        // Get the main event for the current event
        $mainEvent = $this->unifiedAliases->getMainEvent($currentEvent);

        if (null === $mainEvent) {
            if (!$this->unifiedAliases->isMainEvent($currentEvent)) {
                return;
            }

            $mainEvent = $currentEvent;
        }

        // Override the "items" URL attribute with the alias of the main event
        $event->getUrlParameterBag()->setUrlAttribute('events', $mainEvent->alias);
    }
}
