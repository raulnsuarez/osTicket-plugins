<?php

require_once(INCLUDE_DIR . 'class.plugin.php');
require_once('config.php');

// class EnrollInSurvey extends Ticket {
//     /**
//      * Registers a callback function for the ticket.closed event
//      */
//     function registerCallback() {
//         Ticket::setStatus( 'closed', function() {
//             // Retrieve the LimeSurvey server settings from configuration
//             $config = Plugin::getConfig('limesurvey');
//             $server = $config->getServerSettings();

//             // Create a JsonRPCClient object to connect to LimeSurvey API
//             $client = new JsonRPCClient($server['domain'].'/index.php/admin/remotecontrol');
//             $sessionKey = $client->get_session_key($server['user'], $server['passwd']);

//             // Retrieve the survey ID from configuration
//             $surveyId = $config->getSurveyID();

//             // Add the ticket requester as a participant in the survey
//             // $participants = array(
//             //     'email' => $ticket->getEmail(),
//             //     'firstname' => $ticket->getName(),
//             //     'lastname' => 'Second'
//             // );
//             $participants = array(
//                 "email"=>"raulnsuarez@gmail.com",
//                 "lastname"=>"Suarez",
//                 "firstname"=>"Raul"
//             );
//             $result = $client->add_participants($sessionKey, $surveyId, array($participants));
//             if ($result === null) {
//                 // An error occurred while adding the participant
//                 $ticket->logError('Failed to enroll ticket requester in LimeSurvey');
//             }else{
//                 $ticket->logError('Successfully enrolled ticket requester in LimeSurvey');
//             }

//             // Release the LimeSurvey API session key
//             $client->release_session_key($sessionKey);
//         } );
//     }
// }

class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyConfig';

    function bootstrap() {
        // Hook into the 'setStatus' event
        Dispatcher::getInstance()->register(
            'ticket.setStatus',
            array($this, 'onTicketStatusChange')
        );
    }

    function onTicketStatusChange($ticket, $oldStatus, $newStatus, $user) {
        // Check if the new status is 'closed'
        if ($newStatus == TicketStatus::CLOSED) {
            // Call the 'enrollment' function
            $this->enrrollTicketRequesterInLimeSurvey($ticket);
        }
    }

    public function enrrollTicketRequesterInLimeSurvey($ticket) {
        // Get the LimeSurvey API endpoint URL and API key from your configuration
        $server = $this->getConfig()->getServerSettings();
        
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
            $ticket->logError('Failed to enroll ticket requester in LimeSurvey');
        }else{
            // Save the survey response ID in the ticket metadata
            $ticket->addNote("LimeSurvey response ID: $result");
            $ticket->logError('Successfully enrolled ticket requester in LimeSurvey');
        }

        // Release the LimeSurvey API session key
        $client->release_session_key($sessionKey);
    }
};

?>
