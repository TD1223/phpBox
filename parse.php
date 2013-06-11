<?php
class parseCurl {
	//global変数
	private $appid="X-Parse-Application-Id: nQXJhDKYAgkqid3iq7AhVXUtuYjpmQmcKFyrZlCd";
	private $restkey="X-Parse-REST-API-Key: atWzrLNBuqNbh9rFzmUKUSMM3M2dvOtUS0ocSjBf";
	private $parseUrl="https://api.parse.com";

	private $contentTypeJSON = "Content-Type: application/json";

	//REST APIにアクセスした結果を格納する変数
	//json_decodeした後の連想配列
	public $results = array();

	public function parseCurl(){
	}

	/**
	* @param $url 
	* @return boolean (true:got json data, false: something's wrong)
	*
	**/
	public function getCurl ($url) {
		if (empty($url)) {
			trigger_error("param $url is empty on getCurl ($url) ",E_USER_ERROR);
			return false;
		}

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
		 $this->appid,
		 $this->restkey ) );
		$json = curl_exec( $ch );
		if ($json == false) {
			trigger_error("curl_exec failed",E_USER_ERROR);
			return false;
		}
		//echo "raw data<br>";
		//print_r($json);
		//echo "<br>";
		$this->results = json_decode($json, true);
		if ( $this->results == NULL) {
			trigger_error("json_decode failed",E_USER_ERROR);
			return false;
		} 

		curl_close($ch);
		return true;
	}

	/**
	* Functionality:"Logging in"の結果 => $temp
	*  $tempの内容の内、usernameとsessionIdを配列として返す
	* @param string $username   ユーザ名	
	*　@param string $password   パスワード
	* @return int length of array (2:got error response)
	**/
	public function login($username,$password) {
		if (empty($username) && empty($password)) {
			trigger_error("params is empty on login($username,$password) ",E_USER_ERROR);
			return false;
		}

		$url = $this->parseUrl;
		$url.= "/1/login";  //Functionality:"Logging in"

		//set request params
		$url.='?username='.urlencode($username).'&'; 
		$url.='password='.urlencode($password);

		// HTTP GET 
		if(!$this->getCurl($url)) {
			trigger_error("getCurl ($url) return false",E_USER_ERROR);
			return false;
		}
		
		return sizeof($this->results);
	}


	/**
	* RealEstate オブジェクトの一覧を取得する
	* 2次元の連想配列を返す
	* Array ( [0] => Array ( [address] => 東京都日野市高幡 [email] => mizutani.yuki@mail.com 
	*         [manager] => 水谷 [name] => グランコート青山 [phone] => 090-1234-5678 
	*         [createdAt] => 2013-06-04T13:38:02.843Z [updatedAt] => 2013-06-04T13:38:46.587Z 
	*         [objectId] => 9mvfKEPnnr ), [1] => Array (...) )
	*	@return boolean
	**/
	public function getAllRealEstate() {
		$url = $this->parseUrl;
		$url .= "/1/classes/RealEstate"; // Functionality: "Queries"
		// HTTP GET 
		if(!$this->getCurl($url)) {
			trigger_error("getCurl ($url) return false",E_USER_ERROR);
			return false;
		}
		//return $temp;
		// $tempの内容
		//	address,email,manager,name,phone,objectId,createdAt,updatedAt,objectId,ACL
		$resul = $this->results["results"];
		$this->results = $resul;
		return true;
	}


	/**
	* POST JSON Data To Parse
	*   
	* @param $url
	* @param $post_array
	* @return boolean
	*
	**/
	public function postCurl($url, $post_array) {
			if (empty($url) && empty($post_array)) {
				trigger_error("params is empty on postCurl($url, $post_array)",E_USER_ERROR);
				return false;
			}


			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_array);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
		 	$this->appid,
		 	$this->restkey,
		 	$this->contentTypeJSON
		 	 ) );
            $json = curl_exec( $ch );
            //print_r($json);
            if ($json == false) {
				trigger_error("curl_exec failed",E_USER_ERROR);
				return false;
            }
            $this->results = json_decode($json,true);
			//print_r($this->results);
			curl_close($ch);
			return true;

	}


	/**
	* get JSON data constrained by where ...
	* @param $attribute
	* @param $value
	* @param $object
	* @return var length of JSON data (-1:something is wrong, 0:doesn't exist matched data)
	*
	**/
	public function getConstrained($attribute,$value,$object) {
			
			if (empty($attribute) && empty($value)&& empty($object)) {
				trigger_error("params is empty on getConstrained($attribute,$value,$object)",E_USER_ERROR);
				return -1;
			}

			$url = $this->parseUrl;
			$url .= "/1/classes/$object";

			//set request params
			$json_param =json_encode(array($attribute=>$value));
			//echo $json_param; 
			//echo "<br>";
			$url.='?where='.urlencode($json_param);
			//echo $url; 
			//echo "<br>";
			if(!$this->getCurl($url)) {
				trigger_error("getCurl ($url) return false",E_USER_ERROR);
				return -1;
			}

			$resul = $this->results['results'];
			//echo  sizeof($json["results"]); // it should be 0
			$this->results = $resul;
			$len = sizeof($resul);  

			return $len; // the name value is not occupied yet.
	}

	/**
	*
	* POST RealEstateData
	* @param $name
	* @param $address
	* @param $manager
	* @param $email
	* @param $phone
	* @return -1:something is wrong,0:posted RE already exists, 1:posted RE success
	*
	**/
	public function postRealEstate($name,$address,$manager,$email,$phone) {
		if (empty($name) && empty($address)&& empty($manager)&& empty($email)&& empty($phone)) {
			trigger_error("params is empty on postRealEstate($name,$address,$manager,$email,$phone)",E_USER_ERROR);
			return -1;
		}

		$url = $this->parseUrl;
		$url .= "/1/classes/RealEstate";

		$json = json_encode(array(
			'name'=> $name,
			'address'=>$address,
			'manager'=>$manager,
			'email'=>$email,
			'phone'=>$phone));
		//echo $json;
		if($this->getConstrained("name",$name,"RealEstate") > 0) {
			// posted RE already exists
			return 0;
		}

		if (!$this->postCurl($url,$json)) {
			trigger_error("postCurl($url, $post_array) return false",E_USER_ERROR);
			return -1;	
		} 
		//print_r($this->results);
		return 1;
	}


	/**
	*
	* @param　$username
	* @param $password
	* @param $phone
	* @param $email	
	* 返り値：
	*	@return	-1 : error, 2 : Error code returned, 3 : user created
	**/  
	public function postUser($username,$password,$phone,$email) {
			if (empty($username) && empty($password)&& empty($phone)&& empty($email)) {
			trigger_error("params is empty on postUser($username,$password,$phone,$email)",E_USER_ERROR);
			return -1;
		}

			$url = $this->parseUrl;
			$url .= "/1/users";

			$json = json_encode(array(
				'username'=> $username,
				'password'=>$password,
				'phone'=>$phone,
				'email'=>$email));
			//echo $json;

			if (!$this->postCurl($url,$json)) {
				trigger_error("postCurl($url, $post_array) return false",E_USER_ERROR);
				return -1;	
			} 
			$len = sizeof($this->results);
			//print_r ($this->results);
			// errror user already exists

			return $len;
	}

	/**
	* put Object's ACL
	* @param string $object
	* @param string $objectId 
	* @param string $sessionId
	* @param boolean $read read access to *
	* @param boolean $write write access to *
	* @return -1:something is wrong, 1:put operation succeed, 2: error code returned
	**/
	public function putACL($object,$classId,$userId,$sessionToken,$read,$write) {
		$url = $this->parseUrl;
		if ($object=="User") {
			$url .= "/1/users/";
			$url .= $classId;
		} else {
			$url .= "/1/classes/".$object."/";
			$url .= $classId;
		}
		//echo $url;
		//echo "<br>";

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
	 	$this->appid,
	 	$this->restkey,
	 	"X-Parse-Session-Token: ".$sessionToken,
	 	$this->contentTypeJSON
	 	 ) );
        if ($write&&$read) {
	    	$acl = array( "ACL"=>array ($userId=>array("read"=>true, "write"=>true),
	        		"*"=>array("read"=>$read, "write"=>$write)));
	    }
	    elseif($write) {
	    	$acl = array( "ACL"=>array ($userId=>array("read"=>true, "write"=>true),
	        		"*"=>array("write"=>$write)));	
	    }
	    elseif($read) {
	    	$acl = array( "ACL"=>array ($userId=>array("read"=>true, "write"=>true),
	        		"*"=>array("read"=>$read)));
	    } 
	    else {
	    	$acl = array( "ACL"=>array ($userId=>array("read"=>true, "write"=>true)));
	    }
        $put_array = json_encode($acl);
        //echo $put_array;
       //echo "<br>";
        curl_setopt($ch,CURLOPT_POSTFIELDS,$put_array);

        $json = curl_exec( $ch );
        //echo $json;
        if ($json == NULL) {
			trigger_error("curl_exec failed",E_USER_ERROR);
			echo "failed";
			return -1;
        }
        
        $this->results = json_decode($json,true);
		curl_close($ch);
		$len = sizeof($this->results);
		//print_r($this->results);
		return $len;

	}

	/**
	* 予約ObjectをPOSTして作成する
	*　@param $string $userObject	登録するユーザ_UserのObjectID
	* @param $string $realEstateObject	登録するRealEstateのObjectID
	* @param $string $date　		ISO 8601で記述された予約の日付 (yyyy-mm-ddThh:mm:ssZ)
	* @return boolean True:POST成功 False:POST失敗
	**/
	public function createReservation ($userObject, $realEstateObject, $date) 
	{
		//$dateがISO 8601に沿っているかをチェック
		if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/',$date)) {
			trigger_error("$date がISO 8601に沿った文字列でありません",E_USER_ERROR);
			return false;
		} 
		//$userObject, $realEstateObjectがObjectIDかをチェック
		if (!preg_match('/^[a-zA-Z0-9]{10}$/',$realEstateObject)) {
			trigger_error("userObject($userObject) は10桁の英数字である必要があります",E_USER_ERROR);
			return false;
		} 
		if (!preg_match('/^[a-zA-Z0-9]{10}$/',$userObject)) {
			trigger_error("realEstateObject($realEstateObject) は10桁の英数字である必要があります",E_USER_ERROR);
			return false;
		} 

		//POST URL を作成
		$url = $this->parseUrl;
		$url .= "/1/classes/Reservation";

		$json = json_encode(array(
				'date'=> array('__type' => 'Date',
								'iso' => $date),
				'user'=> array('__type' => 'Pointer',
									'className' => '_User',
									'objectId' => $userObject),
				'realestate'=> array('__type' => 'Pointer',
									'className' => 'RealEstate',
									'objectId' => $realEstateObject),
				'ACL' => array ($userObject=>array("read"=>true, "write"=>true),
	        		"*"=>array("read"=>true))
				)
				);
		//echo "<br>";
		//echo $json;
		if (!$this->postCurl($url,$json)) {
					trigger_error("postCurl($url, $post_array) return false",E_USER_ERROR);
					return -1;	
		} 

		// POST成功！
		return true;
	}


	/**
	* 指定したユーザが予約したリストの一覧・リストの数を取得する
	* @param string $userObject ユーザのObjectID
 	* @param bool $count リスト数を結果に表示するかどうか
 	* @param bool $reInfo 取得するリストに不動産情報を載せるかどうか
	* @return bool クエリーが成功したか否か
	* false case: paramsが指定外のフォーマット, getCurlがエラーを返す
	**/
	public function getUserReservationList($userObject, $count, $reInfo) {
		//$userObjectがObjectIDかをチェック
		if (!preg_match('/^[a-zA-Z0-9]{10}$/',$userObject)) {
			trigger_error("userObject($userObject) は10桁の英数字である必要があります",E_USER_ERROR);
			return false;
		} 
		//$sortがboolであることをチェック
		if (!preg_match('/^[01]$/',$count)) {
			trigger_error("count($count) はbooleanである必要があります",E_USER_ERROR);
			return false;
		} 
		//$reInfoがboolであることをチェック
		if (!preg_match('/^[01]$/',$reInfo)) {
			trigger_error("reInfo($reInfo) はbooleanである必要があります",E_USER_ERROR);
			return false;
		} 

		//GET URLを作成
		$url = $this->parseUrl;
		$url .= "/1/classes/Reservation";
		$json_param =json_encode(array('user'=>array(
												'__type' => 'Pointer',
												'className'=>'_User',
												'objectId'=>$userObject)));
		//echo $json_param; 
		//echo "<br>";
		$url.='?where='.urlencode($json_param);
		
		//リスト数を結果に載せるかどうかを設定
		if($count)	$url.="&count=1";
		//結果のリストに載せる情報を指定する（realestate information）
		if ($reInfo) $url.="&include=realestate";

		//echo $url."<br>";

		// HTTP GET 
		
		if(!$this->getCurl($url)) {
			trigger_error("getCurl ($url) return false",E_USER_ERROR);
			return false;
		}
		
		//GET 成功！
		return true;	
	}

	/**
	* 指定したRealEstateの予約リストの一覧・リストの数を取得する
	* @param string $reObject RealEstateのObjectID
 	* @param bool $count リスト数を結果に表示するかどうか
 	* @param bool $userInfo 取得するリストにユーザ情報を載せるかどうか
 	* @param bool $reInfo 取得するリストに不動産情報を載せるかどうか
	* @return bool クエリーが成功したか否か
	* false case: paramsが指定外のフォーマット, getCurlがエラーを返す
	**/
	public function getREReservationList($reObject, $count,$userInfo,$reInfo) {
		//$userObjectがObjectIDかをチェック
		if (!preg_match('/^[a-zA-Z0-9]{10}$/',$reObject)) {
			trigger_error("reObject($reObject) は10桁の英数字である必要があります",E_USER_ERROR);
			return false;
		} 
		//$sortがboolであることをチェック
		if (!preg_match('/^[01]$/',$count)) {
			trigger_error("count($count) はbooleanである必要があります",E_USER_ERROR);
			return false;
		} 
		//$userInfoがboolであることをチェック
		if (!preg_match('/^[01]$/',$userInfo)) {
			trigger_error("userInfo($userInfo) はbooleanである必要があります",E_USER_ERROR);
			return false;
		} 
		//$reInfoがboolであることをチェック
		if (!preg_match('/^[01]$/',$reInfo)) {
			trigger_error("reInfo($reInfo) はbooleanである必要があります",E_USER_ERROR);
			return false;
		} 

		//GET URLを作成
		$url = $this->parseUrl;
		$url .= "/1/classes/Reservation";
		$json_param =json_encode(array('realestate'=>array(
												'__type' => 'Pointer',
												'className'=>'RealEstate',
												'objectId'=>$reObject)));
		//echo $json_param; 
		//echo "<br>";
		$url.='?where='.urlencode($json_param);

		//リスト数を結果に載せるかどうかを設定
		if($count)	$url.="&count=1";

		//結果のリストに載せる情報を指定する（user,realestate information）
		if ($userInfo && $reInfo) $url.="&include=user,realestate";
		elseif ($userInfo) $url.="&include=user";
		elseif ($reInfo) $url.="&include=realestate";

		//echo $url."<br>";

		// HTTP GET 
		
		if(!$this->getCurl($url)) {
			trigger_error("getCurl ($url) return false",E_USER_ERROR);
			return false;
		}
		
		//GET 成功！
		return true;	
	}

	/**
	* 1時間単位で予約をユニークなものにする
	*　そのために23時代の物があるかどうかをチェックするメソッド
	* @param string $date ISO8601 指定(yyyy-mm-ddThh)
	* @param string $realEstateObject
	* @return int -1:error, 0:指定された時間の予約はまだない, 1:指定された時間は既に予約されている
	*
	**/
	public function checkExistingReservation($date, $realEstateObject){
		//$dateが指定されたフォーマットに沿っているかをチェック
		if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}$/',$date)) {
			trigger_error("$date が指定されたフォーマットでありません",E_USER_ERROR);
			return -1;
		} 
		//$userObject, $realEstateObjectがObjectIDかをチェック
		if (!preg_match('/^[a-zA-Z0-9]{10}$/',$realEstateObject)) {
			trigger_error("userObject($userObject) は10桁の英数字である必要があります",E_USER_ERROR);
			return -1;
		} 

		$dateLte = $date.":59:59Z";
		$dateGte = $date.":00:00Z";
		
		//GET URLを作成
		$url = $this->parseUrl;
		$url .= "/1/classes/Reservation";

		$json_param =json_encode(array('date'=>array(
												"\$gte"=>array("__type"=>"Date","iso"=>$dateGte),
												"\$lte"=>array("__type"=>"Date","iso"=>$dateLte)),
										'realestate'=>array(
												'__type' => 'Pointer',
												'className'=>'RealEstate',
												'objectId'=>$realEstateObject)
												));
										
		//echo $json_param; 
		//echo "<br>";
		$url.='?where='.urlencode($json_param);

		// HTTP GET 
		if(!$this->getCurl($url)) {
			trigger_error("getCurl ($url) return false",E_USER_ERROR);
			return -1;
		}
		
		$len = sizeof($this->results["results"]);
		//GET 成功！
		//echo $len."<br>";
		return $len;	
		
		



		
	}


}



?>