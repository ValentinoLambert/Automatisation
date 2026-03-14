<?php
require('../vendor/autoload.php');

use App\Config;

$config1 = Config::getInstance();
echo 'debug = ' . ($config1->get('debug') ? 'true' : 'false') . '<br>';

$config2 = Config::getInstance();
echo 'Même instance ? ' . (($config1 === $config2) ? 'oui' : 'non');