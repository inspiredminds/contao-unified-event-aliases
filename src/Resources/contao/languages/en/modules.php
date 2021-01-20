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

$GLOBALS['TL_LANG']['FMD'][EventReaderModuleController::TYPE] = [
    $GLOBALS['TL_LANG']['FMD']['eventreader'][0].' for unified aliases',
    'This event reader allows to display event aliases via their unified (i.e. main) alias.',
];
