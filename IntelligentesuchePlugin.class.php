<?php

require 'bootstrap.php';

/**
 * IntelligentesuchePlugin.class.php
 */
class IntelligentesuchePlugin extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();
        $this->setupAutoload();
        $navigation = new AutoNavigation(_('Suche'));
        $navigation->setURL(PluginEngine::GetURL($this, array(), 'show/index'));
        Navigation::addItem('/search/suche', $navigation);
        
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/intelligentsearch.css');
        PageLayout::addScript($this->getPluginURL() . '/assets/intelligentsearch.js');

        // Quicksearchhook
        PageLayout::addBodyElements(QuickSearch::get("seminar", new IntelligentSearch())
                ->setAttributes(array("placeholder" => _(Suchen)))
                ->setInputClass("quicksearchbox intelligentsearch")
                ->fireJSFunctionOnSelect('function (loc, name) {window.location = STUDIP.URLHelper.getURL(loc)}')
                ->render());
    }

    public function initialize() {

    }

    public function perform($unconsumed_path) {

        $dispatcher = new Trails_Dispatcher(
                $this->getPluginPath(), rtrim(PluginEngine::getLink($this, array(), null), '/'), 'show'
        );
        $dispatcher->plugin = $this;
        $dispatcher->dispatch($unconsumed_path);
    }

    private function setupAutoload() {
        if (class_exists("StudipAutoloader")) {
            StudipAutoloader::addAutoloadPath(__DIR__ . '/models');
        } else {
            spl_autoload_register(function ($class) {
                include_once __DIR__ . $class . '.php';
            });
        }
    }

}
