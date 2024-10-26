<?php
require 'config.php';
require 'functions.php';

$db = new sqlite3("data.db3");


$title = $config['name'];

if(isset($_GET['read'])){
    $data = getData("menus", ['id' => (int)$_GET['read']]);
    $title = $data['name'];
}

?><!DOCTYPE html>
<html>
<head>
	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-JWXT11N5FV"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'G-JWXT11N5FV');
	</script>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?=$title;?></title>
	<?php
	require '../head/links.php';
	?>

	<link rel='stylesheet' href='src/codemirror.css'>
	<!--<script src='src/codemirror.js'></script>
	<script src='src/codemirror_jsx.js'></script>-->
	<!-- CodeMirror JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
  
  <!-- Language Mode for HTML -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
  
  <!-- Additional Modes for Embedded CSS and JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/javascript/javascript.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/css/css.min.js"></script>
  
  <!-- Optional: Addons for Enhanced Functionality -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/edit/closetag.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/edit/closebrackets.min.js"></script>
</head>
<body>
	<div class="p-2 bg-blue-700 text-white" id="top" style="background-color:#001d3d">
		<?=$config['name'];?>
	</div>
	<div class="w3-row" id="major">
		<div class="w3-col m2 w3-border-right">
			<div class="p-2">
				<h5>Menu</h5>
				<?php
				$read = $db->query("SELECT * FROM menus WHERE parent = '0' ");
				while ($row = $read->fetchArray()) {
					if ($row['type'] == "notes") {
						?>
						<div>
							<i class="fa fa-angle-right"></i> <a href="./?read=<?=$row['id'];?>"><?=$row['name'];?></a>
						</div>
						<?php
					}
					else{
						$found = getData("menus", [
							'parent' => $row['id'],
							'id' => isset($_GET['read']) ? (int)$_GET['read'] : 0
						]) != null;

						$style = $found ? "" : 'style="display:none;"';
						?>
						<div>
							<i class="fa fa-angle-right"></i> <a href="#" onclick="$('#<?=$row['id'];?>_category_inflate').toggle();"><?=$row['name'];?></a>
							<div class="pl-3" id="<?=$row['id'];?>_category_inflate" <?=$style;?>>
								<?php
								$sql = $db->query("SELECT * FROM menus WHERE parent = '".$row['id']."' ");
								while ($r = $sql->fetchArray()) {
									$color = (isset($_GET['read']) ? (int)$_GET['read'] : 0) == $r['id'] ? "text-red-800 hover:text-red-600":"text-blue-800 hover:text-blue-600"
									?>
									<div>
										<i class="fa fa-angle-right"></i> <a class="<?=$color;?>" href="./?read=<?=$r['id'];?>"><?=$r['name'];?></a>
									</div>
									<?php
								}
								?>
							</div>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<div class="w3-col m8">
			<div class="p-3 text-lg">
				<?php
				if (isset($_GET['read'])) {

					$data = getData("menus", ['id' => (int)$_GET['read']]);
					?>
					<h5><?=$data['name'];?></h5>
					<?php

					require 'includes/ParseDown.php';
					$Parsedown = new Parsedown();

					// Convert Markdown to HTML
					$html = $Parsedown->text(file_get_contents("./uploads/".$data['md_file']));
					echo $html;
				}
				?>
			</div>
			<div class="p-3 bg-blue-100">
				<div class="w3-center py-3">From <a href="#">malawi-schools.com</a></div>
			</div>
		</div>
		<div class="w3-col m2 w3-border-left">
			<div class="p-3">
				<?php
				if (isset($Parsedown)) {
					?>
					<h5>On this page</h5>
					<?php
					foreach ($Parsedown->headings as $row) {
						?>
						<div class="block">
							<a href="#<?=$row['id'];?>"><?=$row['text'];?></a>
						<?php
					}
				}
				?>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	window.onload = function() {
		if (innerWidth > innerHeight) {
			let div = document.getElementById("major");
			for (let i = 0; i < div.childNodes.length; i++) {
				const childNode = div.childNodes[i];
				$(childNode).height(innerHeight - _('top').clientHeight).css('overflow-y','auto');
			}
		}


		// Select all textareas with the class 'code-editor'
		var textareas = document.querySelectorAll('.htmlHigh');

		textareas.forEach(function(textarea) {
			CodeMirror.fromTextArea(textarea, {
				mode: "htmlmixed",
				lineWrapping: true,
				smartIndent: false,
				htmlMode: true,
				autocorrect: false,      
				addModeClass: true,


				//mode: "text/html",
				//htmlMode: true,
				//lineWrapping: true,
				//smartIndent: false,
				//addModeClass: true

			});
		});
	}
</script>
</html>