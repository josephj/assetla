Assetla
=========

A wrapper for Assetic.

[![Code Climate](https://codeclimate.com/github/josephj/assetla.png)](https://codeclimate.com/github/josephj/assetla)
[![Travis CI](https://travis-ci.org/josephj/assetla.svg)](https://travis-ci.org/josephj/assetla)

## Configuration File

Make modules for your CSS and JavaScript files. These files can be *.css, *.sass, *.coffee, and *.js.

````php
array(
    'output_path' => 'assets',
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
<?php $assetla = new Assetla('config.php'); ?>
<?php echo $assetla->stylesheet_tags('admin_core'); ?>
<?php echo $assetla->javascript_tags('admin_core'); ?>
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
<?php echo Assetla::stylesheet_tags('admin_core', true); ?>
<?php echo Assetla::javascript_tags('admin_core', true); ?>
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

### For Precompiliation

Execute the following command.

```
vendor/assetla/bin/assetla precompile config.php
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

### For Deployment

Currently it only supports S3. You need to provide some information in `config.php`.

```php
return array(
    'deploy' => array( // For S3 deployment
        'key' => '<api-public-key>',
        'secret' => '<api-secret-key>',
        'acl' => '<public-read>',
        'bucket' => '<bucket-name>',
        'path' => '<save-path>'
    ),
    // other settings        
),
```

Similar to precompilation, but it saves file to S3 instead.

````php
array(
    'modules' => array(
        'admin_core' => array(
            'css' => 'https://<bucket-name>.s3.amazonaws.com/<path>/admin_core_31a85b.min.css',
            'js' => 'https://<bucket-name>.s3.amazonaws.com/<path>/admin_core_6f5a8a.min.js'
        ),
    ),
);
````




## Installation

1. Grab the code `git@github.com:josephj/assetla.git`
1. You need to install several different packages from different package management systems. These steps make sure you could get required compilers (ex. SASS, CoffeeScript, and UglifyJS) installed in local directory.  
  1. `composer install`
  1. `bundle install --path vendor/bundler`
  1. `npm install .`
1. Create a writable folder for outputing the compiled files. 

    ```
    mkdir assets/out
    chmod 777 assets/out
    ```
1. Set config
    
    ```php
    return array(
        'output_path' => 'assets/out',
        'modules' => array(
            'welcome' => array(
                'css' => array(
                    'assets/css/foo.sass',
                ),
                'js' => array(
                    'assets/js/bar.coffee',
                ),
            ),
        ),
    );
    ```

1. Sample PHP view file using Assetla:

    ```php
    <?php
    require 'vendor/autoload.php';
    $assetla = new Assetla('config.php');
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="utf-8">
    <meta name="created" content="2014-09-02">
    <title>Welcome</title>
    <?php echo $assetla->stylesheet_tags('welcome'); ?>
    </head>
    <body>
        <h1>Welcome</h1>
        <div>
           <p>Hello World!</p>
        </div>
        <?php echo $assetla->javascript_tags('welcome'); ?>
    </body>
    </html>
    ```
