<?php
//Parse APIを初期化
require_once("parse.php");
$instance = new parseCurl();


//変数初期化
$username = "";
$userId = "";
$sessionToken = "";
$realEstateList = array();

//Cookie情報取得
session_start();
if(strlen($_SESSION["login"])>0){
    
    $username = $_SESSION["username"];
    $userId = $_SESSION["userId"];
    $sessionToken = $_SESSION["sessionToken"];
    //不動産のリストを取得
    if ($instance->getAllRealEstate()) {
        $realEstateList = $instance->results;
        
    } else {
        echo "リストの取得に失敗しました.<br>";
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
            <li><a href="main.php">HOME</a></li>
            <li class="active"><a href="realestate.php">不動産予約</a></li>
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
<?php
    //echo $realEstateList[0]["name"];
    //echo $realEstateList[0]["address"];
?>
<!--list table of reservation-->
<p></p>
<h1 id="label-of-table"> 物件一覧</h1>
<table class="table table-hover">
    <thead>
        <tr>
            <th>物件名</th>
            <th>住所</th>
        </tr>
    </thead>
    <tbody>
        <?php
            for ($i=0; $i < sizeof($realEstateList); $i++) {
                $tempName = $realEstateList[$i]["name"];
                $tempAddress =  $realEstateList[$i]["address"];
                $tempId = $realEstateList[$i]["objectId"];
                echo "<th><a href=realestateInfo.php?name=$tempName>$tempName</a></th>";
                echo "<th>$tempAddress</th>";
                echo "<th><div><a href=reservation.php?reId=$tempId&reName=$tempName class='bt btn-small btn-danger' type=button >予約</a>";
                echo "<a href=realestateInfo.php?name=$tempName class='bt btn-small btn-info' type=button >詳細</a>
                    </div>
                    </th>
                    </tr>";
            }
        ?>
    </tbody>
</table>
</div>

<script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>