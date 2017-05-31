<?php
//by fenix
error_reporting(E_ALL ^E_NOTICE);
ini_set("display_errors","on");
if($_POST['action'] == "logtrack"){
 $_dt = "";
 $doneby = $_POST['doneby'];
 if($_POST['checked'] && is_array($_POST['checked'])){
 	foreach ($_POST['checked'] as $fp => $v) {
 		$_dt .= $fp."#".$doneby."\n";
 	}
  fwrite(fopen("fer.track","a"),$_dt);
 }
}

if(!file_exists("fer.track")){
fwrite(fopen("fer.track","w"),"");
}
$farr = fopen("fer.track", "r");
if ($farr) {
    while (($farrline = fgets($farr)) !== false){
		$_splt = explode("#",$farrline);
		$_f[$_splt[0]] = $_splt[1];
	}
}


$handle = fopen("err.log", "r");
if ($handle) {
?>
<form method="POST">
<table cellspacing="5" cellpadding="5" style="border-collapse: collapse;font-family: monospace" border="1"><tr><th>By : </th><th><input type="" required="" value="<?=$_REQUEST['doneby']?>" name="doneby"><input type="submit" value="Submit Fixed"></th></tr></table>
<table border=1  cellspacing="5" cellpadding="5" style="border-collapse: collapse;margin-top:10px;font-family: monospace">
<tr><th>Fixed By </th><th>LOG</th></tr>
<?php
    while (($line = fgets($handle)) !== false) {
	$filep = ftell($handle);
	$m = null;
	preg_match( "/^\[(.*?)\]\ \[(.*?)\]\ \[(.*?)\]\ (.*?)$/i", $line, $m );
	?>
	<tr>
	<?php
	if($_f[$filep]){
	?>
	<td style="color:blue;">
	<table border="0"><tr><td><div style="color:green">✔</div></td><td><?php echo $_f[$filep]?></td></tr></table>
	<?php
	}else{
	?>
	<td >
	<table ><tr>
	<td><div style="width: 5px;height: 5px;background: red;float:left;"></div></td>
	<td><input type="checkbox" name="checked[<?=$filep?>]"></td><td><input type="hidden" name="action" value="logtrack"></td></tr></table>
	<?php
	}
	?>
	<td bgcolor="<?=$_f[$filep]?'whitesmoke':'bisque'?>">
		<table style="border-collapse: collapse;color:<?=$_f[$filep]?'gray':'black'?>">
			<?php
			echo "<tr bgcolor=''><td style='border-right:1px solid gray;width:270px;'>" . $m[1] . "</td><td>" . htmlspecialchars ($m[4]) . "</td></tr>";
			?>
		</table>
	</td>
	</tr>
	<?php
    }
?>
</table>
</form>
<?php
    fclose($handle);
} else {
    // error opening the file.
}
exit;
?>