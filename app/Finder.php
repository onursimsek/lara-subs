<?php

declare(strict_types=1);

namespace App;

use App\FinderStrategies\FinderStrategy;
use App\FinderStrategies\OpenSubtitleStrategy;

class Finder
{
    private array $providers = [
        OpenSubtitleStrategy::class,
    ];

    private FinderStrategy $provider;

    public function __construct(array $options = [])
    {
        foreach ($this->providers as $provider) {
            if (!$provider::canHandle()) {
                continue;
            }

            $this->provider = new $provider($options);
        }
    }

    public function find(array $params)
    {
        return $this->provider->find($params);
    }

    public function findByTitle(string $title, array $params = [])
    {
        return $this->provider->findByTitle($title, $params);
    }

    public function findByHash(string $path, array $params = [])
    {
        return $this->provider->findByHash($path, $params);
    }

    public function download(int $subtitleId)
    {
        return $this->provider->download($subtitleId);
    }

    public function saveAsFile(int $subtitleId, string $fileName, string $path): void
    {
        $this->provider->saveAsFile($subtitleId, $fileName, $path);
    }
}
