<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoUnifiedEventAliases;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoUnifiedEventAliasesBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
