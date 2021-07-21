<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class UsersModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = Database::connect()->table('users');
    }

    //测试用
    public function getdata()
    {
    }

    //插入
    public function insert($data = null, bool $returnID = true)
    {
        if ($data != null) {
            return $this->db->insert($data);
        }
        return false;
    }

    //用户名记录查询
    public function usernameQuery($username = null)
    {
        if ($username != null) {
            return $this->db->select('username')
                ->where('username', $username)
                ->get(1, 0)
                ->getResult();
        }
        return false;
    }

    //登录查询用户名密码是否正确
    public function loginQuery($username = null, $password = null)
    {
        if ($username != null && $password != null) {
            return $this->db->select('id,disable')
                ->where('username', $username)
                ->where('password', $password)
                ->get(1, 0)
                ->getResult();
        }
        return false;
    }

    //封禁用户
    public function block($id = null)
    {
        if ($id != null) {
            return $this->db->where('id', $id)
                ->update(array('disable' => 1));
        }
        return false;
    }

    //解封用户
    public function unblock($id = null)
    {
        if ($id != null) {
            return $this->db->where('id', $id)
                ->update(array('disable' => 0));
        }
        return false;
    }

    //分页查看用户信息
    public function getUsers($pages = 1): array
    {
        if ($pages == null) {
            $pages = 1;
        }
        return $this->db->select('id,username,post_num,comment_num,disable')
            ->get(50, $pages * 50 - 50)
            ->getResult();
    }

    public function addComment($id): bool
    {
        return $this->db->set('comment_num', 'comment_num+1', false)
            ->where('id', $id)
            ->update();
    }

    public function addPost($id): bool
    {
        return $this->db->set('post_num', 'post_num+1', false)
            ->where('id', $id)
            ->update();
    }

    public function reduceComment($id): bool
    {
        return $this->db->set('comment_num', 'comment_num-1', false)
            ->where('id', $id)
            ->update();
    }

    public function reducePost($id): bool
    {
        return $this->db->set('post_num', 'post_num-1', false)
            ->where('id', $id)
            ->update();
    }
}
