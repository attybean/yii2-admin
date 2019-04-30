<?php

use mdm\admin\AnimateAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model mdm\admin\models\AuthItem */
/* @var $context mdm\admin\components\ItemController */

$context = $this->context;
$labels = $context->labels();
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

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
<div class="auth-item-view col-md-12 org_yii2admin">
    <h1><?=Html::encode($this->title);?></h1>
    <p>
		<?php /*  MOD START */ ?>
        <?= Html::a(Yii::t('rbac-admin', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('rbac-admin', 'Delete'), ['delete', 'id' => $model->id], [
                /*  MOD END */ 
            'class' => 'btn btn-danger',
            'data-confirm' => Yii::t('rbac-admin', 'Are you sure to delete this item?'),
            'data-method' => 'post',
        ]);?>
    </p>
    <div class="row">
        <div class="col-sm-12 nopadding nopadding--right">
            <?=
			DetailView::widget([
			    'model' => $model,
			    'attributes' => [
			        'name',
			        'description:ntext',
					//  MOD START
			        // 'ruleName',
					//  MOD END
			        'data:ntext',
			    ],
			    'template' => '<tr><th>{label}</th><td>{value}</td></tr>',
			]);
			?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-5 nopadding">
            <input class="form-control search" data-target="available"
                   placeholder="<?=Yii::t('rbac-admin', 'Search for available');?>">
            <select multiple size="20" class="list" data-target="available"></select>
        </div>
        <div class="col-sm-2 org_yii2admin_leftrightbuttons">
				<?php /*  MOD START */ ?>
            <?= Html::a($animateIconright, ['assign', 'id' => $model->id], [
                'class' => 'btn btn-primary btn-assign',
                'data-target' => 'available',
                'title' => Yii::t('rbac-admin', 'Assign'),
            ]);?><br><br>
                        <?=Html::a($animateIconleft, ['remove', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-assign',
                'data-target' => 'assigned',
                'title' => Yii::t('rbac-admin', 'Remove'),
            ]);?>
				<?php /*  MOD END */ ?>
        </div>
        <div class="col-sm-5 nopadding nopadding--right">
            <input class="form-control search" data-target="assigned"
                   placeholder="<?=Yii::t('rbac-admin', 'Search for assigned');?>">
            <select multiple size="20" class="list" data-target="assigned"></select>
        </div>
    </div>
</div>
