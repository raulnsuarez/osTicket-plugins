<?php

require_once(INCLUDE_DIR.'/class.plugin.php');
require_once(INCLUDE_DIR.'/class.forms.php');


class LimeSurveyConfig extends PluginConfig {

    // Provide compatibility function for versions of osTicket prior to
    // translation support (v1.9.4)
    function translate() {
        if (!method_exists('Plugin', 'translate')) {
            return array(
                function($x) { return $x; },
                function($x, $y, $n) { return $n != 1 ? $y : $x; },
            );
        }
        return Plugin::translate('lime-survey');
    }

    public function getServer() {
        return $this->get('domain');
    }

    public function getUser() {
        return $this->get('user');
    }

    public function getPasswd() {
        return $this->get('passwd');
    }

    public function getServerSettings() {
        $settings = [
            'domain'       => $this->getServer(),
            'user'   => $this->getUser(),
            'passwd'    => $this->getPasswd(),
            'scopes' => $scopes,
        ];

        return $settings;
    }

    function getAllOptions() {
        list($__, $_N) = self::translate();
        return array(
            'lss' => new SectionBreakField(array(
                'label' => 'LimeSurvey® Settings',
                'hint' => $__('This section should be all that is required for LimeSurvey'),
            )),
            'domain' => new TextboxField(array(
                'label' => $__('Domain Name'),
                'hint' => $__('FQDN of the LimeSurvey server'),
                'configuration' => array('size'=>40, 'length'=>60),
                'validators' => array(
                function($self, $val) use ($__) {
                    if (strpos($val, '.') === false)
                        $self->addError(
                            $__('Domain name is expected'));
                }),
            )),
            'user' => new TextboxField(array(
                'label' => $__('User'),
                'hint' => $__('User for use in the connection to the Lime Survey Server'),
                'configuration' => array('size'=>40, 'length'=>120),
            )),
            'passwd' => new TextboxField(array(
                'widget' => 'PasswordWidget',
                'label' => $__('Password'),
                'validator' => 'noop',
                'hint' => $__("Password associated with the user account"),
                'configuration' => array('size'=>40),
            ))
        );
    }

    function getOptions() {
        return  $this->getAllOptions();
    }    

    function pre_save(&$config, &$errors) {
        return true;
    }
}

?>
