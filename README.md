# Shop online API

## Table of contents
* [Intro](#intro)
* [Configuration](#configuration-info)
* [Database init](#database-init)
* [Users](#add-new-review)
* [API](#api)


## Init project

1. `export USID=${UID} && docker-compose up -d --remove-orphans --build`
1. `docker-compose run --rm composer install`
1, `docker-compose up -d`
   
## Database init
* run `localhost/migrations/migrate`


## List of all routes

* Add new Product (`METHOD POST`)
  `localhost/api/product/add`
  * `form_field` title=fallout, price=10, user_id=1, status=1

* List of all products (`METHOD GET`)
  `localhost/api/product/list-product?page=1`
  * `params` page=1
  
* Remove product (`METHOD GET`)
  `localhost/api/product/remove`
  * `params` id=1

* Update product (`METHOD POST`)
  `localhost/api/product/update`
  * `form_field` id=1, title=fallout, price=12, user_id=1, status=1

* Create new cart (`METHOD GET`)
  `localhost/api/cart/create`
  * `params` user_id=1

* Add products to cart (`METHOD GET`)
  `localhost/api/cart/add-products`
  * `params` product_id=1, user_id=1, quantiy=5 (Max 10)

* Remove product from cart (`METHOD GET`)
  `localhost/api/cart/remove-products`
  * `params` product_id=1, user_id=1, quantiy=5 (Max 10)

* Remove a Cart (`METHOD GET`)
  `localhost/api/cart/remove`
  * `params` user_id=1, cart_id=1

* List of all products in cart (`METHOD GET`)
  `localhost/api/cart/list-products`
  * `params` user_id=1, cart_id=1

* Run migration  (`METHOD GET`)
  `localhost/migrations/migrate`

* Drop all table in database  (`METHOD GET`)
  `localhost/migrations/down`
  