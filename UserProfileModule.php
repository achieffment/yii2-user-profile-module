<?php

namespace chieff\modules\UserProfile;

class UserProfileModule extends \yii\base\Module
{
    /**
     * If set true, avatars will be encoded
     *
     * @var bool
     */
    public $encodeAvatar = false;

    /**
     * Passphrase for encoding
     *
     * @var string
     */
    public $passphrase = '';

    public $controllerNamespace = 'chieff\modules\UserProfile\controllers';

    /**
     * @p
     */
    public function init()
    {
        parent::init();
    }
}