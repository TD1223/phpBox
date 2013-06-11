<?php
//Parse APIを初期化
require_once("parse.php");
$instance = new parseCurl();


//変数初期化
$username = "";
$userId = "";
$sessionToken = "";
$reservationList = array();
$reId = "";
$realEstateName = "";

$year = "";
$month = "";
$day = "";
$hour = "";

//error変数
$error_year = "";
$error_month = "";
$error_day = "";
$error_hour = "";
$error = "";

$error_exist = false;

//Cookie情報取得
session_start();
if(strlen($_SESSION["login"])>0){
    
    $username = $_SESSION["username"];
    $userId = $_SESSION["userId"];
    $sessionToken = $_SESSION["sessionToken"];
    //物件のIDを取得
    $reId = $_GET["reId"];
    $realEstateName = $_GET["reName"];
    if (!preg_match('/^[a-zA-Z0-9]{10}$/',$reId)){
        echo "クエリーの内容が正しくありません";
    }
    //予約状況の一覧を取得
    if($instance->getREReservationList($reId,True,True,True)) {
        $reservationList = $instance->results;
    } else {
        echo "予約状況の一覧の取得に失敗しました<br>";
    }

} else{
    header("Location: index.php");
}

//ポストされた
if ( $_SERVER["REQUEST_METHOD"] == "POST"){
  //submitボタンが押された
  $error ="post";
  if(isset($_POST["submit"])){
    //print_r($_POST);
    //POSTされた情報を取得
    $year = htmlspecialchars($_POST["year"]);
    $month = htmlspecialchars($_POST["month"]);
    $day = htmlspecialchars($_POST["day"]);
    $hour = htmlspecialchars($_POST["hour"]);

    if ($month < 10) {
        $temp = $month;
        $month = "0".$temp;
    }
    if ($day < 10) {
        $temp = $day;
        $day = "0".$temp;
    }
    if ($hour < 10) {
        $temp = $hour;
        $hour = "0".$temp;
    }

    if (!$error_exist) {
        //reservationDate: ISO 8601で記述された予約の日付 (yyyy-mm-ddThh:mm:ssZ)
        $reservationDate = $year."-".$month."-".$day."T".$hour.":00:00Z";      
        $error = $reservationDate;
        //この時間で既に予約がないか確認
        if(($le=$instance->checkExistingReservation($year."-".$month."-".$day."T".$hour, $reId))>0) {
            //既に予約が入っている
            $error = "この時間帯は既に予約が入っています。";
            //print_r($instance->results);
        } else {
            //予約はない
            echo "予約をPOSTする";
            if($instance->createReservation($userId,$reId, $reservationDate)) {
                //予約成功
                $_SESSION["info"] = "予約が完了しました。";
                header("Location: home.php");
            } else {
                echo "予約を作成する事に失敗しました。";
            }
        }
    }
     


  }    
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
            <li><a href="home.php">HOME</a></li>
            <li ><a href="realestate.php">不動産予約</a></li>
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
    //echo $reservationList["count"];
    //echo $reId;
?>
<!--list table of reservation-->
<p></p>


<h1 id="label-of-table"><?=$realEstateName?>の予約状況一覧</h1>

<table class="table table-hover">
    <thead>
        <tr>
            <th>予約者</th>
            <th>時間</th>
        </tr>
    </thead>
    <tbody>
        <?php
            
            for ($i=0; $i < $reservationList["count"]; $i++) {
                $tempDate = date("Y/m/d H:i",strtotime($reservationList["results"][$i]["date"]["iso"]));
                $tempName =  $reservationList["results"][$i]["user"]["username"];
                echo "<tr><th>$tempName</th>";
                echo "<th>$tempDate</th></tr>";
                
            }
            
        ?>
    </tbody>
</table>


<h1 id="label-of-table">訪問予約を行う</h1>

<?php
if(strlen($error)>0){
    echo "<font size =\"2\" color=\"#da0b00\">{$error}</font><p>";
}
?>
<form class="form-horizontal"　action ="" method="POST">
  <!--date-->
  <div class="control-group">
    <label class="control-label" for="date">訪問日程</label>
    <div class="controls controls-row">
      <select class="span2"　id="date" name ="year" size="1" >
        <option value="2013">2013</option>
        <option value="2014">2014</option>
      </select>

      <b class="span0"><font size="5">年</font></b>
      <select class="span1"　id="date" name ="month" size="1" >
        <?php
        for($i=1;$i<13;$i++):?>
        <option value=<?=$i?>><?=$i?></option>
        <?php endfor;?>
      </select>

      <b class="span0"><font size="5">月</font></b>
      <select class="span1"　id="date" name ="day" size="1" >
        <?php
        for($i=1;$i<32;$i++):?>
        <option value=<?=$i?>><?=$i?></option>
        <?php endfor;?>
      </select>

      <b class="span0"><font size="5">日</font></b>
      <select class="span1"　id="date" name ="hour" size="1" >
        <?php
        for($i=1;$i<25;$i++):?>
        <option value=<?=$i?>><?=$i?></option>
        <?php endfor;?>
      </select>
      <b class="span0"><font size="5">時</font></b>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <input type="submit" value="登録" name="submit" class="btn">
    </div>
  </div>
</form>

</div>

<script src="http://code.jquery.com/jquery-1.8.0.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>