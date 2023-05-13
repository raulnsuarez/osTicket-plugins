<?php

class LimeSurveyConfig extends PluginConfig {
    function getServerSettings() {
        return array(
            'domain' => 'https://survey.test.qsoc.org',
            'user' => 'admin',
            'passwd' => 'PcDepot.305@'
        );
    }

    function getSurveyID() {
        return '775959';
    }

    function getOptions() {
        return array(
            'server_settings' => new SectionBreakField(array(
                'label' => 'LimeSurvey Server Settings'
            )),
            'domain' => new TextboxField(array(
                'label' => 'LimeSurvey Domain',
                'required' => true
            )),
            'user' => new TextboxField(array(
                'label' => 'LimeSurvey API User',
                'required' => true
            )),
            'passwd' => new TextboxField(array(
                'label' => 'LimeSurvey API Key',
                'required' => true
            )),
            'survey_id' => new TextboxField(array(
                'label' => 'LimeSurvey Survey ID',
                'required' => true
            ))
        );
    }
}
