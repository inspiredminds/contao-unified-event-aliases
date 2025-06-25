<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

use InspiredMinds\ContaoUnifiedEventAliases\Controller\FrontendModule\EventReaderModuleController;

$GLOBALS['TL_DCA']['tl_module']['palettes'][EventReaderModuleController::TYPE] = $GLOBALS['TL_DCA']['tl_module']['palettes']['eventreader'];
