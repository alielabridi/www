<?php
 /* include the mysql connect file */
 require_once('connect.php');

// include required files form Facebook SDK
 
// added in v4.0.5
require_once( 'include/Facebook/FacebookHttpable.php' );
require_once( 'include/Facebook/FacebookCurl.php' );
require_once( 'include/Facebook/FacebookCurlHttpClient.php' );
 
// added in v4.0.0
require_once( 'include/Facebook/FacebookSession.php' );
require_once( 'include/Facebook/FacebookRedirectLoginHelper.php' );
require_once( 'include/Facebook/FacebookRequest.php' );
require_once( 'include/Facebook/FacebookResponse.php' );
require_once( 'include/Facebook/FacebookSDKException.php' );
require_once( 'include/Facebook/FacebookRequestException.php' );
require_once( 'include/Facebook/FacebookOtherException.php' );
require_once( 'include/Facebook/FacebookAuthorizationException.php' );
require_once( 'include/Facebook/GraphObject.php' );
require_once( 'include/Facebook/GraphSessionInfo.php' );
 
// added in v4.0.5
use Facebook\FacebookHttpable;
use Facebook\FacebookCurl;
use Facebook\FacebookCurlHttpClient;
 
// added in v4.0.0
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;
 
// start session
session_start();
 
// init app with app id and secret
FacebookSession::setDefaultApplication( '563460800438057','36e24b9369287738867bc35e7cb54fdf' );
 
// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper( $facebook_path.'index.php' );
 
// see if a existing session exists
if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
  // create new session from saved access_token
  $session = new FacebookSession( $_SESSION['fb_token'] );
  
  // validate the access_token to make sure it's still valid
  try {
    if ( !$session->validate() ) {
      $session = null;
    }
  } catch ( Exception $e ) {
    // catch any exceptions
    $session = null;
  }
  
} else {
  // no session exists
  
  try {
    $session = $helper->getSessionFromRedirect();
  } catch( FacebookRequestException $ex ) {
    // When Facebook returns an error
  } catch( Exception $ex ) {
    // When validation fails or other local issues
    echo $ex->message;
  }
  
}
 
// see if we have a session
if ( isset( $session ) ) {
  
  // save the session
  $_SESSION['fb_token'] = $session->getToken();
  // create a session using saved token or the new one we generated at login
  $session = new FacebookSession( $session->getToken() );
  
  // graph api request for user data
  $request = new FacebookRequest( $session, 'GET', '/me' );
  $response = $request->execute();
  // get response
  $graphObject = $response->getGraphObject()->asArray();


  $db = new mysqli($hostname_mysqli,$username_mysqli,$password_mysqli,$database_mysqli);
  if($db->connect_error)
  {
    die("Connect error ({$db->connect_errno}) {$db->connect_error}");
  }
  $id = mysqli_escape_string($db,$graphObject["id"]);
  $result = $db->query("SELECT * FROM `userapps` WHERE `Facebook_ID` = $id;");

  /*Check whether the user is already registered in the database with that ID*/
  if($result->num_rows>0)
    { 
    // if user recognized set a session
    $_SESSION['usr_id']=$graphObject["id"];
    header("location: events.php");
    }
  else {
    header('Location: include/Facebook_SignUp.html');
  }
}
  // see if we have a session
if( isset( $_SESSION["usr_id"] ) ) {


  $db = new mysqli($hostname_mysqli,$username_mysqli,$password_mysqli,$database_mysqli);
  if($db->connect_error)
  {
    die("Connect error ({$db->connect_errno}) {$db->connect_error}");
  }
  $id = mysqli_escape_string($db,$_SESSION["usr_id"]);
  $result = $db->query("SELECT * FROM `userapps` WHERE `Facebook_ID` = $id;");

  /*Check whether the user is already registered in the database with that ID*/
  if($result->num_rows>0)
    { 
    // if user recognized set a session
    header("location: events.php");
    }


  
} 
else {
  // show login url
  $params = array(
    'scope' => 'email',
  );
  $loginUrl =  $helper->getLoginUrl($params);
  ?>

<!DOCTYPE html>
<html>

<head>
	<title>Pikelife</title>

  

<style>
.top_banner{
	background-color: white;
	height:60px;
	width:100%;
	z-index: 3;
	position: fixed;
	top: 0px;
	left: 0px;
	right: 0px;
	border-bottom: 1px solid rgba(0,0,0,0.15);

}
img{
	position: relative;
	left: 100px;
	top: 10px;
}
.text_adver{
	background-color:rgba(0, 0, 0, 0.5);

    width: 500px;
    height: 400px;

	font-family: "Trebuchet MS", Helvetica, sans-serif;
	font-size: 20px;
	position: absolute;
	top: 100px;
	left: 100px;
	color: white;
	padding: 0px 30px 20px 50px;
	border-radius: 40px 40px 40px 40px;
}
.face_book{
	text-align: center;
	background-color:rgba(0, 0, 0, 0.5);
	width: 300px;
	font-family: "Trebuchet MS", Helvetica, sans-serif;
	font-size: 20px;
	position: absolute;
	top: 100px;
	left: 830px;
	color: white;
	padding: 15px 20px 20px 20px;
	border-radius: 40px 40px 40px 40px;
}

        .btn-default {
            background: none;
            border: 2px #fff solid;
            color: #fff;
            padding: 10px 40px 10px 40px;
            font-family: Raleway;
            font-size: 18px;
            font-weight: 700;
        }
        .btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .open .dropdown-toggle.btn-default {
          color: #fff;
          background: none;
          border-color: #C53434;
          cursor: pointer;
        }

        .textfield_css{
          text-align: center;
          background: transparent;
          border-radius: 15px;
          padding: 10px;
          color: white;
          border: 3px #fff solid;
          font-size: 15px;
          font-weight: bold;
        }

        ::-webkit-input-placeholder { /* WebKit browsers */
            color:    #fff;
            font-size: 15px;
            font-weight: bold;
        }
        :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
            color:    #fff;
            opacity:  1;
            font-size: 15px;
          font-weight: bold;
        }
        ::-moz-placeholder { /* Mozilla Firefox 19+ */
            color:    #fff;
            opacity:  1;
            font-size: 15px;
          font-weight: bold;
        }
        :-ms-input-placeholder { /* Internet Explorer 10+ */
            color:    #fff;
            font-size: 15px;
          font-weight: bold;
        }

