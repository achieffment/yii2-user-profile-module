<?php

namespace chieff\modules\UserProfile\controllers;

use webvimark\modules\UserManagement\models\User;
use chieff\modules\UserProfile\models\forms\UserUpdateForm;
use chieff\modules\UserProfile\models\UserProfile;
use yii\web\NotFoundHttpException;
use Yii;

class ProfileController extends \webvimark\components\BaseController
{
    public function behaviors()
    {
        return [
            'ghost-access' => [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    public function actionCreate()
    {
        $this->layout = '//main.php';
        if (Yii::$app->getModule('user-profile')->dataEncode) {
            $model = new UserUpdateForm(['scenario' => 'newUserEncoded']);
        } else {
            $model = new UserUpdateForm(['scenario' => 'newUserDefault']);
        }
        $model->isNewRecord = true;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $user = new User(['scenario' => 'newUser']);
                $user->status = $model->status;
                $user->username = $model->username;
                if (User::hasPermission('bindUserToIp')) {
                    $user->bind_to_ip = $model->bind_to_ip;
                }
                if (User::hasPermission('editUserEmail')) {
                    $user->email = $model->email;
                    $user->email_confirmed = $model->email_confirmed;
                }
                $user->attempts = $model->attempts;
                $user->blocked_at = $model->blocked_at;
                $user->blocked_for = $model->blocked_for;
                $user->password = $model->password;
                $user->repeat_password = $model->repeat_password;
                $result = $user->save();

                if ($result) {
                    if (Yii::$app->getModule('user-profile')->dataEncode) {
                        $profile = new UserProfile(['scenario' => 'encodedUser']);
                    } else {
                        $profile = new UserProfile(['scenario' => 'defaultUser']);
                    }
                    $profile->user_id = $user->id;
                    $profile->avatar = $model->avatarUpload($user->id, $profile->avatar);
                    $profile->firstname = $model->firstname;
                    $profile->lastname = $model->lastname;
                    $profile->patronymic = $model->patronymic;
                    $profile->dob = $model->dob;
                    $profile->phone = $model->phone;
                    $profile->sex = $model->sex;
                    $profile->comment = $model->comment;
                    $profile->job = $model->job;
                    $profile->social = $profile->getSocialStringFromArray($model);
                    $result = $profile->save();

                    if ($result) {
                        $path = Yii::$app->getModule('user-management')->userViewPath ? Yii::$app->getModule('user-management')->userViewPath : 'user-management/user/view';
                        return $redirect === false ? '' : $this->redirect([$path, 'id' => $user->id]);
                    }
                }
            }
        }
        return $this->render('create', compact('model'));
    }

    public function actionUpdate($id)
    {
        $this->layout = '//main.php';
        $user = User::findOne(['id' => $id]);
        if ($user === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $profile = UserProfile::findOne(['user_id' => $id]);
        if ($profile === null) {
            if (Yii::$app->getModule('user-profile')->dataEncode) {
                $profile = new UserProfile(['scenario' => 'encodedUser']);
            } else {
                $profile = new UserProfile(['scenario' => 'defaultUser']);
            }
        }
        if (Yii::$app->getModule('user-profile')->dataEncode) {
            $model = new UserUpdateForm(['scenario' => 'encodedUser']);
        } else {
            $model = new UserUpdateForm(['scenario' => 'defaultUser']);
        }
        $model->isNewRecord = false;
        $model->id = $id;
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $user->status = $model->status;
                $user->username = $model->username;
                if (User::hasPermission('bindUserToIp')) {
                    $user->bind_to_ip = $model->bind_to_ip;
                }
                if (User::hasPermission('editUserEmail')) {
                    $user->email = $model->email;
                    $user->email_confirmed = $model->email_confirmed;
                }
                $user->attempts = $model->attempts;
                $user->blocked_at = $model->blocked_at;
                $user->blocked_for = $model->blocked_for;
                $result = $user->save();

                if ($result) {
                    $profile->user_id = $id;
                    if ($model->avatar_delete) {
                        $profile->avatar = '';
                    } else {
                        $profile->avatar = $model->avatarUpload($id, $profile->avatar);
                    }
                    $profile->firstname = $model->firstname;
                    $profile->lastname = $model->lastname;
                    $profile->patronymic = $model->patronymic;
                    $profile->dob = $model->dob;
                    $profile->phone = $model->phone;
                    $profile->sex = $model->sex;
                    $profile->comment = $model->comment;
                    $profile->job = $model->job;
                    $profile->social = $profile->getSocialStringFromArray($model);
                    $result = $profile->save();

                    if ($result) {
                        $path = Yii::$app->getModule('user-management')->userViewPath ? Yii::$app->getModule('user-management')->userViewPath : 'user-management/user/view';
                        return $redirect === false ? '' : $this->redirect([$path, 'id' => $user->id]);
                    }
                }
            }
        } else {

            $model->status = $user->status;
            $model->username = $user->username;
            $model->bind_to_ip = $user->bind_to_ip;
            $model->email = $user->email;
            $model->email_confirmed = $user->email_confirmed;
            $model->attempts = $user->attempts;
            $model->blocked_at = $user->blocked_at;
            $model->blocked_for = $user->blocked_for;

            $model->firstname = $profile->getAttributeValue('firstname');
            $model->lastname = $profile->getAttributeValue('lastname');
            $model->patronymic = $profile->getAttributeValue('patronymic');
            $model->dob = $profile->getAttributeValue('dob');
            $model->phone = $profile->getAttributeValue('phone');
            $model->sex = $profile->getAttributeValue('sex');
            $model->comment = $profile->getAttributeValue('comment');
            $model->job = $profile->getAttributeValue('job');
            $model->social = $profile->getAttributeValue('social');

            $social = ['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'];
            foreach ($social as $soc) {
                $social_res = $profile->getSocialFromStringByName($model->social, $soc);
                if ($social_res) {
                    $model->$soc = $social_res;
                }
            }

        }

        return $this->render('update', compact('model'));
    }

