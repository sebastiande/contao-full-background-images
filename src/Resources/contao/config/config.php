<?php

$GLOBALS['FBI']['helperClass'] = 'SDC\Contao\FullBackgroundImages\Helper\Helper';

$GLOBALS['TL_HOOKS']['generatePage'][] = array('SDC\Contao\FullBackgroundImages\Runner', 'generate');
