<?php


namespace App\models;


use App\core\IModel;
use App\core\Model;
use App\core\ModelTrait;
use Respect\Validation\Validator as v;

/**
 * Class Product
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property float $price
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @author Layiri Batiene
 * @package App\models
 */
class Product extends Model implements IModel
{

    use ModelTrait;

    protected int $id;
    protected int $user_id;
    protected string $title;
    protected float $price;
    protected int $status = self::PRODUCT_ACTIVE;
    protected int $created_at;
    protected int $updated_at;


    const PRODUCT_ACTIVE = 1;
    const PRODUCT_DISABLE = 0;

    public static function table(): string
    {
        return "products";
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
            'user_id' => v::intVal(),
            'title' => v::stringVal(),
            'price' => v::floatVal(),
            'status' => v::intVal(),
            'created_at' => v::intVal(),
            'updated_at' => v::intVal(),
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(int $status): void
    {
        if($status){
            $this->status = self::PRODUCT_ACTIVE;
        }else{
            $this->status = self::PRODUCT_DISABLE;

        }
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
     * @return bool
     */
    public function save(): bool
    {
        $req = self::$conn->prepare('INSERT INTO ' . self::table() . '(user_id, title, price, status, created_at, updated_at) VALUES(:user_id, :title, :price, :status, :created_at, :updated_at)');
        $val = $req->execute(array(
            'user_id' => $this->user_id,
            'title' => $this->title,
            'price' => $this->price,
            'status' => $this->status,
            'created_at' => time(),
            'updated_at' => time(),
        ));

        return $val ?? false;
    }


    /**
     * Update user
     * @return bool
     */
    public function update(): bool
    {
        $req = $req = self::$conn->prepare('UPDATE ' . self::table() . ' SET user_id=:user_id,title=:title,price=:price,status=:status,updated_at=:updated_at WHERE id=:id');
        $val = $req->execute(array(
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'price' => $this->price,
            'status' => $this->status,
            'updated_at' => time(),
        ));
        return $val ?? false;
    }

    /**
     * Delete element
     *
     * @return bool
     */
    public function delete(): bool
    {
        $req = self::$conn->prepare("DELETE FROM " . self::table() . " WHERE id=?");
        $val = $req->execute([$this->id]);
        return ($val) ?? false;
    }

    /**
     * Get all products
     *
     * @return array
     */
    public function all(): array
    {
        $req = $req = self::$conn->prepare('SELECT *  FROM ' . self::table());
        $req->execute();
        return $req->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * @return object|bool
     */
    public function one(): object|bool
    {
        $req = $req = self::$conn->prepare("SELECT * FROM " . self::table() . " WHERE id=:id");
        $req->execute(['id' => $this->id]);
        $val = $req->fetchObject();
        return $val ?? false;
    }

}