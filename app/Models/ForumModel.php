<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class ForumModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = Database::connect()->table('forum');
    }

    public function insert($data = null, bool $returnID = true)
    {
        if ($data != null) {
            return $this->db->insert($data);
        }
        return false;
    }

    //取版块信息
    public function forumQuery(): array
    {
        return $this->db->select('id,topic')
            ->orderBy("order", "asc")
            ->get(0, 50)
            ->getResult();
    }

    //改变版块内容
    public function change($id = null, $data = null)
    {
        if ($id != null && $data != null) {
            return $this->db->where('id', $id)
                ->update($data);
        }
        return false;
    }

    //改变版块顺序
    public function changeOrder($id = null, $order = null)
    {
        if ($id != null && $order != null) {
            return $this->db->where('id', $id)
                ->update(array('order' => $order));
        }
        return false;
    }

    //删除版块
    public function del($id = null)
    {
        if ($id != null) {
            return $this->db->where('id', $id)
                ->delete();
        }
        return false;
    }

    //分页获取版块
    public function getForums($pages = 0): array
    {
        return $this->db->select('id,topic')
            ->orderBy("order", "asc")
            ->get($pages * 50, 50)
            ->getResult();
    }

    //获取版块名
    public function getTopic($id): array
    {
        return $this->db->select('topic')
            ->where("id", $id)
            ->get(0, 1)
            ->getResult();
    }
}
