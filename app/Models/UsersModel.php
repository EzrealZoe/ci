<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $db;
    function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = \Config\Database::connect();
    }

    function getdata()
    {
        //sql语句
        $sql = "SELECT * FROM users ";
        //$sqlrst = $this->Db->query($sql)->getResultArray();
        //上面的一行是返回数组，下面的一行是返回对象
        $sqlrst = $this->db->query($sql)->getResult();
        return $sqlrst;
    }

    function insert($array)
    {
        $sql = "INSERT INTO users " .
            "(username,nickname, password,email,birthday,sex,province,city,area,last_login_at,updated_at,created_at) " .
            "VALUES " .
            "('$array[0]','$array[1]','$array[2]','$array[3]','$array[4]','$array[5]','$array[6]','$array[0]','$array[0]','$array[0]','$array[0]','$array[0]')";

        $sql = "SELECT * FROM users ";
        //$sqlrst = $this->Db->query($sql)->getResultArray();
        //上面的一行是返回数组，下面的一行是返回对象
        $sqlrst = $this->db->query($sql)->getResult();
        return $sqlrst;
    }
}