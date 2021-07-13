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
    function insert($data = NULL, bool $returnID = true)
    {
        if ($data != NULL) {
            return $this->db->insert($data);
        }
        return false;
    }

    //用户名记录查询
    function usernameQuery($username = NULL)
    {
        if ($username != NULL) {
            return $this->db->select('username')
                ->where('username', $username)
                ->get(0,1)
                ->getResult();
        }
        return false;
    }

    //登录查询用户名密码是否正确
    function loginQuery($username = NULL, $password = NULL)
    {
        if ($username != NULL && $password != NULL) {
            return $this->db->select('username')
                ->where('username', $username)
                ->where('password', $password)
                ->get(0,1)
                ->getResult();
        }
        return false;
    }


}