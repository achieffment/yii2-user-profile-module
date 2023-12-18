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

    public function actionUpdate($id)
    {
        $this->layout = '//main.php';

        $user = User::findOne(['id' => $id]);
        if ($user === null) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $profile = UserProfile::findOne(['user_id' => $id]);
        if ($profile === null) {
            $profile = new UserProfile();
        }

        $model = new UserUpdateForm();
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
                $user->save();

                $profile->user_id = $id;
                $profile->avatar = $model->avatarUpload($id);
                $profile->firstname = $model->firstname;
                $profile->lastname = $model->lastname;
                $profile->patronymic = $model->patronymic;
                $profile->dob = $model->dob;
                $profile->phone = $model->phone;
                $profile->sex = $model->sex;
                $profile->comment = $model->comment;
                $profile->job = $model->job;

                $profile->social = $profile->getSocialStringFromArray($model);

                $profile->save();

                return $redirect === false ? '' : $this->redirect(['/user-management/user/view', 'id' => $user->id]);
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

            $model->avatar = $profile->avatar;
            $model->firstname = $profile->firstname;
            $model->lastname = $profile->lastname;
            $model->patronymic = $profile->patronymic;
            $model->dob = $profile->dob;
            $model->phone = $profile->phone;
            $model->sex = $profile->sex;
            $model->comment = $profile->comment;
            $model->job = $profile->job;

            $social = ['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'];
            foreach ($social as $soc) {
                $social_res = $profile->getSocialFromStringByName($profile->social, $soc);
                if ($social_res) {
                    $model->$soc = $social_res;
                }
            }

        }

        return $this->render('update', compact('model'));
    }
}