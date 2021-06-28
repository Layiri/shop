<?php


namespace App\controllers\api;

use App\controllers\BaseController;
use App\core\Model;
use App\models\Items;
use App\models\Order;
use App\models\Product;
use App\models\User;
use Respect\Validation\Exceptions\ExecutableException;
use Respect\Validation\Validator as v;

class CartController extends BaseController
{

    /**
     * Add product in cart
     *
     * @param array $params
     */
    public function addProductAction(array $params)
    {
        try {
            $product_id = isset($params['product_id']) ? (v::intVal()->validate($params['product_id']) ? $params['product_id'] : false) : false;
            $user_id = isset($params['user_id']) ? (v::intVal()->validate($params['user_id']) ? $params['user_id'] : false) : false;
            $quantity = isset($params['quantity']) ? (v::intVal()->validate($params['quantity']) ? $params['quantity'] : false) : false;

            if ($user_id && $product_id && $quantity !== false) {

                $user = new User();
                $user->setId($user_id);
                if ($user->ifExist()) {
                    $product = new Product();
                    $product->setId($product_id);

                    /**
                     * @var Product $product
                     */
                    $product = $product->one();
                    if ($product && $quantity > 0 && $quantity <= 10) {

                        $order = new Order();
                        $order->setUserId($user_id);
                        $conn = $order->getDb();
                        $conn->beginTransaction();
                        $flag = true;

                        /**
                         * @var Order $active_order
                         */
                        $active_order = $order->findActiveOrder();

                        if (!$active_order) {
                            $active_order = new Order();
                            $active_order->setUserId($user_id);
                            $active_order->setTotalPrice(0.00); // init total
                            $active_order->setPaymentStatus(Order::PAYMENT_STATUS_ACTIVE);
                            $flag = $flag && $active_order->save();
                            $active_order = $active_order->findActiveOrder();
                        }


                        $new_order = new Order();
                        $new_order->setId($active_order->id);
                        $new_order->setUserId($user_id);
                        $new_order->setPaymentStatus(Order::PAYMENT_STATUS_ACTIVE);

                        $items = new Items();
                        $items->setProductId($product->id);
                        $items->setOrderId($new_order->getId());

                        $check = $items->one();


                        if ($check) { //check if product exist
                            if (($check->quantity + $quantity) <= 10) {
                                $items->setQuantity($quantity + $check->quantity);
                                $items->setStatus(Items::ITEMS_ACTIVE);
                                $items->setStatusChecker(Items::ITEMS_ACTIVE);
                                $items->update();
                                $total_payment = ($product->price * $quantity) + $active_order->total_price;
                            } else {
                                $flag = false;
                                $total_payment = $active_order->total_price;
                            }
                        } elseif ($items->checkIfOrderMoreThreeProducts() <= 3) {
                            $total_payment = ($product->price * $quantity) + $active_order->total_price;
                            $items->setQuantity($quantity);
                            $items->setStatus(Items::ITEMS_ACTIVE);
                            $flag = $flag && $items->save();
                        } else {
                            $total_payment = $active_order->total_price;
                            $flag = false;
                        }

                        $new_order->setTotalPrice($total_payment);
                        $flag = $flag && $new_order->update();

                        if ($flag) {
                            // commit the transaction
                            $conn->commit();
                            $this->renderJson([
                                "status" => "success",
                                "cart_id" => $new_order->getId(),
                                "total_price" => round($total_payment, 2),
                                "message" => "Product was added to cart successfully"
                            ]);
                        } else {
                            $conn->rollBack();
                            $this->renderJson([
                                "status" => "error",
                                "message" => "Product was not added to cart successfully"
                            ]);
                        }

                    } else {
                        $this->renderJson([
                            "status" => "error",
                            "message" => "Product not exist or quantity isn't in range[1-10]"
                        ]);
                    }
                } else {
                    $this->renderJson([
                        "status" => "error",
                        "message" => "User not found"
                    ]);
                }
            } else {
                $this->renderJson([
                    "status" => "error",
                    "message" => "Please check your params values or contact admin"
                ]);
            }
        } catch (\Exception $e) {
            $this->renderJson([
                "status" => "error",
                "message" => "Product are not added please, check your data or contact admin"
            ]);
        }
    }

