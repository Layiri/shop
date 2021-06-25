<?php


use App\config\Config;
use PHPUnit\Framework\TestCase;


class ConfigTest extends TestCase
{

    public function newMockClass()
    {
        $stub = new class() extends Config {
            function getStaticMethod($methodName)
            {
                return self::$methodName();
            }
        };
        return $stub;

    }

    public function testConfig() {
        $stub = $this->newMockClass();
        $config = $stub->getStaticMethod('config');
        $this->assertIsArray($config);
    }


}