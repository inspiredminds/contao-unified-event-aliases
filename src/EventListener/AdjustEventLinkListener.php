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
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Events;
use Contao\Template;
use InspiredMinds\ContaoUnifiedEventAliases\UnifiedEventAliases;

/**
 * @Hook("parseTemplate")
 */
class AdjustEventLinkListener
{
    private $unifiedEventAliases;

    public function __construct(UnifiedEventAliases $unifiedEventAliases)
    {
        $this->unifiedEventAliases = $unifiedEventAliases;
    }

    public function __invoke(Template $template): void
    {
        if (0 !== strpos($template->getName(), 'event_')) {
            return;
        }

        $event = CalendarEventsModel::findById($template->id);

        if (!$this->unifiedEventAliases->isUnifiedAliasEnabled($event) || $this->unifiedEventAliases->isMainEvent($event)) {
            return;
        }

        $mainEvent = $this->unifiedEventAliases->getMainEvent($event);

        if (null === $mainEvent) {
            return;
        }

        $event->preventSaving();

        $event->id = 'clone-'.$template->id;
        $event->alias = $mainEvent->alias;

        $template->href = Events::generateEventUrl($event);
    }
}
