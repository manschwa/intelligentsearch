<?php

class ShowController extends StudipController
{

    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
    }

    public function before_filter(&$action, &$args)
    {
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

    public function index_action()
    {
        if ($_SESSION['global_search']['query']) {
            $this->search = new IntelligentSearch();
            $this->search->query($_SESSION['global_search']['query'], $this->getCategoryFilter());
        }
        $this->addSearchSidebar();
    }

    public function fast_action($restriction = null)
    {
        $GLOBALS['perm']->check('root');
        $this->time = IndexManager::sqlIndex($restriction);
        $this->redirect('show/index');
    }

    /**
     *
     */
    private function addSearchSidebar()
    {
        $sidebar = Sidebar::get();
        $sidebar->setImage('sidebar/search-sidebar.png');

        // add some text
        $sidebar->addWidget($this->getCategoryWidget());
        if ($type = $_SESSION['global_search']['category']) {
            $sidebar->addWidget($this->getFacetsWidget($type));
        }

        // Root may update index
        if ($GLOBALS['perm']->have_perm('root')) {
            $actions = new ActionsWidget();
            $actions->addLink(_('Indizieren'), $this->url_for('show/fast'));
            $sidebar->addWidget($actions);
        }

        // On develop display runtime
        if (Studip\ENV == 'development' && $this->search->time && $GLOBALS['perm']->have_perm('admin')) {
            $sidebar->addWidget($this->getRuntimeWidget());
        }
    }

    // customized #url_for for plugins
    function url_for($to)
    {
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

        // offer a reset options only if there is a category selected
        if ($this->getCategoryFilter()) {
            $reset_element = new LinkElement(_('Auswahl aufheben'), $this->url_for('show/reset_category_filter'));
            $category_widget->addElement($reset_element);
        }
        // list all categories included in the result set as Links
        foreach (IntelligentSearch::getIndexObjectTypes() as $type) {
            $category_widget->addLink(IntelligentSearch::getTypeName($type),// . " ($results)",
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
     * @param $type string
     * @return OptionsWidget containing category specific filter options.
     */
    private function getFacetsWidget($type)
    {
        $options_widget = new OptionsWidget;
        $options_widget->setTitle(_('Filtern nach'));
        $filter_options = $this->getFilters($type);

        if ($this->getActiveFilters($type)) {
            $reset_element = new LinkElement(_('Auswahl aufheben'), $this->url_for('show/reset_filter'));
            $options_widget->addElement($reset_element);
        }

        foreach ($filter_options as $filter) {
            $options_widget->addCheckbox($filter,
                $_SESSION['global_search']['filters'][$filter],
                $this->url_for('show/set_filter/' . $type . '/' . $filter . '/' . true),
                $this->url_for('show/set_filter/' . $type . '/' . $filter . '/' . false));
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
     * Getting the category type that should be shown in the search.
     *
     * @return String: category type
     */
    public function getCategoryFilter()
    {
        return $_SESSION['global_search']['category'];
    }

    /**
     * @param $type string: category
     * @return array containing all the possible filters for the given category type.
     */
    private function getFilters($type)
    {
            return IntelligentSearch::getFilterOptions($type);
    }

    /**
     * Retruns the active filter options for the given category type chosen by the user.
     *
     * @param $type string: category type
     * @return array containing only the checked/active filters for the given category.
     */
    private function getActiveFilters($type)
    {
        $filters = array();
        foreach ($_SESSION['global_search']['filters'] as $filter => $value) {
            if ($_SESSION['global_search']['filters'][$filter]) {
                array_push($filters, $filter);
            }
        }
        return $filters;
    }

    /**
     * Set the selected category specific search filter and store the selection in the $_SESSION variable.
     *
     * @param $type string
     * @param null $filter string
     * @param bool $state
     * @throws Trails_DoubleRenderError
     */
    public function set_filter_action($type, $filter = null, $state = true)
    {
        // store view filter in $_SESSION
        if (!is_null($type) && !is_null($filter)) {
            $_SESSION['global_search']['filters'][$filter] = (bool)$state;
        }
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    /**
     * Set the category (highest level of the search) that should be searched for.
     *
     * @param null $category string: category type
     * @throws Trails_DoubleRenderError
     */
    public function set_category_filter_action($category = null)
    {
        // store category filter in $_SESSION
        if (!is_null($category)) {
            $this->resetFilter();
            $_SESSION['global_search']['category'] = $category;
        }
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    public function reset_category_filter_action() {
        $this->resetCategoryFilter();
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    public function reset_filter_action() {
        $this->resetFilter();
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    private function resetFilter()
    {
        $_SESSION['global_search']['filters'] = null;
    }

    private function resetCategoryFilter()
    {
        $this->resetFilter();
        $_SESSION['global_search']['category'] = null;
    }
}
