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

    //查询单条信息
    public function getPost($select, $id = null, $userId = null): array
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

    //删除帖子
    public function del($id = null)
    {
        if ($id != null) {
            return $this->db->where('id', $id)
                ->delete();
        }
        return false;
    }

    //分页查看帖子
    public function getPosts($forum = 1, $pages = 1): array
    {
        if ($pages == null) {
            $pages = 1;
        }
        return $this->db->select('id,title,last_edited_at')
            ->where('forum_id', $forum)
            ->orderBy("last_edited_at", "desc")
            ->get(50, $pages * 50 - 50)
            ->getResult();
    }

    //分页查看用户发布的帖子
    public function getPosted($id, $pages = 1): array
    {
        if ($pages == null) {
            $pages = 1;
        }
        return $this->db->select('id,title')
            ->where('user_id', $id)
            ->orderBy("created_at", "desc")
            ->get(50, $pages * 50 - 50)
            ->getResult();
    }

    public function addComment($id): bool
    {
        return $this->db->set('comment_num', 'comment_num+1', false)
            ->where('id', $id)
            ->update();
    }

}
