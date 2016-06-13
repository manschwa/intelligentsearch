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
        if ($this->query || Request::submitted('search')) {
            if ($_SESSION['global_search']['query'] !== $this->query) {
                $this->resetFilter();
            }
            $_SESSION['global_search']['query'] = $this->query;
        }
    }

    public function index_action() {

        $this->createSidebar();

        if ($_SESSION['global_search']['query']) {
            $this->search = new IntelligentSearch();
            $this->search->query($_SESSION['global_search']['query'], $this->getCategoryFilter());
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
        $sidebar->addWidget($this->getCategoryWidget());
        $sidebar->addWidget($this->getFacetsWidget());

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

    /**
     * Build a LinksWidget for the sidebar to filter out a specific category from your search results.
     * There should only be one category selected at a time.
     *
     * @return LinksWidget containing all categories included in the search result.
     */
    private function getCategoryWidget()
    {
        $category_widget = new LinksWidget();
        $category_widget->setTitle(_('Ergebnisse') . " ({$this->search->count})");

        if ($this->getCategoryFilter()) {
            $reset_element = new LinkElement(_('Auswahl aufheben'), $this->url_for('show/reset_search_filter'));
            $category_widget->addElement($reset_element);
        }
        foreach ($this->search->resultTypes as $type => $results) {
            $category_widget->addLink(IntelligentSearch::getTypeName($type) . " ($results)",
                $this->url_for('show/set_category_filter/' . $type),
                $_SESSION['global_search']['category'] === $type ? Icon::create('arr_1right') : '');
        }
        return $category_widget;
    }

    /**
     * Build an OptionsWidget for the sidebar to choose category specific filters for your search results.
     * The filter options shown depend on the chosen category.
     * There can be more than one filter selected per category.
     *
     * @return OptionsWidget containing category specific filter options.
     */
    private function getFacetsWidget()
    {
        $options_widget = new OptionsWidget;
        $options_widget->setTitle(_('Filtern nach') . " ({$this->search->count})");

        if ($this->getFilterArray()) {
            $reset_element = new LinkElement(_('Auswahl aufheben'), $this->url_for('show/reset_search_filter'));
            $options_widget->addElement($reset_element);
        }
        foreach ($this->search->resultTypes as $type => $results) {
            $options_widget->addCheckbox(IntelligentSearch::getTypeName($type) . " ($results)",
                $_SESSION['global_search']['show'][$type],
                $this->url_for('show/set_search_filter/' . $type . '/' . true),
                $this->url_for('show/set_search_filter/' . $type . '/' . false));
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

    /**
     * Getting the category filter type that should be shown in the search.
     *
     * @return String: category type
     */
    public function getCategoryFilter()
    {
        return $_SESSION['global_search']['category'];
    }

    public function getFilterArray()
    {
        $filters = array();
        foreach ($_SESSION['global_search']['show'] as $type => $value) {
            if ($_SESSION['global_search']['show'][$type]) {
                array_push($filters, $type);
            }
        }
        return $filters;
    }

    /**
     * Set the selected search filter and store the selection in the $_SESSION variable
     */
    public function set_search_filter_action($filter = null, $state = true)
    {
        // store view filter in $_SESSION
        if (!is_null($filter)) {
            $_SESSION['global_search']['show'][$filter] = (bool) $state;
        }

        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    public function set_category_filter_action($category = null)
    {
        // store category filter in $_SESSION
        if (!is_null($category)) {
            $_SESSION['global_search']['category'] = $category;
        }

        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    public function reset_search_filter_action() {
        $this->resetFilter();
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    private function resetFilter()
    {
        foreach ($_SESSION['global_search']['show'] as $type => $value) {
            $_SESSION['global_search']['show'][$type] = (bool) false;
        }
        $_SESSION['global_search']['category'] = null;
    }
}
