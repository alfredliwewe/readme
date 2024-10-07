<?php
require '../functions.php';
$db = new sqlite3("../data.db3");

$time = time();

if (isset($_GET['getMenus'])) {
	$rows = [];

	$read = $db->query("SELECT * FROM menus ");
	while ($row = $read->fetchArray(SQLITE3_ASSOC)) {
		array_push($rows, $row);
	}

	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($rows);
}
elseif(isset($_POST['parent'], $_POST['new_menu'], $_POST['type'], $_POST['md_file'], $_POST['html_file'])){
	//save this
}
?>