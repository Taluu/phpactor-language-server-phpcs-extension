<?php

declare(strict_types=1);

namespace Phpactor\Extension\LanguageServerPhpCs;

final class File
{
    private string $uri;
    private ?string $contents;
    private ?int $version;

    public function __construct(string $uri, ?string $contents = null, ?int $version = null)
    {
        $this->uri = $uri;
        $this->contents = $contents;
        $this->version = $version;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }
}
