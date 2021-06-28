<?php


namespace App\models;


use App\core\Model;

class Migration extends Model
{

    public static function table(): string
    {
        return "";
    }

    /**
     * @param string $sql
     * @param array $array_prepare
     * @return bool
     */
    public function runPrepare(string $sql, array $array_prepare): bool
    {
        $flag = true;
        foreach ($array_prepare as $array) {
            $req = self::$conn->prepare($sql);
            $flag= $flag && $req->execute($array);
            unset($req);
        }
        return $flag;
    }


    /**
     * @param string $sql
     * @return bool
     */
    public function run(string $sql): bool
    {
        return (bool)self::$conn->exec($sql);
    }

}