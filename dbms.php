<?PHP
class dbAdapter
{
private $host;
private $uname;
private $pw;
private $db;
private $result;
private $conn;
private $error;
private $errno;
private $info;
function __construct($host,$uname,$pw,$db)
{
$this->host=$host;
$this->uname=$uname;
$this->pw=$pw;
$this->db=$db;
$this->conn=$this->connect($host,$uname,$pw,$db);
}

function close()
{
mysqli_close($this->conn);	
return "";
}
function data()
{
return mysqli_fetch_row($this->result)[0];
}
function single()
{
return mysqli_fetch_row($this->result);	
}
function errorlog($query,$info,$errorno,$error)
{
$errorFile=fopen("dbmserror.log","w");
$errorText=$query." ".$info."  ".$errorno." ".$error."\n"; 
fwrite($errorFile,$errorText);	
fclose($errorFile);
}
function gconn()
{
return $this->conn;	
}
public  function  connect($host,$uname,$pw,$db)
{
$conn=mysqli_connect($host,$uname,$pw,$db);
return $conn;	
}	
function query($sql)
{
unset($this->result);
$result=mysqli_query($this->conn,$sql);
$this->result=$result;
$this->info=mysqli_info($this->conn);
$this->error=mysqli_error($this->conn);
$this->errorno=mysqli_errno($this->conn);
if($this->errorno!=0)
$this->errorlog($sql,$this->info,$this->errorno,$this->error);
return $result;
}
public function error()
{
echo $this->errorno;
if($this->errorno==0)
return "no errors</br>";
else if($this->errorno==1062)
	return "Duplicate Entry<br/>";
else if($this->errorno==1136)
	return "Column Count Doesnot Match</br>";
}
function isExists()
{
return mysqli_num_rows($this->result)==0?false:true;	
}
function fetchFields($arg)
{
return array_keys(mysqli_fetch_assoc($arg));
}
function json($php=false)
{
$re2=$re=$this->result;
$fields=$this->fetchFields($re2);
$i=0;
if(is_bool($re))
return false;
$result=array();
//Field Names
if(is_object($re))
if(mysqli_num_rows($re)>0)
{
	
	while($row=mysqli_fetch_row($re))
	{
	
	$result[$i]=$row;	
	$field_names[$i]=mysqli_fetch_field($re)->name;
	$i++;
	
	}
}	else
	return json_encode(mysqli_errno($this->conn));
return $php==true?$result:json_encode($result);
}
public function recordsArray($json=true)
{
//header("CONTENT-TYPE:application/json");
$result=array();
$field_names=array();
$fn=$re=$this->result;
while($field=mysqli_fetch_field($fn))
	array_push($field_names,$field->name);
while($row=mysqli_fetch_row($re))
	array_push($result,$row);
	//var_dump($result,$field_names);
for($i=0;$i<count($result);$i++)
	$result[$i]=array_combine($field_names,$result[$i]);
return $json==true?json_encode($result):$result;
}
public function __toString()
{
if(isset($this->result) && $this->result==true && mysqli_num_rows($this->result)>0)
{
return $this->json();
}
else if($this->result==false)
	return mysqli_error($this->conn);
else
	return "no Query Executed";
}

}
//$db=new dbSlave("localhost","root","","lib_mgmt");
//$db->connect("localhost","root","","lib_mgmt");

/*$db->query("set 
character_set_connection=utf8,
character_set_results=utf8,
character_set_client=utf8,
character_set_server=utf8");*/
?>