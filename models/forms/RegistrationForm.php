<?php

namespace chieff\modules\UserProfile\models\forms;

use chieff\modules\UserProfile\models\UserProfile;
use chieff\helpers\SecurityHelper;
use chieff\modules\UserProfile\UserProfileModule;
use webvimark\modules\UserManagement\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\ArrayHelper;
use Yii;
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

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            ['avatar_file', 'file'],
            [['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required'],
            [['user_id', 'sex'], 'integer'],
            ['dob', 'validateDob'],
            ['phone', 'validatePhone'],
            [['phone'], 'string', 'max' => 20],
            [['firstname', 'lastname', 'patronymic'], 'validateName'],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 100],
            [['avatar'], 'string', 'max' => 100],
        ]);
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
            'id' => 'ID',
            'user_id' => UserProfileModule::t('front', 'User ID'),
            'avatar' => UserProfileModule::t('front', 'Avatar'),
            'avatar_file' => UserProfileModule::t('front', 'Avatar'),
            'firstname' => UserProfileModule::t('front', 'Firstname'),
            'lastname' => UserProfileModule::t('front', 'Lastname'),
            'patronymic' => UserProfileModule::t('front', 'Patronymic'),
            'dob' => UserProfileModule::t('front', 'Dob'),
            'phone' => UserProfileModule::t('front', 'Phone'),
            'sex' => UserProfileModule::t('front', 'Sex'),
        ]);
    }

    public function avatarUpload($user_id)
    {
        $this->avatar_file = UploadedFile::getInstance($this, 'avatar_file');
        if (
            $this->avatar_file &&
            $this->validate('avatar_file')
        ) {
            $file_alias = '@webroot';
            $file_folder = '/uploads/avatars/';

            $file_folder_path = Yii::getAlias($file_alias . $file_folder);
            if (!is_dir($file_folder_path)) {
                if (!mkdir($file_folder_path, 0777, true)) {
                    return '';
                }
            }

            $file_name = 'avatar_' . $user_id . '.' . $this->avatar_file->getExtension();

            $file_full_path = $file_folder_path . $file_name;
            $file_alias_full_path = $file_alias . $file_folder . $file_name;

            if ($this->avatar_file->saveAs($file_alias_full_path)) {
                if (Yii::$app->getModule('user-profile')->encodeAvatar) {
                    $data = file_get_contents($file_full_path);
                    $data = SecurityHelper::encode($data, 'aes-256-ctr', Yii::$app->getModule('user-profile')->passphrase);
                    if ($data) {
                        if (file_put_contents($file_full_path, $data) === false) {
                            unset($file_full_path);
                            return '';
                        }
                    } else {
                        unset($file_full_path);
                        return '';
                    }
                }
                return $file_folder . $file_name;
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
        $model->save(false);
    }
}