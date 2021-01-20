<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified Event Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\ContaoManager;

use Contao\CalendarBundle\ContaoCalendarBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use InspiredMinds\ContaoUnifiedEventAliases\ContaoUnifiedEventAliasesBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoUnifiedEventAliasesBundle::class)
                ->setLoadAfter([
                    ContaoCalendarBundle::class,
                    'changelanguage',
                ]),
        ];
    }
}
