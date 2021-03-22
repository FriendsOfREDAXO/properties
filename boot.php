<?php

// Properties setzen
function properties_setProperties($_settings_array)
{
    $_setprefix = '';

    $_prefix = '';
    $_msg = [];
    $_duplicate = [];

    foreach ($_settings_array as $_lc => $_line) {
        $_line = trim($_line);

        if ('#' != substr($_line, 0, 1)) { // Kommentarzeilen übergehen
            $_work = explode(' # ', $_line); // wg. Inline-Kommentaren
            $_set = explode('=', $_work[0]);

            // [Section] als Prefix
            if ('[' == substr($_line, 0, 1) && ']' == substr($_line, -1)) {
                $_prefix = trim(substr($_line, 1, -1));
                continue;
            }

            // PREFIX = als Prefix
            if ('PREFIX' == trim($_set[0]) || 'prefix' == trim($_set[0])) {
                $_prefix = trim($_set[1]);
                continue;
            }

            // Set Property
            if (count($_set) > 1) {
                $_key = trim($_set[0]);
                $_val = trim($_set[1]);
                if (!rex::hasProperty($_prefix . $_key) && !rex::hasProperty($_setprefix . $_prefix . $_key) && !isset($_duplicate[$_setprefix . $_prefix . $_key])) {
                    if (count($_set) > 2) {
                        unset($_set[0]);
                        $_val = trim(implode('=', $_set));
                    }
                    rex::setProperty($_setprefix . $_prefix . $_key, propertiesCastToType($_val));
                } else {
                    $_msg[] = rex_i18n::msg('properties_linecount') . ' ' . ($_lc + 1) . ': ' . $_key . ' = ' . htmlspecialchars($_val) . ' (Section/PREFIX = ' .$_prefix . ')';
                }
                $_duplicate[$_setprefix . $_prefix . $_key] = true;
            }
        }
    }

    return $_msg;
}

// Alle Properties als Array liefern
function properties_getAllProperties()
{
    $addon = rex_addon::get('properties');
    $_settings_array = explode("\n", str_replace("\r", '', $addon->getConfig('properties_settings')));

    $_prefix = 'no_prefix';
    $_out = [];

    foreach ($_settings_array as $_lc => $_line) {
        $_line = trim($_line);

        if ('#' != substr($_line, 0, 1)) { // Kommentarzeilen übergehen
            $_work = explode(' # ', $_line); // wg. Inline-Kommentaren
            $_set = explode('=', $_work[0]);

            // [Section] als Prefix
            if ('[' == substr($_line, 0, 1) && ']' == substr($_line, -1)) {
                $_prefix = trim(substr($_line, 1, -1));
                continue;
            }

            // PREFIX = als Prefix
            if ('PREFIX' == trim($_set[0]) || 'prefix' == trim($_set[0])) {
                $_prefix = trim($_set[1]);
                continue;
            }

            // Set Property
            if (count($_set) > 1) {
                $_key = trim($_set[0]);
                $_val = trim($_set[1]);
                $_out[$_prefix][$_key] = propertiesCastToType($_val);
            }
        }
    }

    return $_out;
}

// Cast string to type
function propertiesCastToType($value)
{
    $value = trim($value);
    $rc = (string) $value;
    $casted = false;

    // bool
    if ('true' == strtolower($value) || 'false' == strtolower($value)) {
        $rc = 'true' == strtolower($value) ? (bool) true : (bool) false;
        $casted = true;
    }

    // integer
    if (!$casted && preg_match("/^\d+$/", $value)) {
        $rc = (int) $value;
        $casted = true;
    }

    // float
    if (!$casted && preg_match('/^[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?$/', $value)) {
        $rc = (float) str_replace(',', '.', $value);
        $casted = true;
    }

    // string
    if (isset($value[0]) && strlen($value) >= 2) {
        if (!$casted && '"' == $value[0] && '"' == $value[strlen($value) - 1]) {
            $rc = (string) substr($value, 1, -1);
            $casted = true;
        }
        if (!$casted && "'" == $value[0] && "'" == $value[strlen($value) - 1]) {
            $rc = (string) substr($value, 1, -1);
            $casted = true;
        }

        // json
        if (!$casted && '{' == $value[0] && '}' == $value[strlen($value) - 1]) {
            $rc = json_decode($value, true);
            $casted = true;
        }
    }

    return $rc;
}

// REDAXO-Properties setzen
$_settings_array = explode("\n", str_replace("\r", '', $this->getConfig('properties_settings')));

if ('properties' != rex_be_controller::getCurrentPagePart(1) && 'properties' != rex_be_controller::getCurrentPagePart(2)) {
    properties_setProperties($_settings_array);
}
