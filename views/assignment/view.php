<?php

use mdm\admin\AnimateAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\Assignment */
/* @var $fullnameField string */

$userName = $model->{$usernameField};
if (!empty($fullnameField)) {
    $userName .= ' (' . ArrayHelper::getValue($model, $fullnameField) . ')';
}
$userName = Html::encode($userName);

$this->title = Yii::t('rbac-admin', 'Assignment') . ' : ' . $userName;

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $userName;

//AnimateAsset::register($this);
YiiAsset::register($this);
$opts = Json::htmlEncode([
    'items' => $model->getItems(),
]);
$this->registerJs("var _opts = {$opts};");
$this->registerJs($this->render('_script.js'));
$animateIconright = '<i class="fa fa-arrow-right"></i>';
$animateIconleft = '<i class="fa fa-arrow-left"></i>';
?>
<div class="assignment-index col-md-12 org_yii2admin">
    <h1><?=$this->title;?></h1>

    <div class="row">
        <div class="col-sm-5 nopadding">
            <input class="form-control search" data-target="available"
                   placeholder="<?=Yii::t('rbac-admin', 'Search for available');?>">
            <select multiple size="20" class="list" data-target="available">
            </select>
        </div>
        <div class="col-sm-2 org_yii2admin_leftrightbuttons">
            <?=Html::a($animateIconright, ['assign', 'id' => (string) $model->id], [
                'class' => 'btn btn-primary btn-assign',
                'data-target' => 'available',
                'title' => Yii::t('rbac-admin', 'Assign'),
            ]);?>
            <?=Html::a($animateIconleft, ['revoke', 'id' => (string) $model->id], [
                'class' => 'btn btn-danger btn-assign',
                'data-target' => 'assigned',
                'title' => Yii::t('rbac-admin', 'Remove'),
            ]);?>
        </div>
        <div class="col-sm-5 nopadding">
            <input class="form-control search" data-target="assigned"
                   placeholder="<?=Yii::t('rbac-admin', 'Search for assigned');?>">
            <select multiple size="20" class="list" data-target="assigned">
            </select>
        </div>
    </div>
</div>
