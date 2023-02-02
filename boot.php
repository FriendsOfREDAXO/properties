<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ForProperties;

use rex_addon;
use rex_be_controller;

// REDAXO-Properties setzen

$addon = rex_addon::get('properties');

$_settings_array = explode("\n", str_replace("\r", '', strval($addon->getConfig('properties_settings', ''))));

if ('properties' !== rex_be_controller::getCurrentPagePart(1) && 'properties' !== rex_be_controller::getCurrentPagePart(2)) {
    ForProperties::setProperties($_settings_array);
}
