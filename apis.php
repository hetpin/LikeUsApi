<?php
include_once './db_connect.php';
// connecting to database
$db = new DB_Connect();
$db->connect();
//set_time_limit(180);

if (isset($_GET['api'])){

	if ($_GET['api'] == "register_android") {
		$isValid = false;
		if(isset($_POST['latitude']) && isset($_POST['longitude']) && isset($_POST['gcm_id']) && 
			isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone']) && 
			isset($_POST['sex']) && isset($_POST['age'])&& isset($_POST['password']))
		{
			$_lat = $_POST['latitude'];
			$_lng = $_POST['longitude'];
			$_name = $_POST['name'];
			$_email = $_POST['email'];
			$_phone = $_POST['phone'];
			$_sex = $_POST['sex'];
			$_age = $_POST['age'];
			$_gcm_id = $_POST['gcm_id'];
			$_pwd_ = $_POST['password'];
			$_devide_type = 0;

			if ($_name != "" && $_pwd_ != "" && $_email != "") $isValid = true;
			
		}else{
			echo "{\"success\":\"false\",\"message\":\"Missing parameters\"}";
		}
		
		if($isValid)
		{
			//** Check db and insert new one if it is not existed
			$result = mysql_query("SELECT count(*) as count FROM `gcm_users` WHERE name = '" . $_name . "'");
			$row = mysql_fetch_array($result);
			if ($row['count'] > 0) 
			{
				echo "{\"success\":\"false\",\"message\":\"the username already exists\"}";
			}
			else 
			{
				$image = $_FILES['image']['name'];
				if ($image) 
				{
					$filename = stripslashes($_FILES['image']['name']);
					
					include('SimpleImage.php'); 
					$image = new SimpleImage(); 
					$image_name = time() . '.jpg';
					$newname = "images/" . $image_name;
					$copied = copy($_FILES['image']['tmp_name'], $newname);
					if (!$copied) 
					{
						echo "{\"success\":\"false\",\"message\":\"copy unsuccessfull!\"}";
						$errors = 1;
					} 
					else 
					{
						$image->load($newname); 
						$normal = 'images/normal/' . $image_name;
						$small = 'images/small/' . $image_name;
						$thumbnail = 'images/thumbnail/' . $image_name;
						$image->resize(300,300); 
						$image->save($normal);
						$image->resize(150,150); 
						$image->save($small);
						$image->resize(50,50); 
						$image->save($thumbnail);
						unlink($newname);

						$_query = 'INSERT INTO `gcm_users` (gcm_regid, name, password, email, device_type, image, latitude, longitude, update_time, phone, sex, age, status,is_liked,is_following) 
										VALUES ("'.$_gcm_id.',","'.$_name.'","'.$_pwd_.'","'.$_email.'",'.$_devide_type.',"'.$image_name.'",'.$_lat.','.$_lng.',"'.date("H:m:s d:m:Y").'","'.$_phone.'",'.$_sex.','.$_age.',1,"","")';
						
						$result = mysql_query($_query);
						if($result === TRUE){
							echo "{\"success\":\"true\",\"message\":\"".mysql_insert_id()."\"}";
						}else{
							echo "{\"success\":\"false\",\"message\":\"".mysql_error()."\"}";
						}
					}
				}
				else 
				{
					echo "{\"success\":\"false\",\"message\":\"no image upload\"}";
				}
			}
		}
		else
		{
			echo "{\"success\":\"false\",\"message\":\"Missing parameters\"}";
		}
		
	}
	else
	if ($_GET['api'] == "login"){

		$user_info = array();
		$_gcm_id = "";

		if (isset($_POST["password"]) && isset($_POST["gcm_id"]) && isset($_POST["name"]) && isset($_POST['latitude']) && isset($_POST['longitude'])){
			$_pwd_ = $_POST["password"];
			$_name = $_POST["name"];
			$_lat = $_POST["latitude"];
			$_lng = $_POST["longitude"];

			$result = mysql_query('SELECT * FROM `gcm_users` WHERE name = "'.$_name.'" AND password = "'.$_pwd_.'"');
		    if (mysql_num_rows($result) == 1){
		        $user_info = mysql_fetch_row($result);
		        if(strpos($user_info[LIKE_GCM_REGID], $_POST["gcm_id"].',') === FALSE){
					$_gcm_id = $user_info[LIKE_GCM_REGID].$_POST["gcm_id"].','; // append
				}else{
					$_gcm_id = $user_info[LIKE_GCM_REGID];
				}
		    }else{
				echo "{\"success\":\"false\",\"message\":\"Login fail! Username or password is incorrect\"}";
		    }
			
		}else{
			echo "{\"success\":\"false\",\"message\":\"Login fail! Missing parameters\"}";
		}

		if (is_array($user_info) && count($user_info) > 0 && updateInfo($user_info[LIKE_ID], array('gcm_regid', 'latitude', 'longitude', 'status'), array('"'.$_gcm_id.'"',$_lat, $_lng, 1))){
			$profile = array('profile' => array(
				'id' => $user_info[LIKE_ID],
				'gcm_regid' => $user_info[LIKE_GCM_REGID],
				'name' => $user_info[LIKE_NAME],
				'email' => $user_info[LIKE_EMAIL],
				'image' => $user_info[LIKE_IMAGE],
				'latitude' => $user_info[LIKE_LATITUDE],
				'longitude' => $user_info[LIKE_LONGITUDE],
				'update_time' => $user_info[LIKE_UPDATE_TIME],
				'phone' => $user_info[LIKE_PHONE],
				'sex' => $user_info[LIKE_SEX],
				'age' => $user_info[LIKE_AGE],
				'status' => $user_info[LIKE_STATUS],
				'is_liked' => $user_info[LIKE_IS_LIKED],
				'is_following' => $user_info[LIKE_IS_FOLLOWING]));

			$resultContent = array('success'=>'true', 
									'time'=>date('H:m:s d:m:Y'), 
									'profile' => array(
												'id' => $user_info[LIKE_ID],
												'gcm_regid' => $user_info[LIKE_GCM_REGID],
												'name' => $user_info[LIKE_NAME],
												'email' => $user_info[LIKE_EMAIL],
												'image' => $user_info[LIKE_IMAGE],
												'latitude' => $user_info[LIKE_LATITUDE],
												'longitude' => $user_info[LIKE_LONGITUDE],
												'update_time' => $user_info[LIKE_UPDATE_TIME],
												'phone' => $user_info[LIKE_PHONE],
												'sex' => $user_info[LIKE_SEX],
												'age' => $user_info[LIKE_AGE],
												'status' => $user_info[LIKE_STATUS],
												'is_liked' => $user_info[LIKE_IS_LIKED],
												'is_following' => $user_info[LIKE_IS_FOLLOWING])
									);
			echo json_encode($resultContent);
		}else{
			echo "{\"success\":\"false\",\"message\":\"Login fail\"}";
		}
	}
	else
	if ($_GET['api'] == "logout"){
		$_error = FALSE;
		if (isset($_POST["gcm_id"]) && isset($_POST['user_id']) && intval($_POST['user_id']) > 0){
			$_gcm_id = $_POST["gcm_id"];
			$_uuid = $_POST['user_id'];

			$user_info = getUserByID($_uuid);
			if ($user_info !== NULL){
				$_gcm_id = str_replace($_POST["gcm_id"].',', '', $user_info[LIKE_GCM_REGID]);

				if (updateInfo($user_info[LIKE_ID], array('gcm_regid','status'), array('"'.$_gcm_id.'"',0)) ){
					echo "{\"success\":\"true\",\"message\":\"".$user_info[LIKE_ID]."\"}";
				}else{
					echo "{\"success\":\"false\",\"message\":\"Logout fail! Cannot switch status mode\"}";
				}
			}else{
				echo "{\"success\":\"false\",\"message\":\"Not found the account\"}";
			}
		    
		}else{
			echo "{\"success\":\"false\",\"message\":\"Logout fail! Missing parameters\"}";
		}
	} 
	else
	if ($_GET['api'] == "post_token") {
		//{"email":"testmail@tesmail.com","device_token":"fdgsgfag4543ckjva9gda","device_type":"0"}
		$content = $_POST["content"];
		$content = str_replace("\\", "", $content);
		$register = json_decode($content);
		if ($register === null) {
			echo "json string error";
		} else {
			$result = mysql_query("SELECT count(*) as count FROM `gcm_users` WHERE email = '" . $register -> email . "'");
			$row = mysql_fetch_array($result);
			if ($row['count'] > 0) {
				if (mysql_query("UPDATE `gcm_users` SET token = '" . $register -> device_token . "', device_type  = '" . $register -> device_type . "' WHERE email = '" . $register -> email . "'")) {
					echo "{\"success\":\"true\",\"message\":\"post token success\"}";
				} else {
					echo "{\"success\":\"false\",\"message\":\"post token false\"}";
				}
			} else {
				echo "{\"success\":\"false\",\"message\":\"email does not exists\"}";
			}
		}
	}
    else
    if ($_GET['api'] == "post_pushtouser") {
		//{"email":"testmail@tesmail.com","device_token":"fdgsgfag4543ckjva9gda","device_type":"0"}
		$content = $_POST["content"];
		$content = str_replace("\\", "", $content);
		$register = json_decode($content);
		if ($register === null) {
			echo "json string error";
		} else {
			$result = mysql_query("SELECT token FROM `gcm_users` WHERE email = '" . $register -> email . "'");
			$row = mysql_fetch_array($result);
			if ($row['count'] > 0) {
                echo "{\"success\":\"true\",\"message\":\"successful\"}";
			} else {
				echo "{\"success\":\"false\",\"message\":\"not have token\"}";
			}
		}
	}
	else
	if ($_GET['api'] == "get_users") {
		$fetch = mysql_query("SELECT * FROM gcm_users");

		$arrayPoints = array();
		while ($row = mysql_fetch_array($fetch)) {
			$arrPoint['id'] = $row['id'];
			$arrPoint['name'] = $row['name'];
			$arrPoint['user_location'] = $row['latitude']."; ".$row['longitude'];	
            $arrPoint['email'] = $row['email'];
            $arrPoint['image'] = $row['image'];
			array_push($arrayPoints, $arrPoint);
		}
		$json = json_encode($arrayPoints);
		$json = str_replace("\\/", "/", $json);
		$json = str_replace("\\\\", "\\", $json);
		$json = str_replace("[", "{\"users\":[", $json);
		$json = str_replace("]", "]}", $json);
		echo $json;
	}
	else
	if ($_GET['api'] == 'mutual_like' && isset($_POST['des_id']) && intval($_POST['des_id']) > 0 && isset($_POST['user_id']) && intval($_POST['user_id']) > 0) {
		$_mutArr = getMutualFriends($_POST['user_id'],$_POST['des_id']);
		if ($_mutArr != NULL){
			for ($i=0; $i < count($_mutArr); $i++) {
				$rRow = getUserByID($_mutArr[$i]);
				$members[] = array('member' => array(
					'id' => $rRow[LIKE_ID],
					'gcm_regid' => $rRow[LIKE_GCM_REGID],
					'name' => $rRow[LIKE_NAME],
					'email' => $rRow[LIKE_EMAIL],
					'image' => $rRow[LIKE_IMAGE],
					'latitude' => $rRow[LIKE_LATITUDE],
					'longitude' => $rRow[LIKE_LONGITUDE],
					'update_time' => $rRow[LIKE_UPDATE_TIME],
					'phone' => $rRow[LIKE_PHONE],
					'sex' => $rRow[LIKE_SEX],
					'age' => $rRow[LIKE_AGE],
					'status' => $rRow[LIKE_STATUS],
					'is_liked' => $rRow[LIKE_IS_LIKED],
					'is_following' => $rRow[LIKE_IS_FOLLOWING]));
			}

			$resultContent = array('success'=>'true', 
									'time'=>date('H:m:s d:m:Y'), 
									'user_id'=> $_POST['user_id'], 
									'des_id'=>$_POST['des_id'],
									'members'=>$members);
			echo json_encode($resultContent);
		}else{
			echo "{\"success\":\"false\",\"message\":\"Not found any mutual friends\"}";
		}
	}
	else
	if (($_GET['api'] == 'following_me' || $_GET['api'] == 'i_liked') && isset($_POST['user_id']) && intval($_POST['user_id']) > 0){
		$_userRow = getUserByID($_POST['user_id']);
		if ($_userRow != NULL){
			$_memberArr = array();
			if ($_GET['api'] == 'following_me'){
				$_memberArr = explode(',', $_userRow[LIKE_IS_LIKED]);
			}else{
				$_memberArr = explode(',', $_userRow[LIKE_IS_FOLLOWING]);
			}
			for ($i=0; $i < count($_memberArr); $i++) {
				$rRow = getUserByID($_memberArr[$i]);
				$members[] = array('member' => array(
					'id' => $rRow[LIKE_ID],
					'gcm_regid' => $rRow[LIKE_GCM_REGID],
					'name' => $rRow[LIKE_NAME],
					'email' => $rRow[LIKE_EMAIL],
					'image' => $rRow[LIKE_IMAGE],
					'latitude' => $rRow[LIKE_LATITUDE],
					'longitude' => $rRow[LIKE_LONGITUDE],
					'update_time' => $rRow[LIKE_UPDATE_TIME],
					'phone' => $rRow[LIKE_PHONE],
					'sex' => $rRow[LIKE_SEX],
					'age' => $rRow[LIKE_AGE],
					'status' => $rRow[LIKE_STATUS],
					'is_liked' => $rRow[LIKE_IS_LIKED],
					'is_following' => $rRow[LIKE_IS_FOLLOWING]));
			}

			$resultContent = array('success'=>'true', 
									'time'=>date('H:m:s d:m:Y'), 
									'members'=>$members);
			echo json_encode($resultContent);
		}else{
			echo "{\"success\":\"false\",\"message\":\"Not found any mutual friends\"}";
		}
	}
	else
	if (($_GET['api'] == "like" || $_GET['api'] == "send_message") && isset($_POST['des_id']) && intval($_POST['des_id']) > 0 && isset($_POST['user_id']) && intval($_POST['user_id']) > 0) {
		if (isset($_POST["des_id"])) {
		    $_receiverId = $_POST["des_id"];
		    $_senderId = $_POST['user_id'];
		    
		    // Store user details in db
		    include_once './GCM.php';

		    $gcm = new GCM();

		    $_check_is_liked = FALSE;
		    // check sender id

		    $sender = getUserByID($_senderId);
		    $receiver = getUserByID($_receiverId);
		    if($sender != NULL && $receiver != NULL && intval($sender[LIKE_STATUS]) == 1 && intval($receiver[LIKE_STATUS]) == 1){
				$headerMsg=str_pad($sender[2],20,' ',STR_PAD_RIGHT);
		        $contentMsg = "";
		        if($_GET['api'] == "like"){
			        $contentMsg = $headerMsg." like your profile!\n- ".date("H:i:s d-m-Y")." -\n";
			        $_check_is_liked = addFriend($_senderId,$_receiverId);
			    }else{
			    	if(isset($_POST["message"])) {
			    		$msg = $_POST["message"];
			        	$contentMsg = $headerMsg."\n".$msg."\n- ".date("H:i:s d-m-Y")." -\n";
			        	$_check_is_liked = TRUE;
			        }
			    }

				if($_check_is_liked == TRUE){
			        $registatoin_ids = explode(',', $receiver[LIKE_GCM_REGID]);
			        $message = array("price" => $contentMsg);
	        		$result = $gcm->send_notification($registatoin_ids, $message);
			        echo $result;
			        $errors = 1;
			    }
			}
		   
		} else {
	        echo "{\"success\":\"false\",\"message\":\"Invalid Request\"}";
	        $errors = 1;
		}

	}
	else
	if ($_GET['api'] == "regist") {
		//{"name":"name test","email":"testmail@mail.com"}
		$content = $_GET["content"];
		$content = str_replace("\\", "", $content);
		$register = json_decode($content);
		if ($register === null) {
			echo "json string error";
		} else {
			$result = mysql_query("SELECT count(*) as count FROM `gcm_users` WHERE email = '" . $register -> email . "'");
			$row = mysql_fetch_array($result);
			if ($row['count'] > 0) {
				echo "{\"success\":\"false\",\"message\":\"email already exists\"}";
			} else {
				//reads the name of the file the user submitted for uploading
				$image = $_FILES['image']['name'];
				// if it is not empty
				if ($image) {
					// get the original name of the file from the clients machine
					$filename = stripslashes($_FILES['image']['name']);
	
					// get the extension of the file in a lower case format
					$extension = getExtension($filename);
					$extension = strtolower($extension);
					// if it is not a known extension, we will suppose it is an error, print an error message
					//and will not upload the file, otherwise we continue
					if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png")) {
						echo "{\"success\":\"false\",\"message\":\"acceptable file types: jpg, jpeg, png\"}";
						$errors = 1;
					} else {
						// get the size of the image in bytes
						// $_FILES[\'image\'][\'tmp_name\'] is the temporary filename of the file in which the uploaded file was stored on the server
						$size = getimagesize($_FILES['image']['tmp_name']);
						$sizekb = filesize($_FILES['image']['tmp_name']);
	
						//compare the size with the maxim size we defined and print error if bigger
						if ($sizekb > 100 * 1024) {
							/*echo '<h1>You have exceeded the size limit!</h1>';*/
							$errors = 1;
						}
	
						//we will give an unique name, for example the time in unix time format
						$image_name = time() . '.' . $extension;
						//the new name will be containing the full path where will be stored (images folder)
	
						$newname = "images/" . $image_name;
						$copied = copy($_FILES['image']['tmp_name'], $newname);
						//we verify if the image has been uploaded, and print error instead
						if (!$copied) {
							echo "{\"success\":\"false\",\"message\":\"copy unsuccessfull!\"}";
							$errors = 1;
						} else {
							make_thumb($newname, $newname, 800, 600);
							// the new thumbnail image will be placed in images/thumbs/ folder
							$normal = 'images/normal/' . $image_name;
							$small = 'images/small/' . $image_name;
							$thumbnail = 'images/thumbnail/' . $image_name;
							// call the function that will create the thumbnail. The function will get as parameters
							//the image name, the thumbnail name and the width and height desired for the thumbnail
							$normal_name = make_thumb($newname, $normal, 300, 300);
							$small_name = make_thumb($newname, $small, 150, 150);
							$thumbnail_name = make_thumb($newname, $thumbnail, 50, 50);
							
							unlink($newname);
	
							mysql_query("INSERT INTO `gcm_users` (gcm_regid,name,image,email,user_location) VALUES ('" . $register -> uuid . "','" . $register -> name . "','" . $image_name . "','" . $register -> email . "','" . $register -> location . "')");
							echo "{\"success\":\"true\",\"message\":\"add offer success.\"}";
						}
					}
				} else {
					echo "{\"success\":\"false\",\"message\":\"no image upload\"}";
				}
			}
		}
	}
	else
    if ($_GET['api'] == "near_by" && isset($_POST['radius']) && doubleval($_POST['radius']) > 0 && 
    	isset($_POST['latitude']) && doubleval($_POST['latitude']) > 0 && 
    	isset($_POST['longitude']) && doubleval($_POST['longitude']) > 0 ) {
		
		$_r = doubleval($_POST['radius']);
		$_r = UNIT_FEET_200*$_r;
		if ($_r > 0){
			$_lat = doubleval($_POST['latitude']);
			$_lng = doubleval($_POST['longitude']);
			
			$_query_square = "SELECT * FROM `gcm_users` WHERE ABS(latitude-".$_lat.") <= ".$_r." AND ABS(longitude-".$_lng.") <=".$_r;

			$_result = mysql_query($_query_square);


			$members = array();

			if ($_result === false) {
				echo "Error: ".msql_error();
			}else{
				while($rRow = mysql_fetch_array($_result))
				{
					$_lt = doubleval($rRow[LIKE_LATITUDE]);
					$_lg = doubleval($rRow[LIKE_LONGITUDE]);
					$_distance = sqrt( pow($_lt - $_lat, 2) + pow($_lg - $_lng, 2));
					if(intval($rRow[LIKE_STATUS]) == 1 && $_distance <= $_r){

						$members[] = array('member' => array(
							'id' => $rRow[LIKE_ID],
							'gcm_regid' => $rRow[LIKE_GCM_REGID],
							'name' => $rRow[LIKE_NAME],
							'email' => $rRow[LIKE_EMAIL],
							'image' => $rRow[LIKE_IMAGE],
							'latitude' => $rRow[LIKE_LATITUDE],
							'longitude' => $rRow[LIKE_LONGITUDE],
							'update_time' => $rRow[LIKE_UPDATE_TIME],
							'phone' => $rRow[LIKE_PHONE],
							'sex' => $rRow[LIKE_SEX],
							'age' => $rRow[LIKE_AGE],
							'status' => $rRow[LIKE_STATUS],
							'is_liked' => $rRow[LIKE_IS_LIKED],
							'is_following' => $rRow[LIKE_IS_FOLLOWING],
							'distance'=>$_distance/UNIT_FEET_200));
					}
				}
			}

			$resultContent = array('success'=>'true', 
									'time'=>date('H:m:s d:m:Y'), 
									'latitude'=> $_lat, 
									'longitude'=>$_lng,
									'radius'=>$_r,
									'members'=>$members);
			echo json_encode($resultContent);
			
		}else{
			echo "{\"success\":\"false\",\"message\":\"R must be greater than 0\"}";
		}
	}
	else
	if ($_GET['api'] == "update_status"){
		if(isset($_POST['status'])){
			$_status = intval($_POST['status']);
			if($_status == 1 || $_status == 0){
				$result = updateInfo($_uuid,array('status'),array($_status));
				if ($result === TRUE){
					echo "{\"success\":\"true\",\"message\":\"Update status for $_uuid\"}";
				}else{
					echo "{\"success\":\"false\",\"message\":\"Update status for ".$_uuid.": ".mysql_error()."\"}";
				}
			}else{
					echo "{\"success\":\"false\",\"message\":\"Update status for ".$_uuid.": Status must be 0 or 1\"}";
				}
		}
	}
	else
	if ($_GET['api'] == "update_avatar" && isset($_POST['user_id']) && intval($_POST['user_id']) > 0){
		$image = $_FILES['image']['name'];
		if ($image) 
		{
			$filename = stripslashes($_FILES['image']['name']);
			$extension = getExtension($filename);
			$extension = strtolower($extension);
			if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif") ) 
			{
				echo "{\"success\":\"false\",\"message\":\"acceptable file types: jpg, jpeg, png, gif\"}";
				$errors = 1;
			} 
			else 
			{

				include('SimpleImage.php'); 
				$image = new SimpleImage(); 
				$image_name = time() . '.jpg';
				$newname = "images/" . $image_name;
				$copied = copy($_FILES['image']['tmp_name'], $newname);
				if (!$copied) 
				{
					echo "{\"success\":\"false\",\"message\":\"copy unsuccessfull!\"}";
					$errors = 1;
				} 
				else 
				{
					$image->load($newname); 
					$normal = 'images/normal/' . $image_name;
					$small = 'images/small/' . $image_name;
					$thumbnail = 'images/thumbnail/' . $image_name;
					$image->resize(300,300); 
					$image->save($normal);
					$image->resize(150,150); 
					$image->save($small);
					$image->resize(50,50); 
					$image->save($thumbnail);
					unlink($newname);
					$_userInfo = getUserByID($_POST['user_id']);
					$result = updateInfo($_POST['user_id'],array('image'),array('"'.$image_name.'"'));
					if($result === TRUE)
					{
						if(file_exists('images/normal/'.$_userInfo[LIKE_IMAGE])) unlink('images/normal/'.$_userInfo[LIKE_IMAGE]);
						if(file_exists('images/small/'.$_userInfo[LIKE_IMAGE])) unlink('images/small/'.$_userInfo[LIKE_IMAGE]);
						if(file_exists('images/thumbnail/'.$_userInfo[LIKE_IMAGE])) unlink('images/thumbnail/'.$_userInfo[LIKE_IMAGE]);
						echo "{\"success\":\"true\",\"message\":\"Update avatar for ".$_POST['user_id']."\"}";
					}else{
						echo "{\"success\":\"false\",\"message\":\"".mysql_error()."\"}";
					}
				}
			}
		}
	}

	if (isset($_POST['user_id']) && isset($_POST['latitude']) && isset($_POST['longitude'])){
		$_uuid = $_POST['user_id'];
		$_lat = $_POST['latitude'];
		$_lng = $_POST['longitude'];
		updateInfo($_uuid, array('latitude','longitude'),array($_lat, $_lng));
	}

}else{
	echo "{\"success\":\"false\",\"message\":\"parameters is undefined!\"}";
}

