<?php

namespace App\ValueObjects;

class DownloadableItem
{
    public function __construct(
        public readonly string $fileName,
        public readonly string $path,
        protected ?int $subtitleId = null
    ) {
    }

    public function getSubtitleId(): ?int
    {
        return $this->subtitleId;
    }

    public function setSubtitleId(?int $subtitleId): static
    {
        $this->subtitleId = $subtitleId;
        return $this;
    }
}
