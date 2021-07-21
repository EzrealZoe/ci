<?php

namespace App\Controllers;

use ReflectionException;
use WebGeeker\Validation\Validation;
use App\Models\ForumModel;
use App\Models\AdminModel;

class Forum extends BaseController
{

    private $adminModel;
    private $forumModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->forumModel = new ForumModel();
    }

    /**
     * @throws ReflectionException
     */
    //创建版块
    public function create()
    {
        $ans = array("status" => "1");
        //查看是否管理员账号登录
        $auth = new Auth();
        $adminId = $auth->authenticate($this->adminModel);

        if ($adminId !== false) {
            try {
                Validation::validate($_POST, [
                    "topic" => "StrLenGeLe:1,10",
                    "info" => "StrLenGeLe:0,255",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }

            $model = $this->forumModel;
            if ($model->isRepeated($_POST['topic'])) {
                //名重复
                $ans["status"] = 3003;
                exit(json_encode($ans));
            }
            $data = $_POST;
            $data['order'] = 0;
            $rst = $model->insert($data);
            if ($rst->connID->errno !== 0) {
                //插入数据库失败
                $ans["status"] = 3002;
            }
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //修改版块内容
    public function edit()
    {
        $ans = array("status" => "1");
        //查看是否管理员账号登录
        $auth = new Auth();
        $adminId = $auth->authenticate($this->adminModel);

        if ($adminId !== false) {
            try {
                Validation::validate($_POST, [
                    "id" => "IntGeLe:1,2100000000",
                    "topic" => "StrLenGeLe:1,10",
                    "info" => "StrLenGeLe:1,255",
                ]);
            } catch (\Exception $e) {
                //数据格式不通过
                $ans["status"] = 2001;
                exit(json_encode($ans));
            }

            $model = $this->forumModel;
            $data = array(
                "topic" => $_POST['topic'],
                "info" => $_POST['info'],
            );
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

    //删除版块
    public function del()
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
            $model = $this->forumModel;
            $rst = $model->del($_POST['id']);
            if ($rst->connID->errno !== 0) {
                //删除失败
                $ans["status"] = 3002;
            }

        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //修改版块排序
    public function changeOrder()
    {
        $ans = array("status" => "1");
        //查看是否管理员账号登录
        $auth = new Auth();
        $adminId = $auth->authenticate($this->adminModel);

        if ($adminId !== false) {
            $model = $this->forumModel;
            for ($i = 0; $i < count($_POST['data']); $i++) {
                try {
                    Validation::validate($_POST['data'][$i], [
                        "id" => "IntGeLe:1,2100000000",
                        "order" => "IntGeLe:0,2100000000",
                    ]);
                } catch (\Exception $e) {
                    //数据格式不通过
                    $ans["status"] = 2001;
                    exit(json_encode($ans));
                }
                $rst = $model->change($_POST['data'][$i]['id'], array("order" => $_POST['data'][$i]['order']));
                if (!$rst) {
                    //更新失败
                    $ans["status"] = 3002;
                    exit(json_encode($ans));
                }
            }
        } else {
            //未登录
            $ans["status"] = 3001;
        }
        exit(json_encode($ans));
    }

    //分页获取版块
    public function getForums()
    {
        $ans = array("status" => "1");
        try {
            Validation::validate($_GET, [
                "p" => "IntGeLe:0,50",
            ]);
        } catch (\Exception $e) {
            //数据格式不通过
            $ans["status"] = 2001;
            exit(json_encode($ans));
        }
        $model = $this->forumModel;
        $rst = $model->getForums($_GET['p']);
        $ans["data"] = $rst;
        exit(json_encode($ans));
    }

    //获取版块名
    public function getTopic()
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
        $model = $this->forumModel;
        $rst = $model->getTopic($_GET['id']);
        $ans["data"] = $rst;
        exit(json_encode($ans));
    }
}
