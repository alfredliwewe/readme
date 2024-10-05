<?php
require 'config.php';
$db = new sqlite3("data.db3");
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$config['name'];?></title>
	<?php
	require '../trials/links.php';
	?>
</head>
<body>
	<div id="root"></div>
</body>
<?php

$files = [
	'admin.jsx'
];
foreach($files as $file){
    echo "<script type=\"text/babel\">".file_get_contents($file)."</script>";
}
?>
</html>