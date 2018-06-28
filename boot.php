<?php

// REDAXO-Properties setzen

$_settings_array = explode("\n", str_replace("\r", '', $this->getConfig('properties_settings')));

$_prefix = '';

if (rex_be_controller::getCurrentPagePart(1) != 'properties' && rex_be_controller::getCurrentPagePart(2) != 'properties') {
    foreach ($_settings_array as $_lc => $_line) {
        $_line = trim($_line);
        if (substr($_line, 0, 1) != '#') { // Kommentarzeilen übergehen

            $_work = explode('#', $_line); // wg. Inline-Kommentaren
            $_set = explode('=', $_work[0]);

            // [Section] als Prefix
            if (substr($_line, 0, 1) == '[' && substr($_line, -1) == ']') {
                $_prefix = trim(substr($_line, 1, -1));
            }
            // Prefix
            if (trim($_set[0]) == 'PREFIX') {
                $_prefix = trim($_set[1]);
            }

            if (count($_set) === 2) {
                if (!rex::hasProperty($_prefix . trim($_set[0]))) {
                    rex::setProperty($_prefix . trim($_set[0]), trim($_set[1]));
                }
            }
        }
    }
}
