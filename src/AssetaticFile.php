<?php

/**
 * Normalize single file of Coffee, JavaScript, SASS, SCSS, and CSS
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
use Assetic\Filter\UglifyCssFilter;
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
        // Minify (Default false)
        $this->config->minify        = (isset($config['minify'])) ? $config['minify'] : false;
        // Compilers (Use local first)
        $this->config->coffeePath    = (isset($config['coffeePath'])) ? $config['coffeePath'] : 'node_modules/coffee-script/bin/coffee';
        $this->config->sassPath      = (isset($config['sassPath'])) ? $config['sassPath'] : 'vendor/bundler/ruby/2.0.0/bin/sass';
        $this->config->uglifyCssPath = (isset($config['uglifyCssPath'])) ? $config['uglifyCssPath'] : 'node_modules/uglifycss/uglifycss';
        $this->config->uglifyJsPath  = (isset($config['uglifyJsPath'])) ? $config['uglifyJsPath'] : 'node_modules/uglify-js/bin/uglifyjs';
        // Output Folder (Optional, save to same directory)
        $this->config->outputFolder  = (isset($config['outputFolder'])) ? $config['outputFolder'] : null;
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

    public function save($targetDir = null)
    {
        // Decide output directory.
        if (!isset($targetDir)) { // via argument
            if (isset($this->config->outputFolder)) { // via config
                $targetDir = $this->config->outputFolder;
            } else { // nothing provided, from file path
                $targetDir = $this->file;
            }
        }
        if (is_file($targetDir)) {
            $targetDir = pathinfo($targetDir);
            $targetDir = $targetDir['dirname'];
        }
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir)) {
                throw new FileNotFoundException("File not found: " . $targetDir);
            }
        }
        $targetDir = rtrim($targetDir, '/') . '/';
        // Decide file name and extension.
        $filename = pathinfo($this->file);
        $filename = $filename['filename'];
        switch ($this->type) {
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
        $targetPath = "{$targetDir}{$filename}.{$extension}";
        $content = $this->dump();
        return file_put_contents($targetPath, $content);
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
