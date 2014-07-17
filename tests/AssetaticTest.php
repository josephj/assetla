<?php

require_once './src/Assetatic.php';

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

    public function testStylesheetTags()
    {
        $assetatic = new Assetatic('tests/fixture/config.php');

        // General
        $value = $assetatic->stylesheet_tags('admin_core');
        $expected =<<<PRINTED
<link rel="stylesheet" href="tests/fixture/assets/css/a.css">
<link rel="stylesheet" href="tests/fixture/assets/output/b.css">
<link rel="stylesheet" href="tests/fixture/vendor/css/reset.css">
PRINTED;
        $this->assertEquals($expected, $value);

        // Combine
        $value = $assetatic->stylesheet_tags('admin_core', true);
        $expected = '<link href="tests/fixture/assets/output/admin_core.css">';
        $this->assertEquals($expected, $value);

        unset($assetatic);
    }

    public function testJavaScriptTags()
    {
        $assetatic = new Assetatic('tests/fixture/config.php');

        // Separate
        $value = $assetatic->javascript_tags('admin_core');
        $expected =<<<PRINTED
<script src="tests/fixture/vendor/js/jquery.js"></script>
<script src="tests/fixture/assets/js/a.js"></script>
<script src="tests/fixture/assets/output/b.js"></script>
PRINTED;
        $this->assertEquals($expected, $value);

        // Combine
        $value = $assetatic->javascript_tags('admin_core', true);
        $expected = '<script src="tests/fixture/assets/output/admin_core.js"></script>';
        $this->assertEquals($expected, $value);


        unset($assetatic);
    }

    public function testCombine()
    {
        $assetatic = new Assetatic('tests/fixture/config.php');

        $assetatic->combine('admin_core', 'css');
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.css'));

        $assetatic->combine('admin_core', 'css', true);
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.min.css'));

        $assetatic->combine('admin_core', 'js');
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.js'));

        $assetatic->combine('admin_core', 'js', true);
        $this->assertTrue(file_exists('tests/fixture/assets/output/admin_core.js'));

        unset($assetatic);
    }

    protected function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
