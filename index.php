<?php

/*
	d4rkcats super vulnerable PHP webapp
	To install: place this script in /var/www and run [ service apache2 start ]
	To use: visit localhost in the browser

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License at (http://www.gnu.org/licenses/) for
	more details.
*/

echo "<!DOCTYPE html>
<html>
<head><title>SSWA</title></head>
<body>
	<h2 align='center'>d4rkcat's super secure-webapp.</h2>
	<p></p>

	<table>
		What's your name?
		<form action='index.php?page=main' method='post'>
			<input type='text' name='welcome'>
			<input type='submit' value='Say Hi!'>
		</form>
		Come again?
		<form action='index.php?page=main' method='post'>
			<input type='text' name='welcome2'>
			<input type='submit' value='Say Hi! (medium)'>
		</form>
	</table>
	<table>
		Say it louder?
		<form action='index.php?page=main' method='post'>
			<input type='text' name='welcome3'>
			<input type='submit' value='Say Hi! (hard)'>
		</form>
		Wtf?
		<form action='index.php?page=main' method='post'>
			<input type='text' name='welcome4'>
			<input type='submit' value='Say Hi! (hard2)'>
		</form>
	</table>
	<table>
		<p></p>
		Ping something?
		<form action='index.php?page=main' method='post'>
			<input type='text' name='ping'>
			<input type='submit' value='Ping Host'>
		</form>
		Another ping?
		<form action='index.php?page=main' method='post'>
			<input type='text' name='ping2'>
			<input type='submit' value='Ping Host (medium)'>
		</form>
	</table>
	<table>
	Ping from hell?
	<form action='index.php?page=main' method='post'>
		<input type='text' name='ping3'>
		<input type='submit' value='Ping Host (hard)'>
	</form>
	</table>
 ";

session_start();
if (isset($_POST["reset"])){
	$_SESSION['pwned'] = null;
	$_SESSION['complete'] = null;
}

function fcount($new)
{
	if (isset($_SESSION['pwned']) && strpos($_SESSION['pwned'], $new) == false){
			$_SESSION['complete'] .= '1';
			$_SESSION['pwned'] .= $new;
	} 
	else {
		$_SESSION['complete'] = '1';
		$_SESSION['pwned'] = 'Completed: '.$new;
	}

}

