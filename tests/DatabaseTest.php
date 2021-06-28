<?php


use App\core\Database;

class DatabaseTest extends \PHPUnit\Framework\TestCase
{
      public function newMockClass()
    {
        $stub = new class() extends Database {
            function getStaticMethod($methodName)
            {
                return self::$methodName();
            }
        };
        return $stub;

    }

    public function testConfig() {
        $stub = $this->newMockClass();
        $config = $stub->getStaticMethod('connect');
        $this->asseray($config);
    }

}