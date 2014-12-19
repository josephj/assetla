<?php
require_once dirname(__FILE__) . '/../../src/Assetla.php';

use Aws\Common\Aws;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class DeployCommand extends CLIFramework\Command {

    private $origin_config_path;
    private $output_config_path;
    private $assetla;
    private $s3;
    private $s3_setting;

    public function brief() {
        return 'Deploy the precompiled assets to S3 bucket';
    }

    public function aliases() {
        return array('dep');
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
        // Module config
        $config = include $this->origin_config_path;

        // Check deply config
        if (empty($config['deploy'])) {
            throw new Exception("You haven't defined the deploy setting in config file.");
        }
        // S3 Instance
        $this->s3_setting = $config['deploy'];
        $s3config = array(
            'key' => $this->s3_setting['key'],
            'secret' => $this->s3_setting['secret']
        );
        try {
            $this->s3 = Aws::factory($s3config)->get('s3');
        } catch (S3Exception $e) {
            $this->logger->error($e->message);
        }
        // Module config
        $config = include $this->origin_config_path;
        $modules = $config['modules'];
        $this->assetla = new Assetla($this->origin_config_path);
        foreach ($modules as $name => $types) {
            foreach ($types as $type => $files) {
                $url = $this->iterate($name, $type);
                $this->logger->info("Generated asset: $url");
                $config['modules'][$name][$type] = array($url);
            }
        }
        // Generate config
        $content = var_export($config, TRUE);
        $content = "<?php\nreturn $content;\n";
        file_put_contents($this->output_config_path, $content);
        $this->logger->info("Generated config: $this->output_config_path");
    }

    private function iterate($name, $type) {
        $file = $this->assetla->combine($name, $type, true, true);
        // Get MIME Type
        $extension = end(explode('.', $file));
        if ($extension === 'js') {
            $content_type = 'application/x-javascript';
        } else {
            $content_type = 'text/css';
        }
        // Gzip
        if (isset($this->s3_setting['gzip']) && $this->s3_setting['gzip']) {
            $gzfile = str_replace($extension, "gz.$extension", $file);
            $fp = gzopen($gzfile, 'w9');
            gzwrite ($fp, file_get_contents($file));
            gzclose($fp);
            $file = $gzfile;
        }
        // Get path
        $path = $this->s3_setting['path'] . '/' . basename($file);
        $object = array(
            'Bucket' => $this->s3_setting['bucket'],
            'Key' => $path,
            'SourceFile' => $file,
            'ACL' => $this->s3_setting['acl'],
            'ContentType' => $content_type,
            'CacheControl' => sprintf('max-age=%u', 86400 * 7) // 1 day * 7 = 1 week
        );
        if (isset($gzfile)) {
            $object['ContentEncoding'] = 'gzip';
        }
        // Cachec control
        if (isset($this->s3_setting['cache']) && !empty($this->s3_setting['cache'])) {
            $object['CacheControl'] = $this->s3_setting['cache'];
        }
        // Upload
        try {
            $this->s3->putObject($object);
        } catch (S3Exception $e) {
            $this->logger->error("There was an error uploading the file.\n");
        }
        $url = sprintf('https://%s.s3.amazonaws.com/%s', $this->s3_setting['bucket'], $path);
        return $url;
    }

}
