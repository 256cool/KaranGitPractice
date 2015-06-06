<?php

namespace app\controllers;

require_once '../googleAPI/src/Google_Client.php';
require_once '../googleAPI/src/contrib/Google_Oauth2Service.php';

		
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Session;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\firstTestForm;
use app\models\InsertToDo;
use app\models\Email;
use app\models\Googleusers;
use app\models\Registereduserforlogin;
use app\models\Registereduser;
use Google_Client;
use Google_Oauth2Service;
use yii\caching\Cache;

class SiteController extends Controller
{
     public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    
	#Controller method for first page
	public function actionMyfirstprojindex()
	{	
		$model = new Registereduserforlogin();
		if($model->load(Yii::$app->request->post()) && $model->validate())
		{  
		  $session = new Session;
		  $session->open();
		  $googledatamodel = Googleusers::findBySql('select * from Googleusers where google_email ="'.$model->emailid.'"')->one();
		  //echo $Googleusers->google_id;
		  $session['user_id'] 				= $googledatamodel->google_id;
		  $session['user_name']            = $googledatamodel->google_name;
		  $session['email'] 				= $googledatamodel->google_email;
		  //$session['profile_url'] 			= $googledatamodel->google_link;
		  $session['profile_image_url'] 	= $googledatamodel->google_picture_link;
		  return $this->render('myfirstprojecthome',['username'=>$session['user_name']]);
		}
		else
		{
		  return $this->render('myFirstProjIndex',['model'=>$model]);
		}
	}
	
	#Controller method to procedd after login button click
	public function actionMylogin()
	{		
	########## Google Settings.. Client ID, Client Secret from https://cloud.google.com/console #############
		$google_client_id 		= '337719399261-klg7sgtt4o3dd1luq0at98ecfum40k7g.apps.googleusercontent.com';
		$google_client_secret 	= 'K_BA_PMfFir5fTzhlYT3r075';
		$google_redirect_url 	= 'http://localhost/basic/web/index.php?r=site%2Fmylogin';# //path to your script
		$google_developer_key 	= 'AIzaSyC50yvEQ-2BU2xfD6-FSJWrGW7tWUSTZUg';
		
		//Start session
		$session = new Session;
		$session->open();
		
		
		//If user wish to log out, we just unset Session variable
		
		$gClient = new Google_Client();
		$gClient->setApplicationName('Login to myFirst.com');
		$gClient->setClientId($google_client_id);
		$gClient->setClientSecret($google_client_secret);
		$gClient->setRedirectUri($google_redirect_url);
		$gClient->setDeveloperKey($google_developer_key);
		
		$google_oauthV2 = new Google_Oauth2Service($gClient);
		
		/*if (1 == 1) 
{ 
  echo "";
  unset($_SESSION['token']);
  $gClient->revokeToken();
  echo "done";
  exit();
  //header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL)); //redirect user back to page
}*/
				
		//If code is empty, redirect user to google authentication page for code.
		//Code is required to aquire Access Token from google
		//Once we have access token, assign token to session variable
		//and we can redirect user back to page and login.
		if (isset($_GET['code'])) 
		{ 	$gClient->authenticate($_GET['code']);
			$_SESSION['token'] = $gClient->getAccessToken();
			//echo "is get set";
			//header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
			//return;
		} 
		
		if (isset($_SESSION['token'])) 
		{   
			$gClient->setAccessToken($_SESSION['token']);
		}
		//For Guest user, get google login url
		$authUrl = $gClient->createAuthUrl();
		if ($gClient->getAccessToken()) 
		{	
		  //For logged in user, get details from google using access token
		  $user 				= $google_oauthV2->userinfo->get();
		  $session['user_id'] 				= $user['id'];
		  $session['user_name']            = filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
		  $session['email'] 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
		  //$session['profile_url'] 			= filter_var($user['link'], FILTER_VALIDATE_URL);
		  $session['profile_image_url'] 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
		  //$session['personMarkup'] 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
		  $_SESSION['token'] 	= $gClient->getAccessToken();
		  $checkuser = Registereduser::findBySql('select * from registereduser where emailid ="'.$session["email"].'"')->all();
		  if($checkuser)
		  {
			  echo "This email ID is already registered, use different account to register!";
		  }
		  else
		  {
		  header('Location:http://localhost/basic/web/index.php?r=site%2Fregister');
		  }
		}
		else
		{
			header("Location:".$authUrl);
		}
		exit();
	}

