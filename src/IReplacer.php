<?php

namespace alexeevdv\shortcode;

/**
 * Interface IReplacer
 * @package alexeevdv\shortcode
 */
interface IReplacer
{
    /**
     * @param IShortcode $shortcode
     * @return string
     */
    public function replace(IShortcode $shortcode);
}
