<?php

class ShowController extends StudipController {

    public function __construct($dispatcher) {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function before_filter(&$action, &$args) {

        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));
//      PageLayout::setTitle('');
    }

    public function index_action() {
        if (Request::submitted('search')) {
            $time = microtime(1);
            $this->query = Request::get('search');
            $this->results = SearchIndex::search($this->query);
            $this->counter = SearchIndex::count($this->query);
            $this->time = microtime(1)-$time;
        }
    }
    
    public function fast_action() {
        $this->time = IndexManager::fast();
    }

    public function create_action($type = null) {
        if ($type) {
            $this->msg = IndexManager::index($type);
        } else {
            $this->msg = "Update finished after: ".IndexManager::indexAll()." Seconds";
        }
    }
    
    public function update_action() {
        if (IndexManager::update()) {
            $this->msg = ("Exhausted! Run again please!");
        } else {
            $this->msg = ("Update done!");
        }
        $this->render_action('create');
    }
    
    public function reset_action() {
        DBManager::get()->query('TRUNCATE TABLE search_index');
        DBManager::get()->query('TRUNCATE TABLE search_object');
        $this->render_action('create');
    }

    // customized #url_for for plugins
    function url_for($to) {
        $args = func_get_args();

        # find params
        $params = array();
        if (is_array(end($args))) {
            $params = array_pop($args);
        }

        # urlencode all but the first argument
        $args = array_map('urlencode', $args);
        $args[0] = $to;

        return PluginEngine::getURL($this->dispatcher->plugin, $params, join('/', $args));
    }

}
