<?php

// Standard-Werte setzen
if (!$this->hasConfig()) {
    $this->setConfig('properties_settings', "# REDAXO-Properties setzen\n# PREFIX = my_\n\nHalloText = Servus Welt!\n");
}
