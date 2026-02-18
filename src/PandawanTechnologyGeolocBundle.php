<?php

declare(strict_types=1);

namespace PandawanTechnology\GeolocBundle;

use PandawanTechnology\GeolocBundle\Command\AddressImporterCommand;
use PandawanTechnology\GeolocBundle\Command\DownloaderCommand;
use PandawanTechnology\GeolocBundle\Downloader\BANDownloader;
use PandawanTechnology\GeolocBundle\Migrations\Version20260121183342;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class PandawanTechnologyGeolocBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $services = $container->services();
        $services->defaults()
            ->autowire()
            ->autoconfigure();

        $shareDir = env('APP_SHARE_DIR');

        $services
            ->set(BANDownloader::class)
            ->set(DownloaderCommand::class)
                ->args([$shareDir])
            ->set(AddressImporterCommand::class)
                ->args([$shareDir])
        ;
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$builder->hasExtension('doctrine_migrations')) {
            return;
        }

        $builder->prependExtensionConfig('doctrine_migrations', [
            'migrations' => [
                'PandawanTechnology\\GeolocBundle\\Migrations' => Version20260121183342::class,
            ],
        ]);
    }
}