/* FUNCTIONS */
//--------- Select Data -----------
function checkFriendStatus($iid, $uid){
	$_iid = getUserByID($iid);
	if ($_iid != NULL){
		$_isFollowing = FALSE;
		if(strpos($_iid[LIKE_IS_FOLLOWING], $uid) === TRUE){
			$_is_following = TRUE;
		}
		$_is_liked = FALSE;
		if(strpos($_iid[LIKE_IS_LIKED], $uid) === TRUE){
			$_is_liked = TRUE;
		}
		if($_is_liked == TRUE && $_is_following == TRUE){
			return 'friend';
		}else if ($_is_liked == TRUE){
			return 'is_liked';
		}else if($_is_following == TRUE){
			return 'is_following';
		}else{
			return 'none';
		}
	}
	return NULL;
}


function getFriendList($user_id){
	$sender = getUserByID($user_id);
	if ($sender != NULL){
		$_is_following = explode(',', $sender[LIKE_IS_FOLLOWING]);
		$_is_liked = explode(',', $sender[LIKE_IS_LIKED]);
		$_friendList = array_intersect($_is_liked, $_is_following);
		return $_friendList;
	}
	return NULL;
}


function getMutualFriends($iid, $uid){
	$_iid = getFriendList($iid);
	$_uid = getFriendList($uid);
	if ($_iid != NULL && $_uid != NULL){
		return array_intersect($_iid, $_uid);
	}
	return NULL;
}


