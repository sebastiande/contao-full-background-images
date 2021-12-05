<?php

$GLOBALS['FBI']['helperClass'] = 'SDC\Contao\Fbi\Helper\FbiHelper';

$GLOBALS['TL_HOOKS']['generatePage'][] = array('SDC\Contao\Fbi\Runner', 'generate');
