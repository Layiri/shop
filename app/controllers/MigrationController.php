<?php


namespace App\controllers;


use App\core\Model;
use App\models\Migration;

class MigrationController extends BaseController
{

    public function up()
    {


        $create_table_users = "
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `email` VARCHAR(100) NOT NULL UNIQUE,
                `phone` VARCHAR(50) NOT NUll UNIQUE,
                `token` VARCHAR(255) NOT NUll UNIQUE,
                `created_at` INT(11) NOT NULL,
                `updated_at` INT(11) NOT NULL
            )ENGINE=InnoDB";

        $create_table_products = "
            CREATE TABLE IF NOT EXISTS `products`(
                `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT(11) NOT NULL,
                `title` VARCHAR(255) NOT NULL UNIQUE,
                `price` DECIMAL (6,2) NOT NULL,
                `status` TINYINT(1)  DEFAULT 0,
                `created_at` INT(11) NOT NULL, 
                `updated_at` INT(11) NOT NULL,
    
                KEY `idx-users_products_table-user_id` (`user_id`),
                CONSTRAINT `fk-users_products_table-user_id` FOREIGN KEY (`user_id`)
                REFERENCES `users` (`id`) ON DELETE CASCADE
                )ENGINE=InnoDB;";


        $create_table_order = "
            CREATE TABLE IF NOT EXISTS `orders`(
                `id` INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
                `user_id` INT(11) NOT NULL,
                `total_price` DECIMAL(6,2) DEFAULT 0.00,
                `payment_status` TINYINT(1) DEFAULT 0,
                `created_at` INT(11) NOT NULL,
                `updated_at` INT(11) NOT NULL,
                
                KEY `idx-users_orders_table-user_id` (`user_id`),
                CONSTRAINT `fk-users_orders_table-user_id` FOREIGN KEY (`user_id`)
                REFERENCES `users` (`id`) ON DELETE CASCADE
                )ENGINE=InnoDB;";


        $create_table_items = "
            CREATE TABLE IF NOT EXISTS `items`(
                `product_id` INT(11) NOT NULL,
                `order_id` INT(11) NOT NULL,
                `status` TINYINT(1) DEFAULT 0,
                `quantity` INT(11) DEFAULT 0,
                `created_at` INT(11) NOT NULL,
                `updated_at` INT(11) NOT NULL, 
    
                KEY `idx-product_table-product_id` (`product_id`),
                CONSTRAINT `fk-product_table-product_id` FOREIGN KEY (`product_id`)
                REFERENCES `products` (`id`),

                KEY `idx-orders_table-order_id` (`order_id`),
                CONSTRAINT `fk-order_table-order_id` FOREIGN KEY (`order_id`)
                REFERENCES `orders` (`id`) ON DELETE CASCADE
                )ENGINE=InnoDB;";


        $insert_user_sql = '
                    INSERT INTO users(name, email, phone, token, created_at, updated_at) 
                    VALUES(:name, :email, :phone, :token, :created_at, :updated_at)';

        $array_user[] = [
            'name' => 'User User',
            'email' => 'user@user',
            'phone' => '987654321',
            'token' => 'qLbj8MT?b+Bzv+K6@hq$YKru_XPLPWXwpR=3Um%hURB@vUwdU4cDk7YftxB$N=3wx3u2yuSdePC^XXUsX@=gCDCe!-4Q5$PX5KvZysrh&!jk!yYtQxLx9XKMNcWerzr*',
            'created_at' => time(),
            'updated_at' => time(),
        ];
        $array_user[] = [
            'name' => 'User2 User2',
            'email' => 'user2@user.com',
            'phone' => '123456789',
            'token' => 'x&J!aPN6&3Kjv#V&yKMBak&m?#DY=W=87-gkx7R@jCqP9eHTcpAf#bFJ3Le3zw+5wNt3=jz_zwdfAA$us+G&Ct&tdd@qwUGX6?zwjhe^cXCRv3m$ckA!7N*gmX8uu!ZD',
            'created_at' => time(),
            'updated_at' => time(),
        ];

        $insert_product_sql = '
                    INSERT INTO products(user_id,title, price, status, created_at, updated_at) 
                    VALUES(:user_id, :title, :price, :status, :created_at, :updated_at)';

        $array_product[] = [
            'user_id' => 1,
            'title' => 'Fallout',
            'price' => 1.99,
            'status' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ];
        $array_product[] = [
            'user_id' => 1,
            'title' => "Don't Starve",
            'price' => 2.99,
            'status' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ];
        $array_product[] = [
            'user_id' => 1,
            'title' => "Baldur's Gate",
            'price' => 3.99,
            'status' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ];
        $array_product[] = [
            'user_id' => 1,
            'title' => "Icewind Dale",
            'price' => 4.99,
            'status' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ];
        $array_product[] = [
            'user_id' => 1,
            'title' => "Bloodborne",
            'price' => 5.99,
            'status' => 1,
            'created_at' => time(),
            'updated_at' => time(),
        ];

        try {
            $migration = new Migration();

            $migration->run($create_table_users);
            $migration->run($create_table_products);
            $migration->run($create_table_order);
            $migration->run($create_table_items);

            $migration->runPrepare($insert_user_sql, $array_user);
            $migration->runPrepare($insert_product_sql, $array_product);


            echo 'init database was successfully';

        } catch (\Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    public function down()
    {
        try {
            $migration = new Migration();
            $migration->run("DROP TABLE items");
            $migration->run("DROP TABLE orders");
            $migration->run("DROP TABLE products");
            $migration->run("DROP TABLE users");

            echo 'Database was down';
        }catch (\Exception $e){

            echo $e->getMessage();
            die;
        }


    }
}