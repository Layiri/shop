<?php


namespace App\models;

use App\core\IModel;
use App\core\Model;
use PDO;
use Respect\Validation\Validator as v;

class User extends Model implements IModel
{

    protected int $id;
    protected string $name;
    protected string $email;
    protected string $phone;
    protected string $token;
    protected int $created_at;
    protected int $updated_at;
    protected static PDO $conn;


    /**
     * @return string
     */
    public static function table(): string
    {
        return 'users';
    }

    /**
     * Get the list of virtual properties
     *
     *
     * @return string[] Defined properties
     */
    protected function getProperties(): array
    {
        return ['id', 'name', 'email', 'phone', 'token', 'created_at', 'updated_at'];
    }

    /**
     * Get the list of properties validators
     *
     * @return array
     */
    protected function getPropertiesValidators(): array
    {
        return [
            'name' => v::stringType(),
            'email' => v::email(),
            'phone' => v::phone(),
            'token' => v::stringType(),
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param int $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(int $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(int $token): void
    {
        $this->token = $token;
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
     * @param string $token
     * @return int
     */
    public function ifExist(string $token): int
    {
        $req = $this->conn->prepare("SELECT * FROM " . self::table() . " WHERE token=:token");
        $req->execute(['token' => $token]);

        return $req->rowCount();
    }


    /**
     * Insert user
     * @return bool
     */
    public function save(): bool
    {
        $req = $this->conn->prepare('INSERT INTO ' . self::table() . '(name,email,phone,token,created_at,updated_at) VALUES(:name, :email, :phone, :token, :created_at, :updated_at)');
        $req->execute(array(
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'token' => $this->token,
            'created_at' => time(),
            'updated_at' => time()
        ));
        return true;
    }

    /**
     * Update user
     * @return bool
     */
    public function update(): bool
    {
        $req = $this->conn->prepare('UPDATE ' . self::table() . ' SET name=:name,email=:email,phone=:phone,token=:token,updated_at=:updated_at WHERE id=:id');
        $req->execute(array(
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'token' => $this->token,
            'updated_at' => time(),
            'id' => $this->id
        ));
        return true;
    }

    /**
     * Delete element
     * @return bool
     */
    public function delete(): bool
    {
        $req = $this->conn->prepare("DELETE FROM " . self::table() . " WHERE id=?");
        $req->execute(array($this->id));

        return true;
    }

    /**
     * @return mixed
     */
    public function all(): mixed
    {
        $req = $this->conn->prepare('SELECT *  FROM ' . self::table());
        $req->execute();
        return $req->fetchAll(PDO::FETCH_CLASS);
    }


    /**
     * @return mixed
     */
    public function one(): mixed
    {
        $req = $this->conn->prepare("SELECT * FROM " . self::table() . " WHERE id=:id");
        $req->execute(['id' => $this->id]);

        return $req->fetch(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
    }

}