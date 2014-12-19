<?php
/**
 * Generate a new config.
 *
 * @author Joseph Chiang <josephj6802@gmail.com>
 */

class CLI extends \CLIFramework\Application {
    public function init() {
        parent::init();
        $this->command('precompile', 'PrecompileCommand');
        $this->command('deploy', 'DeployCommand');
    }
}

