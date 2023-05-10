<?php

/**
 * FilesystemStorage plugin
 *
 * Allows attachment data to be written to the disk rather than in the
 * database
 */

class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyConfig';

    function bootstrap() {
        $config = $this->getConfig();
    }
}

