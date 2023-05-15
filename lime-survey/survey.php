<?php

require_once (INCLUDE_DIR . 'class.plugin.php');
require_once (INCLUDE_DIR . 'class.signal.php');
require_once (INCLUDE_DIR . 'class.app.php');
require_once (INCLUDE_DIR . 'class.dispatcher.php');
require_once (INCLUDE_DIR . 'class.dynamic_forms.php');
require_once (INCLUDE_DIR . 'class.osticket.php');
require_once(INCLUDE_DIR . 'class.ticket.php');
require_once('config.php');

class EnrollInSurvey extends Ticket {

}

class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyConfig';

    function bootstrap() {
        $config = $this->getConfig();
        $server = $config->getServerSettings();
        Signal::connect('ticket.created', function($ticket){ 
            $this->enrrollTicketRequesterInLimeSurvey($server);
        });
    }
    
    function enrrollTicketRequesterInLimeSurvey($server) {       
        // Create a JsonRPCClient object to connect to LimeSurvey API
        $client = new JsonRPCClient($server['domain'].'/index.php/admin/remotecontrol');
        $sessionKey = $client->get_session_key($server['user'], $server['passwd']);

        // Retrieve the survey ID from configuration
        $surveyId = $server['surveyid'];

        // Add the ticket requester as a participant in the survey
        // $participants = array(
        //     'email' => $ticket->getEmail(),
        //     'firstname' => $ticket->getName(),
        //     'lastname' => 'Second'
        // );
        $participants = array(
            "email"=>"raulnsuarez@gmail.com",
            "lastname"=>"Suarez",
            "firstname"=>"Raul"
        );
        $result = $client->add_participants($sessionKey, $surveyId, array($participants));
        if ($result === null) {
            // An error occurred while adding the participant
            //$ticket->logError('Failed to enroll ticket requester in LimeSurvey');
        }else{
            // Save the survey response ID in the ticket metadata
            //$ticket->addNote("LimeSurvey response ID: $result");
            //$ticket->logError('Successfully enrolled ticket requester in LimeSurvey');
        }

        // Release the LimeSurvey API session key
        $client->release_session_key($sessionKey);
    }
}

?>
