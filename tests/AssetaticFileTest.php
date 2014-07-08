<?php

require './vendor/autoload.php';
require './src/AssetaticFile.php';

class AsstaticFileTest extends PHPUnit_Framework_TestCase
{
    public $test_files   = array('tests/fixture/a.js', 'tests/fixture/b.coffee');
    public $result_files = array('tests/fixture/a_result.js', 'tests/fixture/b_result.js');

    public function testOutput()
    {
        // JS
        $file = $this->test_files[0];
        $value = new AssetaticFile($file);
        $value = $value->output();
        $expected = file_get_contents($this->result_files[0]);
        $this->assertEquals($expected, $value);

        // Coffee
        $value = new AssetaticFile($this->test_files[1]);
        $value = $value->output();
        $expected = file_get_contents($this->result_files[1]);
        $this->assertEquals($value, $expected);
    }
}

