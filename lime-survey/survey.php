<?php

require_once (INCLUDE_DIR . 'class.plugin.php');
require_once (INCLUDE_DIR . 'class.signal.php');
require_once (INCLUDE_DIR . 'class.app.php');
require_once (INCLUDE_DIR . 'class.dispatcher.php');
require_once (INCLUDE_DIR . 'class.dynamic_forms.php');
require_once (INCLUDE_DIR . 'class.osticket.php');
require_once (INCLUDE_DIR . 'class.ticket.php');
require_once ('jsonRPC.php');
require_once ('config.php');

class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyConfig';
    const PLUGIN_NAME = 'Automatic Surveys for Tickets';

    private function enrrollTicketRequesterInLimeSurvey($email, $firstname, $lastname) {
        // Get config
        global $config;
        $server = $config->getServer();
        $username = $config->getUser();
        $password = Crypto::decrypt($config->getPasswd(), SECRET_SALT, $config->getKey()); 
        $survey = $config->getSurveyID();
        
        // Create a JsonRPCClient object to connect to LimeSurvey API
        $client = new JsonRPCClient('https://'.$server.'/index.php/admin/remotecontrol');
        $sessionKey = $client->get_session_key($username, $password);
        //Check session Key
        if (is_array($sessionKey)){
            if ($sessionKey['status']){
                return array('status'=> 'ERROR', 'response'=> $sessionKey['status']);
            }
        }

        // Add the ticket requester as a participant in the survey
        $participants = array(
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname
        );

        $result = $client->add_participants($sessionKey, $survey, array($participants), array('id'=>1));
        // Release the LimeSurvey API session key
        $client->release_session_key($sessionKey);
        return array('status'=> 'SESSION','response'=> $result);
        // if ($result === null) {
        //     return false;
        // }else{
        // }
    }

    function bootstrap() {
        global  $config;
        $config = $this->getConfig();
        $event = $config->getEvent();

        Signal::connect($event, function($ticket){
            global $config;
            // Add the ticket requester as a participant in the survey
            $email = $ticket->getEmail()->getEmail();
            $name = $ticket->getName();
            $firstname = $name->getFirst();
            $lastname = $name->getLast();
            //throw new Exception(var_export($email, true));
            $result = $this->enrrollTicketRequesterInLimeSurvey($email, $firstname, $lastname);
            // Save the survey response ID in the ticket metadata
            if ($result['status'] != 'ERROR'){
                $ticket->LogNote(
                    __('Enrollment in LimeSurvey #'. $config->getSurveyID()),
                    __(json_encode($result['response'])),
                    self::PLUGIN_NAME,
                    FALSE
                );
            }else{
                global $msg;
                $msg = $__(json_encode($result['response']));
            }
            
        });
    }
}

?>
