<?php

declare(strict_types=1);

namespace App\Commands;

use App\Enums\ContentType;
use App\Enums\SearchableItemType;
use App\Finder;
use App\Traits\HasSelectableSubtitles;
use App\ValueObjects\DownloadableItem;
use App\ValueObjects\SearchableItem;
use Generator;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use SplFileInfo;

class FindCommand extends Command
{
    use HasSelectableSubtitles;

    public const AVAILABLE_EXTENSIONS = [
        'avi',
        'mp4',
        'mov',
        'mkv',
        'mk3d',
        'webm',
        'ts',
        'mts',
        'm2ts',
        'ps',
        'vob',
        'evo',
        'mpeg',
        'mpg',
        'm1v',
        'm2p',
        'm2v',
        'm4v',
        'movhd',
        'movx',
        'qt',
        'mxf',
        'ogg',
        'ogm',
        'ogv',
        'rm',
        'rmvb',
        'flv',
        'swf',
        'asf',
        'wm',
        'wmv',
        'wmx',
        'divx',
        'x264',
        'xvid'
    ];

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'find
        {--T|title=}
        {--P|path=./}
        {--L|lang=tr}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Find subtitle';

    /**
     * @var Finder
     */
    private Finder $finder;

    public function __construct()
    {
        parent::__construct();

        $this->finder = new Finder();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): bool
    {
        $searchableItems = $this->collectSearchableItems();
        if ($searchableItems->isEmpty()) {
            $this->error('There is no movie or tv-series files');
        }

        $selectedSubtitles = collect();
        /** @var SearchableItem $element */
        while ($element = $searchableItems->shift()) {
            $subtitles = $this->findSubtitle($element);

            if ($subtitles->total_count == 0) {
                $this->info(sprintf('The subtitle not found for %s', $element->text));
                continue;
            }

            if ($subtitles->total_count == 1) {
                $selectedSubtitles->push(
                    $element->toDownloadableItem()
                        ->setSubtitleId($this->collectTitles($subtitles->data)->key())
                );
                continue;
            }

            $subtitle = $this->selectableSubtitles('Please, choose a subtitle!', $subtitles->data);

            $selectedSubtitles->push($element->toDownloadableItem()->setSubtitleId($subtitle));
        }

        /** @var DownloadableItem $subtitle */
        foreach ($selectedSubtitles as $subtitle) {
            if (is_file($subtitle->path . DIRECTORY_SEPARATOR . $subtitle->fileName)) {
                continue;
            }

            $this->finder->saveAsFile($subtitle->getSubtitleId(), $subtitle->fileName, $subtitle->path);
            $this->info(sprintf('The subtitle downloaded for %s', $subtitle->fileName));
            usleep(500);
        }

        return true;
    }

    private function collectSearchableItems()
    {
        if ($this->option('title')) {
            return collect($this->convertSearchableItemFromTitle($this->option('title')));
        }

        if ($this->option('path') && is_dir($this->option('path'))) {
            return collect($this->convertSearchableItemFromPath($this->option('path')));
        }

        if ($this->option('path') && is_file($this->option('path'))) {
            return collect()->push($this->convertSearchableItemFromFile($this->option('path')));
        }

        $this->error('There is no movie or tv-series files');
    }

    private function convertSearchableItemFromTitle(string $title): Generator
    {
        yield new SearchableItem(SearchableItemType::Title, $title);
    }

    private function convertSearchableItemFromPath(string $path): Generator
    {
        $pattern = $path . '/*.{' . implode(',', self::AVAILABLE_EXTENSIONS) . '}';
        foreach (glob($pattern, GLOB_BRACE) as $file) {
            yield $this->convertSearchableItemFromFile($file);
        }
    }

    private function convertSearchableItemFromFile(string $filePath): SearchableItem
    {
        $file = new SplFileInfo($filePath);

        return new SearchableItem(
            SearchableItemType::File,
            $file->getBasename('.' . $file->getExtension()),
            $file
        );
    }

    private function findSubtitle(SearchableItem $item)
    {
        if ($item->type == SearchableItemType::File) {
            $subtitles = $this->finder->findByHash($item->path->getRealPath());
            if ($subtitles->total_count > 0) {
                return $subtitles;
            }
        }

        return match ($item->contentType) {
            ContentType::Movie => $this->finder->findByTitle($item->text),
            ContentType::Episode => $this->finder->findByTitle(
                $item->text,
                ['season_number' => $item->season, 'episode_number' => $item->episode]
            )
        };
    }

    /**
     * Define the command's schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
