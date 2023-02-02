<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ForProperties;

use rex_addon;

$addon = rex_addon::get('properties');

// Standard-Werte setzen
if (!$addon->hasConfig()) {
    $addon->setConfig('properties_settings', "# REDAXO-Properties setzen\n# PREFIX = my_\n\nHalloText = Servus Welt!\n");
}
