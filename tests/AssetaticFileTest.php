<?php

require './vendor/autoload.php';
require './src/AssetaticFile.php';

class AsstaticFileTest extends PHPUnit_Framework_TestCase
{
    public $test_files = array('test/fixture/a.js', 'test/fixture/b.coffee');
    public $result_files = array('test/fixture/a_result.js', 'test/fixture/b_result.js');

    public function testOutput()
    {
        // JS
        $value = new AssetaticFile($this->test_files[0]);
        $value = $value->output();
        $expected = file_get_contents($this->result_files[0]);
        $this->assertEquals($value, $expected);

        // Coffee
        $value = new AssetaticFile($this->test_files[1]);
        $value = $value->output();
        $expected = file_get_contents($this->result_files[1]);
        $this->assertEquals($value, $expected);
    }
}

