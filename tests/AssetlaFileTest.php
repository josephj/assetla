<?php

require_once './src/AssetlaFile.php';

class AssetlaFile_Test extends PHPUnit_Framework_TestCase
{

    protected $assetla_file;
    protected $path = 'tests/fixture';

    protected function tearDown() {
        unset($this->assetla_file);
    }

    public function test_dump_js() {
        $this->assetla_file = new AssetlaFile("{$this->path}/a.js");
        $value = $this->assetla_file->dump();
        $expect = file_get_contents("{$this->path}/a_result.js");
        $this->assertEquals($expect, $value);
    }

    public function test_dump_js_min() {
        $this->assetla_file = new AssetlaFile("{$this->path}/a.js", array('minify' => true));
        $value = $this->assetla_file->dump();
        $value = trim($this->assetla_file->dump());
        $expect = trim(file_get_contents("{$this->path}/a.min.js"));
        $this->assertEquals($expect, $value);
    }

    public function test_dump_coffee() {
        $this->assetla_file = new AssetlaFile("{$this->path}/b.coffee");
        $value = trim($this->assetla_file->dump());
        $expect = trim(file_get_contents("{$this->path}/b_result.js"));
        $this->assertEquals($expect, $value);
    }

    public function test_dump_coffee_min() {
        $this->assetla_file = new AssetlaFile("{$this->path}/b.coffee", array('minify' => true));
        $value = $this->assetla_file->dump();
        $expect = trim(file_get_contents("{$this->path}/b_result.min.js"));
        $this->assertEquals($expect, $value);
    }

    public function test_dump_sass() {
        $this->assetla_file = new AssetlaFile("{$this->path}/sass.sass");
        $value = $this->assetla_file->dump();
        $expect = file_get_contents("{$this->path}/sass.css");
        $this->assertEquals($expect, $value);
    }

    public function test_dump_sass_min() {
        $this->assetla_file = new AssetlaFile("{$this->path}/sass.sass", array('minify' => true));
        $value = $this->assetla_file->dump();
        $expect = file_get_contents("{$this->path}/sass.min.css");
        $this->assertEquals($expect, $value);
    }

    public function test_dump_scss() {
        $this->assetla_file = new AssetlaFile("{$this->path}/scss.scss");
        $value = $this->assetla_file->dump();
        $expect = file_get_contents("{$this->path}/scss.css");
        $this->assertEquals($expect, $value);
    }

    public function test_dump_scss_min() {
        $this->assetla_file = new AssetlaFile("{$this->path}/scss.scss", array('minify' => true));
        $value = $this->assetla_file->dump();
        $expect = file_get_contents("{$this->path}/scss.min.css");
        $this->assertEquals($expect, $value);
    }

    public function test_save() {
        $this->assetla_file = new AssetlaFile("{$this->path}/scss.scss", array('output_path' => 'tests/output'));
        $expect = file_get_contents("{$this->path}/scss.css");

        // Set output path via config
        $this->assetla_file->save();
        $value = file_get_contents("{$this->path}/scss.css");
        $this->assertEquals($expect, $value);
        unlink('tests/output/scss.css');

        // Set output path via method parameter
        $this->assetla_file->save('tests/output');
        $value = file_get_contents('tests/output/scss.css');
        $this->assertEquals($expect, $value);
        unlink('tests/output/scss.css');

        // Save to an non-existent path
        $this->assetla_file->save('tests/non_exist');
        $value = file_get_contents('tests/non_exist/scss.css');
        $this->assertEquals($expect, $value);
        unlink('tests/non_exist/scss.css');
        rmdir('tests/non_exist');
    }

}


