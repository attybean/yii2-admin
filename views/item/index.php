<?php

use yii\helpers\Html;
use yii\grid\GridView;
use mdm\admin\components\RouteRule;
use mdm\admin\components\Configs;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\AuthItem */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$this->title = Yii::t('rbac-admin', $labels['Items']);
$this->params['breadcrumbs'][] = $this->title;

$rules = array_keys(Configs::authManager()->getRules());
$rules = array_combine($rules, $rules);
unset($rules[RouteRule::RULE_NAME]);
//  MOD START
$sup = \Yii::$app->user->can(161)/*'Super System Admin')*/;
//  MOD END
?>
<div class="role-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('rbac-admin', 'Create ' . $labels['Item']), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'name',
                'label' => Yii::t('rbac-admin', 'Name'),
            ],
			//  MOD START
            // [
            //     'attribute' => 'ruleName',
            //     'label' => Yii::t('rbac-admin', 'Rule Name'),
            //     'filter' => $rules
            // ],
			//  MOD END
            [
                'attribute' => 'description',
                'label' => Yii::t('rbac-admin', 'Description'),
            ],
			//  MOD START
            ['class' => 'yii\grid\ActionColumn',
                'template'=> '{view}{update}{delete}',
                'visibleButtons' => [
                    'view' => function($model, $key, $index) use ($sup){                                     
                            return !$model->is_active ||  $sup;
                    },
                    'update' => function($model, $key, $index) use ($sup) {                       
                            return !$model->is_active || $sup;
                    },
                    'delete' => function($model, $key, $index) use ($sup) {                       
                            return !$model->is_active || $sup;
                    }
                ]

                // 'buttons' => [
                //     ''
                // ],
            ],
			//  MOD END
        ],
    ])
    ?>

</div>
