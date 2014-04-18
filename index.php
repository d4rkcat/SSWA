<!--
	d4rkcat's super vulnerable PHP webapp
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
-->

<html>
<body>
	<h2>d4rkcat's super secure-webapp.</h2>
	<p></p>
	What's your name?
	<form action="index.php?page=main" method="post">
		<input type="text" name="welcome">
		<input type="submit" value="Say Hi!">
	</form>
	Come again?
	<form action="index.php?page=main" method="post">
		<input type="text" name="welcome2">
		<input type="submit" value="Say Hi! (harder)">
	</form>
	Would you like to ping something?
	<form action="index.php?page=main" method="post">
		<input type="text" name="ping">
		<input type="submit" value="Ping Host">
	</form>
	How about this ping?
	<form action="index.php?page=main" method="post">
		<input type="text" name="ping2">
		<input type="submit" value="Ping Host (harder)">
	</form>
	the ping from hell?
	<form action="index.php?page=main" method="post">
		<input type="text" name="ping3">
		<input type="submit" value="Ping Host (hardest)">
	</form>
</body>
</html> 

<?php
session_start();
if (isset($_POST["reset"])){
	$_SESSION['pwned'] = null;
	$_SESSION['complete'] = null;
}

function fcount($new)
{
	if (isset($_SESSION['pwned'])){
		if (strpos($_SESSION['pwned'], $new) == false){
		$_SESSION['complete'] .= '1';
		$_SESSION['pwned'] .= $new;
		}
	} else {
		$_SESSION['complete'] = '1';
		$_SESSION['pwned'] = 'Completed: '.$new;
	}

}

file_put_contents('log.php', $_SERVER['HTTP_USER_AGENT']."\r\n");
if (!file_exists('config.php')) {
	$conf = "<?php echo '<h1><font color=3ADF00>(LFI easy): Pwn3D!</font></h1><h3>User: Admin Password:5uP3R-s3Cr3T-p4S5w0rD</h3>'; ?>\r\n";
	file_put_contents('config.php', $conf);
}
if (empty($_GET['page']))
{
	header('Location: index.php?page=main');
}
if(!empty($_POST["welcome"]))
{
	echo '<pre>Hello, '.$_POST["welcome"].'!</pre>';
	if (strpos($_POST["welcome"],'<script>') !== false) {
	echo '<font color = "3ADF00"><h1>(XSS easy): Pwn3D!</h1></font>';
	fcount('<font color=3ADF00>[XSS EASY] </font>');
	}
}
if(!empty($_POST["welcome2"]))
{
	$target = str_replace('<script>', '', $_POST["welcome2"]);
	echo '<pre>Hello, '.$target.'!</pre>';
	if (strpos(strtolower($target),'<script>') !== false) {
	echo '<font color = "FF8000"><h1>(XSS medium): Pwn3D!</h1></font>';
	fcount('<font color = "FF8000">[XSS MED] </font>');
	}
}
elseif(!empty($_POST["ping"]))
{
	echo '<h3>Ping Results:</h3>';
	if (strpos($_POST["ping"],';') !== false || strpos($_POST["ping"],'&&') !== false || strpos($_POST["ping"],'|') !== false) {
	echo '<font color = "3ADF00"><h1>(RCE easy): Pwn3D!</h1></font>';
	fcount('<font color = "3ADF00">[RCE EASY] </font>');
	}
	echo '<pre>'.shell_exec('ping -c 2 '.$_POST["ping"]).'</pre>';
	
}
elseif(!empty($_POST["ping2"]))
{
	echo '<h3>Ping Results:</h3>';
	$substitutions = array(
	'&&' => '',
	';' => '',
	);
	$target = str_replace(array_keys($substitutions), $substitutions, $_POST["ping2"]);
	if (strpos($_POST["ping2"],'|') !== false) {
	if (strlen($_POST["ping2"]) > 2){
	echo '<font color = "FF8000"><h1>(RCE medium): Pwn3D!</h1></font>';
	fcount('<font color = "FF8000">[RCE MED] </font>');
	}
	}
	echo '<pre>'.shell_exec('ping -c 2 '.$target).'</pre>';
}
elseif(!empty($_POST["ping3"]))
{
	echo '<h3>Ping Results:</h3>';
	$substitutions = array(
	'&&' => '',
	';' => '',
	);
	$target = str_replace(array_keys($substitutions), $substitutions, $_POST["ping3"]);
	if (strpos($target,'|') !== false) {
		if (strpos($_POST["ping3"],'|') !== false) {
			if (strpos($_POST["ping3"],' #') !== false) {
				echo '<font color = "FF0000"><h1>(RCE hard): Pwn3D!</h1></font>';
				fcount('<font color = "FF0000">[RCE HARD] </font>');
			}
		$hard = shell_exec('ping -c 2 '.$target.'| /dev/null');
		echo '<pre>'.$hard.'</pre>';
		}
	}
	else{
		echo '<pre>'.shell_exec('ping -c 2 '.$target).'</pre>';
	}
}
elseif(!empty($_GET["page"]))
{
	echo '<h3>';
	include($_GET["page"]. ".php");
	echo '</h3>';
}
if ($_GET["page"] == 'log'){
	if (strpos($_SERVER['HTTP_USER_AGENT'], '<?php') !== false && strpos($_SERVER['HTTP_USER_AGENT'], '$_GET') !== false){
		echo '<font color = "FF0000"><h1>(LFI>RCE hard): Pwn3D!</h1></font>';
		fcount('<font color = "FF0000">[LFI>RCE HARD] </font>');
	}

	$back = "<img width=700 src='http://www.myessentia.com/blog/wp-content/uploads/2012/10/cuttlefish1.jpeg'></img>";
}
elseif ($_GET["page"] == 'config'){
	fcount('<font color = "3ADF00">[LFI EASY] </font>');
	$back = "<img width=500 src='http://thetruthbehindthescenes.files.wordpress.com/2010/07/cuttlefish-3.jpg'></img>";
}
else{
	$back = "<img width=400 src='http://www.daveharasti.com/articles/speciesspotlight/images/cuttlefish2.jpg'></img>";
}
echo '<h3 ALIGN="RIGHT">'.strlen($_SESSION['complete']).'/7</h3>';
if (isset($_SESSION['pwned'])) {
	if (strlen($_SESSION['pwned']) == 296){
	echo '<h1>YOU WIN! GAME OVER...</h1>';
	$back = "<img width=500 src='http://thetruthbehindthescenes.files.wordpress.com/2010/07/cuttlefish-3.jpg'></img>";
}
	echo '<h4>'.$_SESSION['pwned'].'</h4>';
	echo '<form action="index.php?page=main" method="post"><input type="submit" name="reset" value="RESET"></form>';
}
echo $back;
exit()
;?>