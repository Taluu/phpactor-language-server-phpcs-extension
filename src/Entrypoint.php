<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpCs;

use Phpactor\Container\Container;
use Phpactor\Container\ContainerBuilder;
use Phpactor\Container\Extension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\MapResolver\Resolver;

final class Entrypoint implements Extension
{
    private const PHPCS_BINARY_PATH_PARAM = 'language_server.phpcs.bin';
    private const PHPCS_STANDARD_PARAM = 'language_server.phpcs.standard';

    public function load(ContainerBuilder $container)
    {
        $container->register(Linter::class, static function (Container $container): Linter {
            $binaryPath = $container
                ->get(FilePathResolverExtension::class)
                ->resolve(
                    $container->getParameter(self::PHPCS_BINARY_PATH_PARAM)
                )
            ;

            $standard = $container
                ->get(FilePathResolverExtension::class)
                ->resolve(
                    $container->getParameter(self::PHPCS_STANDARD_PARAM)
                )
            ;

            $root = $container
                ->get(FilePathResolverExtension::class)
                ->resolve('%project_root%')
            ;

            $process = new Process(
                $root,
                $binaryPath,
                $standard
            );

            return new Linter($process);
        });
    }

    public function configure(Resolver $schema)
    {
        $schema->setDefaults([
            self::PHPCS_BINARY_PATH_PARAM => '%project_root%/vendor/bin/phpcs',
            self::PHPCS_STANDARD_PARAM => null,
        ]);
    }
}