	public function actionHome()
	{
		//Start session
		$session = new Session;
		$session->open();
		$Googleusers = Googleusers::findBySql('select * from Googleusers where google_id ='.$session["user_id"])->all();
		if($Googleusers)
		{	
			//return $this->render('myfirstprojecthome',['Googleusers'=>$Googleusers]);
			return $this->render('myfirstprojecthome',['username'=>$session['user_name']]);
		}
		else
		{	
			$Googleusers = new Googleusers();
			$Googleusers->google_id = $session['user_id'];
			$Googleusers->google_name = $session['user_name'];
			$Googleusers->google_email = $session['email'];
			//$Googleusers->google_link = $session['profile_url'];
			$Googleusers->google_picture_link = $session['profile_image_url'];
			$Googleusers->insert();
			return $this->render('myfirstprojecthome',['username'=>$session['user_name']]);
		}
	}
	
	
	public function actionBegin($target = 'World')
	{   
		echo "Hey Begin";
		//return $this->render('begin',['target'=>$target]);
	}
	
	public function actionInserttodo()
	{	
		$session = new Session;
		$session->open();
		
		$insert = new InsertToDo();
		$insert->Date = $_POST['date'];
		$insert->Description = $_POST['desc'];
		$insert->useremail = $session['email'];
		$insert->insert();
		
		echo $_POST['date']." ".$_POST['desc']." ".$session['email']." ".$insert->Date;
		//echo Yii::app()->request->getPost("date");
	}
	
	public function actionSendemail()
	{	
		$session = new Session;
		$session->open();
		
		//echo $_POST['tosentemailid']." ".$_POST['sharebuttonId'];
		$insertemail = new Email();
		$insertemail->emailid = $_POST['tosentemailid'];
		$insertemail->todoid = $_POST['sharebuttonId'];
		$insertemail->senderEmail = $session['email'];
		$insertemail->insert();
		
		$value = Yii::$app->mailer->compose()
		->setFrom([$session['email']=>'karan'])
		->setTo($_POST['tosentemailid'])
		->setSubject('Test Email')
		->setHtmlBody('Test content')
		->send();
	}
	
	public function actionMylogout()
	{
		  $session = new Session;
		  $session->open();
		  unset($_SESSION['token']);
		  //$gClient->revokeToken();
		  $session->destroy();
		  $this->redirect('index.php?r=site/myfirstprojindex');
		  
	}
	
	public function actionRegister()
	{	  
		 $session = new Session;
		 $session->open();
		 $model = new Registereduser;
		 
		 if ($model->load(Yii::$app->request->post()) && $model->validate()) 
		 {	
			$model->emailid = $session['email'];
			$model->insert();
			$this->redirect('index.php?r=site/home');
		} 
		 return $this->render('register',['user_name'=>$session['user_name'],'email'=>$session['email'],'profile_image_url'=>$session['profile_image_url'],'model'=>$model]); 
		
	}
	
	public function actionMycache()
	{
		$key = 'demo';
		$cache = Yii::$app->getCache();
		$data = $cache->get($key);
		if ($data === false) {
		// ...generate $data here...
		$data="Karan cache";
		echo "In cache ".$data." cache set";
		$cache->set($key, $data, 0, null);
		}
		else
		{
		  echo $data;
		}
		
		$db=\Yii::$app->db;
		$art=$db->createCommand('select * from email')-> cache(3600)->queryAll();
		
		//return $art;
	}
	
}
