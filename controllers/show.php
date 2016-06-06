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
            $this->search->query($this->query, $this->getFilterArray());
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

    /**
     *
     */
    private function addSearchSidebar() {
        $sidebar = Sidebar::get();

        // add some text
        $sidebar->addWidget($this->getInfoWidget());
        $sidebar->addWidget($this->getOptionsWidget());

        var_dump($_SESSION['global_search']['show']);
        // On develop display runtime
        if (Studip\ENV == 'development' && $this->search->time && $GLOBALS['perm']->have_perm('admin')) {
            $sidebar->addWidget($this->getRuntimeWidget());
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

    private function getInfoWidget()
    {
        $info_widget = new InfoboxWidget();
        $info_widget->setTitle(_('Information'));
        $info_widget->addElement(new InfoboxElement(_('Suchen Sie nach Veranstaltungen, Personen, Dateien, Einrichtungen, Räumen, Forenpostings und Wiki-Einträgen.'), Icon::create('info')));
        return $info_widget;
    }

    private function getOptionsWidget()
    {
        $options_widget = new OptionsWidget;
        $options_widget->setTitle(_('Ergebnisse'));
        if ($this->search->count) {
            $_SESSION['global_search']['show']['all']  = Request::option('show') === 'all' ? true : false;
            $_SESSION['global_search']['show']['some'] = false;
            $options_widget->addCheckbox(_('Alle') . " ({$this->search->count})",
                $_SESSION['global_search']['show']['all'],
                URLHelper::getURL('', array("search" => $this->search->query, "show" => "all")),
                URLHelper::getURL('', array("search" => $this->search->query, "show" => "not_all")));
        }

        foreach ($this->search->resultTypes as $type => $results) {
            if (Request::option('show') === $type) {
                $_SESSION['global_search']['show'][$type] = true;
            } elseif (Request::option('show') === $type . "_off") {
                $_SESSION['global_search']['show'][$type] = false;
            }
            if ($_SESSION['global_search']['show'][$type] === true && $_SESSION['global_search']['show']['all'] === false) {
                $_SESSION['global_search']['show']['some'] = true;
            }
            $options_widget->addCheckbox(IntelligentSearch::getTypeName($type) . " ($results)",
                $_SESSION['global_search']['show'][$type],
                //$this->url_for('admin/courses/set_view_filter/' . $type . '/' . $state),
                URLHelper::getURL('', array("search" => $this->search->query, "filter" => $this->getFilterArray(), "show" => $type)),
                URLHelper::getURL('', array("search" => $this->search->query, "filter" => $this->getFilterArray(), "show" => $type."_off")));
        }
        return $options_widget;
    }

    private function getRuntimeWidget()
    {
        $runtime_widget = new SidebarWidget();
        $runtime_widget->setTitle(_('Laufzeit'));
        $runtime_widget->addElement(new InfoboxElement($this->search->time));
        return $runtime_widget;
    }

    public function getFilterArray()
    {
        $filters = array();
        foreach ($_SESSION['global_search']['show'] as $i => $value) {
            if ($_SESSION['global_search']['show'][$i]) {
                array_push($filters, $i);
            }
        }
        return $filters;
    }

    /**
     * Set the selected search filter and store the selection in configuration
     */
    public function set_search_filter_action($filter = null, $state = true)
    {
        // store view filter in configuration
        if (!is_null($filter)) {
            $filters = $this->getFilterConfig();

            if ($state) {
                $filters = array_diff($filters, array($filter));
            } else {
                $filters[] = $filter;
            }

            $this->setFilterConfig($filters);
        }

        $this->redirect('admin/courses/index');
    }
}
