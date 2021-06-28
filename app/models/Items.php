<?php


namespace App\models;

use App\core\IModel;
use App\core\Model;
use PDO;
use Respect\Validation\Validator as v;

/**
 * Class Items
 *
 * @property int $user_id
 * @property int $product_id
 * @property int $order_id
 * @property int $status
 * @property int $statusChecker
 * @property int $quantity
 * @property int $created_at
 * @property int $updated_at
 *
 * @author Layiri Batiene
 */

class Items extends Model implements IModel
{

    protected int $user_id;
    protected int $product_id;
    protected int $order_id;
    protected int $status;
    protected int $statusChecker;
    protected int $quantity;
    protected int $created_at;
    protected int $updated_at;


    const ITEMS_ACTIVE = 1;
    const ITEMS_REMOVE = 0;

    /**
     * @return string
     */
    public static function table(): string
    {
        return 'items';
    }

    /**
     * Get the list of virtual properties
     *
     *
     * @return string[] Defined properties
     */
    protected function getProperties(): array
    {
        return ['user_id', 'product_id', 'order_id', 'status', 'quantity', 'created_at', 'updated_at'];
    }

    /**
     * Get the list of properties validators
     *
     * @return array
     */
    protected function getPropertiesValidators(): array
    {
        return [
            'user_id' => v::intType(),
            'product_id' => v::intType(),
            'order_id' => v::intType(),
            'status' => v::nullable(v::intType()),
            'quantity' => v::intType(),
            'created_at' => v::intType(),
            'updated_at' => v::intType(),
        ];
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->product_id;
    }

    /**
     * @param int $id
     */
    public function setProductId(int $id): void
    {
        $this->product_id = $id;
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->order_id;
    }


    /**
     * @param int $id
     */
    public function setOrderId(int $id): void
    {
        $this->order_id = $id;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }


    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @param int $status
     */
    public function setStatusChecker(int $status): void
    {
        $this->statusChecker = $status;
    }


    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return date('Y-m-d H:i:s', $this->created_at);
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return date('Y-m-d H:i:s', $this->updated_at);
    }


    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $req = self::$conn->prepare('INSERT INTO ' . self::table() . ' (product_id, order_id, status, quantity, created_at, updated_at) VALUES(:product_id, :order_id, :status, :quantity, :created_at, :updated_at)');
        $req->execute(array(
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'status' => $this->status,
            'quantity' => $this->quantity,
            'created_at' => time(),
            'updated_at' => time(),
        ));
        return true;
    }


    /**
     * Update Items
     * @return bool
     */
    public function update(): bool
    {

        $req = self::$conn->prepare('UPDATE ' . self::table() . ' SET status=:status,quantity=:quantity,updated_at=:updated_at WHERE product_id=:product_id AND order_id=:order_id AND status=:checkstatus');

        $req->execute(array(
            'status' => $this->status,
            'quantity' => $this->quantity,
            'updated_at' => time(),
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'checkstatus' => $this->statusChecker,
        ));
        return true;
    }

    /**
     * remove element
     * @return bool
     */
    public function delete(): bool
    {
        $req = self::$conn->prepare('UPDATE ' . self::table() . ' SET status=:status,updated_at=:updated_at WHERE product_id=:product_id AND order_id:=order_id');

        $req->execute(array(
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'status' => self::ITEMS_REMOVE,
            'updated_at' => time(),
        ));
        return true;
    }

    /**
     * Get all items
     *
     * @return array
     */
    public function all(): array
    {
        $req = self::$conn->prepare('
                SELECT pr.id, pr.title AS name, it.quantity AS quantity, pr.price 
                FROM ' . self::table() . ' AS it
                LEFT JOIN ' . Product::table() . ' AS pr
                ON it.product_id=pr.id
                WHERE it.order_id=:order_id AND it.status=:status
                ');
        $req->execute([
            'order_id' => $this->order_id,
            'status' => self::ITEMS_ACTIVE,
        ]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function one(): mixed
    {
        $req = self::$conn->prepare("SELECT * FROM " . self::table() . " WHERE product_id=:product_id AND order_id=:order_id AND status=:status");
        $req->execute([
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'status' => self::ITEMS_ACTIVE,
        ]);
        return $req->fetch(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
    }


    /**
     * @return int
     */
    public function count(): int
    {
        $req = self::$conn->prepare("SELECT * FROM " . self::table() . " WHERE product_id=:product_id AND order_id=:order_id AND status=:status");
        $req->execute([
            'product_id' => $this->product_id,
            'order_id' => $this->order_id,
            'status' => self::ITEMS_ACTIVE,
        ]);

        return $req->rowCount();
    }

    /**
     * @return int
     */
    public function checkIfOrderMoreThreeProducts(): int
    {
        $req = self::$conn->prepare("
            SELECT * FROM " . self::table() . " WHERE order_id=:order_id AND status=:status");
        $req->execute([
            'order_id' => $this->order_id,
            'status' => self::ITEMS_ACTIVE,
        ]);

        return $req->rowCount();
    }

    /**
     * @return mixed
     */
    public function getAllByOrderID(): mixed
    {
        $req = self::$conn->prepare("SELECT * FROM " . self::table() . " WHERE order_id=:order_id");
        $req->execute([
            'order_id' => $this->order_id,
        ]);
        return $req->fetchAll(PDO::FETCH_CLASS, Items::class);
    }



}