<?php

// Cast string to type
function propertiesCastToType($value)
{
    $value = trim($value);
    $rc = (string) $value;
    $casted = false;

    // bool
    if (strtolower($value) == 'true' or strtolower($value) == 'false') {
        $rc = strtolower($value) == 'true' ? (bool) true : (bool) false;
        $casted = true;
    }

    // integer
    if (!$casted && preg_match("/^\d+$/", $value)) {
        $rc = (int) $value;
        $casted = true;
    }

    // float
    if (!$casted && preg_match("/^\d+(\.\d{1,2})?/", $value)) {
        $rc = (float) $value;
        $casted = true;
    }

    // string
    if (!$casted && $value[0] == '"' && $value[strlen($value)-1] == '"') {
        $rc = (string) substr($value, 1, -1);
        $casted = true;
    }
    if (!$casted && $value[0] == "'" && $value[strlen($value)-1] == "'") {
        $rc = (string) substr($value, 1, -1);
        $casted = true;
    }

    // json
    if (!$casted && $value[0] == '{' && $value[strlen($value)-1] == '}') {
        $rc = json_decode($value, true);
        $casted = true;
    }

    return $rc;
}

// REDAXO-Properties setzen

$_settings_array = explode("\n", str_replace("\r", '', $this->getConfig('properties_settings')));

$_prefix = '';

if (rex_be_controller::getCurrentPagePart(1) != 'properties' && rex_be_controller::getCurrentPagePart(2) != 'properties') {
    foreach ($_settings_array as $_lc => $_line) {
        $_line = trim($_line);
        if (substr($_line, 0, 1) != '#') { // Kommentarzeilen übergehen

            $_work = explode(' # ', $_line); // wg. Inline-Kommentaren
            $_set = explode('=', $_work[0]);

            // [Section] als Prefix
            if (substr($_line, 0, 1) == '[' && substr($_line, -1) == ']') {
                $_prefix = trim(substr($_line, 1, -1));
                continue;
            }
            // Prefix
            if (trim($_set[0]) == 'PREFIX') {
                $_prefix = trim($_set[1]);
                continue;
            }

            // Set Property
            if (count($_set) > 1) {
                $_key = trim($_set[0]);
                $_val = trim($_set[1]);
                if (!rex::hasProperty($_prefix . $_key)) {
                    if (count($_set) > 2) {
                        unset($_set[0]);
                        $_val = trim(implode('=', $_set));
                    }
                    rex::setProperty($_prefix . $_key, propertiesCastToType($_val));
                }
            }
        }
    }
}
