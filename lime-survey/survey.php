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

class ClosedTicketSignal extends Ticket {
    function setStatus($status, $comments='', &$errors=array(), $set_closing_agent=true, $force_close = false) {
        //Adding Signal close at the end of the closed tickets
        throw new Exception(var_export($this->getId(), true));
    }
}


class LimeSurveyPlugin extends Plugin {
    var $config_class = 'LimeSurveyConfig';
    const PLUGIN_NAME = 'Automatic Surveys for Tickets';

    private function enrrollTicketRequesterInLimeSurvey($email, $firstname, $lastname) {
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
        $participants = array(
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname
        );

        $result = $client->add_participants($sessionKey, $survey, array($participants), array('id'=>1));
        if ($result === null) {
            return false;
        }else{
            // Release the LimeSurvey API session key
            $client->release_session_key($sessionKey);
            return true;
        }
    }

    // function handleTicketStatusChange($ticket, $status) {
    //     if ($status === 'closed') {
    //         Signal::send('ticket.close', $ticket);
    //     }
    // }

    function bootstrap() {
        global  $config;
        $config = $this->getConfig();

        // Signal::connect('ticket.setStatus', array($this, 'handleTicketStatusChange'));

        Signal::connect('ticket.closed', function($ticket){
            global $config;
            // Add the ticket requester as a participant in the survey
            $email = $ticket->getEmail()->getEmail();
            $name = $ticket->getName();
            $firstname = $name->getFirst();
            $lastname = $name->getLast();
            //throw new Exception(var_export($email, true));
            $result = $this->enrrollTicketRequesterInLimeSurvey($email, $firstname, $lastname);

            if ($result){
                // Save the survey response ID in the ticket metadata
                $ticket->LogNote(__('Enrolled in Survey with email: '. $email),__('Successfully enrolled ticket requester in LimeSurvey #'. $config->getSurveyID()), self::PLUGIN_NAME, FALSE );
            }else{
                // An error occurred while adding the participant
                $ticket->LogNote(__('Error in survey enrollment process for email: '. $email),__('Failed to enroll ticket requester in LimeSurvey #'. $config->getSurveyID()), self::PLUGIN_NAME, FALSE );
            }

        });


        Signal::connect('ticket.created', function($ticket){
            global $config;
            // Add the ticket requester as a participant in the survey
            $email = $ticket->getEmail()->getEmail();
            $name = $ticket->getName();
            $firstname = $name->getFirst();
            $lastname = $name->getLast();
            //throw new Exception(var_export($email, true));
            $result = $this->enrrollTicketRequesterInLimeSurvey($email, $firstname, $lastname);

            if ($result){
                // Save the survey response ID in the ticket metadata
                $ticket->LogNote(__('Enrolled in Survey with email: '. $email),__('Successfully enrolled ticket requester in LimeSurvey #'. $config->getSurveyID()), self::PLUGIN_NAME, FALSE );
            }else{
                // An error occurred while adding the participant
                $ticket->LogNote(__('Error in survey enrollment process for email: '. $email),__('Failed to enroll ticket requester in LimeSurvey #'. $config->getSurveyID()), self::PLUGIN_NAME, FALSE );
            }

        });
    }
}

?>
