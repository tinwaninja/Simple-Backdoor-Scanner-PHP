<?php
error_reporting(0);
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
function ngecek($string) {
    //tambahkan nama fungsi, class, variable yang sering digunakan pada backdoor
    //add name of the function, class, variable that is often used on the backdoor
    $dicari   = array(
        'base64_encode',
        'base64_decode',
        'FATHURFREAKZ',
        'eval',
        'gzinflate',
        'str_rot13',
        'convert_uu',
        'shell_data',
        'getimagesize',
        'magicboom',
        'exec',
        'shell_exec',
        'fwrite',
        'str_replace',
        'mail',
        'file_get_contents',
        'url_get_contents',
        'symlink',
        'substr',
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
if (ob_get_level() == 0) ob_start();
echo '<h1 align="center">Simple Backdoor Scanner</h1>';
echo '<h3 align="center"><a href="">Tn. Ninja</a></h3>';
foreach ($list as $value) {
    if (is_file($value)) {
        $string = baca($value);
        $cek    = ngecek($string);
        if (empty($cek)) {
            $kata = '<p style="color: green;">'. $value .' => Safe</p><hr>';
			echo $kata;
			$fp = fopen('result-scanner.html', 'a');
			fwrite($fp, $kata."\n");
			fclose($fp);
        } else {
            $kata = '<p style="color: red;">'. $value .' => Found ('. $cek .')</p><hr>';
			echo $kata;
			$fp = fopen('result-scanner.html', 'a');
			fwrite($fp, $kata."\n");
			fclose($fp);
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