    public function actionView($id)
    {
        $this->layout = '//main.php';
        $user = User::findOne(['id' => $id]);
        if ($user === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        if (Yii::$app->getModule('user-profile')->dataEncode) {
            $model = new UserUpdateForm(['scenario' => 'encodedUser']);
        } else {
            $model = new UserUpdateForm(['scenario' => 'defaultUser']);
        }
        $model->id = $id;
        $model->status = $user->status;
        $model->username = $user->username;
        $model->bind_to_ip = $user->bind_to_ip;
        $model->email = $user->email;
        $model->email_confirmed = $user->email_confirmed;
        $model->attempts = $user->attempts;
        $model->blocked_at = $user->blocked_at;
        $model->blocked_for = $user->blocked_for;
        $model->registration_ip = $user->registration_ip;
        $model->created_at = $user->created_at;
        $model->updated_at = $user->updated_at;
        $model->created_by = $user->created_by;
        $model->updated_by = $user->updated_by;
        $profile = UserProfile::findOne(['user_id' => $id]);
        if ($profile !== null) {

            $model->avatar = $profile->avatar;
            $model->firstname = $profile->getAttributeValue('firstname');
            $model->lastname = $profile->getAttributeValue('lastname');
            $model->patronymic = $profile->getAttributeValue('patronymic');
            $model->dob = $profile->getAttributeValue('dob');
            $model->phone = $profile->getAttributeValue('phone');
            $model->sex = $profile->getAttributeValue('sex');
            $model->comment = $profile->getAttributeValue('comment');
            $model->job = $profile->getAttributeValue('job');
            $model->social = $profile->getAttributeValue('social');

            $social = ['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'];
            foreach ($social as $soc) {
                $social_res = $profile->getSocialFromStringByName($model->social, $soc);
                if ($social_res) {
                    $model->$soc = $social_res;
                }
            }

        }
        return $this->render('view', compact('model'));
    }
}