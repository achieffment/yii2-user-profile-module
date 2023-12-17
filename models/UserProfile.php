<?php

namespace chieff\modules\UserProfile\models;

use chieff\helpers\SecurityHelper;
use Yii;
use webvimark\modules\UserManagement\models\User;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string $avatar
 * @property string $firstname
 * @property string $lastname
 * @property string $patronymic
 * @property int $dob
 * @property string $phone
 * @property int $sex
 */
class UserProfile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required'],
            [['user_id', 'dob', 'sex'], 'integer'],
            [['avatar', 'firstname', 'lastname', 'patronymic'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'avatar' => 'Avatar',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'patronymic' => 'Patronymic',
            'dob' => 'Dob',
            'phone' => 'Phone',
            'sex' => 'Sex',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getAvatar()
    {
        if (Yii::$app->getModule('user-profile')->encodeAvatar) {
            $file_alias = Yii::getAlias('@webroot');
            $file_full_path = $file_alias . $this->avatar;
            return SecurityHelper::getImageContent($file_full_path, true, 'aes-256-ctr', Yii::$app->getModule('user-profile')->passphrase);
        }
        return $this->avatar;
    }

    public function beforeDelete()
    {
        if ($this->avatar) {
            $file = Yii::getAlias('@webroot' . '/uploads/avatars/' . $this->avatar);
            if (
                $file &&
                file_exists($file)
            ) {
                unset($file);
            }
        }
        return parent::beforeDelete();
    }

}