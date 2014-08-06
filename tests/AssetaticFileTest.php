<?php

require_once './src/AssetaticFile.php';

class Asstatic_File_Test extends PHPUnit_Framework_TestCase
{

    protected $assetatic_file;
    private $path = 'tests/fixture';

    protected function tearDown() {
        unset($this->assetatic_file);
    }

    public function test_dump_js() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/a.js");
        $value = $this->assetatic_file->dump();
        $expect = file_get_contents("{$this->path}/a_result.js");
        $this->assertEquals($expect, $value);
    }

    public function test_dump_js_min() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/a.js", array('minify' => true));
        $value = $this->assetatic_file->dump();
        $value = trim($this->assetatic_file->dump());
        $expect = trim(file_get_contents("{$this->path}/a.min.js"));
        $this->assertEquals($expect, $value);
    }

    public function test_dump_coffee() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/b.coffee");
        $value = trim($this->assetatic_file->dump());
        $expect = trim(file_get_contents("{$this->path}/b_result.js"));
        $this->assertEquals($expect, $value);
    }

    public function test_dump_coffee_min() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/b.coffee", array('minify' => true));
        $value = $this->assetatic_file->dump();
        $expect = trim(file_get_contents("{$this->path}/b_result.min.js"));
        $this->assertEquals($expect, $value);
    }

    public function test_dump_sass() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/sass.sass");
        $value = $this->assetatic_file->dump();
        $expect = file_get_contents("{$this->path}/sass.css");
        $this->assertEquals($expect, $value);
    }

    public function test_dump_sass_min() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/sass.sass", array('minify' => true));
        $value = $this->assetatic_file->dump();
        $expect = file_get_contents("{$this->path}/sass.min.css");
        $this->assertEquals($expect, $value);
    }

    public function test_dump_scss() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/scss.scss");
        $value = $this->assetatic_file->dump();
        $expect = file_get_contents("{$this->path}/scss.css");
        $this->assertEquals($expect, $value);
    }

    public function test_dump_scss_min() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/scss.scss", array('minify' => true));
        $value = $this->assetatic_file->dump();
        $expect = file_get_contents("{$this->path}/scss.min.css");
        $this->assertEquals($expect, $value);
    }

    public function test_save() {
        $this->assetatic_file = new AssetaticFile("{$this->path}/scss.scss", array('output_folder' => 'tests/output'));
        $expect = file_get_contents("{$this->path}/scss.css");

        // Set output path via config
        $this->assetatic_file->save();
        $value = file_get_contents("{$this->path}/scss.css");
        $this->assertEquals($expect, $value);
        unlink('tests/output/scss.css');

        // Set output path via method parameter
        $this->assetatic_file->save('tests/output');
        $value = file_get_contents('tests/output/scss.css');
        $this->assertEquals($expect, $value);
        unlink('tests/output/scss.css');

        // Save to an non-existent path
        $this->assetatic_file->save('tests/non_exist');
        $value = file_get_contents('tests/non_exist/scss.css');
        $this->assertEquals($expect, $value);
        unlink('tests/non_exist/scss.css');
        rmdir('tests/non_exist');
    }

}


