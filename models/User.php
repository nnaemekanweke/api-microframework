<?php

namespace Models;

use App\Model;

class User extends Model 
{
    protected $table = 'users';
    protected $fillable = ['name', 'email'];

    public function getAll($page = 1, $limit = 10, $search = '') 
    {
        $query = $this->query();

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
        }

        return $query->orderBy('created_at', 'DESC')
                     ->limit($limit)
                     ->offset(($page - 1) * $limit)
                     ->get();
    }

    public function count($search = '') 
    {
        $query = $this->query();

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
        }

        return $query->count();
    }

    public function findById($id) 
    {
        return $this->find($id);
    }
}