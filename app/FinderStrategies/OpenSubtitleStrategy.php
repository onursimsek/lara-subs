<?php

declare(strict_types=1);

namespace App\FinderStrategies;

use OpenSubtitles\Hash;
use OpenSubtitles\OpenSubtitles;

class OpenSubtitleStrategy implements FinderStrategy
{
    private OpenSubtitles $provider;

    private ?object $auth = null;

    public function __construct(array $options = [])
    {
        $this->provider = new OpenSubtitles(config('subtitles.providers.opensubtitles'));
    }

    public static function canHandle(): bool
    {
        return true;
    }

    public function find(array $params)
    {
        return $this->provider->subtitle->find($params);
    }

    public function findByTitle(string $title, array $params = [])
    {
        return $this->provider->subtitle->find(
            $params + [
                'languages' => config('subtitles.default_language'),
                'query' => $title,
            ]
        );
    }

    public function findByHash(string $path, array $params = [])
    {
        $hash = (new Hash())->make($path);

        return $this->provider->subtitle->find(
            $params + [
                'languages' => config('subtitles.default_language'),
                'moviehash' => $hash,
            ]
        );
    }

    private function auth()
    {
        return $this->auth ?? $this->auth = $this->provider->authentication->login(config('subtitles.providers.opensubtitles.credentials'));
    }

    public function download($subtitleId)
    {
        return $this->provider->download->download($this->auth()->token, $subtitleId);
    }

    public function saveAsFile(int $subtitleId, string $fileName, string $path): void
    {
        $resp = $this->download($subtitleId);

        file_put_contents($path . DIRECTORY_SEPARATOR . $fileName, file_get_contents($resp->link));
    }
}
