<?php
include_once "php/accesscontrol.php";
include_once "php/db.php";
include_once "menu.php";

dbConnect();

#checkLevel($level,1);

$thread_id = $_GET['thread_id'];
$here = "/";
$game_page = "${here}game/";
if ( $thread_id == "" ) {
?>
<html>
<head>
<script language='javascript'>
<!--
window.history.back();
//-->
</script>
</head>
<body>
Please hit your browsers back button.
</body>
</html>
<?php
exit;
}


$sql = sprintf("select id, title, auto_vt from Games where thread_id=%s",quote_smart($thread_id));
$result = mysql_query($sql);
if ( mysql_num_rows($result) == 1 ) {
  $game_id = mysql_result($result,0,0);
  $title = mysql_result($result,0,1);
  $tiebreaker = mysql_result($result,0,2);
} else {
  $game_id = 0;
  $title = "Invalid Game";
}
$sql = sprintf("select last_dumped from Post_collect_slots where game_id=%s",$game_id);
$result = mysql_query($sql);
if ( mysql_num_rows($result) == 1 ) {
  $last_dumped = mysql_result($result,0,0);
}

?>
<html>
<head>
<title>Inverted Vote Tally for <?=$title;?></title>
<link rel='stylesheet' type='text/css' href='<?=$here;?>bgg.css'>
</head>
<body>
<?php display_menu();?>
<h1>Inverted Vote Tally for <?=$title;?> as of <?=$last_dumped;?></h1>
<?php
print "<p>Nightfall votes are denoted with an '*' after the player's name.</p>\n";
$sql_game_day = sprintf("select day from Games where id=%s",$game_id);
$result = mysql_query($sql_game_day);
$game_day = mysql_result($result,0,0);
if($game_day > 0)
{
$sql_days = sprintf("select distinct day from Tally_display_inverted where game_id=%s order by day desc",$game_id);
$result_days = mysql_query($sql_days);
if ( mysql_num_rows($result_days) > 0 ) {
while ( $day = mysql_fetch_array($result_days) ) {
?>
<table class='forum_table' width='100%'>
<tr><th colspan='6'>Day <?=$day[0];?></th></tr>
<tr><th width='10%'>Player</th><th width='2%'>Count</th><th>Voted on...</th></tr>
<?php
$sql_votes = sprintf("select voter, total, votes_html from Tally_display_inverted where game_id=%s and day=%s ",$game_id, $day[0]);
$result = mysql_query($sql_votes);
while ( $row = mysql_fetch_array($result) ) {
print "<tr>";
print "<td>".$row['voter']."</td>";
print "<td align='center'>".$row['total']."</td>";
print "<td>".$row['votes_html']."</td>";
print "</tr>\n";
}
print "</table>\n";
$sql_nonvoters = sprintf("select get_non_voters(%d, %d);",$game_id, $day[0]);
$res = mysql_query($sql_nonvoters);
$nonvoters = mysql_result($res,0,0);
print "Not voting: $nonvoters<br><br>\n";  
}
}
}
?>
</body>
</head>
