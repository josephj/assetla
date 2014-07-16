<?php
require './vendor/autoload.php';
require './src/Assetatic.php';

class AsstaticTest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        //$this->assetatic = new Assetatic('tests/fixture/config.php');
    }

    public function testFind()
    {
        $value = Assetatic::find('css/a.css', array('tests/fixture/assets'));
        $expected = 'tests/fixture/assets/css/a.css';
        $this->assertEquals($expected, $value);
    }

    public function testFindFile()
    {
        $assetatic = new Assetatic('tests/fixture/config.php');

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

        unset($assetatic);
    }

    //public function testCompile()
    //{
        //$assetatic = new Assetatic('tests/fixture/config.php');

        //$value = $assetatic->compile('css/sass.sass');
        //$expected = file_get_contents('tests/fixture/assets/css/sass.css');
        //$this->assertEquals($expected, $value);

        //$value = $assetatic->compile('js/coffee.coffee');
        //$expected = file_get_contents('tests/fixture/assets/js/coffee.js');
        //$this->assertEquals($expected, $value);

        //$value = $assetatic->compile('js/coffee.coffee', true);
        //$expected = file_get_contents('tests/output/coffee.min.js');
        //$this->assertEquals($expected, $value);

        //unset($assetatic);

    //}

    public function testStylesheetTags()
    {
        $assetatic = new Assetatic('tests/fixture/config.php');
        $value = $assetatic->stylesheet_tags('admin_core');
        $expected =<<<PRINTED
<link rel="stylesheet" href="tests/fixture/assets/css/a.css">
<link rel="stylesheet" href="tests/fixture/assets/output/b.css">
<link rel="stylesheet" href="tests/fixture/assets/css/reset.css">
PRINTED;
        $this->assertEquals($expected, $value);
        unset($assetatic);
    }

    public function testJavaScriptTags()
    {
        $assetatic = new Assetatic('tests/fixture/config.php');
        $value = $assetatic->javascript_tags('admin_core');
        $expected =<<<PRINTED
<script src="tests/fixture/assets/js/jquery.js"></script>
<script src="tests/fixture/assets/js/a.js"></script>
<script src="tests/fixture/assets/output/b.js"></script>
PRINTED;
        $this->assertEquals($expected, $value);
        unset($assetatic);
    }

}

