<?php

namespace App\Traits;

use App\Enums\ContentType;
use Generator;

use function Laravel\Prompts\select;

trait HasSelectableSubtitles
{
    public function selectableSubtitles(string $label, array $subtitles): int|string
    {
        return select(
            label: 'Select the subtitle you want to download',
            options: iterator_to_array($this->collectTitles($subtitles)),
        );
    }

    public function collectTitles(array $subtitles): Generator
    {
        foreach ($subtitles as $subtitle) {
            yield $subtitle->attributes->files[0]->file_id => match ($subtitle->attributes->feature_details->feature_type) {
                ContentType::Episode->name => $this->episodeTitle($subtitle),
                ContentType::Movie->name => $this->movieTitle($subtitle),
            };
        }
    }

    private function episodeTitle(object $subtitle): string
    {
        return sprintf(
            '%1$s - S%2$02dE%3$02d - %4$s [%5$s]',
            $subtitle->attributes->feature_details->parent_title,
            $subtitle->attributes->feature_details->season_number,
            $subtitle->attributes->feature_details->episode_number,
            $subtitle->attributes->feature_details->title,
            $subtitle->attributes->language,
        );
    }

    private function movieTitle(object $subtitle): string
    {
        return sprintf(
            '%1$s - %2$d [%3$s]',
            $subtitle->attributes->feature_details->title,
            $subtitle->attributes->feature_details->year,
            $subtitle->attributes->language,
        );
    }
}
