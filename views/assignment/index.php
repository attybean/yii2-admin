<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Assignment */
/* @var $usernameField string */
/* @var $extraColumns string[] */

$this->title = Yii::t('rbac-admin', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'label' => 'Id',
        'class' => 'yii\grid\DataColumn', // can be omitted, as it is the default
        'value' => function ($data) {
            return $data->id; // $data['name'] for array data, e.g. using SqlDataProvider.
        },
    ],
    $usernameField,
];
if (!empty($extraColumns)) {
    $columns = array_merge($columns, $extraColumns);
}

$columns[] = [
    'class' => 'yii\grid\ActionColumn',
    'template' => '{view}',
    'buttons' =>
        [
            'update'=>function($url,$model,$key)
            {
                return Html::a("<i class='fa fa-edit'></i>", $url);
            },
            'view'=>function($url,$model,$key)
            {
                return Html::a("<i class='fa fa-eye'></i>", $url);
            },
            'delete'=>function($url,$model,$key)
            {
                return Html::a("<i class='fa fa-trash-alt'></i>", $url);
            }
        ],
];

?>
<div class="assignment-index col-md-12">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a("<i class='fa fa-user-shield'></i>".Yii::t('rbac-admin', 'Role'), "/yii2admin/role", ['class' => 'btn btn-primary']) ?>
    <?= Html::a("<i class='fa fa-user-shield'></i>".Yii::t('rbac-admin', 'Permissons'), "/yii2admin/permission", ['class' => 'btn btn-primary']) ?>

    <?php Pjax::begin(); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]);
    ?>
    <?php Pjax::end(); ?>

</div>
