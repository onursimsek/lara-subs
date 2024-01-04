<?php

declare(strict_types=1);

namespace App\FinderStrategies;

interface FinderStrategy
{
    public static function canHandle(): bool;

    public function find(array $params);

    public function findByTitle(string $title, array $params = []);

    public function findByHash(string $path, array $params = []);

    public function download($subtitleId);

    public function saveAsFile(int $subtitleId, string $fileName, string $path): void;
}
