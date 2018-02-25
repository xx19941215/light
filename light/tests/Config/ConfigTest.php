<?php
namespace Light\Tests\Config;

use Light\Config\Config;
use \PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected $config;
    protected $data;

    public function setUp()
    {
        $this->config = new Config($this->data = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'null' => null,
            'associate' => [
                'x' => 'xxx',
                'y' => 'yyy',
            ],
            'array' => [
                'aaa',
                'zzz',
            ],
            'x' => [
                'z' => 'zoo',
            ],
        ]);

        parent::setUp();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(Config::class, $this->config);
    }

    public function testSet()
    {
        $this->config->set('y', ['yyy']);
        $this->assertEquals(['yyy'], $this->config->get('y'));
    }

    public function testGet()
    {
        $this->assertEquals(['z' => 'zoo'], $this->config->get('x'));
    }

    public function testAll()
    {
        $this->assertEquals($this->data, $this->config->all());
    }

    public function testHas()
    {
        $this->assertTrue($this->config->has('foo'));
    }
}
