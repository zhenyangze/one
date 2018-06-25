<?php

namespace One\Database\Mysql;

class Build
{
    use WhereTrait;
    use StructTrait;

    protected $from = '';

    private $columns = [];

    protected $build = [];

    private $connect;

    /**
     * @var EventBuild
     */
    public $model;

    public function __construct($connection, $model, $model_name, $table)
    {
        $this->from = $table;
        $this->model = $model;
        $this->connect = new Connect($connection, $model_name);
    }

    private $withs = [];

    /**
     * @param $relation
     * @param null $closure
     * @return $this
     */
    public function with($relation, array $closure = null)
    {
        $this->withs[] = [$relation, $closure];
        return $this;
    }

    private function fillSelectWith($res, $call)
    {
        foreach ($this->withs as $v) {
            list($relation, $closure) = $v;
            list($drel, $nrel) = $this->getRel($relation);
            $q = $this->model->$drel();
            if ($closure[0]) {
                $closure[0]($q);
                unset($closure[0]);
            }
            if ($nrel) {
                if ($closure) {
                    $q->with($nrel, array_values($closure));
                } else {
                    $q->with($nrel);
                }
            }
            $q->$call($res)->merge($drel);
        }
        return $res;
    }

    private function getRel($rel)
    {
        $i = strpos($rel, '.');
        if ($i !== false) {
            $drel = substr($rel, 0, $i);
            $nrel = substr($rel, $i + 1);
            return [$drel, $nrel];
        } else {
            return [$rel, false];
        }
    }

    protected function getData($all = false)
    {
        if ($all) {
            return $this->connect->findAll($this->getSelectSql(), $this->build);
        } else {
            $this->limit(1);
            return $this->connect->find($this->getSelectSql(), $this->build);
        }
    }

    public function find($id = null)
    {
        if ($id) {
            $this->where($this->getPriKey(), $id);
        }
        $info = $this->getData();
        if (!$info) {
            return null;
        }
        return $this->fillSelectWith($info, 'setRelationModel');
    }

    /**
     * @return ListModel|false
     */
    public function findAll()
    {
        $info = $this->getData(true);
        if (!$info) {
            return null;
        }
        return $this->fillSelectWith(new ListModel($info), 'setRelationList');
    }

    protected $is_count = 0;

    /**
     * @return int
     */
    public function count()
    {
        $this->is_count = 1;
        $res = $this->getData();
        $this->is_count = 0;
        return $res->row_count;
    }

    protected $sum_column = '';

    /**
     * @param $column
     * @return int
     */
    public function sum($column)
    {
        $this->sum_column = $column;
        $res = $this->getData();
        return $res->sum_value;
    }

    /**
     * @param $data
     * @return string
     */
    public function insert($data, $is_mulit = false)
    {
        $this->connect->exec($this->getInsertSql($data, $is_mulit), $this->build);
        return $this->connect->lastInsertId();
    }

    /**
     * @param $data
     * @return int
     */
    public function update($data)
    {
        return $this->connect->exec($this->getUpdateSql($data), $this->build);
    }

    /**
     * @return int
     */
    public function delete()
    {
        return $this->connect->exec($this->getDeleteSql(), $this->build);
    }

    /**
     * @param \Closure $call
     */
    public function transaction($call)
    {
        $this->connect->beginTransaction();
        try {
            $call();
        } catch (DbException $e) {
            $this->connect->rollBack();
        }
        $this->connect->commit();
    }

    /**
     * @return Connect
     */
    public function getConnect()
    {
        return $this->connect;
    }

    /**
     * @param string $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @param array $column
     * @return $this
     */
    public function column(array $columns)
    {
        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }

    private $distinct = '';

    /**
     * @return $this
     */
    public function distinct($column)
    {
        $this->distinct = $column;
        return $this;
    }

    /**
     * @param string $table
     * @param string $first 条件a
     * @param string $second 条件b
     * @return $this
     */
    public function leftJoin($table, $first, $second = null)
    {
        return $this->join($table, $first, $second, 'left');
    }

