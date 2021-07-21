<?php

namespace App\Controllers;

use ReflectionException;
use WebGeeker\Validation\Validation;
use App\Models\PostModel;
use App\Models\CommentModel;
use App\Models\UsersModel;
use DateTime;

class Comment extends BaseController
{

    private $userModel;
    private $commentModel;
    private $postModel;

    public function __construct()
    {
        $this->userModel = new UsersModel();
        $this->commentModel = new CommentModel();
        $this->postModel = new PostModel();
    }

    /**
     * @throws ReflectionException
     */
    //创建评论
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
                    "post_id" => "IntGeLe:1,2100000000",
                    "content" => "StrLenGeLe:1,255",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }
            if (($this->postModel)->exists($_POST['post_id'])) {
                $model = $this->commentModel;
                $data = $_POST;
                $date = (new DateTime())->format("Y-m-d H:i:s");
                $data['created_at'] = $date;
                $data['last_edited_at'] = $date;
                $data['user_id'] = $userId;
                $rst = $model->insert($data);
                if ($rst->connID->errno !== 0) {
                    //插入数据库失败
                    $ans["status"] = 3002;
                }
                if (!($this->postModel)->addComment($_POST['post_id'])) {
                    //插入数据库失败
                    $ans["status"] = 3002;
                } else {
                    ($this->userModel)->addComment($userId);
                }
            } else {
                //评论不存在
                $ans["status"] = 3003;
            }
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //修改评论
    public function edit()
    {
        $ans = array("status" => "1");
        //查看是否用户账号登录
        $auth = new Auth();
        $userId = $auth->authenticate($this->userModel);

        if ($userId !== false) {
            try {
                Validation::validate($_POST, [
                    "id" => "IntGeLe:1,2100000000",
                    "content" => "StrLenGeLe:1,255",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }
            $model = $this->commentModel;
            if ($model->isPermitted($_POST['id'], $userId)) {
                $data = array(
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
                //无权修改他人的评论或评论不存在
                $ans["status"] = 3003;
            }
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //用户删除评论
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
            $model = $this->commentModel;
            if ($model->isPermitted($_POST['id'], $userId)) {
                $rst = $model->del($_POST['id']);
                if ($rst->connID->errno !== 0) {
                    //删除失败
                    $ans["status"] = 3002;
                }
            } else {
                //无权删除他人的评论或评论不存在
                $ans["status"] = 3003;
            }
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //分页获取评论
    public function getComments()
    {
        $ans = array("status" => "1");
        try {
            Validation::validate($_GET, [
                "id" => "IntGeLe:1,1000000",
                "p" => "IntGeLe:0,50",
            ]);
        } catch (\Exception $e) {
            //数据格式不通过
            $ans["status"] = 2001;
            exit(json_encode($ans));
        }
        $model = $this->commentModel;
        $rst = $model->getComments($_GET['id'], $_GET['p']);
        $ans["data"] = $rst;
        exit(json_encode($ans));
    }
}
