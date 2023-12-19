<?php

namespace chieff\modules\UserProfile\models\forms;

use chieff\helpers\SecurityHelper;
use chieff\modules\UserProfile\UserProfileModule;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\base\Model;
use Yii;
use yii\helpers\Html;
use yii\web\UploadedFile;
use yii\helpers\Url;

class UserUpdateForm extends Model
{
    public $isNewRecord;

    public $id;
    public $username;
    public $status;
    public $attempts;
    public $blocked_at;
    public $blocked_for;
    public $bind_to_ip;
    public $email;
    public $email_confirmed;

    public $password;
    public $repeat_password;

    public $registration_ip;
    public $created_at;
    public $updated_at;
    public $created_by;
    public $updated_by;

    public $avatar;
    public $avatar_file;
    public $avatar_delete;

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
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['id', 'status', 'email_confirmed', 'attempts', 'job'], 'integer'],
            ['username', 'required'],
            ['username', 'purgeXSS'],
            ['username', 'validateUsername'],
            ['username', 'trim'],
            ['blocked_at', 'validateBlockedAt'],
            ['blocked_for', 'validateBlockedFor'],
            ['email', 'email'],
            ['email', 'validateEmailConfirmedUnique'],
            ['bind_to_ip', 'validateBindToIp'],
            ['bind_to_ip', 'trim'],
            ['bind_to_ip', 'string', 'max' => 255],
            ['attempts', 'default', 'value' => 0],
            ['password', 'required', 'on' => ['newUser', 'newUserEncoded', 'newUserDefault']],
            ['password', 'string', 'max' => 255, 'on' => ['newUser', 'newUserEncoded', 'newUserDefault']],
            ['password', 'trim', 'on' => ['newUser', 'newUserEncoded', 'newUserDefault']],
            ['password', 'match', 'pattern' => Yii::$app->getModule('user-management')->passwordRegexp, 'on' => ['newUser', 'newUserEncoded', 'newUserDefault']],
            ['repeat_password', 'required', 'on' => ['newUser', 'newUserEncoded', 'newUserDefault']],
            ['repeat_password', 'compare', 'compareAttribute' => 'password', 'on' => ['newUser', 'newUserEncoded', 'newUserDefault']],

            // Encoded user
            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required', 'on' => ['encodedUser', 'newUserEncoded']],
            ['avatar', 'string','max' => 100, 'on' => ['encodedUser', 'newUserEncoded']],
            ['avatar_file', 'file', 'on' => ['encodedUser', 'newUserEncoded']],
            [['firstname', 'lastname', 'patronymic', 'comment'], 'trim', 'on' => ['encodedUser', 'newUserEncoded']],
            [['firstname', 'lastname', 'patronymic', 'comment'], 'purgeXSS', 'on' => ['encodedUser', 'newUserEncoded']],
            [['firstname', 'lastname', 'patronymic'], 'validateName', 'on' => ['encodedUser', 'newUserEncoded']],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 300, 'on' => ['encodedUser', 'newUserEncoded']],
            ['comment', 'string', 'max' => 1500, 'on' => ['encodedUser', 'newUserEncoded']],
            ['dob', 'validateDob', 'on' => ['encodedUser', 'newUserEncoded']],
            ['dob', 'string', 'max' => 30, 'on' => ['encodedUser', 'newUserEncoded']],
            ['phone', 'validatePhone', 'on' => ['encodedUser', 'newUserEncoded']],
            ['phone', 'string', 'max' => 60, 'on' => ['encodedUser', 'newUserEncoded']],
            [['job', 'sex'], 'string', 'max' => 10, 'on' => ['encodedUser', 'newUserEncoded']],
            [['job', 'sex'], 'validateJobSex', 'on' => ['encodedUser', 'newUserEncoded']],
            ['social', 'string', 'max' => 3000, 'on' => ['encodedUser', 'newUserEncoded']],
            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex', 'comment', 'job', 'social'], 'validateEncode', 'on' => ['encodedUser', 'newUserEncoded']],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'trim', 'on' => ['encodedUser', 'newUserEncoded']],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'purgeXSS', 'on' => ['encodedUser', 'newUserEncoded']],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'string', 'max' => 300, 'on' => ['encodedUser', 'newUserEncoded']],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'validateEncode', 'on' => ['encodedUser', 'newUserEncoded']],

            // Default user
            [['firstname', 'lastname', 'patronymic', 'comment', 'vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'trim', 'on' => ['defaultUser', 'newUserDefault']],
            [['firstname', 'lastname', 'patronymic', 'comment', 'vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'purgeXSS', 'on' => ['defaultUser', 'newUserDefault']],
            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required', 'on' => ['defaultUser', 'newUserDefault']],
            [['firstname', 'lastname', 'patronymic'], 'validateName', 'on' => ['defaultUser', 'newUserDefault']],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 100, 'on' => ['defaultUser', 'newUserDefault']],
            ['avatar', 'string', 'max' => 100, 'on' => ['defaultUser', 'newUserDefault']],
            ['avatar_file', 'file', 'on' => ['defaultUser', 'newUserDefault']],
            ['avatar_delete', 'boolean', 'on' => ['defaultUser', 'newUserDefault']],
            ['sex', 'integer', 'on' => ['defaultUser', 'newUserDefault']],
            ['dob', 'validateDob', 'on' => ['defaultUser', 'newUserDefault']],
            ['phone', 'validatePhone', 'on' => ['defaultUser', 'newUserDefault']],
            ['phone', 'string', 'max' => 20, 'on' => ['defaultUser', 'newUserDefault']],
            ['comment', 'string', 'max' => 500, 'on' => ['defaultUser', 'newUserDefault']],
            ['social', 'string', 'max' => 1000, 'on' => ['defaultUser', 'newUserDefault']],
            [['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'], 'string', 'max' => 100, 'on' => ['defaultUser', 'newUserDefault']],
        ];
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

    public function validateUsername()
    {
        if ($this->username) {
            $user = User::find()->where(['<>', 'id', $this->id])->andWhere(['username' => $this->username])->one();
            if ($user !== null) {
                $this->addError('username', UserManagementModule::t('front', 'Login has been taken'));
            }
        }
    }

    public function validateBlockedAt()
    {
        if (
            $this->blocked_at &&
            !is_numeric($this->blocked_at)
        ) {
            $date = strtotime($this->blocked_at);
            if ($date === false) {
                $this->addError('blocked_at', UserManagementModule::t('front', 'Incorrect blocked at date'));
            }
        }
    }

    public function validateBlockedFor()
    {
        if (
            $this->blocked_for &&
            !is_numeric($this->blocked_for)
        ) {
            $date = strtotime($this->blocked_for);
            if ($date === false) {
                $this->addError('blocked_for', UserManagementModule::t('front', 'Incorrect blocked for date'));
            }
        }
    }

    /**
     * Check that there is no such confirmed E-mail in the system
     */
    public function validateEmailConfirmedUnique()
    {
        if ($this->email) {
            $exists = User::findOne([
                'email' => $this->email,
                'email_confirmed' => 1,
            ]);
            if ($exists and $exists->id != $this->id) {
                $this->addError('email', UserManagementModule::t('front', 'This E-mail already exists'));
            }
        }
    }

    /**
     * Validate bind_to_ip attr to be in correct format
     */
    public function validateBindToIp()
    {
        if ($this->bind_to_ip) {
            $ips = explode(',', $this->bind_to_ip);
            foreach ($ips as $ip) {
                if (
                    !filter_var(trim($ip), FILTER_VALIDATE_IP) &&
                    (preg_match('/^([A-Za-z0-9]{1,6}(:|::){0,1})+$/', trim($ip)) !== 1)
                ) {
                    $this->addError('bind_to_ip', UserManagementModule::t('back', "Wrong format. Enter valid IPs separated by comma"));
                }
            }
        }
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
        return [
            'id' => 'ID',
            'username' => UserManagementModule::t('back', 'Login'),
            'bind_to_ip' => UserManagementModule::t('back', 'Bind to IP'),
            'status' => UserManagementModule::t('back', 'Status'),
            'email_confirmed' => UserManagementModule::t('back', 'E-mail confirmed'),
            'attempts' => UserManagementModule::t('back', 'Attempts'),
            'blocked_at' => UserManagementModule::t('back', 'Blocked at'),
            'blocked_for' => UserManagementModule::t('back', 'Blocked for'),
            'email' => UserManagementModule::t('back', 'E-mail'),
            'avatar' => UserProfileModule::t('front', 'Avatar'),
            'avatar_file' => UserProfileModule::t('front', 'Avatar'),
            'firstname' => UserProfileModule::t('front', 'Firstname'),
            'lastname' => UserProfileModule::t('front', 'Lastname'),
            'patronymic' => UserProfileModule::t('front', 'Patronymic'),
            'dob' => UserProfileModule::t('front', 'Dob'),
            'phone' => UserProfileModule::t('front', 'Phone'),
            'sex' => UserProfileModule::t('front', 'Sex'),
            'comment' => UserProfileModule::t('front', 'Comment'),
            'job' => UserProfileModule::t('front', 'Job'),
            'social' => UserProfileModule::t('front', 'Social'),
            'vk' => UserProfileModule::t('front', 'Vk'),
            'ok' => UserProfileModule::t('front', 'Ok'),
            'telegram' => UserProfileModule::t('front', 'Telegram'),
            'whatsapp' => UserProfileModule::t('front', 'Whatsapp'),
            'viber' => UserProfileModule::t('front', 'Viber'),
            'youtube' => UserProfileModule::t('front', 'Youtube'),
            'twitter' => UserProfileModule::t('front', 'Twitter'),
            'facebook' => UserProfileModule::t('front', 'Facebook'),
            'registration_ip' => UserManagementModule::t('back', 'Registration IP'),
            'created_at' => UserManagementModule::t('back', 'Created'),
            'updated_at' => UserManagementModule::t('back', 'Updated'),
            'created_by' => UserManagementModule::t('back', 'Created by'),
            'updated_by' => UserManagementModule::t('back', 'Updated by'),
            'avatar_delete' => UserProfileModule::t('front', 'Avatar Delete'),
            'password' => UserManagementModule::t('back', 'Password'),
            'repeat_password' => UserManagementModule::t('back', 'Repeat password'),
        ];
    }

    public function getAvatar()
    {
        $path = Yii::$app->getModule('user-profile')->avatarPath;
        if (!Yii::$app->getModule('user-profile')->avatarEncode) {
            $path = str_replace('\\\\', '/', $path);
            $path = str_replace('\\', '/', $path);
            $path = str_replace('//', '/', $path);
            if (strpos($path, '@') !== false) {
                if (preg_match('/@.*\//U', $path, $matches) === 1) {
                    $base = Url::base();
                    if (substr($base, -1, 1) !== '/') {
                        $base .= '/';
                    }
                    $path = str_replace(implode('', $matches), $base, $path);
                    $path = str_replace('//', '/', $path);
                    if (substr($path, 0, 1) !== '/') {
                        $path = '/' . $path;
                    }
                }
            }
        } else {
            $path = Yii::getAlias($path);
        }
        $path .= $this->avatar;
        if (Yii::$app->getModule('user-profile')->avatarEncode) {
            return SecurityHelper::getImageContent($path, true, 'aes-256-ctr', Yii::$app->getModule('user-profile')->passphrase);
        }
        return $path;
    }

    public function avatarUpload($user_id, $avatar)
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
        return $avatar;
    }
}