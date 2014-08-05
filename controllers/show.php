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
        $this->infobox_content = array();

        $this->createSidebar();

        if (Request::submitted('search')) {
            $this->search = new IntelligentSearch();
            $this->search->query($this->query, Request::get('filter'));
            $this->addSearchSidebar();
        }

        $this->infobox = array('picture' => 'infobox/board2.jpg', 'content' => $this->infobox_content); // TODO Bild
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
        $form = '<form class="studip_form">';
        $form .= '<input type="text" style="display: inline; width: 204px;" name="search" value="' . $this->query . '" placeholder="' . _('Suchbegriff') . '">';
        $form .= '</form>';

        $this->infobox_content[] = array ('kategorie' => _('Suche') . ':',
                                          'eintrag'   => array (
                                                             array ('text' => $form)
                                                         )
                                   );

        // Root may update index
        if ($GLOBALS['perm']->have_perm('root')) {
            $link = '<a href="' . $this->url_for('show/fast') . '">' . _('Indizieren') . '</a>';

            $this->infobox_content[] = array ('kategorie' => _('Aktionen') . ':',
                                              'eintrag'   => array (
                                                                 array ('text' => $link)
                                                             )
                                       );
        }
    }

    private function addSearchSidebar() {
        $links = array();

        $links[] = array ('icon' => !$this->search->filter ? 'icons/16/black/arr_1right.png' : '',
                          'text' => '<a href="' . URLHelper::getURL('', array("search" => $this->search->query)) . '">' . _('Alle') . " ({$this->search->count})" . '</a>'
                         );

        foreach ($this->search->resultTypes as $type => $results) {
            $links[] = array ('icon' => $type == $this->search->filter ? 'icons/16/black/arr_1right.png' : '',
                              'text' => '<a href="' . URLHelper::getURL('', array("search" => $this->search->query, "filter" => $type)) . '">' . IntelligentSearch::getTypeName($type) . " ($results)" . '</a>'
                       );
        }

        $this->infobox_content[] = array ('kategorie' => _('Ergebnisse') . ':',
                                          'eintrag'   => $links
                                   );
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
