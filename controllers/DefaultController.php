<?php

namespace mdm\admin\controllers;

use Yii;

/**
 * DefaultController
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class DefaultController extends \yii\web\Controller
{

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
                    $retVal = \Yii::$app->user->can('Super System Admin')? true : false;
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
			Yii::$app->session->setFlash('error', 'Din bruker har ikke tilgang på siden du forsøkte å gå til. Ta kontakt med en administrator om du mener det har oppstått en feil.');
			if(Yii::$app->request->referrer){
				$this->redirect(Yii::$app->request->referrer);
			}else{
				$this->goHome();
			}
		}
    }
	//  MOD END

    /**
     * Action index
     */
    public function actionIndex($page = 'README.md')
    {
        if (strpos($page, '.png') !== false) {
            $file = Yii::getAlias("@mdm/admin/{$page}");
            return Yii::$app->getResponse()->sendFile($file);
        }
        return $this->render('index', ['page' => $page]);
    }
}
