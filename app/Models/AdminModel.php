<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class AdminModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = Database::connect()->table('admin');
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
