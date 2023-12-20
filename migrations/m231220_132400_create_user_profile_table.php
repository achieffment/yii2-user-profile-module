<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_profile}}`.
 */
class m231220_132400_create_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tablename = \Yii::$app->getModule('user-profile')->profile_table;

        // Create profile table
        $this->createTable($tablename, array(
            'id' => 'pk',
            'user_id' => 'int not null',
            'avatar' => 'varchar(100) default null',
            'firstname' => 'varchar(300) not null',
            'lastname' => 'varchar(300) not null',
            'patronymic' => 'varchar(300) not null',
            'dob' => 'varchar(30) not null',
            'phone' => 'varchar(60) not null',
            'sex' => 'varchar(10) not null',
            'comment' => 'varchar(1500) default null',
            'job' => 'varchar(10) default null',
            'social' => 'varchar(3000) default null',
        ), $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable(Yii::$app->getModule('user-profile')->profile_table);
    }
}
