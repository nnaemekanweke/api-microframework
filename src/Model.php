<?php

namespace App;

abstract class Model 
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct() 
    {
        if (!$this->table) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($className) . 's';
        }
    }

    public function query() 
    {
        return new QueryBuilder($this->table);
    }

    public function all() 
    {
        return $this->query()->get();
    }

    public function find($id) 
    {
        return $this->query()->where($this->primaryKey, $id)->first();
    }

    public function where($column, $operator = '=', $value = null) 
    {
        return $this->query()->where($column, $operator, $value);
    }

    public function create($data) 
    {
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $filteredData[$key] = $value;
            }
        }

        $id = $this->query()->insert($filteredData);
        return $this->find($id);
    }

    public function update($id, $data) 
    {
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $filteredData[$key] = $value;
            }
        }

        $this->query()->where($this->primaryKey, $id)->update($filteredData);
        return $this->find($id);
    }

    public function delete($id) 
    {
        return $this->query()->where($this->primaryKey, $id)->delete();
    }
}