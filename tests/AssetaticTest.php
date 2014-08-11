<?php

require_once './src/Assetla.php';

class AsstaticTest extends PHPUnit_Framework_TestCase {

    protected $assetla;

    protected function setUp() {
        $this->assetla = new assetla('tests/fixture/config.php');
    }

    protected function tearDown() {
        unset($this->assetla);
    }

    //public function test_find() {
        //$value = assetla::find('css/a.css', array('tests/fixture/assets'));
        //$expected = 'tests/fixture/assets/css/a.css';
        //$this->assertEquals($expected, $value);
    //}

    public function test_find_file() {
        //$assetla = $this->assetla;

        //$value = $assetla->findFile('tests/fixture/assets/css/a.css');
        //$expected = 'tests/fixture/assets/css/a.css';
        //$this->assertEquals($expected, $value);

        //$value = $assetla->findFile('js/a.js');
        //$expected = 'tests/fixture/assets/js/a.js';
        //$this->assertEquals($expected, $value);

        //$value = $assetla->findFile('js/jquery.js');
        //$expected = 'tests/fixture/vendor/js/jquery.js';
        //$this->assertEquals($expected, $value);

        //$value = $assetla->findFile('css/reset.css');
        //$expected = 'tests/fixture/vendor/css/reset.css';
        //$this->assertEquals($expected, $value);
    }

    public function test_stylesheet_tags() {
        $assetla = $this->assetla;

        // General CSS
        $value = $assetla->stylesheet_tags('admin_core');
        $expected =<<<PRINTED
<link rel="stylesheet" href="tests/fixture/assets/css/a.css">
<link rel="stylesheet" href="tests/fixture/assets/output/b.css">
<link rel="stylesheet" href="tests/fixture/vendor/css/reset.css">
PRINTED;
        $this->assertEquals($expected, $value);

        // Combined CSS
        $value = $assetla->stylesheet_tags('admin_core', true);
        $expected = '<link rel="stylesheet" href="tests/fixture/assets/output/admin_core.css">';
        $this->assertEquals($expected, $value);
    }

    public function test_javascript_tags() {
        $assetla = $this->assetla;

        // General JS
        $value = $assetla->javascript_tags('admin_core');
        $expected =<<<PRINTED
<script src="tests/fixture/vendor/js/jquery.js"></script>
<script src="tests/fixture/assets/js/a.js"></script>
<script src="tests/fixture/assets/output/b.js"></script>
PRINTED;
        $this->assertEquals($expected, $value);

        // Combined JS
        $value = $assetla->javascript_tags('admin_core', true);
        $expected = '<script src="tests/fixture/assets/output/admin_core.js"></script>';
        $this->assertEquals($expected, $value);
    }

    public function test_combine() {
        $assetla = $this->assetla;

        $assetla->combine('admin_core', 'css');
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.css'));

        $assetla->combine('admin_core', 'css', true);
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.min.css'));

        $assetla->combine('admin_core', 'js');
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.js'));

        $assetla->combine('admin_core', 'js', true);
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.js'));
    }

}
