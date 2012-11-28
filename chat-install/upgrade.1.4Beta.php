<?
/*
    WHMCS Addon Live Support - Provides a way for you to instantly communicate
    with your customers.
    Copyright (C) 2010-2012 WHMCS Addon

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upgrade: Live Chat Addon</title>
</head>

<body style="margin: 0px;">

<div style="background-image: url('images/banner_bg.jpg'); background-repeat:repeat-x; width: 100%; text-align: center;"><img src="images/<?php if ($_POST["install"] == "true") echo "after-upgrade"; else echo "pre-install" ?>.jpg" /></div>
<?php if ($_POST["install"] != "true") { ?>
<form method="post" action="upgrade.1.4Beta.php">
	<input type="hidden" name="install" value="true" />
    <center><input type="submit" value="Upgrade!" /></center>
</form>
<?php } ?>

<?php
if ($_POST["install"] == "true") {
	$directoryFinder = explode("/", $_SERVER["SCRIPT_FILENAME"]);
	foreach($directoryFinder as $pathPart) {
		if ($pathPart != "") {
			if ($pathPart != "chat-install") {
				$dir .= "/".$pathPart;
			} else {
				$dir .= "/";
				break;
			}
		}
	}
	
	require($dir."dbconnect.php");
	
	$result2 = mysql_query("SELECT * FROM `tblconfiguration`");
	while($row = mysql_fetch_array($result2)) {
		$settings[$row[0]] = $row[1];
	}
		
	$file = file_get_contents("livechat-upgrade.1.4Beta.sql") or exit ("Error 142: Unable to find sql file!");
	$query = preg_split("/;/", $file) or exit ("Error 143: Unable to split sql file!");
	foreach($query as $sql) {
		mysql_query(trim($sql));
	}
}
?>
</body>
</html>