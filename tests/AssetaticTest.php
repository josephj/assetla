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
    }
}

