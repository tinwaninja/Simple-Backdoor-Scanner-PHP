<?php
set_time_limit(0);
error_reporting(0);
@ini_set('zlib.output_compression', 0);
header("Content-Encoding: none");
ob_start();
function ngelist($dir, &$keluaran = array()) {
    $scan = scandir($dir);
    foreach ($scan as $key => $value) {
        $lokasi = $dir . DIRECTORY_SEPARATOR . $value;
        if (!is_dir($lokasi)) {
            $keluaran[] = $lokasi;
        } else if ($value != "." && $value != "..") {
            ngelist($lokasi, $keluaran);
            $keluaran[] = $lokasi;
        }
    }
    return $keluaran;
}
function baca($filenya) {
	$filesize = filesize($filenya);
	$filesize = round($filesize / 1024 / 1024, 1);
	if($filesize>2) { //max 2mb
		$kata = "Skipped--";
		echo $kata;
		$fp = fopen('result-scanner.html', 'a');
		fwrite($fp, $kata."\n");
		fclose($fp);
	}else {
		$php_file = file_get_contents($filenya);
		$tokens   = token_get_all($php_file);
		$keluaran = array();
		$batas    = count($tokens);
		if ($batas > 0) {
			for ($i = 0; $i < $batas; $i++) {
				if (isset($tokens[$i][1])) {
					$keluaran[] .= $tokens[$i][1];
				}
			}
		}
		$keluaran = array_values(array_unique(array_filter(array_map('trim', $keluaran))));
		return ($keluaran);
	}
}
function ngecek($string) {
    //tambahkan nama fungsi, class, variable yang sering digunakan pada backdoor
    //add name of the function, class, variable that is often used on the backdoor
    $dicari   = array(
        'base64_encode',
        'base64_decode',
        'FATHURFREAKZ',
        'eval',
	'system',
        'gzinflate',
        'str_rot13',
        'convert_uu',
        'shell_data',
        'getimagesize',
        'magicboom',
	'mysql_connect',
	'mysqli_connect',
	'basename',
	'getimagesize',
        'exec',
        'shell_exec',
        'fwrite',
        'str_replace',
        'mail',
        'file_get_contents',
        'url_get_contents',
	'move_uploaded_file',
        'symlink',
        'substr',
	'pathinfo',
        '__file__',
        '__halt_compiler'
    );
    $keluaran = "";
    foreach ($dicari as $value) {
        if (in_array($value, $string)) {
            $keluaran .= $value . ", ";
        }
    }
    if ($keluaran != "") {
        $keluaran = substr($keluaran, 0, -2);
    }
    return $keluaran;
}
$list = ngelist(".");
echo '<h1 align="center">Simple Backdoor Scanner</h1>';
echo '<h3 align="center"><a href="https://github.com/tinwaninja/Simple-Backdoor-Scanner-PHP">Tn. Ninja</a></h3>';
foreach ($list as $value) {
    if (is_file($value)) {
        $string = baca($value);
        $cek    = ngecek($string);
        if (empty($cek)) {
            $kata = '<p style="color: green;">'. $value .' => Safe</p><hr>';
			echo $kata;
        } else if(preg_match("/, /", $cek)) {
            $kata = '<p style="color: red;">'. $value .' => Found ('. $cek .')</p><hr>';
			echo $kata;
			$fp = fopen('result-scanner.html', 'a');
			fwrite($fp, $kata."\n");
			fclose($fp);
        }else{
			$kata = '<p style="color: red;">'. $value .' => Found ('. $cek .')</p><hr>';
			echo $kata;
		}
        ob_flush();
        flush();
        sleep(1);
    }
}
$kata = '<p align="center" style="color: blue;"><a href="result-scanner.html">Success, open result here</a></p><hr>';
echo $kata;
ob_end_flush();
?>
