<?php
/* @var $this yii\web\View */
$this->title = 'My First Project Home Page';
//require '../../basic/vendor/bower/DatePicker.php';
//require '../../basic/vendor/bower/Widget.php';

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\InsertToDo;
use app\models\Email;
use yii\web\Session;
use yii\helpers\Url;
//use yii\jui\yii\widgets\ActiveField;
//use \yii\jui\DatePicker;

?>
<?php 
$model = new InsertToDo;
$script = <<< JS
$(document).ready(function(){
$('#showInboxToDo').hide();
$('#createToDo').hide();
$('#showMyToDo').hide();
$('#shareEmail').hide();
$('#successInsertToDoMsg').hide();
$('#successShareEmailMsg').hide();

var sharebuttonId = 0;

$('#createToDoLink').click(function(){
	$('#showMyToDo').hide();
	$('#shareEmail').hide();
	$('#showInboxToDo').hide();
	$('#createToDo').fadeIn('slow');
});

$('#seeToDoLink').click(function(){
	$('#createToDo').hide();
	$('#shareEmail').hide();
	$('#showInboxToDo').hide();
	$('#showMyToDo').fadeIn('slow');
});

$('#cretaeToDoForm').submit(function(event){
	event.preventDefault();
	event.stopImmediatePropagation();
	var Jdate = document.getElementById('inserttodo-date').value;
	var Jdesc = document.getElementById('inserttodo-description').value;
	if(Jdate != '' && Jdesc != '')
	{
		$.ajax({
				type:'POST',	            
	            url:'index.php?r=site/inserttodo',
			    data:'date='+Jdate+'&desc='+Jdesc,
	            success:function(data) {
				$('#successInsertToDoMsg').show();
				},
				 error:function(jqXHR, textStatus, errorThrown){
				 alert('error::'+errorThrown);}
	         });
	}
	});
	
	$('#shareEmail').submit(function(event){
		event.preventDefault();
		event.stopImmediatePropagation();
		var tosentemailid = document.getElementById('email-emailid').value;
		if(tosentemailid != '')
		{
		$.ajax({
				type:'POST',	            
	            url:'index.php?r=site/sendemail',
			    data:'tosentemailid='+tosentemailid+'&sharebuttonId='+sharebuttonId,
	            success:function(data) {
				$('#successShareEmailMsg').show();
				},
				 error:function(jqXHR, textStatus, errorThrown){
				 alert('error::'+errorThrown);}
	         });
		}	
	});
	
$('#seeMyToDoTable  tr  td  input').on( "click",function() {
	sharebuttonId = this.id;
	$('#shareEmail').fadeIn('slow');
	$('#createToDo').hide();
	$('#showInboxToDo').hide();
	$('#showMyToDo').hide();
});

$('#inboxToDoLink').on( "click",function() {
	$('#createToDo').hide();
	$('#shareEmail').hide();
	$('#showInboxToDo').fadeIn('slow');
	$('#showMyToDo').hide();
});

});

JS;
$this->registerJs($script);
?>
    <div class="jumbotron">
        <h1><?= "Welcome ".$username
		//$Googleusers['0']->google_name ?></h1>
		
	</div>
	<div class="body-content">
		
        <div class="row">
            <div class="col-lg-4">
                <p><a class="btn btn-default" href="#" id="createToDoLink">Create To Do &raquo;</a></p>
            </div>
			<div class="col-lg-4">
                <p><a class="btn btn-default" href="#" id="seeToDoLink">See my To Dos &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <p><a class="btn btn-default" href="#" id="inboxToDoLink">See Inbox To Dos &raquo;</a></p>
            </div>
        </div>
		
    </div>
	
	
	<div id="createToDo">
	<?php $form = yii\widgets\ActiveForm::begin(['method' => 'POST','id'=>'cretaeToDoForm']);?>
	<?= $form->field($model,'Date');?>
	<?= $form->field($model,'Description');?>
	<?= Html::submitButton('Create To Do',['class'=>'btn btn-success','id'=>'insertToDoButton']) ?>
	<?php yii\widgets\ActiveForm::end();?>
	<br/><div id="successInsertToDoMsg" class="alert alert-success">To Do has been created successfully.</div>
	</div>
	
	<div id="shareEmail">
	<?php $emailmodel = new Email;?>
	<?php $form = yii\widgets\ActiveForm::begin(['method' => 'POST','id'=>'shareEmailForm']);?>
	<?= $form->field($emailmodel,'emailid')->textInput()->label('Enter email Id');?>
	<?= Html::submitButton('Send Email',['class'=>'btn btn-success','id'=>'sendEmailButton']) ?>
	<?php yii\widgets\ActiveForm::end();?>
	<br/><div id="successShareEmailMsg" class="alert alert-success">Email has been shared successfully.</div>
	</div>
	
	<div id="showInboxToDo">
	<h4>To Do from Inbox :</h4>
	<?php
	$session = new Session;
	$session->open();
	$InboxToDoRows = Email::findBySql('select * from email where emailid ="'.$session['email'].'"')->all();
	if(!empty($InboxToDoRows))
	{?>
	<table class = "table table-border" id="inboxToDoTable">
	<tr>
	<th>Date</th>
	<th>Description</th>
	<th>Sent By</th>
	</tr>
	  <?php foreach($InboxToDoRows as $row)
	  {	$todoid = $row->todoid;
		$InsertToDo = InsertToDo::findBySql('select Date, Description from insert_to_do where id ='.$todoid)->one();
		
		?>
		<tr>
		<td><?php echo $InsertToDo->Date ?></td>
		<td><?php echo $InsertToDo->Description ?></td>
		<td><?php echo $row->senderEmail ?></td>
		</tr>
		<?php 
	  }
	}
	?>
	</table>
	</div>
	
	<div id="showMyToDo">
	<h4>To Do you created :</h4>
	<?php
	$InsertToDoRows = InsertToDo::findBySql('select * from insert_to_do where useremail ="'.$session['email'].'"')->all();
	if(!empty($InsertToDoRows))
	{?>
	<table class = "table table-border" id="seeMyToDoTable">
	<tr>
	<th>Date</th>
	<th>Description</th>
	<th></th>
	</tr>
	  <?php foreach($InsertToDoRows as $row)
	  {
		$datevar = $row->Date;
		$descvar = $row->Description;
		$id = $row->id;?>
		<tr>
		<td><?php echo $datevar ?></td>
		<td><?php echo $descvar ?></td>
		<td><input class = "btn btn-primary" id="<?php echo $id ?>" type="button" value="share"/></td>
		</tr>
		<?php 
	  }
	}
	?>
	</table>
	</div>
	
	
	
