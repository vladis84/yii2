<?php

namespace yiiunit\framework\base;

use Yii;
use yiiunit\framework\base\stubs\TestController;
use yiiunit\TestCase;

/**
 * @group base
 */
class ControllerTest extends TestCase
{
    protected function setUp()
    {
        $config = [
            'layout'     => 'main',
            'layoutPath' => '@app/framework/base/fixtures',
            'viewPath'   => '@app/framework/base/fixtures',
        ];
        $this->mockApplication($config);
    }

    protected function tearDown()
    {
        parent::tearDown();
        TestController::$actionRuns = [];
    }
    public function testRunAction()
    {
        $this->mockApplication();
        $controller = new TestController('test-controller', Yii::$app);
        $this->assertNull($controller->action);
        $result = $controller->runAction('test1');
        $this->assertEquals('test1', $result);
        $this->assertEquals([
            'test-controller/test1',
        ], TestController::$actionRuns);
        $this->assertNotNull($controller->action);
        $this->assertEquals('test1', $controller->action->id);
        $this->assertEquals('test-controller/test1', $controller->action->uniqueId);

        $result = $controller->runAction('test2');
        $this->assertEquals('test2', $result);
        $this->assertEquals([
            'test-controller/test1',
            'test-controller/test2',
        ], TestController::$actionRuns);
        $this->assertNotNull($controller->action);
        $this->assertEquals('test1', $controller->action->id);
        $this->assertEquals('test-controller/test1', $controller->action->uniqueId);
    }

    public function testFindLayoutFileFindDefaulLayout()
    {
        $controller = new TestController('test-controller', Yii::$app);
        $view = Yii::$app->view;
        $expected = Yii::getAlias(Yii::$app->layoutPath) . DIRECTORY_SEPARATOR . 'main.php';

        $actual = $controller->findLayoutFile($view);

        $this->assertEquals($expected, $actual);
    }

    public function testFindLayoutFindLayoutInSubModules()
    {
        $module = Yii::$app;
        $module->layout = null;
        $subModule = clone $module;
        $subModule->layout = 'main';
        $module->module = $subModule;
        $controller = new TestController('test-controller', $module);
        $view = Yii::$app->view;
        $expected = Yii::getAlias(Yii::$app->layoutPath) . DIRECTORY_SEPARATOR . 'main.php';

        $actual = $controller->findLayoutFile($view);

        $this->assertEquals($expected, $actual);
    }

    public function testFindLayoutFileAliasPathLayout()
    {
        $controller = new TestController('test-controller', Yii::$app);
        $controller->layout = '@app/framework/base/fixtures/main';
        $view = Yii::$app->view;
        $expected = Yii::getAlias(Yii::$app->layoutPath) . DIRECTORY_SEPARATOR . 'main.php';

        $actual = $controller->findLayoutFile($view);

        $this->assertEquals($expected, $actual);
    }

    public function testFindLayoutFileAbsolutePathLayout()
    {
        $controller = new TestController('test-controller', Yii::$app);
        $controller->layout = '/main';
        $view = Yii::$app->view;
        $expected = Yii::getAlias(Yii::$app->layoutPath) . DIRECTORY_SEPARATOR . 'main.php';

        $actual = $controller->findLayoutFile($view);

        $this->assertEquals($expected, $actual);
    }

    public function testFindLayoutViewExtensionPHP5AndFileNotExists()
    {
        Yii::$app->view->defaultExtension = 'php5';
        $controller = new TestController('test-controller', Yii::$app);
        $controller->layout = 'main';
        $view = Yii::$app->view;
        $expected = Yii::getAlias(Yii::$app->layoutPath) . DIRECTORY_SEPARATOR . 'main.php';

        $actual = $controller->findLayoutFile($view);

        $this->assertEquals($expected, $actual);
    }

    public function testFindLayoutViewExtensionTPLAndFileExists()
    {
        Yii::$app->view->defaultExtension = 'tpl';
        $controller = new TestController('test-controller', Yii::$app);
        $controller->layout = 'main';
        $view = Yii::$app->view;
        $expected = Yii::getAlias(Yii::$app->layoutPath) . DIRECTORY_SEPARATOR . 'main.tpl';

        $actual = $controller->findLayoutFile($view);

        $this->assertEquals($expected, $actual);
    }
}
