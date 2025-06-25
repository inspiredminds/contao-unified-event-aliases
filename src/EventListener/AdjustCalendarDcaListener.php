<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\EventListener;

use Contao\CalendarModel;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;

#[AsCallback('tl_calendar', 'config.onload', priority: -32)]
class AdjustCalendarDcaListener
{
    public function __invoke(DataContainer $dc): void
    {
        $GLOBALS['TL_DCA']['tl_calendar']['fields']['master']['eval']['submitOnChange'] = true;

        if (!$dc->id) {
            return;
        }

        $calendar = CalendarModel::findById($dc->id);

        if (null === $calendar || (bool) $calendar->master) {
            return;
        }

        PaletteManipulator::create()
            ->addField('use_unified_aliases', 'language_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_calendar')
        ;
    }
}
