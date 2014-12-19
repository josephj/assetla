<?php

/**
 * Normalize single file of Coffee, JavaScript, SASS, SCSS, and CSS
 * to browser-readable JavaScript and CSS format according to their extension.
 *
 *     $file = new AssetlaFile('./assets/foo.coffee', false);
 *     echo $file->output(); // Must be valid JavaScript format.
 */

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CoffeeScriptFilter;
use Assetic\Filter\UglifyJsFilter;
use Assetic\Filter\UglifyCssFilter;
use Assetic\Filter\Sass\SassFilter;
use Assetic\Filter\Sass\ScssFilter;

class AssetlaFile {

    private $config;
    private $file;
    private $filters;
    private $type;

    public function __construct($path, $config = array()) {
        $this->file = self::find($path);
        $this->type = self::get_type($this->file);
        $this->filters = array();
        $this->config = new StdClass();
        // Minify (Default false)
        $this->config->minify          = (isset($config['minify'])) ? $config['minify'] : false;
        // Compilers (Use local first)
        $this->config->coffee_path     = (isset($config['coffee_path'])) ? $config['coffee_path'] : dirname(__FILE__) . '/../node_modules/coffee-script/bin/coffee';
        $this->config->sass_path       = (isset($config['sass_path'])) ? $config['sass_path'] : dirname(__FILE__) . '/../vendor/bundler/ruby/2.0.0/bin/sass';
        $this->config->uglify_css_path = (isset($config['uglify_css_path'])) ? $config['uglify_css_path'] : dirname(__FILE__) . '/../node_modules/uglifycss/uglifycss';
        $this->config->uglify_js_path  = (isset($config['uglify_js_path'])) ? $config['uglify_js_path'] : dirname(__FILE__) . '/../node_modules/uglify-js/bin/uglifyjs';
        // Output Path (Optional, save to same directory)
        $this->config->output_path     = (isset($config['output_path'])) ? $config['output_path'] : null;
    }

    private static function find($path) {
        $file_path = realpath($path);
        if ($file_path && file_exists($file_path))
        {
            return $file_path;
        }
        throw new Exception("File not found: " . $path);
    }

    private static function get_type($file) {
        $path_parts = pathinfo($file);
        return $path_parts['extension'];
    }

    public function dump() {
        $file = array(new FileAsset($this->file));
        $this->set_type_filter($this->type);
        if ($this->config->minify) {
            $this->set_minify_filter($this->type);
        }
        $result = new AssetCollection($file, $this->filters);
        return $result->dump();
    }

    public function save($target_dir = null) {
        // Decide output directory.
        if ( ! isset($target_dir)) { // via argument
            if (isset($this->config->output_path)) { // via config
                $target_dir = $this->config->output_path;
            } else { // nothing provided, from file path
                $target_dir = $this->file;
            }
        }
        if (is_file($target_dir)) {
            $target_dir = pathinfo($target_dir);
            $target_dir = $target_dir['dirname'];
        }
        if ( ! file_exists($target_dir)) {
            if ( ! mkdir($target_dir)) {
                throw new FileNotFoundException("File not found: " . $target_dir);
            }
        }
        $target_dir = rtrim($target_dir, '/') . '/';
        // Decide file name and extension.
        $filename = pathinfo($this->file);
        $filename = $filename['filename'];
        switch ($this->type)
        {
            case 'css':
            case 'less':
            case 'scss':
            case 'sass':
                $extension = 'css';
            break;
            case 'js':
            case 'coffee':
                $extension = 'js';
            break;
        }

        // Output file
        $target_path = "{$target_dir}{$filename}.{$extension}";
        $content = $this->dump();
        return (file_put_contents($target_path, $content)) ? $target_path : false;
    }

    private function set_type_filter($type) {
        $coffee_path = $this->config->coffee_path;
        switch ($type)
        {
            case 'scss':
                $this->filters[] = new ScssFilter($this->config->sass_path);
            case 'sass':
                $this->filters[] = new SassFilter($this->config->sass_path);
            break;
            case 'coffee':
                $this->filters[] = new CoffeeScriptFilter($this->config->coffee_path);
            break;
        }
        return $this->filters;
    }

    private function set_minify_filter($type) {
        switch ($type)
        {
            case 'css':
            case 'scss':
            case 'sass':
                $this->filters[] = new UglifyCssFilter($this->config->uglify_css_path);
            break;
            case 'js':
            case 'coffee':
                $this->filters[] = new UglifyJsFilter($this->config->uglify_js_path);
            break;
        }
        return $this->filters;
    }


}

?>
