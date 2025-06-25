<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedEventAliases\EventListener;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\ModuleModel;
use InspiredMinds\ContaoUnifiedEventAliases\Controller\FrontendModule\EventReaderModuleController;

#[AsCallback('tl_module', 'config.onload')]
class AdjustCustomTplOptionsCallback
{
    public function __invoke(DataContainer $dc): void
    {
        $module = ModuleModel::findById($dc->id);

        if (null === $module || EventReaderModuleController::TYPE !== $module->type) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_module']['fields']['customTpl']['options_callback'] = (static fn () => Controller::getTemplateGroup('mod_eventreader_', [], 'mod_eventreader'));
    }
}
