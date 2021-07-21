<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class CommentModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        //创建数据库连接
        $this->db = Database::connect()->table('comment');
    }

    public function insert($data = null, bool $returnID = true)
    {
        if ($data != null) {
            return $this->db->insert($data);
        }
        return false;
    }

    //分页查看评论
    public function post($id = null, $pages = 1)
    {
        if ($pages == null) {
            $pages = 1;
        }
        if ($id != null) {
            return $this->db->select('user_id,content')
                ->where('post_id', $id)
                ->orderBy('floor', 'asc')
                ->get(50, $pages * 50 - 50)
                ->getResult();
        }
        return false;
    }

    //查询单条信息
    public function getComment($select, $id = null, $userId = null): array
    {
        $this->db->select($select);
        if ($id != null) {
            $this->db->where('id', $id);
        }
        if ($userId != null) {
            $this->db->where('user_id', $userId);
        }
        return $this->db->get(1, 0)
            ->getResult();
    }

    //改变评论内容
    public function change($id = null, $data = null)
    {
        if ($id != null && $data != null) {
            return $this->db->where('id', $id)
                ->update($data);
        }
        return false;
    }

    //删除评论
    public function del($id = null)
    {
        if ($id != null) {
            return $this->db->where('id', $id)
                ->delete();
        }
        return false;
    }

    //分页查看评论
    public function getComments($id, $pages = 1): array
    {
        if ($pages == null) {
            $pages = 1;
        }
        return $this->db->select('user_id,content')
            ->where('post_id', $id)
            ->orderBy("last_edited_at", "asc")
            ->get(50, $pages * 50 - 50)
            ->getResult();
    }
}
