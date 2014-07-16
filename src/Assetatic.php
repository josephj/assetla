<?php
require './src/AssetaticFile.php';

class Assetatic
{
    protected $config_file;
    protected $config;
    protected $paths;

    public function __construct($config_file = 'config.php')
    {
        $this->config_file = self::find($config_file);
        $this->load();
    }

    private function load()
    {
        $config = include $this->config_file;
        $this->config = $config;
        foreach ($this->config['paths'] as $path)
        {
            $this->paths[] = preg_replace_callback('/\$([A-Z][0-9A-Z_]*)/', array($this, "resolve"), $path);
        }
    }

   /**
    * Search file locally by provided paths.
    *
    * @param string file_name File name only or absolute file path. If only file name provided, will check each path provided.
    * @param array path search path
    */
    public static function find($file_name, $paths = array("."))
    {
        $file_path = realpath($file_name);
        if ($file_path && file_exists($file_path))
        {
            return $file_path;
        }
        foreach ($paths as $path)
        {
            $file_path = rtrim($path, "/") . "/" . $file_name;
            if (file_exists($file_path))
            {
                return $file_path;
            }
        }
        throw new FileNotFoundException("File not found: " . $file_name);
    }

    public function findFile($file_name)
    {
        return (strpos($file_name, "http://") === 0) ? $file_name : self::find($file_name, $this->paths);
    }

    /**
     * Resolve the environment variable.
     */
    private function resolve($matches)
    {
        return isset($_SERVER[$matches[1]]) ? $_SERVER[$matches[1]] : "$" . $matches[1];
    }

    /**
     * Compile file according to it's extension.
     */
    public function compile($file_path, $minify = false)
    {
        $file_path = $this->findFile($file_path);
        $assetatic_file = new AssetaticFile($file_path, array('minify' => $minify));
        return $assetatic_file->dump();
    }

    public function _filter($path)
    {
        $types = ['sass', 'scss', 'coffee'];
        $parts = pathinfo($path);
        $extension = $parts['extension'];
        if (in_array($extension, $types)) {
            $file = new AssetaticFile($path);
            $path = $file->save($this->config['outputFolder']);
        }
        return $path;
    }

    public function stylesheet_tags($module, $single = false)
    {
        $paths = $this->config['modules'][$module]['css'];
        $html = array();
        foreach ($paths as $path) {
            $path = $this->_filter($path);
            $html[] = "<link rel=\"stylesheet\" href=\"{$path}\">";
        }
        return implode("\n", $html);
    }

    public function javascript_tags($module, $single = false)
    {
        $paths = $this->config['modules'][$module]['css'];
        $html = array();
        foreach ($paths as $path) {
            $path = $this->_filter($path);
            $html[] = "<script src=\"{$path}\"></script>";
        }
        return implode("\n", $html);
    }
}

?>
