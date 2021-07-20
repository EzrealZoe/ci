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

    //判断用户是否有权限修改这个帖子
    public function isPermitted($id = null, $userId = null): bool
    {
        if ($id != null && $userId != null) {
            if (count($this->db->select('user_id')
                    ->where('id', $id)
                    ->where('user_id', $userId)
                    ->get(0, 1)
                    ->getResult()) > 0) {
                return true;
            }
        }
        return false;
    }

    //判断是否有这个帖子
    public function exists($id = null): bool
    {
        if ($id != null) {
            if (count($this->db->select('id')
                    ->where('id', $id)
                    ->get(0, 1)
                    ->getResult()) > 0) {
                return true;
            }
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

    //分页查看帖子
    public function getPosts($forum = 1, $pages = 0): array
    {
        return $this->db->select('id,title,last_edited_at')
            ->where('forum_id', $forum)
            ->orderBy("last_edited_at", "desc")
            ->get($pages * 50, 50)
            ->getResult();
    }

    //发帖者查看帖子
    public function getPost($userId, $id): array
    {
        return $this->db->select('id,forum_id,title,content')
            ->where('user_id', $userId)
            ->where('id', $id)
            ->get(0, 1)
            ->getResult();
    }

    //查看一个帖子
    public function viewPost($id): array
    {
        return $this->db->select('user_id,title,content')
            ->where('id', $id)
            ->get(0, 1)
            ->getResult();
    }

    //分页查看用户发布的帖子
    public function getPosted($id, $pages = 0): array
    {
        return $this->db->select('id,title')
            ->where('user_id', $id)
            ->orderBy("created_at", "desc")
            ->get($pages * 50, 50)
            ->getResult();
    }

    public function addComment($id): bool
    {
        return $this->db->set('comment_num', 'comment_num+1', false)
            ->where('id', $id)
            ->update();
    }

    //获取发布者id
    public function getOwner($id)
    {
        return $this->db->select('user_id')
            ->where('id', $id)
            ->get(0, 1)
            ->getResult()[0]
            ->user_id;
    }
}
