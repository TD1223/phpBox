<html>
<head></head>
<body>
$url = "https://www.google.co.jp/";
//$url = "http://www.yahoo.co.jp";
$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, $url );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

$result = curl_exec( $ch );
curl_close($ch);

echo $result;
</body>
</html>
