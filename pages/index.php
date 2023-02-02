<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ForProperties;

use rex_addon;
use rex_view;

$addon = rex_addon::get('properties');

echo rex_view::title($addon->i18n('properties'));

include 'system.properties.php';
