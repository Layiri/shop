<?php


namespace App\models;


use App\core\IModel;
use App\core\Model;
use PDO;
use Respect\Validation\Validator as v;

class Order extends Model implements IModel
{

    protected int $id;
    private int $user_id;
    private ?float $total_price = 0.0;
    private int $payment_status;
    private int $created_at;
    private int $updated_at;
    protected static PDO $conn;

    const PAYMENT_STATUS_ACTIVE = 1;
    const PAYMENT_STATUS_DISABLE = 0;


    /**
     * @return string
     */
    public static function table(): string
    {
        return 'orders';
    }


    /**
     * Get the list of virtual properties
     *
     *
     * @return string[] Defined properties
     */
    protected function getProperties(): array
    {
        return ['id', 'user_id', 'title', 'price', 'status', 'created_at', 'updated_at'];
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
            'title' => v::stringType(),
            'price' => v::floatType(),
            'status' => v::intType(),
            'created_at' => v::intType(),
            'updated_at' => v::intType(),
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $id
     */
    public function setUserId(int $id): void
    {
        $this->user_id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }


    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->total_price;
    }

    /**
     * @param int $status
     */
    public function setPaymentStatus(int $status = self::PAYMENT_STATUS_ACTIVE): void
    {
        $this->payment_status = $status;
    }

    /**
     * @return int
     */
    public function getPaymentStatus(): int
    {
        return $this->payment_status;
    }

    /**
     * @param float $price
     */
    public function setTotalPrice(float $price): void
    {
        $this->total_price = $price;
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


    /**
     * @return bool
     */
    public function save(): bool
    {
        $req = self::$conn->prepare('INSERT INTO ' . self::table() . ' (user_id, total_price, payment_status, created_at, updated_at) VALUES(:user_id, :total_price, :payment_status, :created_at, :updated_at)');
        $req->execute(array(
            'user_id' => $this->user_id,
            'total_price' => $this->total_price,
            'payment_status' => $this->payment_status,
            'created_at' => time(),
            'updated_at' => time(),
        ));
        return true;
    }


    /**
     * Update user
     * @return bool
     */
    public function update(): bool
    {
        $req = self::$conn->prepare('UPDATE ' . self::table() . ' SET user_id=:user_id,total_price=:total_price,payment_status=:payment_status,updated_at=:updated_at WHERE id=:id');
        if ($req->execute(array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_price' => $this->total_price,
            'payment_status' => $this->payment_status,
            'updated_at' => time(),
        ))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update user
     * @return bool
     */
    public function disable(): bool
    {
        $req = self::$conn->prepare('UPDATE ' . self::table() . ' SET payment_status=:payment_status,updated_at=:updated_at WHERE id=:id');
        $req->execute(array(
            'id' => $this->id,
            'payment_status' => self::PAYMENT_STATUS_DISABLE,
            'updated_at' => time(),
        ));
        return true;
    }

    /**
     * Delete element
     * @return bool
     */
    public function delete(): bool
    {
        $req = self::$conn->prepare("DELETE FROM " . self::table() . " WHERE id=?");
        return $req->execute(array($this->id)) ? true : false;
    }

    /**
     * Get all products
     *
     * @return array
     */
    public function all(): array
    {
        $req = self::$conn->prepare('SELECT *  FROM ' . self::table());
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return mixed
     */
    public function one(): mixed
    {
        $req = self::$conn->prepare("SELECT * FROM " . self::table() . " WHERE id=:id");
        $req->execute(['id' => $this->id]);
        return $req->fetch(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
    }

    /**
     * @return mixed
     */
    public function findActiveOrder(): mixed
    {
        $req = self::$conn->prepare("SELECT * FROM " . self::table() . " WHERE user_id=:user_id");
        $req->execute([
            'user_id' => $this->user_id
        ]);
        return $req->fetchObject();

    }

}