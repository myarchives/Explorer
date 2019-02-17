<?php

	# HANDLE ERROR REPORTING
	error_reporting(E_ALL & ~E_NOTICE);

	# FAVOURITE FOLDERS
	$favourites = Array(
		'edgren',
		'vadret-justnu'
	);

	# IGNORE FOLDERS
	$ignore_folders = Array(
		'.vscode'
	);

	# IGNORE FILES
	$ignore_files = Array();

	# IGNORE FILE EXTENSIONS
	$ignore_extensions = Array();



	# FUNCTION
	function format_number($number, $zeros = 0) {
		$explode = explode('.', $number);

		if($explode[1] == null) {
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
		$ite = new RecursiveDirectoryIterator($path);

		$bytestotal = 0;
		$nbfiles = 0;
		foreach(new RecursiveIteratorIterator($ite) AS $filename=>$cur) {
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

	# FUNCTION
	function count_lines($file) {
		$linecount = 0;
		$handle = fopen($file, 'r');

		while(!feof($handle)) {
			$line = fgets($handle);
			$linecount++;
		}

		fclose($handle);

		return format_number($linecount);
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

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/solid.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/fontawesome.css">

<!-- STILMALL -->
<style type="text/css">
html {
	margin: 0;
	padding: 0;
}

body {
	background-color: #111111;
	color: #f5f5f5;
	font-family: 'Arial', sans-serif;
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
	color: #f5f5f5;
	text-decoration: none;
}

ul {
	margin-top: -5px;
	margin-left: -40px;
	list-style-type: none;
}

section > header {
	color: #f5f5f5;
	font-size: 18px;
	font-weight: 700;
	margin-bottom: 20px;
	margin-left: 34px;
}

section > header:not(:first-child) {
	margin-top: 50px;
}

section > ul > li > i {
	color: #f5f5f5;
	margin-right: 5px;
	text-align: center;
	width: 20px;
}

section > ul > li > i.fa-heart {
	color: #f44336;
}

section > ul > li > i.fa-info-circle {
	color: #fff59d;
}

section > ul > li > a,
section > ul > li > .no-link {
	margin-left: 10px;
}

section > ul > li > .directory-size,
section > ul > li > .file-size {
	color: #455a64;
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



div#message {
	font-size: 16px;
	margin-top: 100px;
	text-align: center;
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

	// LOOP
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

			# MESSAGE
			echo '<div id="message">';
				echo 'The requested file could not be found!';
			echo '</div>';

		}



	# IF
	} else {

		# VARIABLES
		$host = $_SERVER['HTTP_HOST'];
		$list_folders = Array();
		$list_files = Array();
		$files = new DirectoryIterator(__DIR__);
		$count_folders = 0;
		$count_files = 0;

		# ARRAYS
		$errors = Array();
		$blacklist = Array(
			'.htaccess',
			'index.php'
		);



		# IF
		if(!$files) {

			echo '<section>';

				# TITLE
				echo '<header class="no-select">';
					echo 'Errors';
				echo '</header>';

				# LIST
				echo '<ul>';
					echo '<li>';
						echo '<i class="fas fa-exclamation-triangle"></i>';
						echo '<span class="no-link">';
							echo 'The directory "'.$dir.'" can\'t be read';
						echo '</span>';
					echo '</li>';
				echo '</ul>';

			echo '</section>';



		# IF
		} else {

			# LOOP
			foreach($files AS $file) {

				# IF
				if($file->isDot() || $file->getBasename() === '.DS_Store') continue;

				# IF
				if(!in_array($file->getFilename(), $blacklist)) {

					# IF
					if($file->isDir()) {
						$list_folders[] = $file->getFilename();
						$count_folders++;

					# IF
					} elseif($file->isFile()) {
						$list_files[] = $file->getFilename();
						$count_files++;
					}

				}

			}

			# SORT
			asort($list_folders);
			asort($list_files);


			# LOOP
			foreach($favourites AS $favourite) {
				if(!file_exists($favourite)) {
					$str = '<li>';
						$str .= '<i class="fas fa-info-circle"></i>';
						$str .= '<span class="no-link">';
							$str .= 'The favourite folder "'.$favourite.'" does not exists';
						$str .= '</span>';
					$str .= '</li>';
				}

				$notices[] = $str;
			}







			echo '<section>';

				# IF
				if($count_folders == 0 AND $count_files == 0) {

					# MESSAGE
					echo '<div id="message">';
						echo 'Please add some folder and/or files to start!';
					echo '</div>';



				# IF
				} else {

					# IF
					if($notices[0] != null) {

						# TITLE
						echo '<header class="no-select">';
							echo 'Notices';
						echo '</header>';

						# LIST
						echo '<ul>';
							foreach($notices AS $notice) {
								echo $notice;
							}
						echo '</ul>';

					}



					# IF
					if($count_folders != 0) {

						# TITLE
						echo '<header class="no-select">';
							echo 'Folders';
						echo '</header>';

						# LIST
						echo '<ul>';

							# LOOP
							foreach($list_folders AS $directory) {
								if(!in_array($directory, $ignore_folders)) {
									echo '<li>';

										# FOLDER
										echo '<i class="fas fa-'.(in_array($directory, $favourites) ? 'heart' : 'folder').'"></i>';
										echo '<a href="http://'.$host.'/'.$directory.'">';
											echo $directory;
										echo '</a>';

										# INFORMATION
										echo '<span class="directory-size no-select" data-name="'.$directory.'">';
											echo '<i class="fas fa-sync fa-spin"></i>';
										echo '</span>';

									echo '</li>';
								}
							}

						echo '</ul>';

					}



					# IF
					if($count_files != 0) {

						# TITLE
						echo '<header class="no-select">';
							echo 'Files';
						echo '</header>';

						# LIST
						echo '<ul>';

							# LOOP
							foreach($list_files AS $fileinfo) {
								if(!in_array($fileinfo, $ignore_files)) {

									# VARIABLES
									$file_info = pathinfo($fileinfo);
									$file_type = $file_info['extension'];

									# ARRAY
									$extensions = Array(
										'code' => 'html',
										'code' => 'css',
										'code' => 'php',
										'image' => 'png',
										'image' => 'jpg',
										'file-archive' => 'zip',
										'file-csv' => 'csv',
										'file-excel' => 'xml',
										'volume' => 'mp3',
										'file-alt' => 'txt',
										'database' => 'sql'
									);


									if(!in_array($file_type, $ignore_extensions)) {
										echo '<li>';

											# FILE
											echo '<i class="fas fa-'.(!in_array($file_type, $extensions) ? 'file' : array_search($file_type, $extensions)).'"></i>';
											echo '<a href="http://'.$host.'/'.$fileinfo.'">'.$fileinfo.'</a>';

											# INFORMATION
											echo '<span class="file-size no-select">';
												echo count_lines($fileinfo).' lines ('.calculate_filesize(filesize($fileinfo)).')';
											echo '</span>';

										echo '</li>';
									}

								}
							}

						echo '</ul>';

					}

				}

			echo '</section>';

		}

	}


?>








</body>
</html>

<?php } ?>