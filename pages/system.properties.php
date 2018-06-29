<?php

$content = '';
$buttons = '';

$func = rex_request('func', 'string');

$csrfToken = rex_csrf_token::factory('properties_properties');

// Konfiguration speichern
if ($func == 'update' && !$csrfToken->isValid()) {
    echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
} elseif ($func == 'update') {
    $this->setConfig(rex_post('settings', [
        ['properties_settings', 'string'],
    ]));

    echo rex_view::success($this->i18n('config_saved'));
}

// Config-Werte bereitstellen
$Values = [];
$Values['properties_settings'] = $this->getConfig('properties_settings');

// Check der Properties und evtl. Warning ausgeben
$_settings_array = explode("\n", str_replace("\r", '', $Values['properties_settings']));

$_prefix = '';
$_msg = [];
$_duplicate = [];

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
            if (rex::hasProperty($_prefix . trim($_set[0])) || isset($_duplicate[$_prefix . trim($_set[0])])) {
                $_msg[] = 'Zeile ' . ($_lc+1) . ': ' . trim($_set[0]) . ' = ' . trim($_set[1]) . ' (Section/PREFIX=' .$_prefix . ')';
            }
            $_duplicate[$_prefix . trim($_set[0])] = true;
        }
    }
}

if (count($_msg) > 0) {
    echo rex_view::warning('<strong>' . $this->i18n('config_warning') . '</strong><br>' . implode('<br>', $_msg));
}

// Formular-Ausgabe
$content .= '<fieldset><legend>' . $this->i18n('config_title_legend') . '</legend>';

$formElements = [];
$n = [];

$file = rex_file::get(rex_path::addon('properties') .'README.md');
$parser = rex_markdown::factory();
$hilfetext = $parser->parse($file);

$html = '
<div class="panel panel-default">
    <header class="panel-heading collapsed" data-toggle="collapse" data-target="#collapse-propertiesinfo">
        <div class="panel-title"><i class="rex-icon rex-icon-info"></i> ' . $this->i18n('properties_config_title_help') . '</div>
    </header>
<div id="collapse-propertiesinfo" class="panel-collapse collapse">
    <div class="panel-body" style="background-color:#fff;">' . $hilfetext . '</div>
</div>
';

$n['field'] = $html;

$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$formElements = [];
$n = [];

$n['label'] = '<label for="properties_settings">' . htmlspecialchars_decode($this->i18n('config_properties_settings')) . '</label>';
$n['field'] = '<textarea class="form-control rex-code" data-codemirror-mode="text/x-ini" rows="25" id="properties_settings" name="settings[properties_settings]">' . $Values['properties_settings'] . '</textarea>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$content .= '</fieldset>';

// Save-Button
$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="save" value="' . $this->i18n('save') . '">' . $this->i18n('save') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

// Ausgabe Section
$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('title_config'), false);
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
