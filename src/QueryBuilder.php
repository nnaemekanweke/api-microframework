<?php

namespace App;

class QueryBuilder 
{
    protected $table;
    protected $wheres = [];
    protected $orderBy = [];
    protected $limit;
    protected $offset;
    protected $joins = [];

    public function __construct($table) 
    {
        $this->table = $table;
    }

    public function where($column, $operator = '=', $value = null) 
    {
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'where',
            'boolean' => 'AND',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];

        return $this;
    }

    public function orWhere($column, $operator = '=', $value = null) 
    {
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'where',
            'boolean' => 'OR',
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        ];

        return $this;
    }

    public function whereIn($column, $values) 
    {
        $this->wheres[] = [
            'type' => 'whereIn',
            'boolean' => 'AND',
            'column' => $column,
            'values' => $values
        ];

        return $this;
    }

    public function whereLike($column, $value) 
    {
        return $this->where($column, 'LIKE', $value);
    }

    public function orderBy($column, $direction = 'ASC') 
    {
        $this->orderBy[] = "`{$column}` {$direction}";
        return $this;
    }

    public function limit($limit) 
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset) 
    {
        $this->offset = $offset;
        return $this;
    }

    public function get() 
    {
        $sql = "SELECT * FROM `{$this->table}`";
        $params = [];

        if (!empty($this->wheres)) {
            $whereConditions = [];
            foreach ($this->wheres as $index => $where) {
                $boolean = $index === 0 ? '' : $where['boolean'];
                
                if ($where['type'] === 'where') {
                    $whereConditions[] = "{$boolean} `{$where['column']}` {$where['operator']} ?";
                    $params[] = $where['value'];
                } elseif ($where['type'] === 'whereIn') {
                    $placeholders = str_repeat('?,', count($where['values']) - 1) . '?';
                    $whereConditions[] = "{$boolean} `{$where['column']}` IN ({$placeholders})";
                    $params = array_merge($params, $where['values']);
                }
            }
            $sql .= " WHERE " . ltrim(implode(' ', $whereConditions));
        }

        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset) {
            $sql .= " OFFSET {$this->offset}";
        }

        return Database::select($sql, $params);
    }

    public function first() 
    {
        $this->limit(1);
        $results = $this->get();
        return $results ? $results[0] : null;
    }

    public function count() 
    {
        $sql = "SELECT COUNT(*) as count FROM `{$this->table}`";
        $params = [];

        if (!empty($this->wheres)) {
            $whereConditions = [];
            foreach ($this->wheres as $index => $where) {
                $boolean = $index === 0 ? '' : $where['boolean'];
                
                if ($where['type'] === 'where') {
                    $whereConditions[] = "{$boolean} `{$where['column']}` {$where['operator']} ?";
                    $params[] = $where['value'];
                } elseif ($where['type'] === 'whereIn') {
                    $placeholders = str_repeat('?,', count($where['values']) - 1) . '?';
                    $whereConditions[] = "{$boolean} `{$where['column']}` IN ({$placeholders})";
                    $params = array_merge($params, $where['values']);
                }
            }
            $sql .= " WHERE " . ltrim(implode(' ', $whereConditions));
        }

        $result = Database::selectOne($sql, $params);
        return (int) $result['count'];
    }

    public function insert($data) 
    {
        $data['created_by'] = $_ENV['CURRENT_USER'] ?? 'nnaemekanweke';

        $columns = '`' . implode('`, `', array_keys($data)) . '`';
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$placeholders})";
        
        return Database::insert($sql, $data);
    }

    public function update($data) 
    {
        $data['updated_by'] = $_ENV['CURRENT_USER'] ?? 'nnaemekanweke';

        $setClause = [];
        $params = [];

        foreach ($data as $key => $value) {
            $setClause[] = "`{$key}` = ?";
            $params[] = $value;
        }

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $setClause);

        if (!empty($this->wheres)) {
            $whereConditions = [];
            foreach ($this->wheres as $index => $where) {
                $boolean = $index === 0 ? '' : $where['boolean'];
                
                if ($where['type'] === 'where') {
                    $whereConditions[] = "{$boolean} `{$where['column']}` {$where['operator']} ?";
                    $params[] = $where['value'];
                }
            }
            $sql .= " WHERE " . ltrim(implode(' ', $whereConditions));
        }

        return Database::update($sql, $params);
    }

    public function delete() 
    {
        $sql = "DELETE FROM `{$this->table}`";
        $params = [];

        if (!empty($this->wheres)) {
            $whereConditions = [];
            foreach ($this->wheres as $index => $where) {
                $boolean = $index === 0 ? '' : $where['boolean'];
                
                if ($where['type'] === 'where') {
                    $whereConditions[] = "{$boolean} `{$where['column']}` {$where['operator']} ?";
                    $params[] = $where['value'];
                }
            }
            $sql .= " WHERE " . ltrim(implode(' ', $whereConditions));
        }

        return Database::delete($sql, $params);
    }
}