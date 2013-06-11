<?php
//Parse APIを初期化
require_once("parse.php");
$instance = new parseCurl();


//変数初期化
$username = "";
$userId = "";
$sessionToken = "";
$reservationList = array();

//Cookie情報取得
session_start();
if(strlen($_SESSION["login"])>0){
    
    $username = $_SESSION["username"];
    $userId = $_SESSION["userId"];
    $sessionToken = $_SESSION["sessionToken"];
    //ユーザの予約リストを取得
    if($instance->getUserReservationList($userId,True,True)) {
        $reservationList = $instance->results;
        //echo "createReservation works right<br>";
        //print_r( $instance->results);
    } else {
        echo "予約リストの取得に失敗しました.<br>";
    }
} else{
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Bootstrap 101 Template</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #fff;
      }
    #label-of-table {
        padding-top: 40px;
        padding-bottom: 10px;
    }
      
    </style>
</head>
<body style="padding-top:40px">

<div class="container">

<!--navigation bar-->
<div class="navbar">
    <div class="container">
    <div class="navbar-inner ">
    
        <a href="" class="brand">KagiBox</a>
        <ul class="nav">
            <li class="active"><a href="home.php">HOME</a></li>
            <li><a href="realestate.php">不動産予約</a></li>
        </ul>
        <ul class="nav pull-right">
            <li class="dropdown">
                <a href="" class="dropdown-toggle" data-toggle="dropdown">
                    <?=$username?>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="">Settings</a></li>
                    <li><a href="logout.php">Logout</a></li>

                </ul>
            </li>
        </ul>
    </div>
    </div>
</div>
<!--navigation bar ends-->

<!--list table of reservation-->
<p></p>

<?php
    //他のページからの情報を受け取る
    if($_SESSION["info"] != "") {
        echo $_SESSION["info"];
        $_SESSION["info"] = "";
    }
?>
<h1 id="label-of-table"> 予約物件一覧</h1>
<table class="table table-hover">
    <thead>
        <tr>
            <th>予約物件</th>
            <th>訪問予定時間</th>
        </tr>
    </thead>
    <tbody>
        <?php   
            for($i=0; $i < $reservationList["count"]; $i++ ) {
                $tempName = $reservationList["results"][$i]["realestate"]["name"];
                $tempDate = date("Y/m/d H:i",strtotime($reservationList["results"][$i]["date"]["iso"]));

                echo "<tr><th><a href=realestate.php?name=$tempName>$tempName</a></th>";
                echo "<th>$tempDate</th></tr>";
            }    
        ?>
    </tbody>
</table>
</div>

<script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>