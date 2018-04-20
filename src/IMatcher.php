<?php

namespace alexeevdv\shortcode;

/**
 * Interface IMatcher
 * @package alexeevdv\shortcode
 */
interface IMatcher
{
    /**
     * @param string $content
     * @param callable $callback
     */
    public function match($content, $callback);
}
