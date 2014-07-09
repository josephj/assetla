<?php

require './vendor/autoload.php';
require './src/AssetaticFile.php';

class AsstaticFileTest extends PHPUnit_Framework_TestCase
{
    private $test_files   = array('tests/fixture/a.js', 'tests/fixture/b.coffee');
    private $result_files = array('tests/fixture/a_result.js', 'tests/fixture/b_result.js', 'tests/fixture/a.min.js');

    public function testJsDump()
    {
        $file = $this->test_files[0];
        $value = new AssetaticFile($file);
        $value = $value->dump();
        $expected = file_get_contents($this->result_files[0]);
        $this->assertEquals($expected, $value);
    }

    public function testMinJsDump()
    {
        $file = $this->test_files[0];
        $value = new AssetaticFile($file, array('minify' => true, 'uglifyJsPath' => '/Users/josephj/.nvm/v0.10.10/bin/uglifyjs'));
        $value = trim($value->dump());
        $expected = trim(file_get_contents($this->result_files[2]));
        $this->assertEquals($expected, $value);
    }

    public function testDumpCoffee()
    {
        $file = $this->test_files[1];
        $value = new AssetaticFile($file, array('coffeePath' => '/Users/josephj/.nvm/v0.10.10/bin/coffee'));
        $value = $value->dump();
        $expected = file_get_contents($this->result_files[1]);
        $this->assertEquals($expected, $value);
    }
}


