<?php

namespace chieff\modules\UserProfile;

use Yii;

class UserProfileModule extends \yii\base\Module
{
    /**
     * If set true, avatars will be encoded
     *
     * @var bool
     */
    public $avatarEncode = false;

    public $avatarPath = '@webroot/uploads/avatars/';

    /**
     * Passphrase for encoding
     *
     * @var string
     */
    public $passphrase = '';

    public $arraySex = [
        -1 => 'Не выбрано',
        0 => 'Мужской',
        1 => 'Женский'
    ];

    public $phoneRegexp = '^\+[1-9]{1} \(\d{3}\) \d{3}-\d{2}-\d{2}$';

    public $phonePlaceholder = '+7 (999) 999-99-99';

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