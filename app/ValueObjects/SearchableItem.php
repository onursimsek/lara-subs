<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\ContentType;
use App\Enums\SearchableItemType;
use SplFileInfo;

class SearchableItem
{
    public readonly ContentType $contentType;
    public readonly int $season;
    public readonly int $episode;

    public function __construct(
        public readonly SearchableItemType $type,
        public readonly string $text,
        public readonly ?SplFileInfo $path = null
    ) {
        $guess = $this->guessWhat($this->text);

        if (array_key_exists('season', $guess) && array_key_exists('episode', $guess)) {
            $this->contentType = ContentType::Episode;
            $this->season = (int)$guess['season'];
            $this->episode = (int)$guess['episode'];
        } else {
            $this->contentType = ContentType::Movie;
        }
    }

    private function guessWhat(string $name): array
    {
        preg_match('/S(?P<season>\d{1,2})E(?P<episode>\d{1,2})/', $name, $matches);
        return $matches;
    }

    public function toDownloadableItem(): DownloadableItem
    {
        return new DownloadableItem($this->text . '.srt', $this->path->getPath());
    }
}
