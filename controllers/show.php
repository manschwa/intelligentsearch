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

        $this->createSidebar();

        if (Request::submitted('search')) {
            $this->search = new IntelligentSearch(Request::get('search'), Request::get('filter'));
            $this->addSearchSidebar();
        }
    }

    public function fast_action() {
        if ($GLOBALS['perm']->check('root'));
        $this->time = IndexManager::sqlIndex();
        $this->redirect('show/index');
    }

    private function createSidebar() {
        $sidebar = Sidebar::get();
        $sidebar->setImage('sidebar/search-sidebar.png');

        $formWidget = new SidebarWidget();
        $formWidget->setTitle(_('Suche'));
        $form = '<form class="studip_form">';
        $form .= '<input type="text" style="display: inline; width: 230px;" name="search" value="' . Request::get('search') . '" placeholder="' . _('Suchbegriff') . '">';
        $form .= '</form>';
        $formWidget->addElement(new WidgetElement($form));
        $sidebar->addWidget($formWidget);


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
        $widget->addLink(_('Alle') . " ({$this->search->count})", URLHelper::getURL('', array("search" => $this->search->query)), !$this->search->filter ? 'icons/16/black/arr_1right.png' : '');
        foreach ($this->search->resultTypes as $type => $results) {
            $widget->addLink(IntelligentSearch::getTypeName($type) . " ($results)", URLHelper::getURL('', array("search" => $this->search->query, "filter" => $type)), $type == $this->search->filter ? 'icons/16/black/arr_1right.png' : '');
        }
        $sidebar->addWidget($widget);
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
