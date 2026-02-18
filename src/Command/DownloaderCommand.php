<?php

declare(strict_types=1);

namespace PandawanTechnology\GeolocBundle\Command;

use PandawanTechnology\GeolocBundle\Downloader\BANDownloader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'pandawan-geoloc:data:download',
    description: 'Download data from la Base Adresse Nationale\' service',
)]
class DownloaderCommand extends Command
{
    public const string FILE_NAME = 'france.csv';

    public function __construct(
        private readonly string $sharedDir,
        private readonly BANDownloader $banDownloader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('download', null, InputOption::VALUE_NONE, 'Forces to re-download the file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $archiveFileName = $this->sharedDir.'/'.static::FILE_NAME;

        if ($input->getOption('download')) {
            $this->banDownloader->download($archiveFileName.'.gz');
        }

        $this->extractArchive($archiveFileName);

        return static::SUCCESS;
    }

    private function extractArchive(string $fileName): void
    {
        // Raising this value may increase performance
        $buffer_size = 4096; // read 4kb at a time
        $out_file_name = str_replace('.gz', '', $fileName);

        // Open our files (in binary mode)
        $file = gzopen($fileName, 'rb');
        $out_file = fopen($out_file_name, 'wb');

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }

        // Files are done, close files
        fclose($out_file);
        gzclose($file);
    }
}
