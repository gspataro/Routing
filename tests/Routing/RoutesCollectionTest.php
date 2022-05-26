<?php

namespace Tests;

use GSpataro\Routing;
use PHPUnit\Framework\TestCase;
use Tests\Util\ExampleController;

final class RoutesCollectionTest extends TestCase
{
    /**
     * Get an instance of RoutesCollection with some pre-defined routes
     * @return Routing\RoutesCollection
     */

    public function getInstance(): Routing\RoutesCollection
    {
        $routesCollection = new Routing\RoutesCollection();
        $routesCollection->feed([
            "home" => [
                "path" => "/",
                "callback" => [ExampleController::class, "exampleMethod"]
            ],
            "article" => [
                "path" => "/article/{id:int}",
                "callback" => [ExampleController::class, "exampleMethod"]
            ],
            "complex" => [
                "path" => "/user/{action:string}/{id:int}",
                "callback" => [ExampleController::class, "exampleMethod"]
            ]
        ]);

        return $routesCollection;
    }

    /**
     * @testdox Test RoutesCollection::add() with duplicate tag
     * @covers RoutesCollection::add
     * @return void
     */

    public function testAddAlreadyFoundException(): void
    {
        $this->expectException(Routing\Exception\RouteFoundException::class);

        $routesCollection = new Routing\RoutesCollection();
        $routesCollection->add(
            "test",
            "/",
            [Routing\Method::GET],
            [ExampleController::class, "exampleMethod"],
            []
        );
        $routesCollection->add(
            "test",
            "/",
            [Routing\Method::GET],
            [ExampleController::class, "exampleMethod"],
            []
        );
    }

    /**
     * @testdox Test RoutesCollection::add() with invalid method
     * @covers RoutesCollection::add
     * @return void
     */

    public function testAddWithInvalidMethod(): void
    {
        $this->expectException(Routing\Exception\InvalidRouteMethodException::class);

        $routesCollection = new Routing\RoutesCollection();
        $routesCollection->add(
            "test",
            "/",
            ["GET"],
            [ExampleController::class, "exampleMethod"],
            []
        );
    }

    /**
     * @testdox Test RoutesCollection::add() with invalid callback
     * @covers RoutesCollection::add
     * @return void
     */

    public function testAddWithInvalidCallback(): void
    {
        $this->expectException(Routing\Exception\InvalidRouteCallbackException::class);

        $routesCollection = new Routing\RoutesCollection();
        $routesCollection->add(
            "test",
            "/",
            [Routing\Method::GET],
            [ExampleController::class, "nonExistingMethod"],
            []
        );
    }

    /**
     * @testdox Test RoutesCollection::add() method with invalid middleware
     * @covers RoutesCollection::add
     * @return void
     */

    public function testAddWithInvalidMiddleware(): void
    {
        $this->expectException(Routing\Exception\InvalidRouteMiddlewareException::class);

        $routesCollection = new Routing\RoutesCollection();
        $routesCollection->add(
            "test",
            "/",
            [Routing\Method::GET],
            [ExampleController::class, "exampleMethod"],
            ["nonExistingMiddleware"]
        );
    }

    /**
     * @testdox Test RoutesCollection::feed() method with incomplete route definition
     * @covers RoutesCollection::feed
     * @return void
     */

    public function testFeedWithIncompleteRoute(): void
    {
        $this->expectException(Routing\Exception\InvalidRouteParamsException::class);

        $routesCollection = new Routing\RoutesCollection();
        $routesCollection->feed([
            "test" => [
                "path" => "/",
                //"callback" => [ExampleController::class, "exampleMethod"]
            ]
        ]);
    }

    /**
     * @testdox Test RoutesCollection::has() method
     * @covers RoutesCollection::has
     * @return void
     */

    public function testHas(): void
    {
        $routesCollection = $this->getInstance();
        $this->assertTrue($routesCollection->has("home"));
        $this->assertFalse($routesCollection->has("nonexisting"));
    }

    /**
     * @testdox Test RoutesCollection::get() method with non existing route
     * @covers RoutesCollection::get
     * @return void
     */

    public function testGet(): void
    {
        $this->expectException(Routing\Exception\RouteNotFoundException::class);

        $routesCollection = $this->getInstance();
        $routesCollection->get("nonexisting");
    }

    /**
     * @testdox Test RoutesCollection::getPath() method
     * @covers RoutesCollection::getPath
     * @return void
     */

    public function testGetPath(): void
    {
        $routesCollection = $this->getInstance();
        $this->assertEquals(
            $routesCollection->getPath(
                "article",
                [
                    "id" => 1
                ]
            ),
            "/article/1"
        );
        $this->assertEquals(
            $routesCollection->getPath(
                "complex",
                [
                    "action" => "edit",
                    "id" => 1
                ]
            ),
            "/user/edit/1"
        );
    }
}
