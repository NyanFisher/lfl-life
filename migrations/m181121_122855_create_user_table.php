<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m181121_122855_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username'=>$this->string(32)->notNull()->unique(),
            'firstName'=>$this->string(32),
            'lastName'=>$this->string(32),
            'leadingFoot'=>$this->boolean(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->date(),
            'updated_at' => $this->date(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
