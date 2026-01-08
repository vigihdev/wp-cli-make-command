<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Enums;

enum ImportStatus: string
{
    case SUCCESS = 'success';
    case FAILED  = 'failed';
    case SKIPPED = 'skipped';
    case WARNING = 'warning';
}
