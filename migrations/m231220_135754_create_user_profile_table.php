<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_profile}}`.
 */
class m231220_135754_create_user_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (\Yii::$app->getModule('user-profile')->dataEncodeMigration === true) {
            return true;
        }

        $tablename = \Yii::$app->getModule('user-profile')->profile_table;

        // Create profile table
        $this->createTable($tablename, array(
            'id' => 'pk',
            'user_id' => 'int not null',
            'avatar' => 'varchar(100) default null',
            'firstname' => 'varchar(100) not null',
            'lastname' => 'varchar(100) not null',
            'patronymic' => 'varchar(100) not null',
            'dob' => 'int not null',
            'phone' => 'varchar(20) not null',
            'sex' => 'tinyint not null',
            'comment' => 'varchar(500) default null',
            'job' => 'tinyint default null',
            'social' => 'varchar(1000) default null',
        ), $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (\Yii::$app->getModule('user-profile')->dataEncodeMigration === true) {
            return true;
        }

        $this->dropTable(Yii::$app->getModule('user-profile')->profile_table);
    }
}
