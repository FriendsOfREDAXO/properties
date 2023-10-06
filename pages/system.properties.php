<?php

declare(strict_types=1);

namespace FriendsOfRedaxo\ForProperties;

use rex_addon;
use rex_csrf_token;
use rex_file;
use rex_fragment;
use rex_i18n;
use rex_markdown;
use rex_path;
use rex_url;
use rex_view;

$addon = rex_addon::get('properties');

$content = '';
$buttons = '';

$func = rex_request('func', 'string');

$csrfToken = rex_csrf_token::factory('properties_properties');

// Konfiguration speichern
if ('update' === $func && !$csrfToken->isValid()) {
    echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
} elseif ('update' === $func) {
    $addon->setConfig((array) rex_post('settings', [
        ['properties_settings', 'string'],
    ]));

    echo rex_view::success($addon->i18n('config_saved'));
}

// Config-Werte bereitstellen
$Values = [];
$Values['properties_settings'] = $addon->getConfig('properties_settings');

// Check der Properties und evtl. Warning ausgeben
$_settings_array = explode("\n", str_replace("\r", '', '' . $Values['properties_settings']));

$_prefix = '';
$_msg = [];
$_duplicate = [];

// Properties setzen
$_msg = ForProperties::setProperties($_settings_array);

// Fehler ausgeben
if ([] !== $_msg) {
    echo rex_view::warning('<strong>' . $addon->i18n('config_warning') . '</strong><br>' . implode('<br>', $_msg));
}

// Formular-Ausgabe
$content .= '<fieldset><legend>' . $addon->i18n('config_title_legend') . '</legend>';

$formElements = [];
$n = [];

$hilfetext = '';
$file = rex_file::get(rex_path::addon('properties') . 'README.md');
if (null !== $file) {
    $parser = rex_markdown::factory();
    $hilfetext = $parser->parse($file);
}

$html = '
<div class="panel panel-default">
    <header class="panel-heading collapsed" data-toggle="collapse" data-target="#collapse-propertiesinfo">
        <div class="panel-title"><i class="rex-icon rex-icon-info"></i> ' . $addon->i18n('properties_config_title_help') . '</div>
    </header>
    <div id="collapse-propertiesinfo" class="panel-collapse collapse">
        <div class="panel-body" style="background-color:#fff;">' . $hilfetext . '</div>
    </div>
</div>
';

$n['field'] = $html;

$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$formElements = [];
$n = [];

$n['label'] = '<label for="properties_settings">' . htmlspecialchars_decode($addon->i18n('config_properties_settings')) . '</label>';
$n['field'] = '<textarea class="form-control codemirror" width="100%" height="500" data-codemirror-mode="text/x-ini" aceeditor-mode="ini" rows="15" id="properties_settings" name="settings[properties_settings]">' . $Values['properties_settings'] . '</textarea>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$content .= '</fieldset>';

// Save-Button
$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="save" value="' . $addon->i18n('save') . '">' . $addon->i18n('save') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

// Ausgabe Section
$fragment = new rex_fragment();
$fragment->setVar('title', $addon->i18n('title_config'), false);
$fragment->setVar('class', 'edit', false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content = '
<form action="' . rex_url::currentBackendPage() . '" method="post">
<input type="hidden" name="func" value="update" />
    ' . $csrfToken->getHiddenField() . '
    ' . $content . '
</form>
';

echo $content;
