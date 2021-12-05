<?php

namespace SDC\Contao\FullBackgroundImages\Tests;

use SDC\Contao\FullBackgroundImages\ContaoFullBackgroundImagesBundle;
use PHPUnit\Framework\TestCase;

class ContaoFullBackgroundImagesBundleTest extends TestCase {
    public function testCanBeInstantiated(): void {
        $bundle = new ContaoFullBackgroundImagesBundle();
        $this->assertInstanceOf('SDC\Contao\FullBackgroundImages\ContaoFullBackgroundImagesBundle', $bundle);
    }
}