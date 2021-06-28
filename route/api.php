<?php


use App\controllers\api\CartController;
use App\controllers\api\ProductController;
use App\controllers\MigrationController;
use App\core\Dispatcher;


(new Dispatcher())
    ->routing('POST /api/product/add', function () {
        (new ProductController())->addAction();
    })
    ->routing('GET /api/product/list-product', function ($params) {
        (new ProductController())->listProductsAction($params);
    })
    ->routing('GET /api/product/remove', function ($params) {
        (new ProductController())->removeAction($params);
    })
    ->routing('POST /api/product/update', function () {
        (new ProductController())->updateAction();
    })
    ->dispatch();


(new Dispatcher())
    ->routing('GET /api/cart/add-products', function ($params) {
        (new CartController())->addProductAction($params);
    })
    ->routing('GET /api/cart/create', function ($params) {
        (new CartController())->createCartAction($params);
    })
    ->routing('GET /api/cart/remove', function ($params) {
        (new CartController())->removeCartAction($params);
    })
    ->routing('GET /api/cart/remove-products', function ($params) {
        (new CartController())->removeProductAction($params);
    })
    ->routing('GET /api/cart/list-products', function ($params) {
        (new CartController())->listProductsAction($params);
    })
    ->dispatch();

(new Dispatcher())
    ->routing('GET /migrations/migrate', function () {
        (new MigrationController())->up();
    })
    ->routing('GET /migrations/down', function () {
        (new MigrationController())->down();
    })
    ->dispatch();
