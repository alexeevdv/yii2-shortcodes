<?php

use alexeevdv\shortcode\CallbackReplacer;
use alexeevdv\shortcode\IShortcode;
use alexeevdv\shortcode\Processor;
use alexeevdv\shortcode\WordpressMatcher;

/**
 * Class ProcessorTest
 */
class ProcessorTest extends \Codeception\Test\Unit
{
    /**
     * @test
     * @dataProvider processProvider
     * @params string $input
     * @params string $output
     */
    public function process($input, $output)
    {
        $parser = new Processor();
        $matcher = new WordpressMatcher;
        $replacer = new CallbackReplacer(function (IShortcode $shortcode) {
            return $shortcode->getName();
        });

        $this->assertEquals($output, $parser->process($input, $matcher, $replacer));
    }

    /**
     * @return array
     */
    public function processProvider()
    {
        return [
            [
                'Some [strong width =  1234]text[/strong] with',
                'Some strong with',
            ],
            [
                '<input name="Post[tag]" />',
                '<input name="Post[tag]" />', // Html attributes should not be parsed
            ],
            [
                '<div class="test">[strong id=123]</div>',
                '<div class="test">strong</div>',
            ],
        ];
    }
}
