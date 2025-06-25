<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\EventListener;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\Template;
use InspiredMinds\ContaoUnifiedEventAliases\UnifiedEventAliases;
use Symfony\Component\Routing\Exception\ExceptionInterface;

#[AsHook('parseTemplate')]
class AdjustEventLinkListener
{
    public function __construct(
        private readonly UnifiedEventAliases $unifiedEventAliases,
        private readonly ContentUrlGenerator $contentUrlGenerator,
    ) {
    }

    public function __invoke(Template $template): void
    {
        if (!str_starts_with($template->getName(), 'event_')) {
            return;
        }

        $event = CalendarEventsModel::findById($template->id);

        if (!$this->unifiedEventAliases->isUnifiedAliasEnabled($event) || $this->unifiedEventAliases->isMainEvent($event)) {
            return;
        }

        $mainEvent = $this->unifiedEventAliases->getMainEvent($event);

        if (!$mainEvent) {
            return;
        }

        $event->preventSaving();

        $event->id = 'clone-'.$template->id;
        $event->alias = $mainEvent->alias;

        try {
            $template->href = $template->imageHref = $this->contentUrlGenerator->generate($event);
        } catch (ExceptionInterface) {
            // noop
        }
    }
}
