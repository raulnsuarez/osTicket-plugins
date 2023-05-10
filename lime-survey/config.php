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

    function getOptions() {
        list($__, $_N) = self::translate();
        return array(
            'msad' => new SectionBreakField(array(
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

    // function pre_save(&$config, &$errors) {

    //     if ($config['domain'] && !$config['servers']) {
    //         if (!($servers = LDAPAuthentication::autodiscover($config['domain'],
    //                 preg_split('/,?\s+/', $config['dns']))))
    //             $this->getForm()->getField('servers')->addError(
    //                 $__("Unable to find LDAP servers for this domain. Try giving
    //                 an address of one of the DNS servers or manually specify
    //                 the LDAP servers for this domain below."));
    //     }
    //     else {
    //         if (!$config['servers'])
    //             $this->getForm()->getField('servers')->addError(
    //                 $__("No servers specified. Either specify a Active Directory
    //                 domain or a list of servers"));
    //         else {
    //             $servers = array();
    //             foreach (preg_split('/\s+/', $config['servers']) as $host)
    //                 if (preg_match('/([^:]+):(\d{1,4})/', $host, $matches))
    //                     $servers[] = array('host' => $matches[1], 'port' => (int) $matches[2]);
    //                 else
    //                     $servers[] = array('host' => $host);
    //         }
    //     }
    //     $connection_error = false;
    //     foreach ($servers as $info) {
    //         // Assume MSAD
    //         $info['options']['LDAP_OPT_REFERRALS'] = 0;
    //         if ($config['tls']) {
    //             $info['starttls'] = true;
    //             // Don't require a certificate here
    //             putenv('LDAPTLS_REQCERT=never');
    //         }
    //         if ($config['bind_dn']) {
    //             $info['binddn'] = $config['bind_dn'];
    //             $info['bindpw'] = $config['bind_pw']
    //                 ? $config['bind_pw']
    //                 : Crypto::decrypt($this->get('bind_pw'), SECRET_SALT,
    //                     $this->getNamespace());
    //         }
    //         // Set reasonable timeouts so we dont exceed max_execution_time
    //         $info['options'] = array(
    //             'LDAP_OPT_TIMELIMIT' => 5,
    //             'LDAP_OPT_NETWORK_TIMEOUT' => 5,
    //         );
    //         $c = new Net_LDAP2($info);
    //         $r = $c->bind();
    //         if (PEAR::isError($r)) {
    //             if (false === strpos($config['bind_dn'], '@')
    //                     && false === strpos($config['bind_dn'], ',dc=')) {
    //                 // Assume Active Directory, add the default domain in
    //                 $config['bind_dn'] .= '@' . $config['domain'];
    //                 $info['bind_dn'] = $config['bind_dn'];
    //                 $c = new Net_LDAP2($info);
    //                 $r = $c->bind();
    //             }
    //         }
    //         if (PEAR::isError($r)) {
    //             $connection_error = sprintf($__(
    //                 '%s: Unable to bind to server %s'),
    //                 $r->getMessage(), $info['host']);
    //         }
    //         else {
    //             $connection_error = false;
    //             break;
    //         }
    //     }
    //     if ($connection_error) {
    //         $this->getForm()->getField('servers')->addError($connection_error);
    //         $errors['err'] = $__('Unable to connect any listed LDAP servers');
    //     }

    //     if (!$errors && $config['bind_pw'])
    //         $config['bind_pw'] = Crypto::encrypt($config['bind_pw'],
    //             SECRET_SALT, $this->getNamespace());
    //     else
    //         $config['bind_pw'] = $this->get('bind_pw');

    //     global $msg;
    //     if (!$errors)
    //         $msg = $__('LDAP configuration updated successfully');

    //     return !$errors;
    // }
}

?>
