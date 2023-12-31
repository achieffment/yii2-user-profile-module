<?php

use chieff\modules\UserProfile\UserProfileModule;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\bootstrap4\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;
use kartik\file\FileInput;
use kartik\date\DatePicker;

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
    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off', 'data-role' => 'password']) ?>
    <?= $form->field($model, 'repeat_password')->passwordInput(['maxlength' => 255, 'autocomplete' => 'off', 'data-role' => 'password-repeat']) ?>
    <?= $this->render('../profile/_password_generator') ?>

    <?= $form->field($model, 'avatar_file')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*', 'multiple' => false],
        'pluginOptions' => [
            'showCaption' => false,
        ]
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

    <?= $form->field($model, 'comment')->textarea(['maxlength' => 500]) ?>

    <div class="row">
        <div class="col-6 col-sm-12 col-md-6">
            <?= $form->field($model, 'vk')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
            <?= $form->field($model, 'ok')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
            <?= $form->field($model, 'telegram')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
            <?= $form->field($model, 'whatsapp')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
        </div>
        <div class="col-6 col-sm-12 col-md-6">
            <?= $form->field($model, 'viber')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
            <?= $form->field($model, 'youtube')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
            <?= $form->field($model, 'twitter')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
            <?= $form->field($model, 'facebook')->textInput(['maxlength' => 100, 'autocomplete' => 'off']) ?>
        </div>
    </div>

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