file_put_contents('log.php', $_SERVER['HTTP_USER_AGENT']."\r\n");
if (!file_exists('config.php')) {
	$conf = "<?php echo '<h1><font color=3ADF00>(LFI easy): Pwn3D!</font></h1><h3>User: Bob Password: 8222583ffa975dc9913c887cd8bbbe0a</h3>'; ?>\r\n";
	file_put_contents('config.php', $conf);
}
if (!file_exists('admin/config.php')) {
	$conf = "<?php echo '<h1><font color=FF8000>(LFI medium): Pwn3D!</font></h1><h3>User: Admin Password: 171a07140255b9daee17190804127c15</h3>'; ?>\r\n";
	shell_exec('mkdir admin');
	file_put_contents('admin/config.php', $conf);
}
if (empty($_GET['page']))
{
	header('Location: index.php?page=main');
}
if(!empty($_POST["welcome"]))
{
	echo '<pre>Hello, '.$_POST["welcome"].'!</pre>'."\r\n";
	if (strpos($_POST["welcome"],'<script>') !== false) {
		echo '<font color = "3ADF00"><h1>(XSS easy): Pwn3D!</h1></font>'."\r\n";
		fcount('<font color=3ADF00>[XSS EASY] </font>');
	}
}
if(!empty($_POST["welcome2"]))
{
	$target = str_replace('script>', 'lolwut>', $_POST["welcome2"]);
	echo '<pre>Hello, '.$target.'!</pre>'."\r\n";
	if (strpos(strtolower($target),'<script>') !== false) {
		echo '<font color = "FF8000"><h1>(XSS medium): Pwn3D!</h1></font>'."\r\n";
		fcount('<font color = "FF8000">[XSS MED] </font>');
	}
}
if(!empty($_POST["welcome3"]))
{
	$target = str_replace('script>', 'lolwut>', $_POST["welcome3"]);;
	echo '<!-- <pre>Hello, '.$target."!</pre> -->\r\n";
	if (strpos(strtolower($target),'<script>') !== false && strpos($target,'-->') !== false) {
		echo '<font color = "FF0000"><h1>(XSS hard): Pwn3D!</h1></font>'."\r\n";
		fcount('<font color = "FF0000">[XSS HARD] </font>');
	}
}
if(!empty($_POST["welcome4"]))
{
	$substitutions = array('<' => '&lt;', '>' => '&gt;');
	$target = urldecode(str_replace(array_keys($substitutions), $substitutions, $_POST["welcome4"]));
	echo '<script>hahyeahright, '.$target."</script>\r\n";
	if (strpos(strtolower($target),'</script><script>') !== false) {
		echo '<font color = "FF0000"><h1>(XSS hard2): Pwn3D!</h1></font>'."\r\n";
		fcount('<font color = "FF0000">[XSS HARD2] </font>');
	}
}
elseif(!empty($_POST["ping"]))
{
	echo '<h3>Ping Results:</h3>';
	$easyrce = shell_exec('ping -c 2 '.$_POST["ping"]);
	if (strpos($_POST["ping"],';') !== false || strpos($_POST["ping"],'&&') !== false || strpos($_POST["ping"],'|') !== false) {
		if (strlen($easyrce) > 4){
			echo '<font color = "3ADF00"><h1>(RCE easy): Pwn3D!</h1></font>'."\r\n";
			fcount('<font color = "3ADF00">[RCE EASY] </font>');
		}
	}
echo '<pre>'.$easyrce.'</pre>'."\r\n";
	
}
elseif(!empty($_POST["ping2"]))
{
	echo '<h3>Ping Results:</h3>'."\r\n";
	$substitutions = array('&&' => '',';' => '', ' ' => '');
	$target = str_replace(array_keys($substitutions), $substitutions, $_POST["ping2"]);
	$midrce = shell_exec('ping -c 2 '.$target);
	if (strpos($_POST["ping2"],'|') !== false && strlen($midrce) > 4){
		echo '<font color = "FF8000"><h1>(RCE medium): Pwn3D!</h1></font>'."\r\n";
		fcount('<font color = "FF8000">[RCE MED] </font>');
	}
echo '<pre>'.$midrce.'</pre>'."\r\n";
}
elseif(!empty($_POST["ping3"]))
{
	echo '<h3>Ping Results:</h3>';
	$substitutions = array('&&' => '', ';' => '', ' ' => '', '#' => '', '|' => '');
	$target = urldecode(str_replace(array_keys($substitutions), $substitutions, $_POST["ping3"]));
	$hardrce = shell_exec('ping -c 2 '.$target.'&> /dev/null');
	if (strpos($target,'|') !== false && strpos($target,' #') !== false && strlen($hardrce) > 4) {
		echo '<font color = "FF0000"><h1>(RCE hard): Pwn3D!</h1></font>'."\r\n";
		fcount('<font color = "FF0000">[RCE HARD] </font>');
	}
echo '<pre>'.$hardrce.'</pre>'."\r\n";
}
elseif(!empty($_GET["page"]) && $_GET["page"] !== 'main')
{
	echo '<h3>';
	include($_GET["page"]. ".php");
	echo '</h3>';
}
$back = "\r\n<img width=400 src='http://www.daveharasti.com/articles/speciesspotlight/images/cuttlefish2.jpg'></img>";
if ($_GET["page"] == 'log'){
	if (strpos($_SERVER['HTTP_USER_AGENT'], '<?php') !== false && strpos($_SERVER['HTTP_USER_AGENT'], '$_GET') !== false){
		echo '<font color = "FF0000"><h1>(LFI>RCE hard): Pwn3D!</h1></font>'."\r\n";
		fcount('<font color = "FF0000">[LFI>RCE HARD] </font>');
	}

$back = "\r\n<img width=700 src='http://www.myessentia.com/blog/wp-content/uploads/2012/10/cuttlefish1.jpeg'></img>";
}
elseif ($_GET["page"] == 'config'){
	fcount('<font color = "3ADF00">[LFI EASY] </font>');
}
elseif ($_GET["page"] == 'admin/config'){
	fcount('<font color = "FF8000">[LFI MED] </font>');
	$back = "<img width=500 src='http://thetruthbehindthescenes.files.wordpress.com/2010/07/cuttlefish-3.jpg'></img>";
}
echo '<h3 ALIGN="RIGHT">'.strlen($_SESSION['complete']).'/10</h3>';
if (isset($_SESSION['pwned'])) {
	if (strlen($_SESSION['pwned']) == 419){
		echo '<h1>YOU WIN! GAME OVER...</h1>';
		$back = "\r\n<img width=700 src='http://4.bp.blogspot.com/-64sMbbfWYVE/T6koKrMcUHI/AAAAAAAANeE/6J8A2N9Bdzk/s1600/cuttle-fish-bone.jpg'></img>";
	}
echo "\r\n".'<h4>'.$_SESSION['pwned'].'</h4>';
echo "\r\n".'<form action="index.php?page=main" method="post"><input type="submit" name="reset" value="RESET"></form>';
}
echo $back;
echo '
</body>
</html> ';
exit(); ?>