<?php

use alexeevdv\shortcode\IShortcode;
use alexeevdv\shortcode\WordpressMatcher;

/**
 * Class WordpressMatcherTest
 */
class WordpressMatcherTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @dataProvider matchProvider
     * @param string $content
     * @param integer $expectedMatches
     */
    public function match($content, $expectedMatches)
    {
        $matcher = new WordpressMatcher;

        $shortcodes = [];
        $matcher->match(
            $content,
            function (IShortcode $shortcode) use (&$shortcodes) {
                $shortcodes[] = $shortcode;
            }
        );
        $this->assertCount($expectedMatches, $shortcodes);
    }

    /**
     * @test
     * @dataProvider attributesProvider
     * @param string $content
     * @param array $expectedAttributes
     */
    public function parseAttributes($content, $expectedAttributes)
    {
        $matcher = new WordpressMatcher;
        $attributes = $matcher->parseAttributes($content);
        $this->assertEquals($expectedAttributes, $attributes);
    }

    /**
     * @test
     * @dataProvider shortcodeNamesProvider
     * @param string $content
     * @param array $expectedNames
     */
    public function parseShortcodeNames($content, $expectedNames)
    {
        $matcher = new WordpressMatcher;
        $names = $matcher->parseShortcodeNames($content);
        asort($expectedNames);
        asort($names);
        $this->assertEquals($expectedNames, $names);
    }

    /**
     * @return array
     */
    public function matchProvider()
    {
        return [
            ['Some [strong width =  1234]text[/strong] with', 1],
            ['<input name="Post[tag]" />', 0],
            ['<div class="test">[strong id=123]</div>', 1],
            ['<div class="test[tag]">[strong id=123]</div>', 1],
        ];
    }

    /**
     * @return array
     */
    public function shortcodeNamesProvider()
    {
        return [
            [
                'Some text with [tag] to be replaced',
                ['tag'],
            ],
            [
                'Some text with [tag] [tag2 id=name text="what ever"] to be replaced',
                ['tag', 'tag2'],
            ],
            [
                'Some [strong]text[/strong] with [tag] [tag2 id=name text="what ever"] to be replaced',
                ['strong', 'tag', 'tag2'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributesProvider()
    {
        return [
            [
                'id="123" noway utf8 = утф8',
                [
                    'id' => 123,
                    0 => 'noway',
                    'utf8' => 'утф8',
                ],
            ],
            [
                'id="123" noway',
                [
                    'id' => 123,
                    0 => 'noway'
                ],
            ],
            [
                'id="123" noway gallery=\'abc\' yes',
                [
                    'id' => 123,
                    0 => 'noway',
                    'gallery' => 'abc',
                    1 => 'yes',
                ],
            ],
        ];
    }
}
