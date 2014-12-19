<?php
require_once dirname(__FILE__) . '/../../src/Assetla.php';

class PrecompileCommand extends CLIFramework\Command {

    private $origin_config_path;
    private $output_config_path;
    private $assetla;

    public function brief() {
        return 'Precompile assets before deploying to different environment';
    }

    public function aliases() {
        return array('pre');
    }

    public function options($opts) {
      $opts->add('c|config:', 'The path of configuration file')
           ->valueName('config')
           ->isa('file');
      $opts->add('o|output:', 'The new configuration file with compiled assets path')
           ->valueName('output');
    }

    public function execute() {
        // Check options
        $options = $this->getOptions();
        $this->origin_config_path = $options['config']->value;
        $this->output_config_path = $options['output']->value;
        if (empty($this->origin_config_path)) {
            throw new Exception('The <config> option is required');
        }
        if (empty($this->output_config_path)) {
            throw new Exception('The <output> option is required');
        }
        // Update config
        $config = include $this->origin_config_path;
        $modules = $config['modules'];
        $this->assetla = new Assetla($this->origin_config_path);
        foreach ($modules as $name => $types) {
            foreach ($types as $type => $files) {
                $file = $this->iterate($name, $type);
                $this->logger->info("Generated asset: $file");
                $config['modules'][$name][$type] = array($file);
            }
        }
        // Generate config
        $content = var_export($config, TRUE);
        $content = "<?php\nreturn $content;\n";
        file_put_contents($this->output_config_path, $content);
        $this->logger->info("Generated config: $this->output_config_path");
    }

    private function iterate($name, $type) {
        return $this->assetla->combine($name, $type, true, true);
    }

}
