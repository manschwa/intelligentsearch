<?php

class IntelligentesucheCronjob extends CronJob {

    public static function getName() {
        return _('Suchindex erneuern');
    }

    public static function getDescription() {
        return _('Aktualisiert den Suchindex der intelligenten Suche');
    }

    public static function getParameters() {
        return array(
            'verbose' => array(
                'type' => 'boolean',
                'default' => false,
                'status' => 'optional',
                'description' => _('Sollen Ausgaben erzeugt werden'),
            ),
        );
    }

    public function setUp() {
        
    }

    public function execute($last_result, $parameters = array()) {
        foreach (glob(__DIR__ . '/models/*') as $filename) {
            require_once $filename;
        }
        IndexManager::sqlIndex();
    }

    public function tearDown() {
        
    }

}
