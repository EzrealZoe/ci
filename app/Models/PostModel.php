<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class PostModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = Database::connect()->table('post');
    }

    public function insert($data = null, bool $returnID = true)
    {
        if ($data != null) {
            return $this->db->insert($data);
        }
        return false;
    }

    //改变帖子内容
    public function change($id = null, $data = null)
    {
        if ($id != null && $data != null) {
            return $this->db->where('id', $id)
                ->update($data);
        }
        return false;
    }

    //删除帖子
    public function del($id = null)
    {
        if ($id != null) {
            return $this->db->where('id', $id)
                ->delete();
        }
        return false;
    }
}