function getUserByID($id) {
	if(intval($id) > 0){
		$_query = 'SELECT * FROM `gcm_users` WHERE id = '.$id;
		
	    $result = mysql_query($_query);
	    if ($result){
	    	if (mysql_num_rows($result) == 1){
		        return mysql_fetch_row($result);
		    }
	    }
	    echo "Error in getUserByID: ".mysql_error();
	}
    return NULL;
}


//--------- Update Data --------------

function addFriend($iid, $uid){
	if (intval($iid) >0 && intval($uid) > 0 && $iid !== $uid){

		$_iid = getUserByID($iid);
		$_uid = getUserByID($uid);
		if ($_iid != NULL && $_uid != NULL){
			$_i_is_following = $_iid[LIKE_IS_FOLLOWING];
			$_i_is_liked = $_iid[LIKE_IS_LIKED];
			$_u_is_following = $_uid[LIKE_IS_FOLLOWING];
			$_u_is_liked = $_uid[LIKE_IS_LIKED];

			if (strpos($_i_is_following, $uid.",") === FALSE){
	        	$_i_is_following = $_i_is_following.$uid.",";
	        	$_query = 'UPDATE `gcm_users` SET is_following = "'.$_i_is_following.'" WHERE id = '.$iid;
				$result = mysql_query($_query);
	        	$_check_following = $result; // chua following uid -> co the like
	        }else{
	        	$_check_following = FALSE; // da following uid
	        }

	        if (strpos($_u_is_liked, $iid.",") === FALSE){
	        	$_u_is_liked = $_u_is_liked.$iid.",";
	        	$_query = 'UPDATE `gcm_users` SET is_liked = "'.$_u_is_liked.'" WHERE id = '.$uid;
				$result = mysql_query($_query);
	        	$_check_liked = $result; // chua duoc liked -> co the like
	        }else{
	        	$_check_liked = FALSE; // da duoc liked
	        }
	        
	        return $_check_liked && $_check_following;
		}
	}
	return FALSE;
}


