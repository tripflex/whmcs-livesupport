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
<title>Install: Live Chat Addon</title>
</head>

<body style="margin: 0px;">
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
		
	$file = file_get_contents("livechat.sql") or exit ("Error 142: Unable to find sql file!");
	$query = preg_split("/;/", $file) or exit ("Error 143: Unable to split sql file!");
	foreach($query as $sql) {
		mysql_query(trim($sql));
	}
	
	$file = file_get_contents("livechat-geoip.sql") or exit ("Error 144: Unable to find sql file!");
	$query = preg_split("/;/", $file) or exit ("Error 145: Unable to split sql file!");
	foreach($query as $sql) {
		mysql_query(trim($sql));
	}
	//echo "Installation Complete! Please <u><strong>DELETE</strong></u> the chat-install folder.<br />Then proceed to the <a href=\"".$settings["SystemURL"]."/admin/addonmodules.php?module=live_chat_settings\">settings page</a>.";
}
?>

<div style="background-image: url('images/banner_bg.jpg'); background-repeat:repeat-x; width: 100%; text-align: center;"><?php if ($_POST["install"] == "true") echo "<a href=\"".$settings["SystemURL"]."/admin/addonmodules.php?module=live_chat_settings\">"; ?><img src="images/<?php if ($_POST["install"] == "true") echo "after-install"; else echo "pre-install" ?>.jpg" border="0" /><?php if ($_POST["install"] == "true") echo "</a>"; ?></div>
<?php if ($_POST["install"] != "true") { ?>
<form method="post" action="install.php">
	<input type="hidden" name="install" value="true" />
    <center><input type="submit" value="Install!" /></center>
</form>
<?php } ?>
</body>
</html>