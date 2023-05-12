<?php

/**
 */
require_once(INCLUDE_DIR.'class.plugin.php');
require_once(INCLUDE_DIR.'class.ticket.php');
require_once('config.php');

 class EnrollInSurvey extends Ticket {

    private function connectToSurvey() 
    {
        $config = LimeSurveyPlugin::getConfig();
        $ticketJSONRPCClient = new JsonRPCClient($config['domain'].'/index.php/admin/remotecontrol');
        $sessionKey = $ticketJSONRPCClient->get_session_key($config['user'], $config['passwd']);
        if (is_array($sessionKey)) 
        {
            return $sessionKey['status'];
        } 
        else 
        {
            $participants = array('{"email":"raulnsuarez@gmail.com","lastname":"Suarez","firstname":"Raul"}');
            $ticketJSONRPCClient->add_participants($sessionKey,$config['surveyid'],$participants);
            $ticketJSONRPCClient->release_session_key($sessionKey);
        }
    }

    public function triggerEnrollment () 
    {
        if(static::isClosed())
        {
            $this->connectToSurvey();
        }
    }

    /**
     * Registers a callback function for the ticket.closed event
     */
    // public static function registerCallback() {
    //     self::register( 'EnrollInSurvey', 'ticket.closed', function($ticket) {
    //         // Retrieve the LimeSurvey server settings from configuration
    //         $config = Plugin::getConfig('limesurvey');
    //         $server = $config->getServerSettings();

    //         // Create a JsonRPCClient object to connect to LimeSurvey API
    //         $client = new JsonRPCClient($server['domain'].'/index.php/admin/remotecontrol');
    //         $sessionKey = $client->get_session_key($server['user'], $server['passwd']);

    //         // Retrieve the survey ID from configuration
    //         $surveyId = $config->getSurveyID();

    //         // Add the ticket requester as a participant in the survey
    //         $participants = array(
    //             'email' => $ticket->getEmail(),
    //             'firstname' => $ticket->getName(),
    //             'lastname' => ''
    //         );
    //         $result = $client->add_participants($sessionKey, $surveyId, array($participants));
    //         if ($result === null) {
    //             // An error occurred while adding the participant
    //             $ticket->logError('Failed to enroll ticket requester in LimeSurvey');
    //         }

    //         // Release the LimeSurvey API session key
    //         $client->release_session_key($sessionKey);
    //     } );
    // }
}

class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyConfig';
    public function bootstrap() 
    {
        //EnrollInSurvey::registerCallback();
        Ticket::register('SurveyEnrollment','triggerEnrollment');

    }
}
