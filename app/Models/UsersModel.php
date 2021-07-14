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
                ->get(0, 1)
                ->getResult();
        }
        return false;
    }

    //登录查询用户名密码是否正确
    public function loginQuery($username = null, $password = null)
    {
        if ($username != null && $password != null) {
            return $this->db->select('username')
                ->where('username', $username)
                ->where('password', $password)
                ->get(0, 1)
                ->getResult();
        }
        return false;
    }
}