    /**
     * Remove Product From cart
     * @param array $params
     */
    public function removeProductAction(array $params)
    {
        try {
            $product_id = isset($params['product_id']) ? (v::intVal()->validate($params['product_id']) ? $params['product_id'] : false) : false;
            $user_id = isset($params['user_id']) ? (v::intVal()->validate($params['user_id']) ? $params['user_id'] : false) : false;
            $quantity = isset($params['quantity']) ? (v::intVal()->validate($params['quantity']) ? $params['quantity'] : false) : false;

            if ($user_id && $product_id && $quantity !== false) {
                $user = new User();
                $user->setId($user_id);
                if ($user->ifExist()) { // if user exist
                    $product = new Product();
                    $product->setId($product_id);

                    /**
                     * @var Product $product
                     */
                    $product = $product->one();

                    if ($product && $quantity <= 10) {
                        $get_order = new Order();
                        $get_order->setUserId($user_id);

                        $order = new Order();
                        $order->setUserId($user_id);
                        $conn = $order->getDb();
                        $conn->beginTransaction();
                        $flag = true;

                        /**
                         * @var Order $active_order
                         */
                        $active_order = $get_order->findActiveOrder();

                        if (!$active_order) {
                            $active_order = new Order();
                            $active_order->setUserId($user_id);
                            $active_order->setTotalPrice(0.00); // init total
                            $active_order->setPaymentStatus();
                            $flag = $flag && $active_order->save();
                            $active_order = $active_order->findActiveOrder();
                        }

                        $new_order = new Order();
                        $new_order->setId($active_order->id);
                        $new_order->setUserId($user_id);
                        $new_order->setPaymentStatus(Order::PAYMENT_STATUS_ACTIVE);

                        $items = new Items();
                        $items->setProductId($product->id);
                        $items->setOrderId($new_order->getId());


                        $check = $items->one();

                        if ($check) { //check if product exist
                            if (($check->quantity - $quantity) > 0) { // check if quantity > 0
                                $items->setQuantity($check->quantity - $quantity);
                                $items->setStatus(Items::ITEMS_ACTIVE);
                                $items->setStatusChecker(Items::ITEMS_ACTIVE);
                                $flag = $flag && $items->update();
                                $total_payment = $active_order->total_price - ($product->price * $quantity);
                            } elseif (($check->quantity - $quantity) == 0) { // check if quanti ==0
                                $items->setQuantity(0);
                                $items->setStatus(Items::ITEMS_REMOVE);
                                $items->setStatusChecker(Items::ITEMS_ACTIVE);
                                $flag = $flag && $items->update();
                                $total_payment = $active_order->total_price - ($product->price * $quantity);
                            } else {
                                $total_payment = $active_order->total_price;
                                $flag = false;
                            }
                        } else {
                            $total_payment = $active_order->total_price;
                            $flag = false;
                        }

                        $new_order->setTotalPrice($total_payment);
                        $flag = $flag && $new_order->update();

                        if ($flag) {
                            // commit the transaction
                            $conn->commit();
                            $this->renderJson([
                                "status" => "success",
                                "cart_id" => $new_order->getId(),
                                "total_price" => round($total_payment, 2),
                                "message" => "Products was removed  from cart successfully"
                            ]);

                        } else {
                            $conn->rollBack();
                            $this->renderJson([
                                "status" => "error",
                                "message" => "Product wasn't removed from cart successfully"
                            ]);
                        }
                    } else {
                        $this->renderJson([
                            "status" => "error",
                            "message" => "Product not exist or quantity isn't in range[1-10]"
                        ]);
                    }
                } else {
                    $this->renderJson([
                        "status" => "error",
                        "message" => "User not found"
                    ]);
                }
            } else {
                $this->renderJson([
                    "status" => "error",
                    "message" => "User not found"
                ]);
            }
        } catch (\Exception $e) {
            $this->renderJson([
                "status" => "error",
                "message" => "Please check your params values or contact admin"
            ]);
        }
    }

    /**
     * List of all product in cart
     */
    public function listProductsAction($params)
    {
        try {
            $cart_id = isset($params['cart_id']) ? (v::intVal()->validate($params['cart_id']) ? $params['cart_id'] : false) : false;
            $user_id = isset($params['user_id']) ? (v::stringVal()->validate($params['user_id']) ? $params['user_id'] : false) : false;

            if ($user_id && $cart_id) {
                $user = new User();
                $user->setId($user_id);
                if ($user->ifExist()) { // check if user exist

                    $get_order = new Order();
                    $get_order->setUserId($user_id);


                    $order = new Order();
                    $order->setUserId($user_id);
                    /**
                     * @var Order $active_order
                     */
                    $active_order = $get_order->findActiveOrder();

                    if ($active_order) {

                        $items = new Items();
                        $items->setUserId($user_id);
                        $items->setOrderId($active_order->id);
                        $list_products = $items->all();

                        $this->renderJson([
                            "status" => "success",
                            "cart_id" => $active_order->id,
                            "products" => $list_products,
                            "total_price" => $active_order->total_price,
                        ]);

                    } else {
                        $this->renderJson([
                            "status" => "success",
                            "message" => "Cart isn't enable",
                        ]);
                    }
                } else {
                    $this->renderJson([
                        "status" => "success",
                        "message" => "User not exist",
                    ]);
                }
            } else {
                $this->renderJson([
                    "status" => "success",
                    "message" => "Please check your params data}",
                ]);
            }
        } catch (\Exception $e) {
            $this->renderJson([
                "status" => "error",
                "message" => "Please check your params values or contact admin"
            ]);
        }
    }


