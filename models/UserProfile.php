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
 * @property int $job
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
            // Encoded user
            [['user_id', 'firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required', 'on' => 'encodedUser'],
            ['user_id', 'integer', 'on' => 'encodedUser'],
            ['avatar', 'string', 'max' => 100, 'on' => 'encodedUser'],
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

            // Default user
            [['firstname', 'lastname', 'patronymic', 'comment'], 'trim', 'on' => 'defaultUser'],
            [['firstname', 'lastname', 'patronymic', 'comment'], 'purgeXSS', 'on' => 'defaultUser'],
            [['user_id', 'firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex'], 'required', 'on' => 'defaultUser'],
            [['user_id', 'sex', 'job'], 'integer', 'on' => 'defaultUser'],
            ['dob', 'validateDob', 'on' => 'defaultUser'],
            ['phone', 'validatePhone', 'on' => 'defaultUser'],
            [['phone'], 'string', 'max' => 20, 'on' => 'defaultUser'],
            [['firstname', 'lastname', 'patronymic'], 'validateName', 'on' => 'defaultUser'],
            [['firstname', 'lastname', 'patronymic'], 'string', 'min' => 2, 'max' => 100, 'on' => 'defaultUser'],
            [['avatar'], 'string', 'max' => 100, 'on' => 'defaultUser'],
            [['comment'], 'string', 'max' => 500, 'on' => 'defaultUser'],
            [['social'], 'string', 'max' => 1000, 'on' => 'defaultUser'],
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
                }
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

        if (Yii::$app->getModule('user-profile')->dataEncode) {
            foreach (['firstname', 'lastname', 'patronymic', 'dob', 'phone', 'sex', 'comment', 'job', 'social'] as $attribute) {
                $this->setAttributeValue($attribute);
            }
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
                $social_links[] = $soc . ':' . $model->$soc;
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
            $social_res = preg_grep('/^' . $name . ':/U', $social_cur);
            if ($social_res) {
                $social_res = implode('', $social_res);
                return str_replace($name . ':', '', $social_res);
            }
        }
    }

    public function getAttributeValue($attribute) {
        if (
            Yii::$app->getModule('user-profile')->dataEncode &&
            ($this->$attribute != '' && $this->$attribute != null)
        ) {
            return SecurityHelper::decode($this->$attribute, 'aes-256-ctr', Yii::$app->getModule('user-profile')->passphrase);
        }
        return $this->$attribute;
    }

    public function setAttributeValue($attribute) {
        if (
            Yii::$app->getModule('user-profile')->dataEncode &&
            ($this->$attribute != '' && $this->$attribute != null)
        ) {
            $this->$attribute = SecurityHelper::encode($this->$attribute, 'aes-256-ctr', Yii::$app->getModule('user-profile')->passphrase);
        }
    }
}