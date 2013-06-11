<?php
//アクセス制限 登録後にしかアクセスできない
if (!$_COOKIE["registerCompleted"]) {
    header("Location: index.php");
} 

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>ユーザ登録が完了しました</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
        <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #fff;
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
            <li class="active"><a href="index.php">TOP</a></li>
        </ul>
    </div>
    </div>
</div>

<!--toppage-->
<div class="container " style="text-align: center">
 <h3>登録が完了しました</h3>
 <p>ログインをする</p>
 <p><button class="bt btn-large" type="button" onClick="location.href='login.php'">ログイン</button></p>
 
</div>

<script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>