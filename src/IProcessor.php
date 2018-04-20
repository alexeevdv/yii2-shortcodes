<?php

namespace alexeevdv\shortcode;

/**
 * Interface IProcessor
 * @package alexeevdv\shortcode
 */
interface IProcessor
{
    /**
     * @param string $content
     * @return string
     */
    public function process($content, IMatcher $matcher, IReplacer $replacer);
}
