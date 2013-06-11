<?php
require_once("parse.php");
$instance = new parseCurl();

//session設定
session_start();

$_SESSION["login"] = 0;
$_SESSION["username"] = "";
$_SESSION["userId"] = "";
$_SESSION["sessionToken"] = "";

//変数初期化
$error = "";
$username = "";
$password = "";

//error変数
$error_username = "";
$error_password = "";

$error_exist = false;

//ポストされた
if ( $_SERVER["REQUEST_METHOD"] == "POST"){
  //submitボタンが押された
  //$error ="post";
  if(isset($_POST["submit"])){
   // $error ="submit";
    //POSTされた情報を取得
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    

    //入力内容をチェック
    
    if (strlen($username) == 0)
      {$error_username = "ユーザIDが入力されていません"; $error_exist = true;}
    if (strlen($password) == 0)
      {$error_password = "passwordが入力されていません"; $error_exist = true;}
    if (!$error_exist) 
    {
      //Access to Parse API for logging in
      $len = $instance->login($username,$password);
      if ($len == 2) {
        $error = $instance->results['error'];
      }
      else {
        //print_r ($instance->results);
        $_SESSION["login"] = 1;
        //sessionにsessionTokenを埋め込む
        $_SESSION["username"] = $instance->results['username'];
        $_SESSION["userId"] = $instance->results['objectId'];
        $_SESSION["sessionToken"] = $instance->results['sessionToken'];
        //$error = $resul;
        //print_r($instance->results);  
        header("Location: home.php");
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
    <title>ログイン画面</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
        <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #fff;
      }
      
   
      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
</head>
<body style="padding-top:40px">



<!--navigation bar-->
<div class="navbar">
    <div class="container">
    <div class="navbar-inner ">
        <a class="brand">KagiBox</a>
        <ul class="nav">
            <li><a href="index.php">TOP</a></li>
        </ul>
    </div>
    </div>
</div>

 <div class="container">
      <form class="form-signin" action ="" method="POST">
        <h2 class="form-signin-heading">Please Log in</h2>
        <div class = "text-error"><?=$error?></div>
        <div class = "text-error"><?=$error_username?></div>
        <div class = "text-error"><?=$error_password?></div>
        <input type="text" class="input-block-level" placeholder="User Name" name="username" value ="<?=$username?>">
        <input type="password" class="input-block-level" placeholder="Password" name="password" value ="<?=$password?>">
        <button class="btn btn-large btn-primary" type="submit" name="submit">Log in</button>
      </form>

    </div> <!-- /container -->

<script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>