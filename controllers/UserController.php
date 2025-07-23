<?php

namespace Controllers;

use App\Response;
use Models\User;

class UserController 
{
    protected $userModel;

    public function __construct() 
    {
        $this->userModel = new User();
    }

    public function index($request) 
    {
        $page = (int)$request->query('page', 1);
        $limit = (int)$request->query('limit', 10);
        $search = $request->query('search', '');

        $users = $this->userModel->getAll($page, $limit, $search);
        $total = $this->userModel->count($search);

        return Response::success([
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit),
                'has_next' => $page < ceil($total / $limit),
                'has_prev' => $page > 1
            ]
        ], 'Users retrieved successfully');
    }

    public function store($request) 
    {
        $data = $request->input();

        // Validation
        $errors = $this->validateUser($data);
        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $user = $this->userModel->create($data);
        return Response::success($user, 'User created successfully');
    }

    public function show($request, $id) 
    {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            return Response::error('User not found', 404);
        }

        return Response::success($user, 'User retrieved successfully');
    }

    public function update($request, $id) 
    {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            return Response::error('User not found', 404);
        }

        $data = $request->input();
        $errors = $this->validateUser($data, true);
        
        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $updatedUser = $this->userModel->update($id, $data);
        return Response::success($updatedUser, 'User updated successfully');
    }

    public function destroy($request, $id) 
    {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            return Response::error('User not found', 404);
        }

        $this->userModel->delete($id);
        return Response::success(null, 'User deleted successfully');
    }

    private function validateUser($data, $isUpdate = false) 
    {
        $errors = [];

        if (!$isUpdate || isset($data['name'])) {
            if (empty($data['name'])) {
                $errors['name'] = 'Name is required';
            } elseif (strlen($data['name']) < 2) {
                $errors['name'] = 'Name must be at least 2 characters';
            }
        }

        if (!$isUpdate || isset($data['email'])) {
            if (empty($data['email'])) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            }
        }

        return $errors;
    }
}