<?PHP
include "dbms.php";
$db=new dbAdapter("hostname","username","password","dbname");
$_GET["json"]=false;
$db->query("select * from collection limit 0,10");
if($_GET["json"]==true){
	header("Content-type:application/json");
	echo json_encode($db->recordsArray());	
	exit(0);
}
else{
	
	tableTag($db->recordsArray(false));
	
}
function tableTag($array){
	echo "\n<table>\n";
	foreach($array as $tr){
	echo "	<tr>\n";
		foreach($tr as $td1=>$td2)
		echo "		<td>".$td1."</td><td>".$td2."</td>\n";
	echo "	<tr>\n";
	}
	echo "</table>\n";
	

	
}
?>
