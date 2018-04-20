<?php

namespace alexeevdv\yii\shortcodes;

use alexeevdv\shortcodes\IReplacer;
use alexeevdv\shortcodes\IShortcode;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetReplacer
 * @package alexeevdv\yii\shortcodes
 */
class WidgetReplacer implements IReplacer
{
    /**
     * @var array
     */
    public $map = [];

    /**
     * @param IShortcode $shortcode
     * @return string
     */
    public function replace(IShortcode $shortcode)
    {
        $widgetConfig = ArrayHelper::getValue($this->map, $shortcode->getName(), false);
        if ($widgetConfig === false) {
            return false;
        }

        if (!is_array($widgetConfig)) {
            $widgetConfig = ['class' => $widgetConfig];
        }

        $class = ArrayHelper::getValue($widgetConfig, 'class');
        ob_start();
        $widget = $class::begin(ArrayHelper::merge($shortcode->getParams(), $widgetConfig));
        echo $shortcode->getContent();
        $widget->end();
        return ob_get_clean();
    }
}
