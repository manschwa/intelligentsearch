<?php

require 'bootstrap.php';

/**
 * IntelligentesuchePlugin.class.php
 */
class IntelligentesuchePlugin extends StudIPPlugin implements SystemPlugin {

    public function __construct() {
        parent::__construct();
        $this->setupAutoload();
        $navigation = new AutoNavigation(_('Globale Suche'));
        $navigation->setURL(PluginEngine::GetURL($this, array(), 'show/index'));
        
        //Insert even before courses search
        Navigation::insertItem('/search/suche', $navigation, 'courses');
        
        // Take over search button
        Navigation::getItem('/search')->setURL(PluginEngine::GetURL($this, array(), 'show/index'));
        
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/intelligentsearch.css');
        PageLayout::addScript($this->getPluginURL() . '/assets/intelligentsearch.js');

        // Quicksearchhook
        PageLayout::addBodyElements(QuickSearch::get("seminar", new IntelligentSearch())
                ->setAttributes(array("placeholder" => _(Suchen)))
                ->setInputClass("quicksearchbox intelligentsearch")
                ->fireJSFunctionOnSelect('function (loc, name) {window.location = STUDIP.URLHelper.getURL("plugins.php/intelligentesucheplugin/show/open/"+loc)}')
                ->render());
        // Notifications for Users
        NotificationCenter::addObserver(new IndexObject_User, "insert", "UserDidCreate");
        NotificationCenter::addObserver(new IndexObject_User, "update", "UserDidUpdate");
        NotificationCenter::addObserver(new IndexObject_User, "delete", "UserDidDelete");
        // Notifications for Courses
        NotificationCenter::addObserver(new IndexObject_Seminar, "insert", "CourseDidCreate");
        NotificationCenter::addObserver(new IndexObject_Seminar, "update", "CourseDidUpdate");
        NotificationCenter::addObserver(new IndexObject_Seminar, "delete", "CourseDidDelete");
        // Notifications for Documents
        NotificationCenter::addObserver(new IndexObject_Document, "insert", "DocumentDidCreate");
        NotificationCenter::addObserver(new IndexObject_Document, "update", "DocumentDidUpdate");
        NotificationCenter::addObserver(new IndexObject_Document, "delete", "DocumentDidDelete");
        // Notifications for Institutes
        NotificationCenter::addObserver(new IndexObject_Institute, "insert", "InstituteDidCreate");
        NotificationCenter::addObserver(new IndexObject_Institute, "update", "InstituteDidUpdate");
        NotificationCenter::addObserver(new IndexObject_Institute, "delete", "InstituteDidDelete");
        // Notifications for Forumentries
        NotificationCenter::addObserver(new IndexObject_Forumentry, "insert", "ForumAfterInsert");
        NotificationCenter::addObserver(new IndexObject_Forumentry, "update", "ForumAfterUpdate");
        NotificationCenter::addObserver(new IndexObject_Forumentry, "delete", "ForumBeforeDelete");
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

    public static function onEnable($pluginId) {
        parent::onEnable($pluginId);
        $_SESSION['global_search'] = array();
    }

    public static function onDisable($pluginId) {
        parent::onDisable($pluginId);
        $_SESSION['global_search'] = array();
    }

}
