<?php


use App\controllers\api\CartController;
use App\controllers\api\ProductController;
use App\core\Dispatcher;
//var_dump($pattern);
//var_dump($action);
//die;



(new Dispatcher())
    ->routing('GET /api/product/list-product?papa=', function ($params) {
        (new ProductController())->listProductsAction($params);
    })
    ->routing('/api/product/add', function () {
        (new ProductController())->addAction();
    })
    ->dispatch();

(new Dispatcher())
    ->routing('GET /api/product/list-product/{aa}', function ($params) {
        (new ProductController())->listProductsAction($params);
    })
    ->dispatch();

(new Dispatcher())
    ->routing('/api/product/remove', function ($params) {
        (new ProductController())->removeAction($params);
    })
    ->dispatch();

(new Dispatcher())
    ->routing('/api/product/update', function () {
        (new ProductController())->updateAction();
    })
    ->dispatch();



//(new Dispatcher())
//    ->routing('/api/cart/add-product', function () {
//        (new CartController())->addProductAction();
//    })
//    ->routing('/api/cart/create', function () {
//        (new CartController())->createAction();
//    })
//    ->routing('/api/cart/list-products/user{id}', function ($params) {
//        (new CartController())->removeAction($params);
//    })
//    ->routing('/api/cart/update', function ($params) {
//        (new CartController())->updateAction($params);
//    })
//    ->dispatch();

(new Dispatcher())
    ->routing('GET /migrations/migrate', function () {
        (new \App\controllers\MigrationController())->up();
    })
    ->dispatch();
