<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ForProperties;

use rex;
use rex_addon;
use rex_i18n;

use function count;
use function strlen;

/**
 * ForProperties.
 */

final class ForProperties
{
    /** @var string */
    private const SETPREFIX = '';

    /** @var array<int|string, string> */
    private static $_msg = [];

    /** @var array<int|string, bool> */
    private static $_duplicate = [];

    /**
     * Set properties.
     * @param array<int|string, string> $_settings_array
     * @return array<int|string, string>
     * @api
     */
    public static function setProperties(array $_settings_array): array
    {
        $_prefix = '';

        foreach ($_settings_array as $_linenumber => $_line) {
            $_line = trim($_line);

            if ('#' === substr($_line, 0, 1)) { // Kommentarzeilen übergehen
                continue;
            }

            $_work = explode(' # ', $_line); // wg. Inline-Kommentaren
            $_set = explode('=', $_work[0]);

            // [Section] als Prefix
            if ('[' === substr($_line, 0, 1) && ']' === substr($_line, -1)) {
                $_prefix = trim(substr($_line, 1, -1));
                continue;
            }

            // PREFIX = als Prefix
            if ('PREFIX' === trim($_set[0]) || 'prefix' === trim($_set[0])) {
                $_prefix = trim($_set[1]);
                continue;
            }

            // Set Property
            if (count($_set) > 1) {
                self::setProperty($_set, $_prefix, (int) $_linenumber);
                self::$_duplicate[self::SETPREFIX . $_prefix . trim($_set[0])] = true;
            }
        }

        return self::$_msg;
    }

    /**
     * Set property.
     * @param array<int|string, string> $_set
     * @api
     */
    public static function setProperty(array $_set, string $_prefix, int $_linenumber): void
    {
        $_key = trim($_set[0]);
        $_val = trim($_set[1]);

        if (!rex::hasProperty($_prefix . $_key) && !rex::hasProperty(self::SETPREFIX . $_prefix . $_key) && !isset(self::$_duplicate[self::SETPREFIX . $_prefix . $_key])) {
            if (count($_set) > 2) {
                unset($_set[0]);
                $_val = trim(implode('=', $_set));
            }

            rex::setProperty(self::SETPREFIX . $_prefix . $_key, self::castToType($_val));
        } else {
            self::$_msg[] = rex_i18n::msg('properties_linecount') . ' ' . ($_linenumber + 1) . ': ' . $_key . ' = ' . htmlspecialchars($_val) . ' (Section/PREFIX = ' . $_prefix . ')';
        }
    }

    /**
     * Get all properties.
     * @return array<string, array<string, mixed>>
     * @api
     */
    public static function getAllProperties(): array
    {
        $addon = rex_addon::get('properties');

        $_settings_array = explode("\n", str_replace("\r", '', '' . $addon->getConfig('properties_settings')));

        $_prefix = 'no_prefix';
        $_out = [];

        foreach ($_settings_array as $_line) {
            $_line = trim($_line);

            if ('#' === substr($_line, 0, 1)) { // Kommentarzeilen übergehen
                continue;
            }

            $_work = explode(' # ', $_line); // wg. Inline-Kommentaren
            $_set = explode('=', $_work[0]);

            // [Section] als Prefix
            if ('[' === substr($_line, 0, 1) && ']' === substr($_line, -1)) {
                $_prefix = trim(substr($_line, 1, -1));
                continue;
            }

            // PREFIX = als Prefix
            if ('PREFIX' === trim($_set[0]) || 'prefix' === trim($_set[0])) {
                $_prefix = trim($_set[1]);
                continue;
            }

            // Set Property
            if (count($_set) > 1) {
                $_key = trim($_set[0]);
                $_val = trim($_set[1]);
                $_out[$_prefix][$_key] = self::castToType($_val);
            }

        }

        return $_out;
    }

    /**
     * Cast property type.
     * @return int|float|string|bool|mixed
     * @api
     */
    public static function castToType(string $value)
    {
        $value = trim($value);

        // bool
        if ('true' === strtolower($value) || 'false' === strtolower($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // integer
        if (1 === preg_match('/^\\d+$/', $value)) {
            return (int) $value;
        }

        // float
        if (1 === preg_match('/^[-+]?\d*[.,]?\d+([eE][-+]?\d+)?$/', $value)) {
            return (float) str_replace(',', '.', $value);
        }

        // string
        if (isset($value[0]) && strlen($value) >= 2) {
            if ('"' === $value[0] && '"' === $value[strlen($value) - 1]) {
                return substr($value, 1, -1);
            }

            if ("'" === $value[0] && "'" === $value[strlen($value) - 1]) {
                return substr($value, 1, -1);
            }

            // json
            if ('{' === $value[0] && '}' === $value[strlen($value) - 1]) {
                return json_decode($value, true);
            }
        }

        return $value;
    }
}
