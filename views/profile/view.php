<?php

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\models\rbacDB\Role;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var webvimark\modules\UserManagement\models\User $model
 */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => UserManagementModule::t('back', 'Users'), 'url' => ['/user-management/user/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">
    <h2 class="lte-hide-title"><?= $this->title ?></h2>
    <div class="panel panel-default">
        <div class="panel-body">
            <p>
                <?
                    $userCreatePath = Yii::$app->getModule('user-management')->userCreatePath ? Yii::$app->getModule('user-management')->userCreatePath : '/user-management/user/create';
                    $userUpdatePath = Yii::$app->getModule('user-management')->userUpdatePath ? Yii::$app->getModule('user-management')->userUpdatePath : '/user-management/user/update';
                ?>
                <?= GhostHtml::a(UserManagementModule::t('back', 'Create'), [$userCreatePath], ['class' => 'btn btn-sm btn-success']) ?>
                <?= GhostHtml::a(UserManagementModule::t('back', 'Edit'), [$userUpdatePath, 'id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                <?= GhostHtml::a(
                    UserManagementModule::t('back', 'Roles and permissions'),
                    ['/user-management/user-permission/set', 'id' => $model->id],
                    ['class' => 'btn btn-sm btn-secondary']
                ) ?>
                <?= GhostHtml::a(UserManagementModule::t('back', 'Delete'), ['/user-management/user/delete', 'id' => $model->id], [
                    'class' => 'btn btn-sm btn-danger pull-right',
                    'data' => [
                        'confirm' => UserManagementModule::t('back', 'Are you sure you want to delete this user?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'status',
                        'value' => User::getStatusValue($model->status),
                    ],
                    'username',
                    [
                        'attribute' => 'email',
                        'value' => $model->email,
                        'format' => 'email',
                        'visible' => User::hasPermission('viewUserEmail'),
                    ],
                    [
                        'attribute' => 'email_confirmed',
                        'value' => $model->email_confirmed,
                        'format' => 'boolean',
                        'visible' => User::hasPermission('viewUserEmail'),
                    ],
                    [
                        'label' => UserManagementModule::t('back', 'Roles'),
                        'value' => implode('<br>', ArrayHelper::map(Role::getUserRoles($model->id), 'name', 'description')),
                        'visible' => User::hasPermission('viewUserRoles'),
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'bind_to_ip',
                        'visible' => User::hasPermission('bindUserToIp'),
                    ],
                    array(
                        'attribute' => 'registration_ip',
                        'value' => Html::a($model->registration_ip, "http://ipinfo.io/" . $model->registration_ip, ["target" => "_blank"]),
                        'format' => 'raw',
                        'visible' => User::hasPermission('viewRegistrationIp'),
                    ),
                    array(
                        'attribute' => 'attempts',
                        'value' => $model->attempts,
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'firstname',
                        'value' => $model->firstname,
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'lastname',
                        'value' => $model->lastname,
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'patronymic',
                        'value' => $model->patronymic,
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'dob',
                        'value' => date('d-m-Y', $model->dob),
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'phone',
                        'value' => function($model) {
                            if ($model->phone) {
                                if (preg_match_all('/[0-9]/', $model->phone, $matches) !== false) {
                                    $matches = implode('', $matches[0]);
                                    if (substr($matches, 0, 1) == '7') {
                                        $matches = '+' . $matches;
                                        return Html::a($model->phone, 'tel:' . $matches);
                                    }
                                }
                                return $model->phone;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'sex',
                        'value' => function($model) {
                            if (
                                is_numeric($model->sex) &&
                                isset(Yii::$app->getModule('user-profile')->arraySex[$model->sex])
                            ) {
                                return Yii::$app->getModule('user-profile')->arraySex[$model->sex];
                            } else if (is_numeric($model->sex)) {
                                return $model->sex;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'comment',
                        'value' => $model->comment,
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'job',
                        'value' => function($model) {
                            if (
                                is_numeric($model->job) &&
                                isset(Yii::$app->getModule('user-profile')->arrayJob[$model->job])
                            ) {
                                return Yii::$app->getModule('user-profile')->arrayJob[$model->job];
                            } else if (is_numeric($model->job)) {
                                return $model->job;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'vk',
                        'value' => function($model) {
                            if (
                                $model->vk &&
                                (strpos($model->vk, 'https') !== false)
                            ) {
                                return Html::a($model->vk, $model->vk);
                            } else if ($model->vk) {
                                return $model->vk;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'ok',
                        'value' => function($model) {
                            if (
                                $model->ok &&
                                (strpos($model->ok, 'https') !== false)
                            ) {
                                return Html::a($model->ok, $model->ok);
                            } else if ($model->ok) {
                                return $model->ok;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'telegram',
                        'value' => function($model) {
                            if (
                                $model->telegram &&
                                (strpos($model->telegram, 'https') !== false)
                            ) {
                                return Html::a($model->telegram, $model->telegram);
                            } else if ($model->telegram) {
                                return $model->telegram;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'whatsapp',
                        'value' => function($model) {
                            if (
                                $model->whatsapp &&
                                (strpos($model->whatsapp, 'https') !== false)
                            ) {
                                return Html::a($model->whatsapp, $model->whatsapp);
                            } else if ($model->whatsapp) {
                                return $model->whatsapp;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'viber',
                        'value' => function($model) {
                            if (
                                $model->viber &&
                                (strpos($model->viber, 'https') !== false)
                            ) {
                                return Html::a($model->viber, $model->viber);
                            } else if ($model->viber) {
                                return $model->viber;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'youtube',
                        'value' => function($model) {
                            if (
                                $model->youtube &&
                                (strpos($model->youtube, 'https') !== false)
                            ) {
                                return Html::a($model->youtube, $model->youtube);
                            } else if ($model->youtube) {
                                return $model->youtube;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'twitter',
                        'value' => function($model) {
                            if (
                                $model->twitter &&
                                (strpos($model->twitter, 'https') !== false)
                            ) {
                                return Html::a($model->twitter, $model->twitter);
                            } else if ($model->twitter) {
                                return $model->twitter;
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'facebook',
                        'value' => function($model) {
                            if (
                                $model->facebook &&
                                (strpos($model->facebook, 'https') !== false)
                            ) {
                                return Html::a($model->facebook, $model->facebook);
                            } else if ($model->facebook) {
                                return $model->facebook;
                            }
                        },
                        'format' => 'raw',
                    ),

                    array(
                        'attribute' => 'avatar',
                        'value' => function($model) {
                            if ($model->avatar) {
                                return Html::img($model->getAvatar(), ['style' => 'max-height: 300px']);
                            }
                        },
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'blocked_at',
                        'value' => $model->blocked_at ? date('d-m-Y H:i', $model->blocked_at) : null,
                        'format' => 'raw',
                    ),
                    array(
                        'attribute' => 'blocked_for',
                        'value' => $model->blocked_for ? date('d-m-Y H:i', $model->blocked_for) : null,
                        'format' => 'raw',
                    ),
                    'created_at:datetime',
                    'updated_at:datetime',
                    [
                        'attribute' => 'created_by',
                        'value' => function ($model) {
                            if ($model->created_by) {
                                $user = User::findByIdSelectUsername($model->created_by);
                                if ($user) {
                                    return Html::a($user['username'], [Yii::$app->getModule('user-management')->userViewPath ? Yii::$app->getModule('user-management')->userViewPath : '/user-management/user/view', 'id' => $model->created_by], ['data-pjax' => 0]);
                                }
                                return $model->created_by;
                            }
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'updated_by',
                        'value' => function ($model) {
                            if ($model->updated_by) {
                                $user = User::findByIdSelectUsername($model->updated_by);
                                if ($user) {
                                    return Html::a($user['username'], [Yii::$app->getModule('user-management')->userViewPath ? Yii::$app->getModule('user-management')->userViewPath : '/user-management/user/view', 'id' => $model->updated_by], ['data-pjax' => 0]);
                                }
                                return $model->updated_by;
                            }
                        },
                        'format' => 'raw',
                    ],
                ],
            ]) ?>
        </div>
    </div>
</div>