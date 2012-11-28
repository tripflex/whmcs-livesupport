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
@error_reporting(0);
@ini_set("register_globals", "off");
/*
This file is the main controller for operators.
1. Allows operators to view clients requesting chat
2. Shows chat statuses
3. Transfer calls
*/

// Find WHMCS Directory
//    Set $pathPart to the folder to exclude from.
$directoryFinder = explode("/", $_SERVER["SCRIPT_FILENAME"]);
$x = 0;

foreach($directoryFinder as $pathPart) {
	if ($x < count($directoryFinder)-2) {
		if ($pathPart != "") {
				$dir .= "/".$pathPart;
		}
	} else {
		$dir .= "/";
		break;	
	}
	$x++;
}


require_once($dir."dbconnect.php");
// Begin Check Function

function check_license($licensekey,$localkey="") {
    return array("status"=>"Active");
}

// End Check Function


# Get Variables from storage (retrieve from wherever it's stored - DB, file, etc...)
if (!isset($chat_settings)) {
	  $result2 = mysql_query("SELECT * FROM `chat_settings`");
	  while($row = mysql_fetch_array($result2)) {
		  $chat_settings[$row[0]] = $row[1];
	  }
}

//echo $chat_settings["localkey"];
# The call below actually performs the license check. You need to pass in the license key and the local key data
$results = check_license($chat_settings["licensekey"],$chat_settings["localkey"]);

# For Debugging, Echo Results
//echo "<textarea cols=100 rows=20>"; print_r($results); echo "</textarea>";
//echo ":".$results["status"];
if ($results["status"]=="Active") {
    # Allow Script to Run
    if ($results["localkey"]) {
        # Save Updated Local Key to DB or File
		if (!isset($chat_settings["localkey"])) {
			$result2 = mysql_query("INSERT INTO `chat_settings` (`value`, `setting`) VALUES ('".$results["localkey"]."', 'localkey')") or die(mysql_error());
		} else {
			$result2 = mysql_query("UPDATE `chat_settings` SET `value`='".$results["localkey"]."' WHERE `setting`='localkey'") or die(mysql_error());
		}
		
    }
} else {
	echo "<strong>Your license key has come back with an error.</strong><br /><br />If you believe this is a mistake please contact <a href='http://whmcsaddon.com'>WHMCS Addon's Support Team</a> with the following debug information:<br /><textarea style='width: 500px; height: 50px;'>".$results["curl"]."</textarea>";
	
	$invalidLicense = true;
	$licenseStatus = $results["status"];
}

if (!$invalidLicense) {
?>

<style type="text/css">
#monitor {
	background-color: white;
}
.monitorTable {
	width: 945px;
	height: 38px;
	background-repeat:no-repeat;
}
.monitorHeader {
	background-image: url(images/tableheader.jpg);
}
.monitorContent {
	background-image: url(images/tablecontent.jpg);
}
th {
	font-size: 14px;
	font-weight: bold;
	font-family: arial;
	color: #5a5959;
}
.monitorHName {
	padding-left: 22px;
	text-align: left;
	width: 145px;
}
.monitorHDepartment {
	text-align: left;
	width: 154px;
}
.monitorHQuestion {
	text-align: left;
	width: 296px;
}
.monitorHStatus {
	text-align: center;
	width: 74px;
	font-size: 10px;
	font-weight: 100;
}
.monitorHActions {
	text-align: center;
	width: 190px;
	font-size: 10px;
	font-weight: 100;
}
.monitorHMore {
	text-align: center;
	width: 74px;
	font-size: 10px;
	font-weight: 100;
}
.monitorHBetween {
	color: #cacaca;
	font-size: 12px;
}

td {
	font-size: 12px;
	font-family: arial;
	color: #5a5959;
}
.monitorName {
	padding-left: 22px;
	text-align: left;
	width: 145px;
}
.monitorDepartment {
	text-align: left;
	width: 154px;
}
.monitorQuestion {
	text-align: left;
	width: 296px;
}
.monitorStatus {
	text-align: center;
	width: 74px;
	font-size: 10px;
	font-weight: 100;
}
.monitorActions {
	text-align: center;
	width: 190px;
	font-size: 10px;
	font-weight: 100;
}
.monitorMore {
	text-align: center;
	width: 74px;
	font-size: 14px;
	font-weight: bold;
}
.actionAnswer {
	color: #060;
	font-size: 12px;
}
.actionIgnore {	
	color: #900;
}
.additionalInfo {
	background-color:#f1f1f1;
	border:solid 1px #dedede;
	margin-top: 5px;
}
textarea.adminNotes {
	width: 250px;
	height: 75px;
}
#liveupdate {}

#receiver, #activereceiver {
	visibility: hidden;
	width: 0px;
	height: 0px;
	overflow: hidden;
}

