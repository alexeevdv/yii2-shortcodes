<?php

namespace alexeevdv\shortcode;

/**
 * Class Shortcode
 * @package alexeevdv\shortcode
 */
class Shortcode implements IShortcode
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var array
     */
    private $_params = [];

    /**
     * @var string
     */
    private $_content;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @inheritdoc
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }
}
