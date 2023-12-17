<?php

use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;
use kartik\file\FileInput;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\forms\RegistrationForm $model
 */

$this->title = UserManagementModule::t('front', 'Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-registration">
    <h2 class="text-center"><?= $this->title ?></h2>
    <?php $form = ActiveForm::begin([
        'id' => 'user',
        'layout' => 'horizontal',
        'validateOnBlur' => false,
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]); ?>
    <?= $form->field($model, 'username')->textInput(['maxlength' => 50, 'autocomplete' => 'off', 'autofocus' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
    <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

    <?= $form->field($model, 'avatar_file')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*', 'multiple' => false],
        'pluginOptions' => [
            'showCaption' => false,
        ]
    ]); ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
    <?= $form->field($model, 'lastname')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
    <?= $form->field($model, 'patronymic')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
    <?= $form->field($model, 'dob')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
    <?= $form->field($model, 'phone')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>
    <?= $form->field($model, 'sex')->textInput(['maxlength' => 255, 'autocomplete' => 'off']) ?>

    <?= $form->field($model, 'captcha')->widget(Captcha::className(), [
        'template' => '<div class="row"><div class="col-sm-2">{image}</div><div class="col-sm-3">{input}</div></div>',
        'captchaAction' => ['/user-management/auth/captcha']
    ]) ?>
    <div class="form-group">
        <?= Html::submitButton(
            '<i class="fa fa-check"></i> ' . UserManagementModule::t('front', 'Register'),
            ['class' => 'btn btn-primary']
        ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>