<?php

class ShowController extends StudipController {

    public function __construct($dispatcher) {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function before_filter(&$action, &$args) {

        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));

        // Find query
        $this->query = Request::get('utf8') ? studip_utf8decode(Request::get('search')) : Request::get('search');
    }

    public function index_action() {

        $this->createSidebar();

        if (Request::submitted('search')) {
            $this->search = new IntelligentSearch();
            $this->search->query($this->query, Request::get('filter'));
        }
        $this->addSearchSidebar();
    }

    public function open_action($id) {
        $stmt = DBManager::get()->prepare('SELECT * FROM search_object WHERE object_id = ? LIMIT 1');
        $stmt->execute(array($id));
        $location = $GLOBALS['ABSOLUTE_URI_STUDIP'].IntelligentSearch::getLink($stmt->fetch(PDO::FETCH_ASSOC));
        header("location: $location");die;
    }

    public function fast_action($restriction = null) {
        $GLOBALS['perm']->check('root');
        $this->time = IndexManager::sqlIndex($restriction);
        $this->redirect('show/index');
    }

    private function createSidebar() {
        $sidebar = Sidebar::get();
        $sidebar->setImage('sidebar/search-sidebar.png');

        // Root may update index
        if ($GLOBALS['perm']->have_perm('root')) {
            $actions = new ActionsWidget();
            $actions->addLink(_('Indizieren'), $this->url_for('show/fast'));
            $sidebar->addWidget($actions);
        }
    }

    private function addSearchSidebar() {
        $sidebar = Sidebar::get();
        $widget = new LinksWidget;
        $widget->setTitle(_('Ergebnisse'));
        if ($this->search->count) {
            $widget->addLink(_('Alle') . " ({$this->search->count})", URLHelper::getURL('', array("search" => $this->search->query)), !$this->search->filter ? 'icons/16/black/arr_1right.png' : '');
        }
        foreach ($this->search->resultTypes as $type => $results) {
            $widget->addLink(IntelligentSearch::getTypeName($type) . " ($results)", URLHelper::getURL('', array("search" => $this->search->query, "filter" => $type)), $type == $this->search->filter ? 'icons/16/black/arr_1right.png' : '');
        }

        $sidebar->addWidget($widget);

        // On develop display runtime
        if (Studip\ENV == 'development' && $GLOBALS['perm']->have_perm('admin')) {
            $info = new SidebarWidget();
            $info->setTitle(_('Laufzeit'));
            $info->addElement(new InfoboxElement($this->search->time));
            $sidebar->addWidget($info);
        }
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
