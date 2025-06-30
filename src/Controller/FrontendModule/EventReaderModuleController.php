<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\Controller\FrontendModule;

use Contao\CalendarEventsModel;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\CoreBundle\Routing\ResponseContext\HtmlHeadBag\HtmlHeadBag;
use Contao\CoreBundle\Routing\ResponseContext\ResponseContextAccessor;
use Contao\Input;
use Contao\ModuleEventReader;
use Contao\ModuleModel;
use InspiredMinds\ContaoUnifiedEventAliases\UnifiedEventAliases;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsFrontendModule(self::TYPE, 'events', 'mod_eventreader')]
class EventReaderModuleController extends ModuleEventReader
{
    public const TYPE = 'eventreader_unified_aliases';

    public function __construct(
        private readonly UnifiedEventAliases $unifiedAliases,
        private readonly ContentUrlGenerator $contentUrlGenerator,
        private readonly ResponseContextAccessor $responseContextAccessor,
    ) {
    }

    public function __invoke(ModuleModel $model, string $section): Response
    {
        parent::__construct($model, $section);

        return new Response($this->generate());
    }

    protected function compile(): void
    {
        $override = $this->override();

        parent::compile();

        // Override the canonical URI as well, if applicable
        if ($override) {
            $responseContext = $this->responseContextAccessor->getResponseContext();

            if ($responseContext?->has(HtmlHeadBag::class) && !$override->canonicalLink && !$this->cal_keepCanonical) {
                $responseContext->get(HtmlHeadBag::class)
                    ->setCanonicalUri($this->contentUrlGenerator->generate($override, [], UrlGeneratorInterface::ABSOLUTE_URL))
                ;
            }
        }
    }

    private function override(): CalendarEventsModel|null
    {
        if (!$event = CalendarEventsModel::findOneByAlias(Input::get('auto_item', false, true))) {
            return null;
        }

        // Check if unified aliases feature is enabled for this event
        if (!$this->unifiedAliases->isUnifiedAliasEnabled($event)) {
            return null;
        }

        // Get the actual event for the current language
        if (!$actualEvent = $this->unifiedAliases->getEventForCurrentLanguage($event)) {
            return null;
        }

        // Check if this event is actually allowed here
        if (!CalendarEventsModel::findPublishedByParentAndIdOrAlias($actualEvent->alias, $this->cal_calendar)) {
            return null;
        }

        // Redirect in case the detail URL was accessed with the regular alias of the event
        if ($this->unifiedAliases->isCurrentLanguage($event) && !$this->unifiedAliases->isMainEvent($event)) {
            $this->redirectToMainEventUrl($event);
        }

        // Override the 'auto_item' variable
        Input::setGet('auto_item', $actualEvent->alias);

        // Return the verified event with the unified alias
        return $event;
    }

    private function redirectToMainEventUrl(CalendarEventsModel $event): void
    {
        if (!$mainEvent = $this->unifiedAliases->getMainEvent($event)) {
            return;
        }

        $event = $event->cloneDetached();
        $event->alias = $mainEvent->alias;

        throw new RedirectResponseException($this->contentUrlGenerator->generate($event, [], UrlGeneratorInterface::ABSOLUTE_URL), Response::HTTP_MOVED_PERMANENTLY);
    }
}
