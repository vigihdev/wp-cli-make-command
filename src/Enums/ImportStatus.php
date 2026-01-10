<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Enums;

enum ImportStatus: string
{
    /**
     * The import was successful.
     */
    case SUCCESS = 'success';

    /**
     * The import failed.
     */
    case FAILED  = 'failed';

    /**
     * The import was skipped.
     */
    case SKIPPED = 'skipped';

    /**
     * The import had warnings.
     */
    case WARNING = 'warning';
}
