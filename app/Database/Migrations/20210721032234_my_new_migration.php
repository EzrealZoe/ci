<?php


use Phinx\Migration\AbstractMigration;

class MyNewMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $users = $this->table('users');
        $users->addColumn('username', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 20])
            ->addColumn('nickname', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 20])
            ->addColumn('password', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 32])
            ->addColumn('head', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 255, 'null' => true])
            ->addColumn('email', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 320])
            ->addColumn('birthday', 'date')
            ->addColumn('sex', 'boolean')
            ->addColumn('province', 'integer')
            ->addColumn('city', 'integer')
            ->addColumn('area', 'integer')
            ->addColumn('post_num', 'integer')
            ->addColumn('comment_num', 'integer')
            ->addColumn('disable', 'boolean')
            ->addColumn('last_login_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addColumn('created_at', 'datetime')
            ->create();

        $admin = $this->table('admin');
        $admin->addColumn('username', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 20])
            ->addColumn('password', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 32])
            ->create();

        $comment = $this->table('comment');
        $comment->addColumn('post_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('content', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 255])
            ->addColumn('created_at', 'datetime')
            ->addColumn('last_edited_at', 'datetime')
            ->create();

        $post = $this->table('post');
        $post->addColumn('forum_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('title', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 30])
            ->addColumn('content', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 255])
            ->addColumn('comment_num', 'integer')
            ->addColumn('created_at', 'datetime')
            ->addColumn('last_edited_at', 'datetime')
            ->create();

        $forum = $this->table('forum');
        $forum->addColumn('topic', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 10])
            ->addColumn('info', 'string', ['collation' => "utf8mb4_general_ci", 'limit' => 255, 'null' => true])
            ->addColumn('order', 'integer')
            ->create();
    }
}
