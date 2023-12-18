<?php

namespace chieff\modules\UserProfile\models\forms;

use chieff\helpers\SecurityHelper;
use chieff\modules\UserProfile\UserProfileModule;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\base\Model;
use Yii;
use yii\web\UploadedFile;
use yii\helpers\Url;

class UserUpdateForm extends Model
{

    public $id;
    public $username;
    public $status;
    public $attempts;
    public $blocked_at;
    public $bind_to_ip;
    public $email;
    public $email_confirmed;

    public $avatar;
    public $avatar_file;
    public $firstname;
    public $lastname;
    public $patronymic;
    public $dob;
    public $phone;
    public $sex;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'required'],
            ['username', 'validateUsername'],
            ['username', 'trim'],
            [['id', 'status', 'email_confirmed', 'attempts'], 'integer'],
            ['blocked_at', 'validateBlockedAt'],
            ['attempts', 'default', 'value' => 0],
            ['email', 'email'],
            ['email', 'validateEmailConfirmedUnique'],
            ['bind_to_ip', 'validateBindToIp'],
            ['bind_to_ip', 'trim'],
            ['bind_to_ip', 'string', 'max' => 255],
            ['avatar_file', 'file'],
            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required'],
            [['sex'], 'integer'],
            ['dob', 'validateDob'],
            ['phone', 'validatePhone'],
            [['phone'], 'string', 'max' => 20],
            [['firstname', 'lastname', 'patronymic'], 'validateName'],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 100],
            [['avatar'], 'string', 'max' => 100],
        ];
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
        if ($this->blocked_at) {
            $date = strtotime($this->blocked_at);
            if ($date === false) {
                $this->addError('blocked_at', UserManagementModule::t('front', 'Incorrect blocked at date'));
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
        return [
            'id' => 'ID',
            'username' => UserManagementModule::t('back', 'Login'),
            'bind_to_ip' => UserManagementModule::t('back', 'Bind to IP'),
            'status' => UserManagementModule::t('back', 'Status'),
            'email_confirmed' => UserManagementModule::t('back', 'E-mail confirmed'),
            'attempts' => UserManagementModule::t('back', 'Attempts'),
            'blocked_at' => UserManagementModule::t('back', 'Blocked at'),
            'email' => UserManagementModule::t('back', 'E-mail'),
            'avatar' => UserProfileModule::t('front', 'Avatar'),
            'avatar_file' => UserProfileModule::t('front', 'Avatar'),
            'firstname' => UserProfileModule::t('front', 'Firstname'),
            'lastname' => UserProfileModule::t('front', 'Lastname'),
            'patronymic' => UserProfileModule::t('front', 'Patronymic'),
            'dob' => UserProfileModule::t('front', 'Dob'),
            'phone' => UserProfileModule::t('front', 'Phone'),
            'sex' => UserProfileModule::t('front', 'Sex'),
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
}