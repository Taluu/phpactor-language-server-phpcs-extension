<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpCs;

use Amp\Promise;
use Generator;
use Phpactor\TextDocument\TextDocumentUri;
use function Amp\call;

final class Linter
{
    private Process $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public function lint(string $url, ?string $text): Promise
    {
        return call(function () use ($url, $text): Generator {
            if ($text === null) {
                yield $this->process->analyse(TextDocumentUri::fromString($url)->path());
                return;
            }

            $name = tempnam(sys_get_temp_dir(), 'phpcs_ls_');
            file_put_contents($name, $text);
            yield from $this->process->analyse($name);
            unlink($name);
        });
    }
}
