<?php

class ShowController extends StudipController
{
    public function __construct($dispatcher)
    {
        parent::__construct($dispatcher);
        $this->plugin = $dispatcher->plugin;
        $this->search = new IntelligentSearch();
    }

    public function before_filter(&$action, &$args)
    {
        $this->set_layout($GLOBALS['template_factory']->open('layouts/base_without_infobox'));

        // Find query
        $this->query = Request::get('utf8') ? studip_utf8decode(Request::get('search')) : Request::get('search');
        if ($this->query || Request::submitted('search')) {
            if ($_SESSION['global_search']['query'] !== $this->query) {
                $this->resetFacetFilters();
            }
            $_SESSION['global_search']['query'] = $this->query;
        }
    }

    public function index_action()
    {
        $this->addSearchSidebar();
    }

    public function indexing_action($restriction = null)
    {
        $GLOBALS['perm']->check('root');
        $this->time = IndexManager::sqlIndex($restriction);
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    public function open_action($id) {
        $stmt = DBManager::get()->prepare('SELECT * FROM search_object WHERE object_id = ? LIMIT 1');
        $stmt->execute(array($id));
        $location = $GLOBALS['ABSOLUTE_URI_STUDIP'].IntelligentSearch::getLink($stmt->fetch(PDO::FETCH_ASSOC));
        header("location: $location");die;
    }

    /**
     *
     */
    private function addSearchSidebar()
    {
        $sidebar = Sidebar::get();
        $sidebar->setImage('sidebar/search-sidebar.png');

        //TODO don't call getCategoryWidget() twice...
        $this->getCategoryWidget();

        if ($type = $_SESSION['global_search']['category']) {
            $class = $this->search->getClass($type);
            $object = new $class;
            $facets_widget = $this->getFacetsWidget($object);
        }

        if ($_SESSION['global_search']['query'] || $_SESSION['global_search']['category']) {
            $this->search->query($_SESSION['global_search']['query'], $this->getCategoryFilter());
        }

        $category_widget = $this->getCategoryWidget();
        $sidebar->addWidget($category_widget);
        if ($facets_widget) {
            $sidebar->addWidget($facets_widget);
        }

        // Root may update index
        if ($GLOBALS['perm']->have_perm('root')) {
            $actions = new ActionsWidget();
            $actions->addLink(_('Indizieren'), $this->url_for('show/indexing'));
            $sidebar->addWidget($actions);
        }

        // On develop display runtime
        if (Studip\ENV == 'development' && $this->search->time && $GLOBALS['perm']->have_perm('admin')) {
            $sidebar->addWidget($this->getRuntimeWidget());
        }
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
        $result_count = $this->search->count ? " ({$this->search->count})" : '';
        $category_widget->setTitle(_('Ergebnisse') . $result_count);

        // offer a reset options only if there is a category selected
        if ($this->getCategoryFilter()) {
            $reset_element = new LinkElement(_('Auswahl aufheben'), $this->url_for('show/reset_category_filter'));
//            $reset_element->addClass('subclass');
            $category_widget->addElement($reset_element);
        }
        // list all possible categories as Links
        $index_object_types = $this->search->getIndexObjectTypes();
        foreach ($index_object_types as $type) {
            $class = $this->search->getClass($type);
            $object = new $class;
            if (!$_SESSION['global_search']['query'] || $this->search->resultTypes[$type] || $_SESSION['global_search']['category'] === $type) {
                $category_widget->addElement($this->categoryLink($type, $object));
            }
        }
        return $category_widget;
    }

    /**
     * @param $type string
     * @return LinkElement
     */
    private function categoryLink($type, $object)
    {
        $facet_count = $this->search->resultTypes[$type] ? " ({$this->search->resultTypes[$type]})" : '';
        return new LinkElement($object->getName() . $facet_count,
            $this->url_for('show/set_category_filter/' . $type),
            $_SESSION['global_search']['category'] === $type ? Icon::create('arr_1right') : '');
    }

    /**
     * Build an OptionsWidget for the sidebar to choose category specific filters for your search results.
     * The filter options shown depend on the chosen category.
     * There can be more than one filter selected per category.
     *
     * @param $object
     * @return OptionsWidget containing category specific filter options.
     */
    private function getFacetsWidget($object)
    {
        $options_widget = new OptionsWidget;
        $options_widget->setTitle(_('Filtern nach'));

        // Select-Filters
        if (method_exists($object, 'getSelectFilters')) {
            $select_filters = $object->getSelects();
            foreach ($select_filters as $name => $selects) {
                $selected = $_SESSION['global_search']['selects'][$name];
                $options_widget->addElement(new WidgetElement($name));
                $options_widget->addSelect($name,                       // Label
                    $this->url_for('show/set_select/' . $name),         // URL
                    $name,                                              // Name
                    $selects,                                           // all options
                    // need to do this because of implicit type conversion (string to int in associative array)
                    preg_match('/^[1-9][0-9]*$/', $selected) ? (int)$selected : $selected);      // selected option
            }
        }
        // Facet-Filters (checkboxes)
        if (method_exists($object, 'getFacetFilters')) {
            if ($this->search->getActiveFilters()) {
                $reset_element = new LinkElement(_('Auswahl aufheben'), $this->url_for('show/reset_filter'));
                $options_widget->addElement($reset_element);
            }

            $filter_options = $object->getFacets();
            foreach ($filter_options as $facet) {
                $options_widget->addCheckbox($facet,                            // Name
                    $_SESSION['global_search']['facets'][$facet],               // state
                    $this->url_for('show/set_facet/' . $facet . '/' . true),    // check action
                    $this->url_for('show/set_facet/' . $facet . '/' . false));  // uncheck action
            }
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
     * Set the selected category specific search filter and store the selection in the $_SESSION variable.
     *
     * @param $type string
     * @param null $filter string
     * @param bool $state
     * @throws Trails_DoubleRenderError
     */
    public function set_facet_action($facet = null, $state = true)
    {
        // store facet filter in $_SESSION
        if (!is_null($facet)) {
            $_SESSION['global_search']['facets'][$facet] = (bool)$state;
        }
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    /**
     * @param null $name
     * @throws Trails_DoubleRenderError
     */
    public function set_select_action($name = null)
    {
        // store facet filter in $_SESSION
        if (!is_null($name)) {
            $_SESSION['global_search']['selects'][$name] = Request::option($name);
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
            $this->resetFacetFilters();
            $this->resetSelectFilters();
            $_SESSION['global_search']['category'] = $category;
        }
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    public function reset_category_filter_action() {
        $this->resetCategoryFilter();
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    public function reset_filter_action() {
        $this->resetFacetFilters();
        $this->redirect($this->url_for('show/index?search=' . $_SESSION['global_search']['query']));
    }

    private function resetSelectFilters()
    {
        $_SESSION['global_search']['selects'] = array();
    }

    private function resetFacetFilters()
    {
        $_SESSION['global_search']['facets'] = array();
    }

    private function resetCategoryFilter()
    {
        $this->resetFacetFilters();
        $this->resetSelectFilters();
        $_SESSION['global_search']['category'] = null;
    }

    // customized #url_for for plugins
    function url_for($to = '')
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
}