</style>

<link rel="stylesheet" href="css/auth-buttons.css">

    <!-- prettyify -->
    <link rel="stylesheet" href="http://google-code-prettify.googlecode.com/svn/trunk/src/prettify.css">
    <script src="http://google-code-prettify.googlecode.com/svn/trunk/src/prettify.js"></script>

    <script type="text/javascript" src="/background_slider/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="/background_slider/jquery.vegas.js"></script>

    <link rel="stylesheet" type="text/css" href="/background_slider/jquery.vegas.css" />
</head>

<body>
	<div class='top_banner'><a href="/"><img src="images/logo_home.png"></a></div>
	<div class="text_adver">
	      <h3>The best experiences of life, only happen, once with friends.</h3>
	      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PikeLife makes it easy to gather and do things with your friends, while keeping those experiences unforgettable.<br><br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sign up and find out what your friends are doing<br><br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"Sign in with facebook" will get you in touch with your friends that are using our application in an instant
        <br><br> this application is free and forever
	</div>
	<div class="face_book">
      <h3>Start Now</h3>
    <form action="index.php" method="post">
    <?php
    /*Authentification form*/
    if(isset($_POST["pass"]) AND isset($_POST["email"]) ){
      $pass = $_POST["pass"];
      $email = $_POST["email"];
      $db = new mysqli($hostname_mysqli,$username_mysqli,$password_mysqli,$database_mysqli);
      if($db->connect_error){
        die("Connect error ({$db->connect_errno}) {$db->connect_error}");
      }
      $email = $db->real_escape_string($email);
      $pass = $db->real_escape_string(md5($pass));
      $sql = "SELECT * FROM `userapps` WHERE `usr_email` = '$email' AND `password` = '$pass';";
      $result = $db->query($sql);
      if($result->num_rows==1){
        $row = $result->fetch_assoc();
        $_SESSION["usr_id"] = $row["Facebook_ID"];
      }
      else 
        echo $error_message = "You entered an incorrect identification";



    }
    ?>
      
        <input class = "textfield_css" type="text" name="email" placeholder="Email" required><br><br>
        <input class = "textfield_css" type="password" name="pass" placeholder="Password" required><br><br>
        <input class="btn btn-default" type="submit" value="login">
    </form>
	      <p><a class="btn-auth btn-facebook large" href="<?= $helper->getLoginUrl($params) ?>">Sign in with <b>Facebook</b></a></p>
        Not yet member?
        <em><a style="text-decoration: none; color:red" href="Register.php">Register</a></em><br><em><a style="text-decoration: none; color:red" href="contactUs.php">Contact Us</a></em>
	      
	</div>
	<div id="fb-root"></div>

	<script type="text/javascript">

    $.vegas('slideshow', {
      delay:7000,
      backgrounds:[
        { src:'/background_slider/images/background.jpg', fade:1000 },
        { src:'/background_slider/images/background2.jpg', fade:1000 },
        { src:'/background_slider/images/background3.jpg', fade:1000 },
        { src:'/background_slider/images/background4.jpg', fade:1000 },
        { src:'/background_slider/images/background5.jpg', fade:1000 },
        { src:'/background_slider/images/background6.jpg', fade:1000 },
        { src:'/background_slider/images/background7.jpg', fade:1000 }
      ]
    })('overlay', {
      src:'/background_slider/overlays/10.png'
    });
    </script>

</body>

</html>
<?php
}
?>