    /**
     * Add new cart
     *
     * @param array $params
     */
    public function createCartAction(array $params)
    {
        try {
            $user_id = isset($params['user_id']) ? (v::intVal()->validate($params['user_id']) ? $params['user_id'] : false) : false;

            if ($user_id) {

                $user = new User();
                $user->setId($user_id);
                if ($user->ifExist()) { // Check if user exist

                    $order = new Order();
                    $order->setUserId($user_id);
                    $conn = $order->getDb();
                    $conn->beginTransaction();
                    $flag = true;

                    /**
                     * @var Order $active_order
                     */
                    $active_order = $order->findActiveOrder();

                    $flag = true;
                    if (!$active_order) {
                        $active_order = new Order();
                        $active_order->setUserId($user_id);
                        $active_order->setTotalPrice(0.00); // init total
                        $active_order->setPaymentStatus(Order::PAYMENT_STATUS_ACTIVE);
                        $flag = $flag && $active_order->save();
                        $active_order = $active_order->findActiveOrder();
                    } else {
                        $this->renderJson([
                            "status" => "error",
                            "message" => "Cart already exist"
                        ]);
                    }
                    if ($flag) {
                        // commit the transaction
                        $conn->commit();
                        $this->renderJson([
                            "status" => "success",
                            "cart_id" => $active_order->id,
                            "total_price" => $active_order->total_price,
                            "message" => "Cart was created successfully"
                        ]);
                    } else {
                        $conn->rollBack();
                        $this->renderJson([
                            "status" => "error",
                            "message" => "Cant create new cart"
                        ]);
                    }
                } else {
                    $this->renderJson([
                        "status" => "error",
                        "message" => "User not found"
                    ]);
                }
            } else {
                $this->renderJson([
                    "status" => "error",
                    "message" => "Please check your params values or contact admin"
                ]);
            }
        } catch (\Exception $e) {
            $this->renderJson(["status" => "error",
                "message" => "Please check your data or contact admin"]);
        }
    }


    /**
     * Remove Product From cart
     * @param array $params
     */
    public function removeCartAction(array $params)
    {
        try {
            $cart_id = isset($params['cart_id']) ? (v::intVal()->validate($params['cart_id']) ? $params['cart_id'] : false) : false;
            $user_id = isset($params['user_id']) ? (v::intVal()->validate($params['user_id']) ? $params['user_id'] : false) : false;

            if ($user_id && $cart_id) {
                $user = new User();
                $user->setId($user_id);
                if ($user->ifExist()) { // if user exist

                    $get_order = new Order();
                    $get_order->setUserId($user_id);

                    $order = new Order();
                    $order->setUserId($user_id);
                    $conn = $order->getDb();
                    $conn->beginTransaction();
                    $flag = true;

                    /**
                     * @var Order $active_order
                     */
                    $active_order = $get_order->findActiveOrder();

                    if (!$active_order) {
                        $this->renderJson([
                            "status" => "success",
                            "message" => "Cart has been already removed"
                        ]);
                    }

                    $flag = true;

                    $new_order = new Order();
                    $new_order->setId($active_order->id);
                    $new_order->setUserId($user_id);
                    $new_order->setTotalPrice($active_order->total_price);
                    $new_order->setPaymentStatus(Order::PAYMENT_STATUS_DISABLE);
                    $flag = $new_order->update();

                    $item = new Items();
                    $item->setOrderId($new_order->getId());

                    /**
                     * @var Items[] $items
                     */
                    $items = $item->getAllByOrderID();

                    if ($items) { //check if product exist
                        foreach ($items as $ite) {
                            $ite->setStatus(Items::ITEMS_REMOVE);
                            $ite->setStatusChecker(Items::ITEMS_ACTIVE);
                            $flag = $flag && $ite->update();
                        }
                    }

                    if ($flag) {
                        // commit the transaction
                        $conn->commit();
                        $this->renderJson([
                            "status" => "success",
                            "message" => "Cart was removed successfully"
                        ]);

                    } else {
                        $conn->rollBack();
                        $this->renderJson([
                            "status" => "error",
                            "message" => "Cart wasn't removed"
                        ]);
                    }
                } else {
                    $this->renderJson([
                        "status" => "error",
                        "message" => "User not found"
                    ]);
                }
            } else {
                $this->renderJson([
                    "status" => "error",
                    "message" => "Please check your params values or contact admin"
                ]);
            }
        } catch (\Exception $e) {
            $this->renderJson([
                "status" => "error",
                "message" => "Please check your params values or contact admin"
            ]);
        }
    }

}