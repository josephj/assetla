<?php

return array(
    'output_path' => 'assets',
    'deploy' => array( // For S3 deployment
        'key' => '<api-public-key>',
        'secret' => '<api-secret-key>',
        'acl' => '<public-read>',
        'bucket' => '<bucket-name>',
        'path' => '<save-path>',
    ),
    'modules' => array(
        'demo' => array(
            'css' => array(
                'static/lib/bootstrap/css/bootstrap.min.css',
                'static/css/base.scss',
                'static/css/demo.sass',
            ),
            'js' => array(
                'static/js/jquery-1.11.1.min.js',
                'static/lib/bootstrap/js/bootstrap.min.js',
                'static/js/demo.coffee',
            ),
        ),
    ),
);

