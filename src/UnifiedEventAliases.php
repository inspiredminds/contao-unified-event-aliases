<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified Event Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedEventAliases;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\PageModel;
use Symfony\Component\HttpFoundation\RequestStack;

class UnifiedEventAliases
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Check whether the main calendar for the given event has unified aliases enabled.
     */
    public function isUnifiedAliasEnabled(CalendarEventsModel $event): bool
    {
        $calendar = CalendarModel::findById((int) $event->pid);

        if ((int) $calendar->master > 0) {
            $calendar = CalendarModel::findById((int) $calendar->master);
        }

        if (null === $calendar) {
            return false;
        }

        return (bool) $calendar->use_unified_aliases;
    }

    /**
     * Checks whethe the given event is for the current language.
     */
    public function isCurrentLanguage(CalendarEventsModel $event): bool
    {
        $calendar = CalendarModel::findById((int) $event->pid);

        if (null === $calendar) {
            throw new \RuntimeException('Could not find calendar for event ID "'.$event->id.'".');
        }

        $target = PageModel::findWithDetails($calendar->jumpTo);

        if (null === $target) {
            throw new \RuntimeException('Could not find target page for calendar ID "'.$calendar->id.'".');
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('Could not get current request.');
        }

        return $target->rootLanguage === $request->getLocale();
    }

    /**
     * Checks whether the given event is the main event.
     */
    public function isMainEvent(CalendarEventsModel $event): bool
    {
        $calendar = CalendarModel::findById($event->pid);

        if (null === $calendar) {
            throw new \RuntimeException('Could not find calendar for event ID "'.$event->id.'".');
        }

        return 0 === (int) $calendar->master;
    }

    /**
     * Returns the main event for the given event, if applicable.
     */
    public function getMainEvent(CalendarEventsModel $event): ?CalendarEventsModel
    {
        if (0 === (int) $event->languageMain) {
            return null;
        }

        return CalendarEventsModel::findById((int) $event->languageMain);
    }

    /**
     * Returns the associated event for the given event and language.
     */
    public function getEventForLanguage(CalendarEventsModel $event, string $language): ?CalendarEventsModel
    {
        $searchId = (int) ($event->languageMain ?: $event->id);
        $t = CalendarEventsModel::getTable();
        $articles = CalendarEventsModel::findBy(
            ["($t.id = ? OR $t.languageMain = ?)"],
            [$searchId, $searchId]
        );

        if (null === $articles) {
            return null;
        }

        foreach ($articles as $article) {
            $calendar = CalendarModel::findById((int) $article->pid);

            if (null === $calendar) {
                continue;
            }

            $target = PageModel::findWithDetails($calendar->jumpTo);

            if (null === $target) {
                return null;
            }

            if ($target->rootLanguage === $language) {
                return $article;
            }
        }

        return null;
    }

    /**
     * Returns the associated event for the given event and the current language.
     */
    public function getEventForCurrentLanguage(CalendarEventsModel $event): ?CalendarEventsModel
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('Could not get current request.');
        }

        return $this->getEventForLanguage($event, $request->getLocale());
    }
}
