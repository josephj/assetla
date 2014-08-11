#!/usr/bin/env php
<?php
/**
 * Generate a new config.
 *
 * @author Joseph Chiang <josephj6802@gmail.com>
 */

// Required libraries.
require_once dirname(__FILE__) . '/Assetla.php';

set_exception_handler('handleException');

// Basic argument check
$action = $argv[1];
$config_path = $argv[2];
if ( ! isset($action)) {
    throw new Exception('No action specified');
}
if ( ! isset($config_path)) {
    $config_path = './config.php';
}
if ( ! in_array($action, array('precompile', 'deploy'))) {
    throw new Exception('Not a valid action');
}

// Read config file
$assetla = new Assetla($config_path);
$config = include $config_path;
$modules = $config['modules'];
foreach ($modules as $name => $types) {
    foreach ($types as $type => $files) {
        $file = $assetla->combine($name, $type, true, true);
        $config['modules'][$name][$type] = array($file);
    }
}
$content = var_export($config, TRUE);
$content = "<?php\nreturn $content;\n";

// Save to new file
if ($action === 'deploy') {
    $action = 'production';
}
$save_config = str_replace('.php', ".${action}.php", $config_path);
file_put_contents($save_config, $content);
echo $save_config . "\n";

function handleException(Exception $ex) {
    echo $ex->getMessage() . "\n";
}

?>
