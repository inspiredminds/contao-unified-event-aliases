<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified Event Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\Controller\FrontendModule;

use Contao\CalendarEventsModel;
use Contao\CalendarModel;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Events;
use Contao\Input;
use Contao\ModuleEventReader;
use Contao\ModuleModel;
use InspiredMinds\ContaoUnifiedEventAliases\UnifiedEventAliases;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(EventReaderModuleController::TYPE, category="events", template="mod_eventreader")
 */
class EventReaderModuleController extends ModuleEventReader
{
    public const TYPE = 'eventreader_unified_aliases';

    private $unifiedAliases;

    public function __construct(UnifiedEventAliases $unifiedAliases)
    {
        $this->unifiedAliases = $unifiedAliases;
    }

    public function __invoke(ModuleModel $model, string $section): Response
    {
        parent::__construct($model, $section);

        return new Response($this->generate());
    }

    protected function compile(): void
    {
        $this->overrideItems();

        parent::compile();
    }

    private function overrideItems(): void
    {
        $event = CalendarModel::findOneByAlias(Input::get('items', false, true));

        // Check if this is a valid event alias
        if (null === $event) {
            return;
        }

        // Check if unified aliases feature is enabled for this event
        if (!$this->unifiedAliases->isUnifiedAliasEnabled($event)) {
            return;
        }

        // Get the actual event for the current language
        $actualEvent = $this->unifiedAliases->getEventForCurrentLanguage($event);

        if (null === $actualEvent) {
            return;
        }

        // Check if this event is actually allowed here
        if (null === CalendarEventsModel::findPublishedByParentAndIdOrAlias($actualEvent->alias, $this->cal_calendar)) {
            return;
        }

        // Redirect in case the detail URL was accessed with the regular alias of the event
        if ($this->unifiedAliases->isCurrentLanguage($event) && !$this->unifiedAliases->isMainEvent($event)) {
            $this->redirectToMainEventUrl($event);
        }

        // Override the "items" variable
        Input::setGet('items', $actualEvent->alias);
    }

    private function redirectToMainEventUrl(CalendarEventsModel $event): void
    {
        $mainEvent = $this->unifiedAliases->getMainEvent($event);

        if (null === $mainEvent) {
            return;
        }

        $event->preventSaving();

        $event->id = 'clone-'.$event->id;
        $event->alias = $mainEvent->alias;

        throw new RedirectResponseException(Events::generateEventUrl($event, true), Response::HTTP_MOVED_PERMANENTLY);
    }
}
