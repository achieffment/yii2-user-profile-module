<?php

namespace chieff\modules\UserProfile\controllers;

class TestController extends \webvimark\components\BaseController
{
    public function actionTest() {
        return $this->render('test');
    }
}