#method {
	background-image:url("images/session_view.jpg");
	background-repeat:no-repeat;
	margin:5px;
	padding:5px;
	width:205px;
}

.loadImage {
	margin-left: 10px;
	margin-top: 10px;
	float: right;
}

#blackFader {
	background-color:black;
	width: 1px;
	height: 1px;
	position: fixed;
	top: -500px;
	left: -500px;	
}

#chatScripts {
	background-color:#E1E1E1;
	font-size:12px;
	padding:5px;
	width:450px;
	border:1px solid #CCC;
	position: absolute;
	top: -500px;
	left: -500px;
}

.scriptTable  {
	border:1px solid #CCCCCC;
	border-collapse:collapse;
}
.scriptTable td {
	padding:5px;
	width:100px;
}
.scriptTable td textarea {
	width:100%;
}
.scriptTable td input {
	margin-left:25px;
}
#onlineButton {
background-color:white;
background-image:url("images/online_bar.jpg");
background-repeat:repeat-x;
border-top:1px solid #999999;
border-left:1px solid #999999;
bottom:0;color:#000000;
height:18px;
padding:2px;
position:fixed;
right:0;	
}
</style>



<?

if ($invalidLicense) {
	echo "<div class=\"errorbox\">";
	echo "Your licensing for this software has come back with an error.";
	echo "</div>";
}
?>

<img src="images/loading.gif" class="loadImage" />

<div id='onlineButton'></div>
<div id='soundButton' style='background-color:white;background-image:url("images/online_bar.jpg");background-repeat:repeat-x;border-top:1px solid #999999;bottom:0;color:#000000;height:18px;padding:2px;position:fixed;right:42px;border-left:1px solid #999999;'></div>

<div class="newReq">
	<div id="method">
		New Requests
	</div>
	<table class="monitorTable monitorHeader">
	  <tr>
		<th class="monitorHName">Name</th>
		<th class="monitorHDepartment">Department</th>
		<th class="monitorHQuestion">Question</th>
		<th class="monitorHStatus">Status</th>
		<th class="monitorHBetween">|</th>
		<th class="monitorHActions">Actions</th>
		<th class="monitorHBetween">|</th>
		<th class="monitorHMore">GeoIP</th>
	  </tr>
	</table>
	<div id="liveupdateNew"></div>
</div>

<div id="curReq">
	<div id="method">
		Current Requests
	</div>
	<table class="monitorTable monitorHeader">
	  <tr>
		<th class="monitorHName">Name</th>
		<th class="monitorHDepartment">Department</th>
		<th class="monitorHQuestion">Question</th>
		<th class="monitorHStatus">Status</th>
		<th class="monitorHBetween">|</th>
		<th class="monitorHActions">Actions</th>
		<th class="monitorHBetween">|</th>
		<th class="monitorHMore">GeoIP</th>
	  </tr>
	</table>
	<div id="liveupdateCur"></div>
</div>

<div id="monitor">
	<div id="method">
		Live Monitor
	</div>
	<table class="monitorTable monitorHeader">
    	<tr>
        	<th class="monitorHName">User</th>
            <th class="monitorHDepartment">IP Address</th>
            <th class="monitorHQuestion">Current Page</th>
            <th class="monitorHStatus">Total Time</th>
            <th class="monitorHBetween">|</th>
            <th class="monitorHActions">Actions</th>
            <th class="monitorHBetween">|</th>
            <th class="monitorHMore">GeoIP</th>
       </tr>
   </table>
   <div id="liveupdateMon"></div>
</div>
	<div id="blackFader" onclick="cancelInjectScript()"></div>
	<div id="chatScripts">
		<table width="100%" class="scriptTable">
			<tr>
				<th>Name</th>
                <th>Description</th>
                <th>Script Value</th>
                <th></th>
			</tr><?
$result = mysql_query("SELECT * FROM `chat_scripts`;");

$countScript = 0;
while($row = mysql_fetch_array($result)) {
	echo "<tr><td>". $row["name"] ."</td><td>". $row["description"] ."</td><td width=\"40%\"><textarea autocomplete=\"off\" class=\"scriptElementTextarea s".$countScript."\">". $row["value"] ."</textarea></td><td width=\"110px\"><input type=\"submit\" onclick=\"sendInjectScript('.scriptElementTextarea.s".$countScript."');\" value=\"Send\" /></td></tr>";
	$countScript++;
}
?>
			<tr><td><i>Custom Script</i></td><td colspan="2"><textarea autocomplete="off" class="scriptElementTextarea sCustom"></textarea></td><td width="110px"><input type="submit" onclick="sendInjectScript('.scriptElementTextarea.sCustom');" value="Send" /></td></tr>
 		</table>
    </div>
    
<div id="receiver"></div>
<div id="activereceiver"></div>
    
<script type="text/javascript" src="../includes/jscript/adminchat.js.php"></script>
<? } ?>