<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var chieff\modules\UserProfile\models\forms\UserUpdateForm $model
 */

$userViewPath = Yii::$app->getModule('user-management')->userViewPath ? Yii::$app->getModule('user-management')->userViewPath : '/user-management/user/view';

$this->title = UserManagementModule::t('back', 'Editing user: ') . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => UserManagementModule::t('back', 'Users'), 'url' => ['/user-management/user/index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => [$userViewPath, 'id' => $model->id]];
$this->params['breadcrumbs'][] = UserManagementModule::t('back', 'Editing');
?>
<div class="user-update">
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $this->render('_form', compact('model')) ?>
        </div>
    </div>
</div>