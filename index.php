<?php

//date_default_timezone_set('America/New_York');
$spath = str_replace("\\","/",getcwd()).'/';
$servroot = rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/';
if ( !empty( $_SERVER['HTTPS'] ) ) {
	$hostroot = 'https://'.$_SERVER['HTTP_HOST'].'/';
}else{
	$hostroot = 'http://'.$_SERVER['HTTP_HOST'].'/';
}
$hpath = str_replace($servroot, $hostroot, $spath);
$name = basename(__FILE__, '.php'); 
$fname = basename(__FILE__); 
/*echo getcwd().'<br>'; 
echo $spath.'<br>';
echo $servroot.'<br>';
echo $hostroot.'<br>';
echo $hpath;
echo $fname.'<br>';
echo $name.'<br>'; exit;*/

$isdb = false;
if(is_file('DB.sqlite')){$isdb = true;}
try{
	$db = new PDO('sqlite:DB.sqlite');
}
catch(PDOException $e){
	 exit($e->getMessage());
}
if($isdb == false){
	$tables = array();
	$tables['subscribers'] = array('id'=>'INTEGER PRIMARY KEY AUTOINCREMENT','endpoint'=>'TEXT','auth'=>'TEXT','p256dh'=>'TEXT');
	foreach($tables as $ke => $val){

		$sql = "CREATE TABLE IF NOT EXISTS ".$ke." (";
		    foreach($val as $k => $v){
		 		$sql .= $k." ".$v.", ";
		 	}
		    $sql = rtrim($sql, ", ");
		    $sql .= ")";
			$db->exec($sql);
	}
}

$publickey = 'BEDJrJXtU90CR4c6RF88oURuwQQj_O60Zb1o4OoYyH4aDXTNg8qQIO6PSpR7FeUUN42u09NaNcwli6iRHrQ0nqs';
$privatekey = 'acsYsSx1vjKNW-oilwVGFywb5FvN5dFU3diMTCCqOPg';

$_POST = json_decode(file_get_contents('php://input'), true);



if(isset($_POST['axn']) && $_POST['axn'] != NULL){
	$output = '';
	switch($_POST['axn']){
		case "subscribe":
			//filter out bad data
			$myQuery = "SELECT * FROM subscribers WHERE endpoint = ".$db->quote($_POST['endpoint']);
			try{
			    $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
			    if($result['id'] == NULL || $result['id'] == ""){
					$my_query = "REPLACE INTO subscribers (endpoint, p256dh, auth) VALUES (".$db->quote($_POST['endpoint']).", ".$db->quote($_POST['key']).", ".$db->quote($_POST['token'])."); ";
					    echo $my_query.'<BR><BR>';
					    try {
					        $db->exec($my_query);
					        }
					    catch(PDOException $e)
					        {
					        echo $e->getMessage();
					        }
					    //$i++;
					    $output .= 'adding user <br>';
		   	    }else{
			        $output .= 'user exists in db :  <br>';
			        
			    }
			}
			catch(PDOException $e){
			     exit($e->getMessage());
			} 
			echo $output;
			exit;
			break;
		case "unsubscribe":
			$myQuery = "SELECT * FROM subscribers WHERE endpoint = ".$db->quote($_POST['endpoint']);
			try{
			    $result = $db->query($myQuery)->fetch(PDO::FETCH_ASSOC);
			    if($result['id'] != NULL){
					$my_query = "DELETE FROM subscribers WHERE endpoint = ".$db->quote($_POST['endpoint']);
					   // exit($my_query);
					    try {
					        $db->exec($my_query);
					        }
					    catch(PDOException $e)
					        {
					        echo $e->getMessage();
					        }
					    $output .= 'removing user <br>';
		   	    }else{
			        $output .= 'user does not exist in db :  <br>';
			        
			    }
			}
			catch(PDOException $e){
			     exit($e->getMessage());
			} 
			echo $output;
			exit;
			break;
		default:
	}
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Web Push Notification</title>

	<script type="text/javascript">
		
		var aspkey = '<?php echo $publickey; ?>' 

	</script>
</head>
<body>
		<center>
			<h1>Web Push Notification</h1>

			<button class="pushbtn" disabled>Enable Push Notification</button>
			<button class="sendpushbtn">Send Notification</button>
		</center>
<script type="text/javascript" src="scripts/main.js"></script>
</body>
</html>
