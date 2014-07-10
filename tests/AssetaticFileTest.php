<?php

require './vendor/autoload.php';
require './src/AssetaticFile.php';

class AsstaticFileTest extends PHPUnit_Framework_TestCase
{
    private $test_files   = array('tests/fixture/a.js', 'tests/fixture/b.coffee');
    private $result_files = array('tests/fixture/a_result.js', 'tests/fixture/b_result.js', 'tests/fixture/a.min.js');

    public function testDumpJs()
    {
        $file = $this->test_files[0];
        $value = new AssetaticFile($file);
        $value = $value->dump();
        $expected = file_get_contents($this->result_files[0]);
        $this->assertEquals($expected, $value);
    }

    public function testDumpJsMin()
    {
        $file = $this->test_files[0];
        $value = new AssetaticFile($file, array('minify' => true));
        $value = trim($value->dump());
        $expected = trim(file_get_contents($this->result_files[2]));
        $this->assertEquals($expected, $value);
    }

    public function testDumpCoffee()
    {
        $file = $this->test_files[1];
        $value = new AssetaticFile($file);
        $value = $value->dump();
        $expected = file_get_contents($this->result_files[1]);
        $this->assertEquals($expected, $value);
    }

    public function testDumpCoffeeMin()
    {
        $file = $this->test_files[0];
        $value = new AssetaticFile($file, array('minify' => true));
        $value = trim($value->dump());
        $expected = trim(file_get_contents($this->result_files[2]));
        $this->assertEquals($expected, $value);
    }

    public function testDumpSass()
    {
        $value = new AssetaticFile('tests/fixture/sass.sass');
        $value = $value->dump();
        $expected = file_get_contents('tests/fixture/sass.css');
        $this->assertEquals($expected, $value);
    }

    public function testDumpScss()
    {
        $value = new AssetaticFile('tests/fixture/scss.scss');
        $value = $value->dump();
        $expected = file_get_contents('tests/fixture/scss.css');
        $this->assertEquals($expected, $value);
    }

    public function testDumpSassMin()
    {
        $value = new AssetaticFile('tests/fixture/sass.sass');
        $value = $value->dump();
        $expected = file_get_contents('tests/fixture/sass.min.css');
        $this->assertEquals($expected, $value);
    }

    public function testDumpScssMin()
    {
        $value = new AssetaticFile('tests/fixture/scss.scss');
        $value = $value->dump();
        $expected = file_get_contents('tests/fixture/scss.min.css');
        $this->assertEquals($expected, $value);
    }


}


