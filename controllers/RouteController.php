<?php

namespace mdm\admin\controllers;

use Yii;
use mdm\admin\models\Route;
use yii\web\Controller;
use yii\filters\VerbFilter;

/**
 * Description of RuleController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class RouteController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['post'],
                    'assign' => ['post'],
                    'remove' => ['post'],
                    'refresh' => ['post'],
                ],
            ],
        ];
    }

	//  MOD START
	public function beforeAction($action)
	{
		$retVal = parent::beforeAction($action);
		if( $retVal )
		{
			$retVal = false;
			switch( $action->id )
			{
				case "index":
				case "create":
				case "assign":
				case "refresh":
				$retVal = \Yii::$app->user->can(161)/*'Super System Admin')*/? true : false;
				break;
				
				
				default:
				$retVal = false;
				break;
			}
		}
		if($retVal)
		{
			return $retVal;
		}
		else
		{
			Yii::$app->session->setFlash('error', 'Din bruker har ikke tilgang p� siden du fors�kte � g� til. Ta kontakt med en administrator om du mener det har oppst�tt en feil.');
			if(Yii::$app->request->referrer){
			$this->redirect(Yii::$app->request->referrer);
			}else{
			$this->goHome();
			}
		}
	}
	//  MOD END

    /**
     * Lists all Route models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Route();
        return $this->render('index', ['routes' => $model->getRoutes()]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->getResponse()->format = 'json';
        $routes = Yii::$app->getRequest()->post('route', '');
        $routes = preg_split('/\s*,\s*/', trim($routes), -1, PREG_SPLIT_NO_EMPTY);
        $model = new Route();
        $model->addNew($routes);
        return $model->getRoutes();
    }

    /**
     * Assign routes
     * @return array
     */
    public function actionAssign()
    {
        $routes = Yii::$app->getRequest()->post('routes', []);
        $model = new Route();
        $model->addNew($routes);
        Yii::$app->getResponse()->format = 'json';
        return $model->getRoutes();
    }

    /**
     * Remove routes
     * @return array
     */
    public function actionRemove()
    {
        $routes = Yii::$app->getRequest()->post('routes', []);
        $model = new Route();
        $model->remove($routes);
        Yii::$app->getResponse()->format = 'json';
        return $model->getRoutes();
    }

    /**
     * Refresh cache
     * @return type
     */
    public function actionRefresh()
    {
        $model = new Route();
        $model->invalidate();
        Yii::$app->getResponse()->format = 'json';
        return $model->getRoutes();
    }
}
