<?php

require_once './src/AssetaticFile.php';

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
        unset($value);
    }

    public function testDumpSass()
    {
        $value = new AssetaticFile('tests/fixture/sass.sass');
        $value = $value->dump();
        $expected = file_get_contents('tests/fixture/sass.css');
        $this->assertEquals($expected, $value);
        unset($value);
    }

    public function testDumpScss()
    {
        $value = new AssetaticFile('tests/fixture/scss.scss');
        $value = $value->dump();
        $expected = file_get_contents('tests/fixture/scss.css');
        $this->assertEquals($expected, $value);
        unset($value);
    }

    public function testDumpSassMin()
    {
        $value = new AssetaticFile('tests/fixture/sass.sass', array('minify' => true));
        $value = $value->dump();
        $expected = file_get_contents('tests/fixture/sass.min.css');
        $this->assertEquals($expected, $value);
        unset($value);
    }

    public function testDumpScssMin()
    {
        $value = new AssetaticFile('tests/fixture/scss.scss', array('minify' => true));
        $value = $value->dump();
        $expected = file_get_contents('tests/fixture/scss.min.css');
        $this->assertEquals($expected, $value);
        unset($value);
    }

    public function testSave($targetFolder = '.')
    {
        $file = new AssetaticFile('tests/fixture/scss.scss', array('outputFolder' => 'tests/output'));
        $expected = file_get_contents('tests/fixture/scss.css');

        // Set output path via config
        $file->save();
        $value = file_get_contents('tests/output/scss.css');
        $this->assertEquals($expected, $value);
        unlink('tests/output/scss.css');

        // Set output path via method parameter
        $file->save('tests/output');
        $value = file_get_contents('tests/output/scss.css');
        $this->assertEquals($expected, $value);
        unlink('tests/output/scss.css');

        // Save to an non-existent path
        $file->save('tests/non_exist');
        $value = file_get_contents('tests/non_exist/scss.css');
        $this->assertEquals($expected, $value);
        unlink('tests/non_exist/scss.css');
        rmdir('tests/non_exist');

        unset($file);
    }

}


