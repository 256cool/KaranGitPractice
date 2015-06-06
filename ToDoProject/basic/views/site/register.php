<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
$this->title = 'My Yii Application';
?>  
	<div class="site-login">
    <h1><?= "Nudostilo Registration Page"; ?></h1>
	<img src="<?= $profile_image_url ?>" height="100" width="100"/>
	<p class="lead"><?= "Hi ".$user_name ?></p>
	<p class="lead"><?= "Email ".$email ?></p>
		
    <p>Please fill out the following fields to Register:</p>

    <?php $form = yii\widgets\ActiveForm::begin([
        'id' => 'login-form',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-2\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

		<?= $form->field($model,'password')->passwordInput();?><br/>
		<?= $form->field($model,'password_repeat')->passwordInput();?>
		<?= Html::submitButton('Register',['class'=>'btn btn-success','id'=>'registerButton']) ?>
		<?php yii\widgets\ActiveForm::end();?>

</div>
