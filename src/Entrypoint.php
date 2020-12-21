<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpCs;

use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\MapResolver\Resolver;

final class Entrypoint implements Extension
{
    private const PHPCS_BINARY_PATH_PARAM = 'language_server.phpcs.bin';
    private const PHPCS_STANDARD_PARAM = 'language_server.phpcs.standard';

    public function load(ContainerBuilder $container)
    {
    }

    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PHPCS_BINARY_PATH_PARAM => '%project_root%/vendor/bin/phpcs',
            self::PHPCS_STANDARD_PARAM => null,
        ]);
    }
}
