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
        $this->config->coffeePath        = (isset($config['coffeePath'])) ? $config['coffeePath'] : '/usr/bin/coffee';
        $this->config->sassPath          = (isset($config['sassPath'])) ? $config['sassPath'] : '/usr/bin/sass';
        $this->config->uglifyJsPath      = (isset($config['uglifyJsPath'])) ? $config['uglifyJsPath'] : '/usr/bin/uglifyjs'; // Version 2
        $this->config->uglifyCssPath     = (isset($config['uglifyCssPath'])) ? $config['uglifyCssPath'] : '/usr/bin/uglifycss';
        $this->config->nodeCoffeePath    = (isset($config['nodeCoffeePath'])) ? $config['nodeCoffeePath'] : null;
        $this->config->nodeSassPath      = (isset($config['nodeSassPath'])) ? $config['nodeSassPath'] : null;
        $this->config->nodeUglifyJsPath  = (isset($config['nodeUglifyJsPath'])) ? $config['nodeUglifyJsPath'] : null;
        $this->config->nodeUglifyCssPath = (isset($config['nodeUglifyCssPath'])) ? $config['nodeUglifyCssPath'] : null;
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
            case 'sass':
                $this->filters[] = new SassFilter($this->config->sassPath, $this->config->nodeSassPath);
            break;
            case 'coffee':
                $this->filters[] = new CoffeeScriptFilter($this->config->coffeePath, $this->config->nodeCoffeePath);
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
                $this->filters[] = new UglifyCssFilter($this->config->uglifyCssPath, $this->config->nodeUglifyCssPath);
            break;
            case 'js':
            case 'coffee':
                $this->filters[] = new UglifyJsFilter($this->config->uglifyJsPath, $this->config->nodeUglifyJsPath);
            break;
        }
        return $this->filters;
    }


}

?>
