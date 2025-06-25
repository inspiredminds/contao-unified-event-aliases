<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\ContaoManager;

use Contao\CalendarBundle\ContaoCalendarBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use InspiredMinds\ContaoUnifiedEventAliases\ContaoUnifiedEventAliasesBundle;
use Terminal42\ChangeLanguage\Terminal42ChangeLanguageBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(ContaoUnifiedEventAliasesBundle::class)
                ->setLoadAfter([
                    ContaoCalendarBundle::class,
                    Terminal42ChangeLanguageBundle::class,
                ]),
        ];
    }
}
