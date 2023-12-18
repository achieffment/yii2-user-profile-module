<?php

namespace chieff\modules\UserProfile\models;

use chieff\helpers\SecurityHelper;
use chieff\modules\UserProfile\UserProfileModule;
use Yii;
use webvimark\modules\UserManagement\models\User;
use yii\helpers\Html;
use yii\helpers\Url;

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
 * @property string $comment
 * @property string $job
 * @property string $social
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
            [['user_id', 'sex', 'job'], 'integer'],
            ['dob', 'validateDob'],
            ['phone', 'validatePhone'],
            [['phone'], 'string', 'max' => 20],
            [['firstname', 'lastname', 'patronymic'], 'validateName'],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 100],
            [['avatar'], 'string', 'max' => 100],
            [['comment'], 'string', 'max' => 500],
            ['comment', 'purgeXSS'],
            [['social'], 'string', 'max' => 1000],
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => UserProfileModule::t('front', 'User ID'),
            'avatar' => UserProfileModule::t('front', 'Avatar'),
            'firstname' => UserProfileModule::t('front', 'Firstname'),
            'lastname' => UserProfileModule::t('front', 'Lastname'),
            'patronymic' => UserProfileModule::t('front', 'Patronymic'),
            'dob' => UserProfileModule::t('front', 'Dob'),
            'phone' => UserProfileModule::t('front', 'Phone'),
            'sex' => UserProfileModule::t('front', 'Sex'),
            'comment' => UserProfileModule::t('front', 'Comment'),
            'job' => UserProfileModule::t('front', 'Job'),
            'social' => UserProfileModule::t('front', 'Social'),
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

    public function beforeSave($insert)
    {
        if (
            $this->dob &&
            !is_numeric($this->dob)
        ) {
            $date = strtotime($this->dob);
            if ($date !== false) {
                $this->dob = $date;
            } else {
                $this->dob = null;
            }
        }
        if (
            !$insert &&
            isset($this->oldAttributes['avatar']) &&
            $this->oldAttributes['avatar'] &&
            ($this->oldAttributes['avatar'] != $this->avatar)
        ) {
            $path = Yii::$app->getModule('user-profile')->avatarPath;
            $path = Yii::getAlias($path);
            $path .= $this->oldAttributes['avatar'];
            unlink($path);
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function beforeDelete()
    {
        if ($this->avatar) {
            $path = Yii::$app->getModule('user-profile')->avatarPath;
            $path = Yii::getAlias($path);
            $path .= $this->avatar;
            unlink($path);
        }
        return parent::beforeDelete();
    }

    public function getSocialStringFromArray($model)
    {
        $social = ['vk', 'ok', 'telegram', 'whatsapp', 'viber', 'youtube', 'twitter', 'facebook'];
        $social_links = [];
        foreach ($social as $soc) {
            if ($model->$soc) {
                $social_links[] = $soc . '%^&*(:' . $model->$soc;
            }
        }
        if ($social_links) {
            return implode(',', $social_links);
        }
        return '';
    }

    public function getSocialFromStringByName($string, $name)
    {
        $social_cur = explode(',', $string);
        if ($social_cur) {
            $social_res = preg_grep('/^' . $name . '\%\^\&\*\(:/U', $social_cur);
            if ($social_res) {
                $social_res = implode('', $social_res);
                return str_replace($name . '%^&*(:', '', $social_res);
            }
        }
    }

}