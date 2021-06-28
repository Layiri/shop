<?php


use App\core\Model;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    use TestCaseTrait;
    protected $newAnonymousClassFromModel;

    protected function setUp()
    {
        // Create a new instance from the Abstract Class
        $this->newAnonymousClassFromModel = new class extends Model {
            // Just a sample public function that returns this anonymous instance
            protected static function connectDb() : PDO
            {
                return aaa;
            }

            public static function table(): string
            {
                return "table";
            }

        };
    }

    public function testConnectMethod()
    {
        // Let's test the public function we created in the anonymous class
        $this->assertInstanceOf(
            Model::class,
            $this->newAnonymousClassFromModel->connectDb()
        );
    }

    public function testAbstractClassInternalMethod()
    {
        // Let's test a function declared in the Abstract Clas
        $this->assertTrue(
            $this->newAnonymousClassFromModel->connectDb()
        );
    }
}

