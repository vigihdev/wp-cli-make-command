<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Exceptions;

use Throwable;
use Vigihdev\WpCliModels\UI\WpCliStyle;

final class MakeHandlerException implements MakeHandlerExceptionInterface
{
    private ?WpCliStyle $io = null;

    public function __construct()
    {
        $this->io = new WpCliStyle();
    }

    /**
     * @param Throwable $e
     * @return void
     */
    public function handle(Throwable $e): void
    {
        $this->io->errorBlock($e->getMessage());

        // TODO: Implement handle() method.
    }
}
