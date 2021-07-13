<?php

namespace App\Models;

use CodeIgniter\Model;
use mysql_xdevapi\Exception;

class UsersModel extends Model
{
    protected $db;

    function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = \Config\Database::connect()->table('users');
    }

    //测试用
    function getdata()
    {

        $sql = "SELECT * FROM users ";
        return $this->db->query($sql)->getResult();
    }

    //插入
    function insert($data = null, bool $returnID = true)
    {
        if ($data != null) {
            return $this->db->insert($data);
        }
        return false;
    }

    //用户名记录查询
    function usernameQuery($username = null)
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
    function loginQuery($username = null, $password = null)
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
