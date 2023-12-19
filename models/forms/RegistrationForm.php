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
    public $avatar;
    public $avatar_file;

    public $firstname;
    public $lastname;
    public $patronymic;
    public $dob;
    public $phone;
    public $sex;
    public $comment;
    public $job;
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

            // Encoded user
            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required', 'on' => 'encodedUser'],
            ['avatar', 'string','max' => 100, 'on' => 'encodedUser'],
            ['avatar_file', 'file', 'on' => 'encodedUser'],
            [['firstname', 'lastname', 'patronymic', 'comment'], 'trim', 'on' => 'encodedUser'],
            [['firstname', 'lastname', 'patronymic', 'comment'], 'purgeXSS', 'on' => 'encodedUser'],
            [['firstname', 'lastname', 'patronymic'], 'validateName', 'on' => 'encodedUser'],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 300, 'on' => 'encodedUser'],
            ['comment', 'string', 'max' => 1500, 'on' => 'encodedUser'],
            ['dob', 'validateDob', 'on' => 'encodedUser'],
            ['dob', 'string', 'max' => 30, 'on' => 'encodedUser'],
            ['phone', 'validatePhone', 'on' => 'encodedUser'],
            ['phone', 'string', 'max' => 60, 'on' => 'encodedUser'],
            [['job', 'sex'], 'string', 'max' => 10, 'on' => 'encodedUser'],
            [['job', 'sex'], 'validateJobSex', 'on' => 'encodedUser'],
            ['social', 'string', 'max' => 3000, 'on' => 'encodedUser'],
            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex', 'comment', 'job', 'social'], 'validateEncode', 'on' => 'encodedUser'],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'trim', 'on' => 'encodedUser'],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'purgeXSS', 'on' => 'encodedUser'],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'string', 'max' => 300, 'on' => 'encodedUser'],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'validateEncode', 'on' => 'encodedUser'],

            // Default user
            [['firstname', 'lastname', 'patronymic', 'comment', 'vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'trim', 'on' => 'defaultUser'],
            [['firstname', 'lastname', 'patronymic', 'comment', 'vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'purgeXSS', 'on' => 'defaultUser'],
            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required', 'on' => 'defaultUser'],
            [['firstname', 'lastname', 'patronymic'], 'validateName', 'on' => 'defaultUser'],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 100, 'on' => 'defaultUser'],
            ['avatar', 'string', 'max' => 100, 'on' => 'defaultUser'],
            ['avatar_file', 'file', 'on' => 'defaultUser'],
            ['sex', 'integer', 'on' => 'defaultUser'],
            ['dob', 'validateDob', 'on' => 'defaultUser'],
            ['phone', 'validatePhone', 'on' => 'defaultUser'],
            ['phone', 'string', 'max' => 20, 'on' => 'defaultUser'],
            ['comment', 'string', 'max' => 500, 'on' => 'defaultUser'],
            ['social', 'string', 'max' => 1000, 'on' => 'defaultUser'],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'string', 'max' => 100, 'on' => 'defaultUser'],
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
            if (preg_match('/^[A-Za-zА-Яа-яЁё]{2,}$/u', $this->firstname) !== 1) {
                $this->addError('firstname', UserProfileModule::t('front', 'Incorrect firstname'));
            }
        }
        if ($this->lastname) {
            if (preg_match('/^[A-Za-zА-Яа-яЁё]{2,}$/u', $this->lastname) !== 1) {
                $this->addError('lastname', UserProfileModule::t('front', 'Incorrect lastname'));
            }
        }
        if ($this->patronymic) {
            if (preg_match('/^[A-Za-zА-Яа-яЁё]{2,}$/u', $this->patronymic) !== 1) {
                $this->addError('patronymic', UserProfileModule::t('front', 'Incorrect patronymic'));
            }
        }
    }

    public function validateJobSex($attribute)
    {
        if (
            ($this->$attribute != '' && $this->$attribute != null) &&
            !is_numeric($this->$attribute)
        ) {
            $this->addError($attribute, UserProfileModule::t('front', 'Smth went wrong'));
        }
    }

    public function validateEncode($attribute)
    {
        if (
            Yii::$app->getModule('user-profile')->dataEncode &&
            ($this->$attribute != '' && $this->$attribute != null)
        ) {
            $value = SecurityHelper::encode($this->$attribute, 'aes-256-ctr', Yii::$app->getModule('user-profile')->passphrase);
            if (!$value) {
                $this->addError($attribute, UserProfileModule::t('front', 'Smth went wrong'));
            } else {
                $length = mb_strlen($value);
                if (
                    ($attribute == 'firstname' || $attribute == 'lastname' || $attribute == 'patronymic') &&
                    ($length > 300)
                ) {
                    $this->addError($attribute, UserProfileModule::t('front', 'Max exception'));
                } else if (
                    ($attribute == 'comment') &&
                    ($length > 1500)
                ) {
                    $this->addError($attribute, UserProfileModule::t('front', 'Max exception'));
                } else if (
                    ($attribute == 'social') &&
                    ($length > 3000)
                ) {
                    $this->addError($attribute, UserProfileModule::t('front', 'Max exception'));
                } else if (
                    ($attribute == 'dob') &&
                    ($length > 30)
                ) {
                    $this->addError($attribute, UserProfileModule::t('front', 'Smth went wrong'));
                } else if (
                    ($attribute == 'phone') &&
                    ($length > 60)
                ) {
                    $this->addError($attribute, UserProfileModule::t('front', 'Smth went wrong'));
                } else if (
                    ($attribute == 'sex' || $attribute == 'job') &&
                    ($length > 10)
                ) {
                    $this->addError($attribute, UserProfileModule::t('front', 'Smth went wrong'));
                } else if (
                    in_array($attribute, ['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook']) &&
                    ($length > 300)
                ) {
                    $this->addError($attribute, UserProfileModule::t('front', 'Max exception'));
                }
            }
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
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
        if (Yii::$app->getModule('user-profile')->dataEncode) {
            $model = new UserProfile(['scenario' => 'encodedUser']);
        } else {
            $model = new UserProfile(['scenario' => 'defaultUser']);
        }
        $model->user_id = $user->id;
        $model->avatar = $this->avatarUpload($user->id);
        $model->firstname = $this->firstname;
        $model->lastname = $this->lastname;
        $model->patronymic = $this->patronymic;
        $model->dob = $this->dob;
        $model->phone = $this->phone;
        $model->sex = $this->sex;
        $model->comment = $this->comment;
        $model->social = $model->getSocialStringFromArray($this);
        $model->save(false);
    }
}