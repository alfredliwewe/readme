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
	<div class="p-2 bg-blue-700 text-white" id="top" style="background-color:#001d3d">
		<?=$config['name'];?>
	</div>
	<div class="w3-row" id="major">
		<div class="w3-col m2 w3-border-right">
			<h5>Menu</h5>
		</div>
		<div class="w3-col m8">
			<p>Contents</p>
		</div>
		<div class="w3-col m2 w3-border-left">
			<p>Navigation</p>
		</div>
	</div>
</body>
<script type="text/javascript">
	window.onload = function() {
		let div = document.getElementById("major");
		for (let i = 0; i < div.childNodes.length; i++) {
			const childNode = div.childNodes[i];
			$(childNode).height(innerHeight - $('#top').height());
		}
	}
</script>
</html>