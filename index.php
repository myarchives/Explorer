<?php

	# FEL
	error_reporting(E_ALL & ~E_NOTICE);

	# FAVOURITE FOLDERS
	$favourites = Array(
		'erik-edgren-blog'
	);



	# FUNCTION
	function format_number($number, $zeros = 0) {
		$explode = explode('.', $number);

		if(count($explode[1]) == 0) {
			return number_format($explode[0], 0, ',', ' ');
		} else {
			return number_format($number, $zeros, ',', ' ');
		}
	}

	# FUNCTION
	function calculate_filesize($size) {
		$units = array('B', 'kB', 'MB', 'GB', 'TB');
		for($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
		return format_number($size, 2).' '.$units[$i];
	}

	# FUNCTION
	function scan_dir($path) {
		$ite=new RecursiveDirectoryIterator($path);

		$bytestotal = 0;
		$nbfiles = 0;
		foreach(new RecursiveIteratorIterator($ite) as $filename=>$cur) {
			$filesize=$cur->getSize();
			$bytestotal+=$filesize;
			$nbfiles++;
			$files[] = $filename;
		}

		$bytestotal = $bytestotal;

		return Array(
			'total_files' => $nbfiles,
			'total_size' => $bytestotal,
			'files' => $files
		);
	}







	# IF
	if(isset($_GET['dirsize'])) {

		# IF
		if(file_exists($_GET['dirsize'])) {

			# VARIABLE
			$files = scan_dir($_GET['dirsize']);

			# STRING
			echo format_number($files['total_files']).' files ('.calculate_filesize($files['total_size']).')';


		# IF
		} else {

			# CODE
			echo 'error-1';

		}



	# IF
	} else {

?>







<!DOCTYPE html>

<html>
<head>


<!--  TITEL  -->
<title>Explorer</title>

<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.2.0/css/all.css">

<!-- STILMALL -->
<style type="text/css">
@import 'https://fonts.googleapis.com/css?family=Roboto:300|Roboto+Condensed:300';



html {
	margin: 0;
	padding: 0;
}

body {
	background-color: #000000;
	color: #f8f8f8;
	font-family: 'Roboto', sans-serif;
	font-size: 14px;
	font-weight: 300;
	line-height: 28px;
	margin: 50px auto;
	padding: 0;
	overflow-y: scroll;
	text-align: justify;
	width: 600px;
}



a {
	color: #f8f8f8;
	text-decoration: none;
}

ul {
	margin-top: -5px;
	margin-left: -40px;
	list-style-type: none;
}

section {
	display: inline-block;
}

section > header {
	color: #222222;
	font-family: 'Roboto Condensed', sans-serif;
	font-size: 34px;
	font-weight: 300;
	margin-bottom: 20px;
	margin-left: 34px;
	text-transform: lowercase;
}

section > header:not(:first-child) {
	margin-top: 50px;
}

section > ul > li > i {
	color: #373737;
	margin-right: 5px;
	text-align: center;
	width: 20px;
}

section > ul > li > i.fa-heart {
	color: #6e2424;
}

section > ul > li > i.fa-exclamation-triangle {
	color: #99981b;
}

section > ul > li > a,
section > ul > li > .no-link {
	margin-left: 10px;
}

section > ul > li > .directory-size {
	color: #373737;
	margin-left: 20px;
}

section > ul > li > .no-link > span {
	color: #f3f3cd;
}



.no-select {
	cursor: default;

	user-select: none;
	-moz-user-select: none;
	-khtml-user-select: none;
	-webkit-user-select: none;
	-webkit-user-drag: none;
}



.directory-size > i {
	color: #111111;
	font-size: 14px;
}

.directory-size > .error {
	color: #2e1111;
	font-size: 14px;
}

.directory-size > .error > i {
	margin-right: 10px;
}







@media only screen and (max-width: 840px), only screen and (max-device-width: 840px) {

	body {
		padding: 0 30px;
		width: calc(100% - (30px * 2));
	}

}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {

	$('.directory-size').each(function() {

		// VARIABLE
		var directory = $(this).attr('data-name');

		// GET
		$.ajax({
			url: '?dirsize=' + directory,
			method: 'GET',
			success: function(s) {

				// IF
				if(s == 'error-1') {

					// HTML
					$('.directory-size[data-name="' + directory + '"]').html('<span class="error"><i class="fas fa-exclamation-circle"></i>Can\'t get directory info!</span>');


				// IF
				} else {

					// TEXT
					$('.directory-size[data-name="' + directory + '"]').text(s);

				}

			},


			error: function(e) {

				// HTML
				$('.directory-size[data-name="' + directory + '"]').html('<span class="error"><i class="fas fa-exclamation-circle"></i>Can\'t get directory info!</span>');

			}
		});

	});

});
</script>

<meta name="viewport" content="initial-scale=1.0, user-scalable=no">



</head>
<body>







<?php


	# IF
	if(isset($_GET['err'])) {

		# IF
		if($_GET['err'] == '404') {

			echo '<b>Error!</b><br>';
			echo 'The wished file can\'t be found!';

		}



	# IF
	} else {

		# VARIABLES
		$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$address = substr($url, strpos($url, '/') + 1);
		$dir = ($address == '' ? '.': $address);
		$blacklist = Array('.', '..', '.htaccess', 'index.php');
		$array_folder = Array();
		$array_file = Array();
		$directories = Array();
		$files_list = Array();
		$files = scandir($dir);


		# LOOP
		foreach($files AS $file) {

			# IF
			if(!in_array($file, $blacklist) AND strpos($file, '_') === false) {

				# IF
				if(is_dir($dir.'/'.$file)) {
					$directories[] = $file;


				# IF
				} else {
					$files_list[] = $file;
				}

			}

		}











		# ARRAY
		$errors = Array();

		# LOOP
		foreach($favourites AS $favourite) {
			if(!file_exists($favourite)) {
				$str = '<li>';
					$str .= '<i class="fas fa-exclamation-triangle"></i>';
					$str .= '<span class="no-link">';
						$str .= 'The favourite folder <span>'.$favourite.'</span> does not exists';
					$str .= '</span>';
				$str .= '</li>';
			}

			$errors[] = $str;
		}



		echo '<section>';

			# IF
			if($errors[0] != null) {

				# TITLE
				echo '<header class="no-select">';
					echo 'Errors';
				echo '</header>';

				# LIST
				echo '<ul>';
					foreach($errors AS $error) {
						echo $error;
					}
				echo '</ul>';

			}



			# TITLE
			echo '<header class="no-select">';
				echo 'Folders';
			echo '</header>';

			# LIST
			echo '<ul>';

				# LOOP
				foreach($directories AS $directory) {
					echo '<li>';
						echo '<i class="fas fa-'.(in_array($directory, $favourites) ? 'heart' : 'folder').'"></i>';
						echo '<a href="http://'.$url . $directory.'">';
							echo $directory;
						echo '</a>';

						echo '<span class="directory-size no-select" data-name="'.$directory.'">';
							echo '<i class="fas fa-sync fa-spin"></i>';
						echo '</span>';
					echo '</li>';
				}

			echo '</ul>';



			# IF
			if(count($files_list) != 0) {
				echo '<header class="no-select">Files</header>';

				# LISTA
				echo '<ul>';

					# LOOP
					foreach($files_list AS $file) {

						# VARIABLES
						$file_info = pathinfo($file);
						$file_type = $file_info['extension'];

						# ARRAY
						$extensions = Array(
							'file-image' => 'jpg',
							'file-code' => 'html',
							'file-code' => 'php',
							'file-archive' => 'zip'
						);


						echo '<li>';
							echo '<i class="fas fa-'.array_search($file_type, $extensions).'"></i>';
							echo '<a href="http://'.$url . $file.'">'.$file.'</a>';

							echo '<span class="no-select">';
								echo calculate_filesize(filesize($address . $file));
							echo '</span>';
						echo '</li>';
					}

				echo '</ul>';
			}

		echo '</section>';

	}


?>








</body>
</html>

<?php } ?>
