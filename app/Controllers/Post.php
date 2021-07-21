<?php

namespace App\Controllers;

use ReflectionException;
use WebGeeker\Validation\Validation;
use App\Models\PostModel;
use App\Models\AdminModel;
use App\Models\UsersModel;
use DateTime;

class Post extends BaseController
{

    private $userModel;
    private $adminModel;
    private $postModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->adminModel = new AdminModel();
        $this->postModel = new PostModel();
    }
    
    /**
     * @throws ReflectionException
     */
    //创建帖子
    public function create()
    {
        $ans = array("status" => "1");
        //查看是否用户账号登录
        $auth = new Auth();
        $userInfo = $auth->authenticating($this->userModel);
        if ($userInfo !== false) {
            if ($userInfo->disable == '1') {
                //被封禁
                $ans["status"] = 3002;
                exit(json_encode($ans));
            }

            $userId = $userInfo->id;
            try {
                Validation::validate($_POST, [
                    "title" => "StrLenGeLe:1,30",
                    "forum_id" => "IntGeLe:1,100",
                    "content" => "StrLenGeLe:1,255",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }

            $model = $this->postModel;
            $data = $_POST;
            $date = (new DateTime())->format("Y-m-d H:i:s");
            $data['created_at'] = $date;
            $data['last_edited_at'] = $date;
            $data['user_id'] = $userId;
            $data['comment_num'] = 0;
            //var_dump($data);
            $rst = $model->insert($data);
            if ($rst->connID->errno !== 0) {
                //插入数据库失败
                $ans["status"] = 3002;
            } else {
                ($this->userModel)->addPost($userId);
            }

        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //修改帖子
    public function edit()
    {
        $ans = array("status" => "1");
        //查看是否用户账号登录
        $auth = new Auth();
        $userInfo = $auth->authenticating($this->userModel);
        if ($userInfo !== false) {
            if ($userInfo->disable == '1') {
                //被封禁
                $ans["status"] = 3002;
                exit(json_encode($ans));
            }

            $userId = $userInfo->id;
            try {
                Validation::validate($_POST, [
                    "id" => "IntGeLe:1,2100000000",
                    "title" => "StrLenGeLe:1,30",
                    "forum_id" => "IntGeLe:1,100",
                    "content" => "StrLenGeLe:1,255",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }
            $model = $this->postModel;
            if ($model->isPermitted($_POST['id'], $userId)) {
                $data = array(
                    "title" => $_POST['title'],
                    "forum_id" => $_POST['forum_id'],
                    "content" => $_POST['content']
                );
                $date = (new DateTime())->format("Y-m-d H:i:s");
                $data['last_edited_at'] = $date;
                $rst = $model->change($_POST['id'], $data);
                if (!$rst) {
                    //更新失败
                    $ans["status"] = 3002;
                }
            } else {
                //无权修改他人的帖子或帖子不存在
                $ans["status"] = 3003;
            }
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //用户删除帖子
    public function del()
    {
        $ans = array("status" => "1");
        //查看是否用户账号登录
        $auth = new Auth();
        $userId = $auth->authenticate($this->userModel);

        if ($userId !== false) {
            try {
                Validation::validate($_POST, [
                    "id" => "IntGeLe:1,2100000000",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }
            $model = $this->postModel;
            if ($model->isPermitted($_POST['id'], $userId)) {
                $rst = $model->del($_POST['id']);
                if ($rst->connID->errno !== 0) {
                    //删除失败
                    $ans["status"] = 3002;
                } else {
                    ($this->userModel)->reducePost($userId);
                }
            } else {
                //无权删除他人的帖子或帖子不存在
                $ans["status"] = 3003;
            }
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //管理员删除帖子
    public function adminDel()
    {
        $ans = array("status" => "1");
        //查看是否管理员账号登录
        $auth = new Auth();
        $adminId = $auth->authenticate($this->adminModel);

        if ($adminId !== false) {
            try {
                Validation::validate($_POST, [
                    "id" => "IntGeLe:1,2100000000",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }
            $model = $this->postModel;
            $userId = $model->getOwner($_POST['id']);
            $rst = $model->del($_POST['id']);
            if ($rst->connID->errno !== 0) {
                //删除失败
                $ans["status"] = 3002;
            } else {
                ($this->userModel)->reducePost($userId);
            }
        } else {
            //管理员未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //管理员置顶与取消置顶帖子
    public function stick()
    {
        $ans = array("status" => "1");
        //查看是否管理员账号登录
        $auth = new Auth();
        $adminId = $auth->authenticate($this->adminModel);

        if ($adminId !== false) {
            try {
                Validation::validate($_POST, [
                    "id" => "IntGeLe:1,2100000000",
                    "isStuck" => "Bool",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }
            $model = $this->postModel;

            $date = new DateTime();
            if ($_POST['isStuck'] == 'false') {
                $date = $date->modify('+100 year');
            }
            $data = array('last_edited_at' => $date->format("Y-m-d H:i:s"));
            $rst = $model->change($_POST['id'], $data);
            if (!$rst) {
                //更新失败
                $ans["status"] = 3002;
            }
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //分页获取帖子
    public function getPosts()
    {
        $ans = array("status" => "1");
        try {
            Validation::validate($_GET, [
                "f" => "IntGeLe:1,1000000",
                "p" => "IntGeLe:0,50",
            ]);
        } catch (\Exception $e) {
            //数据格式不通过
            $ans["status"] = 2001;
            exit(json_encode($ans));
        }
        $model = $this->postModel;
        if ($_GET['f'] == null) {
            $_GET['f'] = 1;
        }
        $rst = $model->getPosts($_GET['f'], $_GET['p']);
        $ans["data"] = $rst;
        exit(json_encode($ans));
    }

    //发帖者查看帖子
    public function getPost()
    {
        $ans = array("status" => "1");
        //查看是否用户账号登录
        $auth = new Auth();
        $userId = $auth->authenticate($this->userModel);

        if ($userId !== false) {
            try {
                Validation::validate($_GET, [
                    "id" => "IntGeLe:1,2100000000",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }
            if ($_GET['id'] == null) {
                $ans["status"] = 2002;
                exit(json_encode($ans));
            }

            $model = $this->postModel;
            $rst = $model->getPost($userId, $_GET['id']);
            $ans["data"] = $rst;
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //分页获取用户发布的帖子
    public function getPosted()
    {
        $ans = array("status" => "1");
        //查看是否用户账号登录
        $auth = new Auth();
        $userId = $auth->authenticate($this->userModel);

        if ($userId !== false) {
            try {
                Validation::validate($_GET, [
                    "p" => "IntGeLe:0,50",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }
            $model = $this->postModel;
            $rst = $model->getPosted($userId, $_GET['p']);
            $ans["data"] = $rst;
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //查看帖子
    public function viewPost()
    {
        $ans = array("status" => "1");
        try {
            Validation::validate($_GET, [
                "id" => "IntGeLe:1,2100000000",
            ]);
        } catch (\Exception $e) {
            //数据格式不通过
            $ans["status"] = 2001;
            exit(json_encode($ans));
        }
        if ($_GET['id'] == null) {
            $ans["status"] = 2002;
            exit(json_encode($ans));
        }

        $model = $this->postModel;
        $rst = $model->viewPost($_GET['id']);
        $ans["data"] = $rst;
        exit(json_encode($ans));
    }
}
