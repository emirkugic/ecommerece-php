<?php

require_once __DIR__ . '/../dao/UserDao.class.php';

class UserService
{

    private $user_dao;

    public function __construct()
    {
        $this->user_dao = new UserDao();
    }

    public function get_user_by_id($id)
    {
        return $this->user_dao->get_user_by_id($id);
    }

    public function get_all_users($order = "-id")
    {
        return $this->user_dao->get_all_users($order);
    }

    public function add_user($user)
    {
        $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
        return $this->user_dao->add_user($user);
    }

    public function update_user($id, $user)
    {
        $success = $this->user_dao->update_user($id, $user);
        return $success ? $user : false;
    }

    public function delete_user_by_id($id)
    {
        return $this->user_dao->delete_user_by_id($id);
    }


    // public function get_user_by_email($email)
    // {
    //     return $this->user_dao->get_user_by_email($email);
    // }

    // for login
    public function authenticate_user($email, $password)
    {
        $user = $this->user_dao->get_user_by_email($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}
