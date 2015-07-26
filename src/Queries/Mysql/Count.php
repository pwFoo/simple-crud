<?php
namespace SimpleCrud\Queries\Mysql;

use SimpleCrud\Queries\BaseQuery;
use SimpleCrud\Entity;
use PDOStatement;
use PDO;

/**
 * Manages a database select count query in Mysql databases
 */
class Count extends BaseQuery
{
    use WhereTrait;

    protected $limit;

    /**
     * @see QueryInterface
     *
     * $entity->count($where, $marks, $limit)
     *
     * {@inheritdoc}
     */
    public static function execute(Entity $entity, array $args)
    {
        $count = self::getInstance($entity);

        if (isset($args[0])) {
            $count->where($args[0], isset($args[1]) ? $args[1] : null);
        }

        if (isset($args[2])) {
            $count->limit($args[2]);
        }

        return $count->get();
    }

    /**
     * Adds a LIMIT clause
     *
     * @param integer $limit
     *
     * @return self
     */
    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Adds new marks to the query
     *
     * @param array $marks
     *
     * @return self
     */
    public function marks(array $marks)
    {
        $this->marks += $marks;

        return $this;
    }

    /**
     * Run the query and return a statement with the result
     *
     * @return PDOStatement
     */
    public function run()
    {
        $statement = $this->entity->getDb()->execute((string) $this, $this->marks);
        $statement->setFetchMode(PDO::FETCH_NUM);

        return $statement;
    }

    /**
     * Run the query and return the value
     *
     * @return integer
     */
    public function get()
    {
        $result = $this->run()->fetch();

        return (int) $result[0];
    }

    /**
     * Build and return the query
     *
     * @return string
     */
    public function __toString()
    {
        $query = "SELECT COUNT(*) FROM `{$this->entity->table}`";

        if (!empty($this->where)) {
            $query .= ' WHERE ('.implode(') AND (', $this->where).')';
        }

        if (!empty($this->limit)) {
            $query .= ' LIMIT '.$this->limit;
        }

        return $query;
    }
}