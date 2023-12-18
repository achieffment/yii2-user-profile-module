<?php

use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use webvimark\extensions\BootstrapSwitch\BootstrapSwitch;
use chieff\modules\UserProfile\UserProfileModule;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use webvimark\modules\UserManagement\components\GhostHtml;
use kartik\file\FileInput;
use kartik\datetime\DateTimePicker;
use kartik\date\DatePicker;

/**
 * @var yii\web\View $this
 * @var chieff\modules\UserProfile\models\forms\UserUpdateForm $model
 */

$this->title = UserManagementModule::t('back', 'Editing user: ') . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => UserManagementModule::t('back', 'Users'), 'url' => ['/user-management/user/index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['/user-management/user/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = UserManagementModule::t('back', 'Editing');
?>
<div class="user-update">
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="user-form">

                <?php $form = ActiveForm::begin([
                    'id' => 'user',
                    'layout' => 'horizontal',
                    'validateOnBlur' => false,
                    'options' => [
                        'enctype' => 'multipart/form-data'
                    ]
                ]); ?>

                <?= $form->field($model, 'status')->dropDownList(User::getStatusList()) ?>
                <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

                <?php if (User::hasPermission('bindUserToIp')): ?>
                    <?= $form->field($model, 'bind_to_ip')
                        ->textInput(['maxlength' => 255])
                        ->hint(UserManagementModule::t('back', 'For example: 123.34.56.78, 168.111.192.12')) ?>
                <?php endif; ?>

                <?php if (User::hasPermission('editUserEmail')): ?>
                    <?= $form->field($model, 'email')->textInput(['maxlength' => 255]) ?>
                    <?= $form->field($model, 'email_confirmed')->checkbox() ?>
                <?php endif; ?>

                <?= $form->field($model, 'attempts')->textInput(['type' => 'number']) ?>

                <?= $form->field($model, 'blocked_at')->widget(DateTimePicker::classname(), [
                    'options' => [
                        'value' => $model->blocked_at ? (is_numeric($model->blocked_at) ? date('d-m-Y H:i', $model->blocked_at) : $model->blocked_at) : null,
                    ],
                    'pluginOptions' => [
                        'todayBtn' => true,
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'format' => 'dd-m-yyyy HH:ii',
                        'startDate' => date('d-m-Y H:i'),
                    ]
                ]); ?>

                <?= $form->field($model, 'blocked_for')->widget(DateTimePicker::classname(), [
                    'options' => [
                        'value' => $model->blocked_for ? (is_numeric($model->blocked_for) ? date('d-m-Y H:i', $model->blocked_for) : $model->blocked_for) : null,
                    ],
                    'pluginOptions' => [
                        'todayBtn' => true,
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'format' => 'dd-m-yyyy HH:ii',
                        'startDate' => date('d-m-Y H:i'),
                    ]
                ]); ?>

                <?
                    $avatar = ['showCaption' => false];
                    if ($model->avatar) {
                        $avatar['initialPreview'] = $model->getAvatar();
                        $avatar['initialPreviewAsData'] = true;
                        $avatar['overwriteInitial'] = true;
                    }
                ?>
                <?= $form->field($model, 'avatar_file')->widget(FileInput::classname(), [
                    'options' => ['accept' => 'image/*', 'multiple' => false],
                    'pluginOptions' => $avatar
                ]); ?>

                <?= $form->field($model, 'sex')->dropDownList(Yii::$app->getModule('user-profile')->arraySex) ?>

                <?= $form->field($model, 'firstname')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
                <?= $form->field($model, 'lastname')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
                <?= $form->field($model, 'patronymic')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>

                <?= $form->field($model, 'dob')->widget(DatePicker::classname(), [
                    'options' => [
                        'value' => $model->dob ? (is_numeric($model->dob) ? date('d-m-Y', $model->dob) : $model->dob) : null,
                        'placeholder' => UserProfileModule::t('front', 'Enter dob')
                    ],
                    'pluginOptions' => [
                        'todayBtn' => true,
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy',
                        'startDate' => '01-01-1950'
                    ]
                ]); ?>

                <?= $form->field($model, 'phone')->textInput(['maxlength' => 20, 'autocomplete' => 'off', 'placeholder' => Yii::$app->getModule('user-profile')->phonePlaceholder]) ?>

                <div class="form-group">
                    <?= Html::submitButton(
                        '<i class="fa fa-check"></i> ' . UserManagementModule::t('back', 'Save'),
                        ['class' => 'btn btn-primary']
                    ) ?>
                    <?= GhostHtml::a(
                        UserManagementModule::t('back', 'Change password'),
                        ['/user-management/user/change-password', 'id' => $model->id],
                        ['class' => 'btn btn-secondary', 'data-pjax' => 0]);
                    ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
            <?php BootstrapSwitch::widget() ?>
        </div>
    </div>
</div>