<?php

namespace App\core;


class Pagination extends Model
{
    protected string $table;
    protected int $total_records;
    protected ?int $limit = 3;
    protected int $page;

    public static function table(): string
    {
        return "";
    }



    /**
     * Set total records of items in database
     */
    public function setTotalRecords()
    {
        $query = "SELECT id FROM $this->table";
        $req = self::$conn->prepare($query);
        $req->execute();
        $this->total_records = $req->rowCount();
    }

    /**
     * Get data after paginate
     *
     * @return array
     */
    public function getData(): array
    {
        $start = 0;
        if ($this->getCurrentPage() > 1) {
            $start = ($this->getCurrentPage() * $this->limit) - $this->limit;
        }
        $query = "SELECT * FROM $this->table LIMIT $start, $this->limit";

        $req = self::$conn->prepare($query);
        $req->execute();
        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Current page
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return isset($this->page) ? (int)$this->page : 1;
    }

    /**
     * Total pages for pagination
     *
     * @return float
     */
    public function getTotalPages(): float
    {
        return ceil($this->total_records / $this->limit);
    }

    /**
     * Get previous page
     *
     * @return int
     */
    public function getPrevPage(): int
    {
        return ($this->getCurrentPage() > 1) ? $this->getCurrentPage() : 1;
    }

    /**
     * Get next page
     *
     * @return int
     */
    public function getNextPage(): int
    {
        return ($this->getCurrentPage() < $this->getTotalPages()) ? $this->getCurrentPage() + 1 : $this->getTotalPages();
    }


    /**
     * Set page number
     *
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     *
     * Get Page number
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table)
    {
        $this->table = $table;
    }
}
