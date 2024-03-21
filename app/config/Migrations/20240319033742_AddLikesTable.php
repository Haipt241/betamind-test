<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddLikesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('likes');
        $table->addColumn('article_id', 'integer', ['default' => null, 'null' => false,])
            ->addColumn('user_id', 'integer', ['default' => null, 'null' => false,])
            ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP',])
            ->addForeignKey('article_id', 'articles', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->addForeignKey('user_id', 'users', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION'])
            ->create();
    }
}
