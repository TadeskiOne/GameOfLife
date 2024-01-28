<?php

$default = require __DIR__ . '/game.php';

$opt = getopt('', ['size::', 'dens::', 'dur::', 'cycle::']);

$custom = array_filter(
    [
        'grid_size'                   => $opt['size'] ?? null,
        'first_gen_alive_probability' => $opt['dens'] ?? null,
        'gen_duration'                => $opt['dur'] ?? null,
        'game_cycle'                  => $opt['cycle'] ?? null,
    ],
    fn($option) => !is_null($option)
);

return array_merge($default, $custom);