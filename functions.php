<?php
function getData($table, $id){
	global $db;
	if (is_numeric($id)) {
		$sql  = $db->query("SELECT * FROM $table WHERE id = '$id' ");
		return $sql->fetchArray(SQLITE3_ASSOC);
	}
	else{
		$wheres = [];

		foreach ($id as $key => $value) {
			$value = $db->escapeString($value);
			array_push($wheres, "`$key` = '$value' ");
		}

		return $db->query("SELECT * FROM `$table` WHERE ".implode(" AND ", $wheres))->fetchArray(SQLITE3_ASSOC);
	}
}

function getAll($table, $id=null){
	global $db;
	
	if (is_array($id)) {
		$wheres = [];

		foreach ($id as $key => $value) {
			$value = $db->escapeString($value);
			array_push($wheres, "`$key` = '$value' ");
		}

		$rows = [];
		$read = $db->query("SELECT * FROM `$table` WHERE ".implode(" AND ", $wheres));
		while ($row = $read->fetchArray(SQLITE3_ASSOC)) {
			array_push($rows, $row);
		}

		return $rows;
	}
	elseif ($id == null) {
		$read = $db->query("SELECT * FROM `$table` ");
		$rows = [];
		while ($row = $read->fetchArray(SQLITE3_ASSOC)) {
			array_push($rows, $row);
		}

		return $rows;
	}
	else{
		$read = $db->query("SELECT * FROM `$table` ");
		$rows = [];
		while ($row = $read->fetchArray(SQLITE3_ASSOC)) {
			$rows[$row[$id]] = $row;
		}

		return $rows;
	}
}

function db_update($table, $cv, $where){
	global $db;
	$wheres = [];

	foreach ($where as $key => $value) {
		$value = $db->escapeString($value);
		array_push($wheres, "`$key` = '$value' ");
	}

	$contentValues = [];

	foreach ($cv as $key => $value) {
		$value = $db->escapeString($value);
		array_push($contentValues, "`$key` = '$value' ");
	}
	$cvs = implode(", ", $contentValues);

	return $db->query("UPDATE `$table` SET $cvs WHERE ".implode(" AND ", $wheres));
}

function db_delete($table, $where){
	global $db;
	$wheres = [];

	foreach ($where as $key => $value) {
		$value = $db->escapeString($value);
		array_push($wheres, "`$key` = '$value' ");
	}

	return $db->query("DELETE FROM `$table` WHERE ".implode(" AND ", $wheres));
}

function db_insert($table, $array)
{
	global $db;

	$columns = [];
	$values = [];
	$read = $db->query("PRAGMA table_info(`$table`)");
	while ($row = $read->fetchArray(SQLITE3_ASSOC)) {
		array_push($columns, "`{$row['name']}`");
		if ($row['pk'] == "1") {
			array_push($values, "NULL");
		}
		else{
			$value = isset($array[$row['name']]) ? $db->escapeString($array[$row['name']]) : "0";
			array_push($values, "'$value'");
		}
	}

	$sql = "INSERT INTO `$table` (".implode(",",$columns).") VALUES (".implode(",",$values).")";
	$db->query($sql);
	//return $db->insert_id;
}

function db_query($sql){
	global $db;

	$read = $db->query($sql);
	$rows = [];
	while ($row = $read->fetchArray(SQLITE3_ASSOC)) {
		array_push($rows, $row);
	}

	return $rows;
}


function curl_post($url, array $post = NULL, array $options = array()) { 
	$defaults = array( 
		CURLOPT_POST => 1, 
		CURLOPT_HEADER => 0, 
		CURLOPT_URL => $url, 
		CURLOPT_FRESH_CONNECT => 1, 
		CURLOPT_RETURNTRANSFER => 1, 
		CURLOPT_FORBID_REUSE => 1, 
		CURLOPT_TIMEOUT => 5, 
		CURLOPT_POSTFIELDS => http_build_query($post) 
	); 

	$ch = curl_init(); 
	curl_setopt_array($ch, ($options + $defaults)); 
	if( ! $result = curl_exec($ch)) { 
		//trigger_error(curl_error($ch)); 
		return curl_error($ch);
	} 
	else{
		return $result;
	}
	curl_close($ch);
}

function sendEmail($to, $subject, $message, $attachement = null, $attachement_name = null){
	global $db;

	$time = time();
	$message = $db->escapeString($message);
	$ins = $db->query("INSERT INTO emails (id, receiver, header, content, attachment, date) VALUES (NULL, '$to', '$subject', '$message', '$attachement_name', '$time')");
	if ($attachement != null && $attachement_name != null) {
		file_put_contents("emails/".$attachement_name, base64_decode($attachement));

		$from = "egssccap@gmail.com";
		return curl_post("http://kuchitekete-music.com/mail.php", ['from' => $from, 'email' => strtolower(trim(trim(trim($to)))), 'subject' => $subject, 'message' => $message, 'attachement' => $attachement, 'attachement_name' => $attachement_name], []);
	}
	else{
		$from = "egssccap@gmail.com";
		return curl_post("http://kuchitekete-music.com/mail.php", ['from' => $from, 'email' => $to, 'subject' => $subject, 'message' => $message], []);
	}
	
}
?>