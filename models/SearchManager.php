<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SearchManager
 *
 * @author intelec
 */
class SearchManager {

    public static $resultTypes = array();

    public static function isVisible($object) {
        if ($GLOBALS['perm']->have_perm('root')) {
            return true;
        }
        $class = "IndexObject_{$object->type}";
        return $class::isVisible($object);
    }

}
