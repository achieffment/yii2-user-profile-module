<?php

use webvimark\modules\UserManagement\models\rbacDB\AuthItemGroup;
use webvimark\modules\UserManagement\models\rbacDB\Permission;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\rbacDB\Route;
use yii\db\Migration;

/**
 * Class m231220_132543_create_basic_profile_permissions
 */
class m231220_132543_create_basic_profile_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $groupCode = 'userManagement';

        Role::assignRoutesViaPermission('Admin', 'createProfiles', [
            '/user-profile/profile/create',
        ], 'Create profiles', $groupCode);
        Role::assignRoutesViaPermission('Admin', 'editProfiles', [
            '/user-profile/profile/update',
        ], 'Edit profiles', $groupCode);
        Role::assignRoutesViaPermission('Admin', 'viewProfiles', [
            '/user-profile/profile/view',
        ], 'View profiles', $groupCode);

        Permission::addChildren('createProfiles', ['viewProfiles', 'viewProfiles']);
        Permission::addChildren('editProfiles', ['viewProfiles', 'viewProfiles']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        Permission::deleteAll(['name' => [
            'createProfiles',
            'editProfiles',
            'viewProfiles',
        ]]);
//        echo "m231220_132543_create_basic_profile_permissions cannot be reverted.\n";
//
//        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231220_132543_create_basic_profile_permissions cannot be reverted.\n";

        return false;
    }
    */
}
