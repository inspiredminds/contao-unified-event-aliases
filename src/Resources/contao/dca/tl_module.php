<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Unified Event Aliases extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use InspiredMinds\ContaoUnifiedEventAliases\Controller\FrontendModule\EventReaderModuleController;

$GLOBALS['TL_DCA']['tl_module']['palettes'][EventReaderModuleController::TYPE] = $GLOBALS['TL_DCA']['tl_module']['palettes']['eventreader'];
