<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Tests\Commands\Post;

use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;

class PostMockCommand extends Base_Post_Command
{
    public function __construct()
    {
        parent::__construct('test');
    }

    public function __invoke(array $args, array $assoc_args)
    {
        $this->title = $args[0];
        parent::__invoke($args, $assoc_args);
    }

    public function transformAassocArgumentToDtoPublic(array $assoc_args)
    {
        return $this->transformAassocArgumentToDto($assoc_args);
    }
}
