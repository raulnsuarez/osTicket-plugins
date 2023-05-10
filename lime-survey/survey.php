<?php

/**
 * FilesystemStorage plugin
 *
 * Allows attachment data to be written to the disk rather than in the
 * database
 */
require_once(INCLUDE_DIR.'class.plugin.php');
require_once('config.php');

class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyConfig';

    function bootstrap() {
        $config = $this->getConfig();
    }
}

