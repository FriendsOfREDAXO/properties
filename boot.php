<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ForProperties;

use rex_addon;
use rex_be_controller;

// REDAXO-Properties setzen

$addon = rex_addon::get('properties');

$settings_string = $addon->getConfig('properties_settings', '');
if (!is_string($settings_string)) {
    $settings_string = '';
}

$_settings_array = explode("\n", str_replace("\r", '', $settings_string));
if ('properties' === rex_be_controller::getCurrentPagePart(1)) {
    return;
}

if ('properties' === rex_be_controller::getCurrentPagePart(2)) {
    return;
}

ForProperties::setProperties($_settings_array);
