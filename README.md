Assetatic
=========

A wrapper of Assetic. (underconstruction)

## Configuration File

Make modules for your CSS and JavaScript files. These files can be *.css, *.sass, *.coffee, and *.js.

````php
array(
    'outputFolder' => 'assets',
    'modules' => array(
        'admin_core' => array(
            'css' => array(
                'media/css/admin/reset.css',
                'media/css/admin/text.css',
                'media/css/admin/fluid.css',
                'media/css/admin/core/button.scss', // SCSS
            ),
            'js' => array(
                'media/js/admin/jquery-1.8.1.min.js',
                'media/js/admin/jquery.mousewheel-min.js',
                'media/js/admin/event.coffee', // COFFEE
            ),
        ),
    ),
);
```


## Usage

### General Usage

```php
<?php $assetatic = new Assetatic('config.php'); ?>
<?php echo $assetatic->stylesheet_tags('admin_core'); ?>
<?php echo $assetatic->javascript_tags('admin_core'); ?>
```

It will output the following HTML.

```html
<!-- admin_core.css (start) -->
<link type="stylesheet" href="media/css/admin/reset.css"/>
<link type="stylesheet" href="media/css/admin/text.css"/>
<link type="stylesheet" href="media/css/admin/fluid.css"/>
<link type="stylesheet" href="assets/button.css "/> <!-- button.scss -->
<!-- admin_core.css (end) -->
<!-- admin_core.js (end) -->
<script src="media/js/admin/jquery-1.8.1.min.js"></script>
<script src="media/js/admin/jquery.mousewheel-min.js"></script>
<script src="assets/event.js"></script> <!-- event.coffee -->
<!-- admin_core.js (end) -->
```

### Concatenate

Or you can concatenate to single file for less requests.

```php
<?php echo Assetatic::stylesheet_tags('admin_core', true); ?>
<?php echo Assetatic::javascript_tags('admin_core', true); ?>
```

It will output the following HTML.

```html
<!-- admin_core.css (start) -->
<link type="stylesheet" href="assets/admin_core.css"/>
<!-- admin_core.css (end) -->
<!-- admin_core.js (end) -->
<script src="assets/admin_core.js"></script>
<!-- admin_core.js (end) -->
```

### For Deployment

Execute the following command.

```
vendor/assetatic/bin/assetatic precompile config.php
```

It will do minification, concatenation, and overwriting the configuration tasks.

```php
array(
    'modules' => array(
        'admin_core' => array(
            'css' => 'media/css/admin_core_31a85b.min.css',
            'js' => 'media/js/admin_core_6f5a8a.min.js'
        ),
    ),
);
```

## Installation

1. git clone
1. bundle
1. npm install .
1. Set config
1. Output css/js tags.
