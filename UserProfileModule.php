<?php

namespace chieff\modules\UserProfile;

use Yii;

class UserProfileModule extends \yii\base\Module
{
    /**
     * Path to save avatars
     *
     * @var string
     */
    public $avatarPath = '@webroot/uploads/avatars/';

    /**
     * If set true, avatars will be encoded
     *
     * @var bool
     */
    public $avatarEncode = false;

    /**
     * Secure passphrase for encoding
     *
     * @var string
     */
    public $passphrase = '';

    /**
     * Array of sex
     *
     * @var array
     */
    public $arraySex = [
        -1 => 'Не выбрано',
        0 => 'Мужской',
        1 => 'Женский'
    ];

    /**
     * Phone regexp
     *
     * @var string
     */
    public $phoneRegexp = '^\+[1-9]{1} \(\d{3}\) \d{3}-\d{2}-\d{2}$';

    /**
     * Phone placeholder
     *
     * @var string
     */
    public $phonePlaceholder = '+7 (999) 999-99-99';

    /**
     * Array of job titles
     *
     * @var array
     */
    public $arrayJob = [
        0 => 'Не выбрано',
        1 => 'Менеджер по продажам',
        2 => 'Аккаунт-менеджер',
        3 => 'Маркетолог',
        4 => 'Старший маркетолог',
        5 => 'SEO-специалист',
        6 => 'Старший SEO-специалист',
        7 => 'Программист',
        8 => 'Старший программист',
    ];

    /**
     * If set true, data will be encoded
     *
     * @var array
     */
    public $dataEncode = false;

    public $controllerNamespace = 'chieff\modules\UserProfile\controllers';

    /**
     * @p
     */
    public function init()
    {
        parent::init();
    }

    /**
     * I18N helper
     *
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null|string $language
     *
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if (!isset(Yii::$app->i18n->translations['modules/user-profile/*'])) {
            Yii::$app->i18n->translations['modules/user-profile/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'ru',
                'basePath' => '@vendor/chieff/yii2-user-profile-module/messages',
                'fileMap' => [
                    'modules/user-profile/back' => 'back.php',
                    'modules/user-profile/front' => 'front.php',
                ],
            ];
        }
        return Yii::t('modules/user-profile/' . $category, $message, $params, $language);
    }
}