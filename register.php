<?php
require_once("parse.php");
$instance = new parseCurl();

//session設定
session_start();
$_SESSION["login"] = 0;

//変数初期化
$error = "";
$username = "";
$password = "";
$password2 = "";
$phone1 = "";
$phone2 = "";
$phone3 = "";
$email = "";

//error変数
$error_username = "";
$error_password = "";
$error_password2 = "";
$error_phone = "";
$error_email = "";

$error_exist = false;

//ポストされた
if ( $_SERVER["REQUEST_METHOD"] == "POST"){
  //submitボタンが押された
  $error ="post";
  if(isset($_POST["submit"])){
    $error ="submit";
    //POSTされた情報を取得
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    $password2 = htmlspecialchars($_POST["password2"]);
    $phone1 = htmlspecialchars($_POST["phone1"]);
    $phone2 = htmlspecialchars($_POST["phone2"]);
    $phone3 = htmlspecialchars($_POST["phone3"]);
    $email = htmlspecialchars($_POST["email"]);

    //入力内容をチェック
    
    if (strlen($username) == 0)
      {$error_username = "ユーザIDが入力されていません"; $error_exist = true;}
    if (strlen($password) == 0)
      {$error_password = "passwordが入力されていません"; $error_exist = true;}
    if (strlen($password2) == 0)
      {$error_password2 = "password2が入力されていません"; $error_exist = true;}
    if(strlen($password) && strlen($password2) && $password != $password2)
      {$error_password ="パスワードが一致しません"; $error_exist = true;}

    if (strlen($phone1) == 0)
      {$error_phone = "phoneが入力されていません"; $error_exist = true;}
    if (strlen($phone2) == 0)
      {$error_phone = "phoneが入力されていません"; $error_exist = true;}
    if (strlen($phone3) == 0){$error_phone = "phoneが入力されていません";}
    if (strlen($email) == 0)
      {$error_email = "emailが入力されていません"; $error_exist = true;}

    if (!$error_exist) {
      $phone_num = $phone1."-".$phone2."-".$phone3;
      
      //Access to Parse API for signing up
      $resul = $instance->postUser($username,$password,$phone_num,$email);
      
      if( $resul == 2) {
        //error occured in Parse
        $error = $instance->results['error']; 
      } 
      elseif( $resul < 0) {
       $error = "parse.php doesn't work right. see the error code on console."; 
      }
      else {
        //API works right!
        
        //set ACL 
        $UserId = $instance->results['objectId'];
        $sessionToken = $instance->results['sessionToken'];
        $resul = $instance->putACL("User",$UserId,$UserId,$sessionToken,true,false);
        if ($resul == 2) {
          echo "fail to set ACL";
          echo $this->results['error'];
        } 
        else {
          setcookie("registerCompleted", true, time() + 5);
          //print_r($instance->results);  
          header("Location: registerCompleted.php");  
        }
      }         
    }


  }    
}

//if ($instance->login($user,$passwd)) print_r($instance->results);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>登録画面</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
        <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #fff;
      }
      #intro {
        padding-left: 100px;
      }
    </style>
</head>
<body style="padding-top:40px">
<!--navigation bar-->
<div class="navbar">
    <div class="container">
    <div class="navbar-inner ">
        <a  class="brand">KagiBox</a>
        <ul class="nav">
            <li><a href="index.php">TOP</a></li>
        </ul>
    </div>
    </div>
</div>
<h5 id="intro">以下の情報を入力してください</h5>
<!-- error displayed when parse.php doesn't work right -->
<?php
if(strlen($error)>0){
    echo "<font size =\"2\" color=\"#da0b00\">{$error}</font><p>";
}
?>


<!--input form-->
<div class="container">
<div class="span12">

<form class="form-horizontal"　action ="" method="POST">
  <!--username-->
  <div class="control-group">
    <label class="control-label" for="name">Name</label>
    <div class="controls">
    <input type="text" id="name" name="username" value ="<?=$username?>">
    <span class="help-inline"><div class = "text-error"><?php echo $error_username; ?></div></span>
    </div>
  </div>
  <!--email-->
  <div class="control-group">
    <label class="control-label" for="email">E-mail</label>
    <div class="controls">
    <input type="text" id="email" name="email" value ="<?=$email?>" >
    <span class="help-inline"><div class = "text-error"><?php echo $error_email; ?></div>
    </div>
  </div>
  <!--phone-->
  <div class="control-group">
    <label class="control-label" for="phone">Phone Number</label>
    <div class="controls controls-row">
      <input type="text" class="span1" id="phone" name="phone1" value ="<?=$phone1?>">
      <b class="span0"><font size="5">-</font></b>
      <input type="text" class="span1" name="phone2" value ="<?=$phone2?>">
      <b class="span0"><font size="5">-</font></b>
      <input type="text" class="span1" name="phone3" value ="<?=$phone3?>">
      <span class="help-inline"><div class = "text-error"><?php echo $error_phone; ?></div>
    </div>
    
   </div>

  <!--passewd-->
  <div class="control-group">
    <label class="control-label" for="pwd">Password</label>
    <div class="controls">
    <input type="password" id="pwd" name="password" value ="<?=$password?>">
    <span class="help-inline"><div class = "text-error"><?php echo $error_password; ?></div>
    </div>
  </div>
  <!--passwd confirmed-->
  <div class="control-group">
    <label class="control-label" for="pwd-confirmed">Password (confirmed)</label>
    <div class="controls">
    <input type="password" id="pwd-confirmed" name="password2" value ="<?=$password2?>">
    <span class="help-inline"><div class = "text-error"><?php echo $error_password2; ?></div>
    </div>
  </div>

  <div class="control-group">
    <div class="controls">
      <input type="submit" value="登録" name="submit" class="btn">
    </div>
  </div>

</form>

</div>

</div>

<script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>