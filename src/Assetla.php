<?php
require_once dirname(__FILE__) . "/AssetlaFile.php";

class Assetla {

    protected $root_path;
    protected $config_file;
    protected $config;
    protected $paths;

    public function __construct($config_file = 'config.php') {
        $this->config_file = self::find($config_file);
        $this->load();
    }

    private function load() {
        $config = include $this->config_file;
        $this->config = $config;
        $this->root_path = (isset($this->config['root_path'])) ? $this->config['root_path'] : '.';
    }

   /**
    * Search file locally by provided paths.
    *
    * @param string file_name File name only or absolute file path. If only file name provided, will check each path provided.
    * @param array path search path
    */
    public static function find($file_name, $paths = array(".")) {
        $file_path = realpath($file_name);
        if ($file_path && file_exists($file_path)) {
            return $file_path;
        }
        foreach ($paths as $path) {
            $file_path = rtrim($path, "/") . "/" . $file_name;
            if (file_exists($file_path)) {
                return $file_path;
            }
        }
        throw new Exception("File not found: " . $file_name);
    }

    public function findFile($file_name) {
        return self::find($file_name, $this->config_path);
    }

    /**
     * Resolve the environment variable.
     */
    private function resolve($matches) {
        return isset($_SERVER[$matches[1]]) ? $_SERVER[$matches[1]] : "$" . $matches[1];
    }

    /**
     * Compile file according to it's extension.
     */
    public function compile($file_path, $minify = false) {
        $file_path = $this->findFile($file_path);
        $assetla_file = new AssetlaFile($file_path, array('minify' => $minify));
        return $assetla_file->dump();
    }

    public function _filter($path) {
        $types = ['sass', 'scss', 'coffee'];
        $parts = pathinfo($path);
        $extension = $parts['extension'];
        if (in_array($extension, $types)) {
            $file = new AssetlaFile($path);
            $path = $file->save($this->config['output_path']);
        }
        return $path;
    }

    // Combine all the module files and save to a single file
    public function combine($module, $type, $is_minify = false, $has_stamp = false) {
        $files = $this->config['modules'][$module][$type];
        if (!$is_minify) {
            $save_path = $this->config['output_path'] . "/$module.$type";
        } else {
            $save_path = $this->config['output_path'] . "/$module.min.$type";
        }
        $handle = fopen($save_path, "w+");
        foreach ($files as $file) {
            $file = new AssetlaFile($file, array('minify' => true));
            fwrite($handle, $file->dump());
            unset($file);
        }
        fclose($handle);
        if ($has_stamp) {
            $stamp = substr(md5(file_get_contents($save_path)), 0, 6);
            if (rename($save_path, $this->config['output_path'] . "/${module}_${stamp}.min.$type")) {
                $save_path = $this->config['output_path'] . "/${module}_${stamp}.min.$type";
            };
        }
        return $save_path;
    }

    public function get_tags($module, $type, $combined = false) {
        if ($type === 'css') {
            $template = '<link rel="stylesheet" href="%s">';
        } elseif ($type === 'js') {
            $template = '<script src="%s"></script>';
        }
        if ($combined == true) {
            $path = $this->combine($module, $type);
            return sprintf($template, $path);
        } else {
            $paths = $this->config['modules'][$module][$type];
            $html = array();
            foreach ($paths as $path) {
                $path = $this->_filter($path);
                $html[] = sprintf($template, $path);
            }
            return implode("\n", $html);
        }
    }

    /**
     * Generate CSS link tags belonging to a specific module.
     */
    public function stylesheet_tags($module, $combined = false) {
        return $this->get_tags($module, 'css', $combined);
    }

    /**
     * Generate JavaScript script tags belonging to a specific module.
     */
    public function javascript_tags($module, $combined = false) {
        return $this->get_tags($module, 'js', $combined);
    }

}