    /**
     * @param string $table
     * @param string $first
     * @param string $second
     * @return $this
     */
    public function rightJoin($table, $first, $second = null)
    {
        return $this->join($table, $first, $second, 'right');
    }

    private $joins = [];

    /**
     * @param string $table
     * @param string $first
     * @param string $second
     * @param string $type
     * @return $this
     */
    public function join($table, $first, $second = null, $type = 'inner')
    {
        $join = new Join($table, $first, $second, $type);
        list($data, $str) = $join->get();
        $this->joins[] = $str;
        $this->whereRaw('', $data);
        return $this;
    }

    private $group_by = '';

    /**
     * @param string $group_by
     * @return $this
     */
    public function groupBy($group_by)
    {
        $this->group_by = $group_by;
        return $this;
    }

    private $order_by = [];

    /**
     * @param $order_by
     * @return $this
     */
    public function orderBy($order_by)
    {
        $this->order_by[] = $order_by;
        return $this;
    }

    private $limit = 0;

    /**
     * @param int $limit
     * @param int $skip
     * @return $this
     */
    public function limit(int $limit, $skip = 0)
    {
        $this->limit = $skip . ',' . $limit;
        return $this;
    }

    private function getWhere()
    {
        list($this->build, $where) = $this->toWhere();
        if ($where) {
            $where = ' where ' . $where;
        }
        return $where;
    }

    protected function getSelectSql()
    {
        $sql = 'select';
        if ($this->is_count) {
            $column = ' count(*) as row_count ';
        } else if ($this->sum_column) {
            $column = ' sum(' . $this->sum_column . ') as sum_value ';
        } else if ($this->distinct) {
            $column = ' distinct ' . $this->distinct;
        } else {
            $column = implode(',', $this->columns);
            if (!$column) {
                $column = '*';
            }
        }
        $sql .= ' ' . $column . ' from ' . $this->from;
        foreach ($this->joins as $v) {
            $sql .= ' ' . $v;
        }
        $sql .= $this->getWhere();
        if ($this->order_by) {
            $sql .= ' order by ' . implode(',', $this->order_by);
        }
        if ($this->limit) {
            $sql .= ' limit ' . $this->limit;
        }
        return $sql;
    }

    private function getInsertSql($data, $is_mulit = false)
    {
        $sql = 'insert into ' . $this->from;
        $data = $this->filter($data);
        $build = [];
        $keys = array_keys($data);
        $sql .= ' (' . implode(',', $keys) . ')';
        if ($is_mulit) {
            $values = [];
            $val = array_values($data);
            foreach ($val[0] as $i => $v) {
                foreach ($val as $j => $el) {
                    $build[] = $val[$j][$i];
                }
                $values[] = '(' . substr(str_repeat(',?', count($keys)), 1) . ')';
            }
            $sql .= ' values ' . implode(',', $values);
        } else {
            $build = array_values($data);
            $sql .= ' values (' . substr(str_repeat(',?', count($keys)), 1) . ')';
        }
        $this->build = $build;
        return $sql;
    }

    private function getUpdateSql($data)
    {
        $sql = 'update ' . $this->from . ' set ';
        $build = [];
        $data = $this->filter($data);
        foreach ($data as $k => $v) {
            if (is_numeric($k)) {
                $sql .= "{$v[0]}={$v[1]}";
            } else {
                $sql .= "{$k}=?,";
                $build[] = $v;
            }
        }
        $sql = substr($sql, 0, -1);
        $this->setPriWhere();
        $sql .= $this->getWhere();
        $this->build = array_merge($build, $this->build);
        return $sql;
    }

    private function getDeleteSql()
    {
        $sql = 'delete from ' . $this->from;
        $this->setPriWhere();
        $sql .= $this->getWhere();
        return $sql;
    }

    private function setPriWhere()
    {
        if (!$this->where) {
            $pri = $this->getPriKey();
            if (property_exists($this->model, $pri)) {
                $this->where($pri, $this->model->$pri);
            }
        }
    }

    public function toArray()
    {
        $obj = get_object_vars($this->model);
        foreach ($obj as &$v) {
            if (is_object($v)) {
                $v = $v->toArray();
            }
        }
        return $obj;
    }

}