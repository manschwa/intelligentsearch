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
        
        // Create search
        $this->setInfoBoxImage('sidebar/search-sidebar.png');
        $form = '<form class="studip_form">';
        $form .= '<input type="text" style="display: inline;" name="search" value="'.Request::get('search').'" placeholder="'._('Suchbegriff').'">';
        $form .= '</form>';
        $this->addToInfobox('Suche', $form);
        
        if (Request::submitted('search')) {
            $this->search = new IntelligentSearch(Request::get('search'));
            foreach ($this->search->resultTypes as $type => $results) {
                $this->addToInfobox('Typen', "<a href='".URLHelper::getURL('', array("search" => $this->search->query, "filter" => $type))."'>".IntelligentSearch::getTypeName($type)." ($results)</a>");
            }
            $this->addToInfobox(_('Info'), sprintf(_('%s Ergebnisse in %s Sekunden'), $this->search->count, round($this->search->time, 3)));
            
        }
        
        // Root may update index
        if ($GLOBALS['perm']->have_perm('root')) {
             $this->addToInfobox(_('Aktionen'), "<a href='".$this->url_for('show/fast')."'>Indizieren</a>");
             $this->addToInfobox(_('Aktionen'), "Achtung! Dauert etwas!");
        }
        
    }
    
    public function fast_action() {
        if ($GLOBALS['perm']->check('root'));
        $this->time = IndexManager::sqlIndex();
        $this->redirect('show/index');
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
