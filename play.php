<?php

require_once __DIR__ . '/vendor/autoload.php';

$config = require __DIR__ . '/configs/configs.php';

\Tadeskione\Glider\Game::instance($config)->run();