function updateInfo($id,$fieldNameArr, $fieldValueArr){
	if ( intval($id) < 1 || is_array($fieldNameArr) == FALSE || is_array($fieldValueArr) == FALSE || count($fieldNameArr) != count($fieldValueArr)){
		echo 'The number of fields of Name Array is not matched with the Value Array\'s';
		return FALSE;
	}

	mysql_query("SET AUTOCOMMIT=0");
	mysql_query("BEGIN");

	$_query = 'UPDATE `gcm_users` SET';
	
	for ($i=0; $i < count($fieldNameArr) - 1; $i++) { 
		$_query = $_query.' '.$fieldNameArr[$i].' = '.$fieldValueArr[$i].',';
	}

	$_query = $_query.' '.$fieldNameArr[count($fieldNameArr) - 1].' = '.$fieldValueArr[count($fieldValueArr) - 1].' WHERE id = '.$id;
	

	$result = mysql_query($_query);
	
	if ($result) {
	    mysql_query("COMMIT");
	    return TRUE;
	} else {        
	    mysql_query("ROLLBACK");
		echo "updateInfoById:".mysql_error()."<br/>";
		return FALSE;
	}
}

// --------- Fake Data ---------------
function fakeData($from, $to){
	for ($i=$from; $i < $to; $i++) { 
		$_uuid = "uuid_".$i;
		$_name = "name_".$i;
		$_email = "email_".$i.'@email.com';
		$_phone = "phone_".$i;
		$_sex = 0;
		$_age = 20;
		$_devide_type = 0;
		$image_name = "image_".$i;
		$_lat = getdoublenumbers(21,21,0000001,9999999);
		$_lng = getdoublenumbers(105,105,0000001,9999999);
		$_query = 'INSERT INTO `gcm_users` (gcm_regid, name, password, email, device_type, image, latitude, longitude, update_time, phone, sex, age,is_liked,is_following) 
						VALUES ("'.$_uuid.',","'.$_name.'","'.$_name.'","'.$_email.'",'.$_devide_type.',"'.$image_name.'",'.$_lat.','.$_lng.',"'.date("H:m:s d:m:Y").'","'.$_phone.'",'.$_sex.','.$_age.',"","")';
		
		$result = mysql_query($_query);
		if($result === TRUE){
			echo $i.": ".mysql_insert_id()."<br/>";
		}else{
			echo $i.": ".mysql_error()."<br/>";
		}
	}

}


function getdoublenumbers($units_min,$units_max,$tens_min, $tens_max){
	$units = rand($units_min, $units_max);
	$tens = rand($tens_min, $tens_max);
	$doublenumber = "$units.$tens";
	return doubleval($doublenumber);
}

if(isset($_GET['fakeDataFrom']) && isset($_GET['fakeDataTo'])){
	$_from = intval($_GET['fakeDataFrom']);
	$_to = intval($_GET['fakeDataTo']);
	echo "$_from -> $_to<br/>";
	fakeData($_from, $_to);
}
/*
21.069713
105.806376
*/


?>