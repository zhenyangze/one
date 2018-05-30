<?php

namespace One\Database\Mysql;

use One\ConfigTrait;

class Connect
{

    use ConfigTrait;

    private static $pdo = [];

    private $key;

    private $model;

    /**
     * Connect constructor.
     * @param string $key
     * @param string $model
     */
    public function __construct($key, $model)
    {
        $this->key = $key;
        $this->model = $model;
    }

    /**
     * @param string $sql
     * @param array $data
     * @return \PDOStatement
     */
    public function execute($sql, $data = [])
    {
        $res = $this->getPdo()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL]);
        $res->setFetchMode(\PDO::FETCH_CLASS, $this->model);
        if (!$res->execute($data)) {
            if ($this->isBreak($res->errorInfo()[2])) {
                return $this->close()->execute($sql, $data);
            }
            throw new DbException(json_encode(['info' => $res->errorInfo(), 'sql' => $sql]), 1);
        }
        return $res;
    }

    public function getDns()
    {
        return self::$conf[$this->key]['dns'];
    }


    private function close()
    {
        unset(self::$pdo[$this->key]);
        return $this;
    }


    private function isBreak($error)
    {
        $info = [
            'server has gone away',
            'no connection to the server',
            'Lost connection',
            'is dead or not enabled',
            'Error while sending',
            'decryption failed or bad record mac',
            'server closed the connection unexpectedly',
            'SSL connection has been closed unexpectedly',
            'Error writing data to the connection',
            'Resource deadlock avoided',
        ];

        foreach ($info as $msg) {
            if (false !== stripos($error, $msg)) {
                return true;
            }
        }
        return false;
    }


    /**
     * @param string $sql
     * @param array $data
     * @return mixed
     */
    public function find($sql, $data = [])
    {
        return $this->execute($sql, $data)->fetch();
    }

    /**
     * @param string $sql
     * @param array $data
     * @return mixed
     */
    public function findAll($sql, $data = [])
    {
        return $this->execute($sql, $data)->fetchAll();
    }

    /**
     * @param string $sql
     * @param int
     */
    public function exec($sql, $data = [])
    {
        return $this->execute($sql, $data)->rowCount();
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->getPdo()->lastInsertId();
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        if ($this->inTransaction()) {
            return true;
        }
        return $this->getPdo()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        if ($this->inTransaction()) {
            return $this->getPdo()->rollBack();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function commit()
    {
        if ($this->inTransaction()) {
            return $this->getPdo()->commit();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->getPdo()->inTransaction();
    }


    /**
     * @param string $key
     * @return \PDO
     */
    public function getPdo()
    {
        if (!isset(self::$pdo[$this->key])) {
            self::$pdo[$this->key] = $this->createPdo(self::$conf[$this->key]);
        }
        return self::$pdo[$this->key];
    }

    /**
     * @param array $conf
     * @return \PDO
     * @throws DbException
     */
    private function createPdo($conf)
    {
        try {
            return new \PDO($conf['dns'], $conf['username'], $conf['password'], $conf['ops']);
        } catch (\PDOException $e) {
            throw new \DbException('connection failed ' . $e->getMessage(), 0);
        }
    }

}