<?php

namespace chieff\modules\UserProfile\models\forms;

use chieff\modules\UserProfile\models\UserProfile;
use chieff\helpers\SecurityHelper;
use chieff\modules\UserProfile\UserProfileModule;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\Html;
use yii\web\UploadedFile;

class RegistrationForm extends \webvimark\modules\UserManagement\models\forms\RegistrationForm
{
    public $user_id;
    public $avatar;
    public $avatar_file;
    public $firstname;
    public $lastname;
    public $patronymic;
    public $dob;
    public $phone;
    public $sex;
    public $comment;
    public $social;

    public $vk;
    public $ok;

    public $telegram;
    public $whatsapp;
    public $viber;

    public $youtube;
    public $twitter;
    public $facebook;

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['avatar', 'string', 'max' => 100],
            ['avatar_file', 'file'],

            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required'],

            [['user_id', 'sex'], 'integer'],

            ['dob', 'validateDob'],
            ['phone', 'validatePhone'],

            ['phone', 'string', 'max' => 20],

            [['firstname', 'lastname', 'patronymic'], 'validateName'],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 100],

            ['comment', 'string', 'max' => 500],
            ['social', 'string', 'max' => 1000],

            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'string', 'max' => 100],

            [['firstname', 'lastname', 'patronymic', 'comment', 'vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'trim'],
            [['firstname', 'lastname', 'patronymic', 'comment', 'vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'purgeXSS']
        ]);
    }

    /**
     * Remove possible XSS stuff
     *
     * @param $attribute
     */
    public function purgeXSS($attribute)
    {
        $this->$attribute = Html::encode($this->$attribute);
    }

    public function validateDob()
    {
        if ($this->dob) {
            $date = strtotime($this->dob);
            if ($date === false) {
                $this->addError('dob', UserProfileModule::t('front', 'Incorrect dob'));
            }
        }
    }

    public function validatePhone()
    {
        if ($this->phone) {
            if (preg_match('/' . Yii::$app->getModule('user-profile')->phoneRegexp . '/', $this->phone) !== 1) {
                $this->addError('phone', UserProfileModule::t('front', 'Incorrect phone'));
            }
        }
    }

    public function validateName()
    {
        if ($this->firstname) {
            if (preg_match('/[A-Za-zА-Яа-я]{2,}/', $this->firstname) !== 1) {
                $this->addError('firstname', UserProfileModule::t('front', 'Incorrect firstname'));
            }
        }
        if ($this->lastname) {
            if (preg_match('/[A-Za-zА-Яа-я]{2,}/', $this->lastname) !== 1) {
                $this->addError('lastname', UserProfileModule::t('front', 'Incorrect lastname'));
            }
        }
        if ($this->patronymic) {
            if (preg_match('/[A-Za-zА-Яа-я]{2,}/', $this->patronymic) !== 1) {
                $this->addError('patronymic', UserProfileModule::t('front', 'Incorrect patronymic'));
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'user_id' => UserProfileModule::t('front', 'User ID'),
            'avatar' => UserProfileModule::t('front', 'Avatar'),
            'avatar_file' => UserProfileModule::t('front', 'Avatar'),
            'firstname' => UserProfileModule::t('front', 'Firstname'),
            'lastname' => UserProfileModule::t('front', 'Lastname'),
            'patronymic' => UserProfileModule::t('front', 'Patronymic'),
            'dob' => UserProfileModule::t('front', 'Dob'),
            'phone' => UserProfileModule::t('front', 'Phone'),
            'sex' => UserProfileModule::t('front', 'Sex'),
            'comment' => UserProfileModule::t('front', 'Comment'),
            'social' => UserProfileModule::t('front', 'Social'),
            'vk' => UserProfileModule::t('front', 'Vk'),
            'ok' => UserProfileModule::t('front', 'Ok'),
            'telegram' => UserProfileModule::t('front', 'Telegram'),
            'whatsapp' => UserProfileModule::t('front', 'Whatsapp'),
            'viber' => UserProfileModule::t('front', 'Viber'),
            'youtube' => UserProfileModule::t('front', 'Youtube'),
            'twitter' => UserProfileModule::t('front', 'Twitter'),
            'facebook' => UserProfileModule::t('front', 'Facebook')
        ]);
    }

    public function avatarUpload($user_id)
    {
        $this->avatar_file = UploadedFile::getInstance($this, 'avatar_file');
        if (
            $this->avatar_file &&
            $this->validate('avatar_file')
        ) {
            $path = Yii::$app->getModule('user-profile')->avatarPath;
            $path = Yii::getAlias($path);
            if (!is_dir($path)) {
                if (!mkdir($path, 0777, true)) {
                    return '';
                }
            }
            $file_name = 'avatar_' . $user_id . '.' . $this->avatar_file->getExtension();
            $file_path = $path . $file_name;
            if ($this->avatar_file->saveAs($file_path)) {
                if (
                    Yii::$app->getModule('user-profile')->avatarEncode &&
                    (($data = file_get_contents($file_path)) !== false)
                ) {
                    $data = SecurityHelper::encode($data, 'aes-256-ctr', Yii::$app->getModule('user-profile')->passphrase);
                    if ($data) {
                        if (file_put_contents($file_path, $data) === false) {
                            unlink($file_path);
                            return '';
                        }
                    } else {
                        unlink($file_path);
                        return '';
                    }
                }
                return $file_name;
            }
            return '';
        }
        return '';
    }

    /**
     * Look in parent class for details
     *
     * @param User $user
     */
    protected function saveProfile($user)
    {
        $model = new UserProfile();
        $model->user_id = $user->id;
        $model->avatar = $this->avatarUpload($user->id);
        $model->firstname = $this->firstname;
        $model->lastname = $this->lastname;
        $model->patronymic = $this->patronymic;
        $model->dob = $this->dob;
        $model->phone = $this->phone;
        $model->sex = $this->sex;
        $model->comment = $this->comment;
        $model->social = $model->getSocialStringFromArray($model);
        $model->save(false);
    }
}