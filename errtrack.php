<!-- by fenix -->

<script src="/js/jquery-1.11.1.min.js"></script>
<script>
	var lastChecked = null;

$(document).ready(function() {
    var $chkboxes = $('.chkbox');
    $chkboxes.click(function(e) {
        if(!lastChecked) {
            lastChecked = this;
            return;
        }

        if(e.shiftKey) {
            var start = $chkboxes.index(this);
            var end = $chkboxes.index(lastChecked);

            $chkboxes.slice(Math.min(start,end), Math.max(start,end)+ 1).prop('checked', lastChecked.checked);

        }

        lastChecked = this;
    });

});


</script>
<center><font size="5">Simple Bug Checklist</font></center>
<table id="main_table" style="margin:5px;"><tr><td><a href="/tools/tool_errorlog.php?gethost=abc"><b>ABC.com</b></a></td></tr></table>
<?php

$_elogs = ["abc" => "abc.com"];// if error logs is in format abc.com.errorlog

if(!$_REQUEST['gethost']) $_REQUEST['gethost'] = "abc";
if($_elogs[$_REQUEST['gethost']]){
	$_host = $_REQUEST['gethost'];
}else{
exit("No log has defined for requested host");
}
exec("ls -ftr /var/log/httpd/".$_elogs[$_host].".error* | tail -1",$o);

$_errfile = $o[0];
//$_errfile = "err2.log";
if(!file_exists($_errfile)) exit("Log file not found : $_errfile");
$_inod = fileinode($_errfile);

error_reporting(E_ALL ^E_NOTICE);
ini_set("display_errors","on");
if($_POST['action'] == "logtrack"){
 $_dt = "";
 $doneby = $_POST['doneby'];
 if($_POST['checked'] && is_array($_POST['checked'])){
 	foreach ($_POST['checked'] as $fp => $v) {
 		$_dt .= $_inod."#".$fp."#".$doneby."\n";
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
		$_f[$_splt[1]]['value'] = $_splt[2];
		$_f[$_splt[1]]['inode'] = $_splt[0];
	}
}


$handle = fopen($_errfile, "r");
if ($handle) {

	$_cnt = 0;
	exec("wc -l < $_errfile",$oo);
	$_cnt = $oo[0];
	$_counter = 0;
?>
<form method="POST">
<table cellspacing="5" cellpadding="5" style="border-collapse: collapse;font-family: monospace" border="1"><tr><th>By : </th><th><input type="" required="" value="<?=$_REQUEST['doneby']?>" name="doneby">&nbsp;<input type="submit" value="update"></th></tr></table>
<table border=1  cellspacing="2" cellpadding="2" style="border-collapse: collapse;margin-top:10px;font-family: monospace">
<tr><th>Fixed By </th><th>LOG</th></tr>
<?php

    while (($ln = fgets($handle)) !== false) {
    	if($_counter++ < $_cnt - 500  ) continue;
    	preg_match( "/^\[(.*?)\]\ \[(.*?)\]\ \[(.*?)\]\ (.*?)$/i", $ln, $m1 );
    	$uniq = md5($m1[1].$m1[3]);
		$fdata[$uniq] = $ln;//ftell($handle)
	}
	$fdata = array_reverse($fdata);
	foreach ($fdata as $filep => $line) {

	$m = null;
	preg_match( "/^\[(.*?)\]\ \[(.*?)\]\ \[(.*?)\]\ (.*?)$/i", $line, $m );
	?>
	<tr>
	<?php

	if(	$_f[$filep]['inode'] == $_inod && $_f[$filep]){
	?>
	<td style="color:blue;">
	<table border="0"><tr><td><div style="color:green">✔</div></td><td><?php echo $_f[$filep]['value']?></td></tr></table>
	<?php
	}else{
	?>
	<td >
	<table ><tr>
	<td><div style="width: 5px;height: 5px;background: red;float:left;"></div></td>
	<td><input style="cursor: pointer;" class="chkbox" type="checkbox" name="checked[<?=$filep?>]"></td><td><input type="hidden" name="action" value="logtrack"></td></tr></table>
	<?php
	}
	?>
	<td bgcolor="<?=($_f[$filep]['inode'] == $_inod && $_f[$filep]['value'])?'whitesmoke':'bisque'?>">
		<table style="font-size:12px;border-collapse: collapse;color:<?=($_f[$filep]['inode'] == $_inod && $_f[$filep]['value'])?'gray':'black'?>">
			<?php
			echo "<tr bgcolor=''><td style='border-right:1px solid gray;width:210px;font-size:11px;'>" . $m[1] . "</td><td><div style='width:100%;word-break: break-word;font-size:11px;'>" . htmlspecialchars ($m[4]) . "</dvi></td></tr>";
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
