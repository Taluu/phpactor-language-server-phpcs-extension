<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpCs;

use Amp;
use Amp\Process\Process as AmpProcess;
use Phpactor\LanguageServerProtocol\Diagnostic;
use Phpactor\LanguageServerProtocol\DiagnosticSeverity;
use Phpactor\LanguageServerProtocol\Position;
use Phpactor\LanguageServerProtocol\Range;

use function Amp\ByteStream\buffer;
use function Amp\call;

final class Process
{
    private string $cwd;
    private string $binaryPath;
    private ?string $standard;

    public function __construct(
        string $cwd,
        string $binaryPath,
        ?string $standard
    ) {
        $this->cwd = $cwd;
        $this->binaryPath = $binaryPath;
        $this->standard = $standard;
    }

    public function analyse(string $filename): Amp\Promise
    {
        return call(function () use ($filename): iterable {
            $args = [
                $this->binaryPath,
            ];

            if ($this->standard !== null) {
                $args[] = '--standard';
                $args[] = $this->standard;
            }

            $args[] = $filename;

            $process = new AmpProcess($args, $this->cwd);
            $stdout = yield buffer($process->getStdout());
            $exitCode = yield $process->join();

            if ($exitCode > 1) {
                return [];
            }

            return $this->parse($stdout);
        });
    }

    private function parse(string $json): iterable
    {
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        foreach ($decoded['files'] as $file => $report) {
            foreach ($report['messages'] as $message) {
                $position = new Position(
                    $message['line'] - 1,
                    $message['column'] - 1
                );

                yield new Diagnostic(
                    new Range($position, $position),
                    $message['message'],
                    $message['type'] === 'ERROR' ? DiagnosticSeverity::ERROR : DiagnosticSeverity::WARNING,
                    null,
                    "PHP_CodeSniffer ({$message['source']})"
                );
            }
        }
    }
}
