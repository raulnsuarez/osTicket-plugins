<?php

require_once (INCLUDE_DIR . 'class.plugin.php');
require_once (INCLUDE_DIR . 'class.signal.php');
require_once (INCLUDE_DIR . 'class.app.php');
require_once (INCLUDE_DIR . 'class.dispatcher.php');
require_once (INCLUDE_DIR . 'class.dynamic_forms.php');
require_once (INCLUDE_DIR . 'class.osticket.php');
require_once(INCLUDE_DIR . 'class.ticket.php');
require_once('config.php');

class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyConfig';

    function enrrollTicketRequesterInLimeSurvey($ticket) {
        // Get config
        global $config;
        $server = $config->getServer();
        $username = $config->getUser();
        $password = $config->getPasswd();
        $survey = $config->getSurveyID();
        
        // Create a JsonRPCClient object to connect to LimeSurvey API
        $client = new JsonRPCClient($server.'/index.php/admin/remotecontrol');
        $sessionKey = $client->get_session_key($username, $password);

        // Add the ticket requester as a participant in the survey
        $ticket_email = $ticket->getEmail();
        $ticket_name = $ticket->getName();
        $participants = array(
            'email' => 'aaa@sd.com',
            'firstname' => $ticket_name,
            'lastname' => 'Second'
        );
        // $participants = array(
        //     "email"=>"raulnsuarez@gmail.com",
        //     "lastname"=>"Suarez",
        //     "firstname"=>"Raul"
        // );
        $result = $client->add_participants($sessionKey, $survey, array($participants), array('id'=>1));
        if ($result === null) {
            // An error occurred while adding the participant
            //$ticket->logError('Failed to enroll ticket requester in LimeSurvey');
        }else{
            // Save the survey response ID in the ticket metadata
            //$ticket->postNote(array('note'=>"LimeSurvey response ID: ". $result, 'title'=>'User enrrolled in Survey', array(), $ticket->getStaff()));
            //$ticket->logError('Successfully enrolled ticket requester in LimeSurvey');
        }

        // Release the LimeSurvey API session key
        $client->release_session_key($sessionKey);
    }

    function bootstrap() {
        global  $config;
        $config = $this->getConfig();
        Signal::connect('ticket.created', function($ticket, &$extras){
            $this->enrrollTicketRequesterInLimeSurvey($ticket);
        });
    }
}

?>
