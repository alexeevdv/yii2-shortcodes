<?php

namespace alexeevdv\shortcode;

/**
 * Interface IShortcode
 * @package alexeevdv\shortcode
 */
interface IShortcode
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getParams();

    /**
     * @return string
     */
    public function getContent();
}
