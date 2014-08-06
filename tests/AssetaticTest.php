<?php

require_once './src/Assetatic.php';

class AsstaticTest extends PHPUnit_Framework_TestCase {

    protected $assetatic;

    protected function setUp() {
        $this->assetatic = new Assetatic('tests/fixture/config.php');
    }

    protected function tearDown() {
        unset($this->assetatic);
    }

    public function test_find() {
        $value = Assetatic::find('css/a.css', array('tests/fixture/assets'));
        $expected = 'tests/fixture/assets/css/a.css';
        $this->assertEquals($expected, $value);
    }

    public function test_find_file() {
        $assetatic = $this->assetatic;

        $value = $assetatic->findFile('css/a.css');
        $expected = 'tests/fixture/assets/css/a.css';
        $this->assertEquals($expected, $value);

        $value = $assetatic->findFile('js/a.js');
        $expected = 'tests/fixture/assets/js/a.js';
        $this->assertEquals($expected, $value);

        $value = $assetatic->findFile('js/jquery.js');
        $expected = 'tests/fixture/vendor/js/jquery.js';
        $this->assertEquals($expected, $value);

        $value = $assetatic->findFile('css/reset.css');
        $expected = 'tests/fixture/vendor/css/reset.css';
        $this->assertEquals($expected, $value);
    }

    public function test_stylesheet_tags() {
        $assetatic = $this->assetatic;

        // General CSS
        $value = $assetatic->stylesheet_tags('admin_core');
        $expected =<<<PRINTED
<link rel="stylesheet" href="tests/fixture/assets/css/a.css">
<link rel="stylesheet" href="tests/fixture/assets/output/b.css">
<link rel="stylesheet" href="tests/fixture/vendor/css/reset.css">
PRINTED;
        $this->assertEquals($expected, $value);

        // Combined CSS
        $value = $assetatic->stylesheet_tags('admin_core', true);
        $expected = '<link rel="stylesheet" href="tests/fixture/assets/output/admin_core.css">';
        $this->assertEquals($expected, $value);
    }

    public function test_javascript_tags() {
        $assetatic = $this->assetatic;

        // General JS
        $value = $assetatic->javascript_tags('admin_core');
        $expected =<<<PRINTED
<script src="tests/fixture/vendor/js/jquery.js"></script>
<script src="tests/fixture/assets/js/a.js"></script>
<script src="tests/fixture/assets/output/b.js"></script>
PRINTED;
        $this->assertEquals($expected, $value);

        // Combined JS
        $value = $assetatic->javascript_tags('admin_core', true);
        $expected = '<script src="tests/fixture/assets/output/admin_core.js"></script>';
        $this->assertEquals($expected, $value);
    }

    public function test_combine() {
        $assetatic = $this->assetatic;

        $assetatic->combine('admin_core', 'css');
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.css'));

        $assetatic->combine('admin_core', 'css', true);
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.min.css'));

        $assetatic->combine('admin_core', 'js');
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.js'));

        $assetatic->combine('admin_core', 'js', true);
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.js'));
    }

}
