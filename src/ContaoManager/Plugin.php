<?php

namespace SDC\Contao\FullBackgroundImages\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use SDC\Contao\FullBackgroundImages\ContaoFullBackgroundImagesBundle;

class Plugin implements BundlePluginInterface {

    public function getBundles(ParserInterface $parser) {
        return [
            BundleConfig::create(ContaoFullBackgroundImagesBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}