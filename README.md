yii2-shortcodes
=================
[![Build Status](https://travis-ci.org/alexeevdv/yii2-shortcodes.svg?branch=master)](https://travis-ci.org/alexeevdv/yii2-shortcodes) 
[![codecov](https://codecov.io/gh/alexeevdv/yii2-shortcodes/branch/master/graph/badge.svg)](https://codecov.io/gh/alexeevdv/yii2-shortcodes)
![PHP 5.6](https://img.shields.io/badge/PHP-5.6-green.svg) 
![PHP 7.0](https://img.shields.io/badge/PHP-7.0-green.svg) 
![PHP 7.1](https://img.shields.io/badge/PHP-7.1-green.svg) 
![PHP 7.2](https://img.shields.io/badge/PHP-7.2-green.svg)
![PHP 7.3](https://img.shields.io/badge/PHP-7.3-green.svg)

Yii2 behavior for rendering widgets with WordPress style shortcodes.


## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```bash
$ php composer.phar require alexeevdv/yii2-shortcodes "~0.1.0"
```

or add

```
"alexeevdv/yii2-shortcodes": "~0.1.0"
```

to the ```require``` section of your `composer.json` file.

## Configuration
```php
//...
'components' => [
    //...
    'view' => [
        'as shortcode' => [
            'class' => alexeevdv\yii\shortcodes\ShortcodeBehavior::class,
            'map' => [
                'feedback' => frontend\widgets\ContactForm::class,
                'gallery' => [ 
                    'class' => frontend\widgets\GalleryWidget::class,
                    'theme' => 'dark',
                ],
            ],
        ],
    ],
    //...
],
//...
```

## Usage

```php
namespace frontend\widgets;

class GalleryWidget extends \yii\base\Widget
{
    public $id;
    
    public $theme;
    
    public function run()
    {
        // render your gallery here using $id and $theme
    }
}
```

```php
//Anywhere in your layouts, views or rendered content:

[gallery id=413]

```
