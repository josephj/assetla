<?php

/**
 * Normalize Coffee, JavaScript, SASS, SCSS, and CSS files
 * to browser-readable JavaScript and CSS format according to their extension.
 *
 *     $file = new AssetaticFile('./assets/foo.coffee', false);
 *     echo $file->output(); // Must be valid JavaScript format.
 */

require 'vendor/autoload.php';

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Filter\CoffeeScriptFilter;
use Assetic\Filter\UglifyJsFilter;
use Assetic\Filter\Sass\SassFilter;
use Assetic\Filter\Sass\ScssFilter;

class AssetaticFile
{
    private $file;
    private $type;
    private $filters;
    private $config;

    public function __construct($path, $config = array())
    {
        $this->file = self::find($path);
        $this->type = self::getType($this->file);
        $this->filters = array();
        $this->config = new StdClass();
        $this->config->minify            = (isset($config['minify'])) ? $config['minify'] : false;
        $this->config->coffeePath        = (isset($config['coffeePath'])) ? $config['coffeePath'] : 'node_modules/coffee-script/bin/coffee';
        $this->config->sassPath          = (isset($config['sassPath'])) ? $config['sassPath'] : 'vendor/bundler/ruby/2.0.0/bin/sass';
        $this->config->uglifyJsPath      = (isset($config['uglifyJsPath'])) ? $config['uglifyJsPath'] : 'node_modules/uglify-js/bin/uglifyjs';
        $this->config->uglifyCssPath     = (isset($config['uglifyCssPath'])) ? $config['uglifyCssPath'] : 'node_modules/uglifycss/uglifycss';
    }

    private static function find($path)
    {
        $filePath = realpath($path);
        if ($filePath && file_exists($filePath))
        {
            return $filePath;
        }
        throw new FileNotFoundException("File not found: " . $path);
    }

    private static function getType($file)
    {
        $path_parts = pathinfo($file);
        return $path_parts['extension'];
    }

    public function dump()
    {
        $file = array(new FileAsset($this->file));
        $this->setTypeFilter($this->type);
        if ($this->config->minify) {
            $this->setMinifyFilter($this->type);
        }
        $result = new AssetCollection($file, $this->filters);
        return $result->dump();
    }

    private function setTypeFilter($type)
    {
        $coffeePath = $this->config->coffeePath;
        switch ($type) {
            case 'scss':
                $this->filters[] = new ScssFilter($this->config->sassPath);
            case 'sass':
                $this->filters[] = new SassFilter($this->config->sassPath);
            break;
            case 'coffee':
                $this->filters[] = new CoffeeScriptFilter($this->config->coffeePath);
            break;
        }
        return $this->filters;
    }

    private function setMinifyFilter($type)
    {
        switch ($type) {
            case 'css':
            case 'scss':
            case 'sass':
                $this->filters[] = new UglifyCssFilter($this->config->uglifyCssPath);
            break;
            case 'js':
            case 'coffee':
                $this->filters[] = new UglifyJsFilter($this->config->uglifyJsPath);
            break;
        }
        return $this->filters;
    }


}

?>
