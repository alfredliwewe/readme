<?php
require '../functions.php';
$db = new sqlite3("../data.db3");

$time = time();

if (isset($_GET['getMenus'])) {
	$rows = [];

	$read = $db->query("SELECT * FROM menus ");
	while ($row = $read->fetchArray(SQLITE3_ASSOC)) {
		$row['parent_data'] = $row['parent'] != 0 ? db_get("menus", ['id' => $row['parent']]) : ['name' => ''];
		array_push($rows, $row);
	}

	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($rows);
}
elseif(isset($_POST['parent'], $_POST['new_menu'], $_POST['type'], $_POST['md_file'])){
	//save this
	db_insert("menus", [
		'name' => $_POST['new_menu'],
		'type' => $_POST['type'],
		'md_file' => $_POST['md_file'],
		//'html_file' => $_POST['html_file'],
		'parent' => $_POST['parent'],
		'created' => $time,
	]);

	if($_POST['type'] == "notes"){
		file_put_contents('../uploads/'.$_POST['md_file'], "");
		//file_put_contents('../uploads/'.$_POST['html_file'], "");
	}

	echo json_encode(['status' => true, 'message' => "Success"]);
}
elseif(isset($_POST['menu_id'], $_POST['edit_menu'], $_POST['type'], $_POST['md_file'])){
	//update this
	db_update("menus", [
		'name' => $_POST['edit_menu'],
		'type' => $_POST['type'],
		'md_file' => $_POST['md_file'],
		'parent' => $_POST['parent'],
		//'html_file' => $_POST['html_file'],
	], ['id' => $_POST['menu_id']]);

	echo json_encode(['status' => true, 'message' => "Success"]);
}
?>