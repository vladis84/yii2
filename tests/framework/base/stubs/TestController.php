<?php

namespace yiiunit\framework\base\stubs;

use yii\base\Controller;

class TestController extends Controller
{
    public static $actionRuns = [];

    public function actionTest1()
    {
        self::$actionRuns[] = $this->action->uniqueId;

        return 'test1';
    }
    public function actionTest2()
    {
        self::$actionRuns[] = $this->action->uniqueId;
        
        return 'test2';
    }
}
