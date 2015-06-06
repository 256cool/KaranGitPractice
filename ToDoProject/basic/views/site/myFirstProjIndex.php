<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use app\models\Registereduser;
$this->title = 'My Yii Application';
?>  
    <div class="site-index">
        <h1>NudoStilo</h1>

        <p class="lead">Welcome to the world of Fashion!</p>
		
		<?php $form = yii\widgets\ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>
		
		<?= $form->field($model,'emailid'); ?><br/>
		<?= $form->field($model,'password')->passwordInput(); ?>
		<?= Html::submitButton('Login',['class'=>'btn btn-success','id'=>'registerButton']) ?>
		<?= Html::button('Register With Google', ['class'=>'btn btn-success','onclick' => 'js:document.location.href="index.php?r=site/mylogin"']); ?>
		<?php yii\widgets\ActiveForm::end();?>
		<!-- , array('onclick' => 'js:document.location.href="index.php?r=site/mylogin"') -->
        <!--<p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Login With Google</a></p>-->
    </div>
