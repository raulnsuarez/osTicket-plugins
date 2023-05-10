<?php

/**
 * FilesystemStorage plugin
 *
 * Allows attachment data to be written to the disk rather than in the
 * database
 */

class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyPluginConfig';

    // function bootstrap() {
    //     $config = $this->getConfig();
    //     $uploadpath = $config->get('uploadpath');
    //     list($__, $_N) = $config::translate();
    //     if ($uploadpath) {
    //         FileStorageBackend::register('F', 'FilesystemStorage');
    //         FilesystemStorage::$base = $uploadpath;
    //         FilesystemStorage::$desc = $__('Filesystem') .': '.$uploadpath;
    //     }
    // }
}

