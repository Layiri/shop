<?php


namespace App\controllers\api;


use App\controllers\BaseController;
use App\core\Model;
use App\core\Pagination;
use App\models\Product;
use Respect\Validation\Validator as v;

class ProductController extends BaseController
{

    /**
     * Add new product
     */
    public function addAction()
    {
        try {
            $user_id = isset($_POST['user_id']) ? (v::intVal()->validate($_POST['user_id']) ? $_POST['user_id'] : false) : false;
            $title = isset($_POST['title']) ? (v::stringVal()->validate($_POST['title']) ? $_POST['title'] : false) : false;
            $price = isset($_POST['price']) ? (v::floatVal()->validate($_POST['price']) ? $_POST['price'] : false) : false;
            $status = isset($_POST['status']) ? (v::intVal()->validate($_POST['status']) ? $_POST['status'] : false) : false;

            if ($user_id && $title && $price && $status !== false) {
                $model = new Product();
                $model->setUserId($user_id);
                $model->setTitle($title);
                $model->setPrice($price);
                $model->setStatus($status);

                if ($model->save()) {
                    $this->renderJson([
                        "status" => "success",
                        "message" => "Product was added successfully"
                    ]);
                } else {
                    $this->renderJson([
                        "status" => "errors",
                        "message" => "Can't save data. Please check your params values or contact admin"
                    ]);
                }
            } else {
                $this->renderJson([
                    "status" => "errors",
                    "message" => "Please check your params values or contact admin"
                ]);
            }
        } catch (\Exception $e) {
            $this->renderJson([
                "status" => "errors",
                "message" => "Please check your params values or contact admin"
            ]);
        }
    }

    /**
     * @param array $params
     */
    public function listProductsAction(array $params)
    {
        try {
            $page = isset($params['page']) ? (v::intVal()->validate($params['page']) ? $params['page'] : false) : false;

            if ($page) {

                $pagination = new Pagination();
                $pagination->setTable(Product::table());
                $pagination->setTotalRecords();
                $pagination->setPage($page);
                $list_products = $pagination->getData();

                $this->renderJson([
                    "status" => "success",
                    "products" => $list_products,
                    "page" => $pagination->getPage(),
                    "start" => 1,
                    "end" => $pagination->getTotalPages(),
                    "prev" => $pagination->getPrevPage(),
                    "next" => $pagination->getNextPage(),
                ]);

            } else {
                $this->renderJson([
                    "status" => "errors",
                    "message" => "Please add valid page number"
                ]);
            }
        } catch (\Exception $e) {
            $this->renderJson([
                "status" => "errors",
                "message" => "Please check your params values or contact admin"
            ]);
        }
    }

    /**
     * Remove product
     *
     * @param array $params
     */
    public function removeAction(array $params)
    {
        try {
            $id = isset($params['id']) ? (v::intVal()->validate($params['id']) ? $params['id'] : false) : false;
            if ($id) {
                $product = new Product();
                $product->setId($id);

                if ($product->one()) {
                    if($product->delete()){
                        $this->renderJson([
                            "status" => "success",
                            "message" => "Product was removed successfully"
                        ]);
                    }else{
                        $this->renderJson([
                            "status" => "success",
                            "message" => "Product can't be removed!"
                        ]);
                    }
                } else {
                    $this->renderJson([
                        "status" => "errors",
                        "message" => "Product don't exists"
                    ]);
                }
            } else {
                $this->renderJson([
                    "status" => "errors",
                    "message" => "Please product id"
                ]);
            }
        } catch (\Exception $e) {
            $this->renderJson([
                "status" => "errors",
                "message" => "Please check your params values or contact admin"
            ]);
        }
    }

    /**
     * Update Product
     */
    public function updateAction()
    {
        try {
            $id = isset($_POST['id']) ? (v::intVal()->validate($_POST['id']) ? $_POST['id'] : false) : false;
            $user_id = isset($_POST['user_id']) ? (v::intVal()->validate($_POST['user_id']) ? $_POST['user_id'] : false) : false;
            $title = isset($_POST['title']) ? (v::stringVal()->validate($_POST['title']) ? $_POST['title'] : false) : false;
            $price = isset($_POST['price']) ? (v::floatVal()->validate($_POST['price']) ? $_POST['price'] : false) : false;
            $status = isset($_POST['status']) ? (v::intVal()->validate($_POST['status']) ? $_POST['status'] : false) : false;

            if ($id && $user_id && $title && $price && $status !== false) {
                $product = new Product();
                $product->setId($id);
                if ($product->one()) {
                    $product->setUserId($_POST['user_id']);
                    $product->setTitle($_POST['title']);
                    $product->setPrice($_POST['price']);
                    $product->setStatus($status);
                    if($product->update()) {
                        $this->renderJson([
                            "status" => "success",
                            "message" => "Product was updated successfully"
                        ]);
                    }else{
                        $this->renderJson([
                            "status" => "success",
                            "message" => "Product can't be updated"
                        ]);
                    }
                } else {
                    $this->renderJson([
                        "status" => "errors",
                        "message" => "Product don't exists"
                    ]);
                }
            } else {
                $this->renderJson([
                    "status" => "errors",
                    "message" => "Please check your params values or contact admin"
                ]);
            }

        } catch (\Exception $e) {
            $this->renderJson([
                "status" => "errors",
                "message" => "Please check your params values or contact admin"
            ]);
        }
    }
}