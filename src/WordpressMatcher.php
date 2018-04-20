<?php

namespace alexeevdv\shortcode;

/**
 * Class WordpressMatcher
 * @package alexeevdv\shortcode
 */
class WordpressMatcher implements IMatcher
{
    const SHORTCODE_NAMES_REGEX = '@\[([^<>&/\[\]\x00-\x20=]++)@';
    const SHORTCODE_ATTRIBUTES_REGEX = '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)/';

    /**
     * @inheritdoc
     */
    public function match($content, $callback)
    {
        $regexp = $this->getShortCodesRegex($this->parseShortcodeNames($content));


        $textarr = preg_split( $this->get_html_split_regex(), $content, -1, PREG_SPLIT_DELIM_CAPTURE );
        foreach ( $textarr as &$element ) {
            if ( '' == $element || '<' !== $element[0] ) {
                $element = preg_replace_callback( "/$regexp/", function ($match) use ($callback) {
                    return call_user_func($callback, $this->buildShortcode($match));
                }, $element );
            }
        }
        $content = implode( '', $textarr );
        return $content;
    }

    function get_html_split_regex() {
        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        $comments =
            '!'           // Start of comment, after the <.
            . '(?:'         // Unroll the loop: Consume everything until --> is found.
            .     '-(?!->)' // Dash not followed by end of comment.
            .     '[^\-]*+' // Consume non-dashes.
            . ')*+'         // Loop possessively.
            . '(?:-->)?';   // End of comment. If not found, match all input.

        $cdata =
            '!\[CDATA\['  // Start of comment, after the <.
            . '[^\]]*+'     // Consume non-].
            . '(?:'         // Unroll the loop: Consume everything until ]]> is found.
            .     '](?!]>)' // One ] not followed by end of comment.
            .     '[^\]]*+' // Consume non-].
            . ')*+'         // Loop possessively.
            . '(?:]]>)?';   // End of comment. If not found, match all input.

        $escaped =
            '(?='           // Is the element escaped?
            .    '!--'
            . '|'
            .    '!\[CDATA\['
            . ')'
            . '(?(?=!-)'      // If yes, which type?
            .     $comments
            . '|'
            .     $cdata
            . ')';

        $regex =
            '/('              // Capture the entire match.
            .     '<'           // Find start of element.
            .     '(?'          // Conditional expression follows.
            .         $escaped  // Find end of escaped element.
            .     '|'           // ... else ...
            .         '[^>]*>?' // Find end of normal element.
            .     ')'
            . ')/';
        // phpcs:enable
        return $regex;
    }

    /**
     * @param string $content
     * @return array
     */
    public function parseShortcodeNames($content)
    {
        preg_match_all( static::SHORTCODE_NAMES_REGEX, $content, $matches );
        return $matches[1];
    }

    /**
     * @param string $content
     * @return array
     */
    public function parseAttributes($content)
    {
        $attributes = array();
        $content = preg_replace("/[\x{00a0}\x{200b}]+/u", ' ', $content);
        preg_match_all(static::SHORTCODE_ATTRIBUTES_REGEX, $content, $match, PREG_SET_ORDER );
        foreach ( $match as $m ) {
            if ( ! empty( $m[1] ) ) {
                $attributes[ strtolower( $m[1] ) ] = stripcslashes( $m[2] );
            } elseif ( ! empty( $m[3] ) ) {
                $attributes[ strtolower( $m[3] ) ] = stripcslashes( $m[4] );
            } elseif ( ! empty( $m[5] ) ) {
                $attributes[ strtolower( $m[5] ) ] = stripcslashes( $m[6] );
            } elseif ( isset( $m[7] ) && strlen( $m[7] ) ) {
                $attributes[] = stripcslashes( $m[7] );
            } elseif ( isset( $m[8] ) && strlen( $m[8] ) ) {
                $attributes[] = stripcslashes( $m[8] );
            } elseif ( isset( $m[9] ) ) {
                $attributes[] = stripcslashes( $m[9] );
            }
        }
        // Reject any unclosed HTML elements
        foreach ( $attributes as &$value ) {
            if ( false !== strpos( $value, '<' ) ) {
                if ( 1 !== preg_match( '/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value ) ) {
                    $value = '';
                }
            }
        }
        return $attributes;
    }

    /**
     * @param array $tagNames
     * @return string
     *
     *
     */
    protected function getShortCodesRegex($tagNames = [])
    {
        $tagregexp = join('|', array_map('preg_quote', $tagNames));
        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        return
            '\\['                                // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)"                     // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag ...
            .     '\\]'                          // ... and closing bracket
            . '|'
            .     '\\]'                          // Closing bracket
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            .             '[^\\[]*+'             // Not an opening bracket
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            .                 '[^\\[]*+'         // Not an opening bracket
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
        // phpcs:enable
    }

    /**
     * @param array $match
     * @return IShortcode
     */
    public function buildShortcode(array $match)
    {
        $shortcode = new Shortcode;
        $shortcode->setName($match[2]);
        $shortcode->setParams($this->parseAttributes($match[3]));
        $shortcode->setContent($match[5]);
        return $shortcode;
    }
}
