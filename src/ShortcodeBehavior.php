<?php

namespace alexeevdv\yii\shortcodes;

use alexeevdv\shortcodes\IMatcher;
use alexeevdv\shortcodes\IProcessor;
use alexeevdv\shortcodes\IReplacer;
use alexeevdv\shortcodes\Processor;
use alexeevdv\shortcodes\WordpressMatcher;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\ViewEvent;
use yii\di\Instance;
use yii\web\View;

/**
 * Class ShortcodeBehavior
 * @package alexeevdv\yii\shortcodes
 */
class ShortcodeBehavior extends Behavior
{
    /**
     * @var string|array|IProcessor
     */
    public $processor = Processor::class;

    /**
     * @var string|array|IMatcher
     */
    public $matcher = WordpressMatcher::class;

    /**
     * [
     *     'youtube' => YoutubeWidget::class,
     *     'product' => ['class' => ProductWidget::class, 'theme' => 'dark'],
     * ]
     * @var array
     */
    public $map = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            View::EVENT_AFTER_RENDER => 'onAfterRender',
        ];
    }

    /**
     * @param ViewEvent $event
     * @throws InvalidConfigException
     */
    public function onAfterRender(ViewEvent $event)
    {
        $event->output = $this
            ->getProcessor()
            ->process(
                $event->output,
                $this->getMatcher(),
                $this->getReplacer()
            )
        ;
    }

    /**
     * @return IProcessor
     * @throws InvalidConfigException
     */
    protected function getProcessor()
    {
        return Instance::ensure($this->processor, IProcessor::class);
    }

    /**
     * @return IMatcher
     * @throws InvalidConfigException
     */
    protected function getMatcher()
    {
        return Instance::ensure($this->matcher, IMatcher::class);
    }

    /**
     * @return IReplacer
     * @throws InvalidConfigException
     */
    protected function getReplacer()
    {
        return Yii::createObject([
            'class' => WidgetReplacer::class,
            'map' => $this->map,
        ]);
    }
}
