<?php
/**
 * Created by PhpStorm.
 * User: manschwa
 * Date: 22.06.16
 * Time: 10:25
 */
abstract class IndexObject
{
    protected $name;
    protected $facets;

    abstract public function __construct();
    abstract public function sqlIndex();
    abstract public function getLink($object);
    abstract public function getAvatar();

    /**
     * @return mixed
     */
    public function getCondition()
    {
        return null;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        if (is_string($name)) {
            $this->name = (string)$name;
        }
    }

    /**
     * @param array $facets
     */
    public function setFacets($facets)
    {
        if (is_array($facets)) {
            $this->facets = (array)$facets;
        }
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
