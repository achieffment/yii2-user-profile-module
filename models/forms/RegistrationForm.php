<?php

namespace chieff\modules\UserProfile\models\forms;

use chieff\modules\UserProfile\models\UserProfile;
use chieff\helpers\SecurityHelper;
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
            [['user_id', 'dob', 'sex'], 'integer'],
            [['avatar', 'firstname', 'lastname', 'patronymic'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
        ]);
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => 'ID',
            'user_id' => 'User ID',
            'avatar' => 'Avatar',
            'avatar_file' => 'Avatar',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'patronymic' => 'Patronymic',
            'dob' => 'Dob',
            'phone' => 'Phone',
            'sex' => 'Sex',
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