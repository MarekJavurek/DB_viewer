<?php
@session_start();
error_reporting(0);

if (isset($_GET["phpinfo"])){
phpinfo();
}else{
// verze programu
$_SESSION["lng"]["version"] = "1.5";
// jazykova verze
if (isset($_GET["lang"])){
@include "./lng/".$_GET["lang"];
$_SESSION["lang"] = $_GET["lang"];
}
elseif (isset($_SESSION["lang"])){
@include "./lng/".$_SESSION["lang"];
}else{
include "./lng/eng.lng.php";
}


echo "<?phpxml version=\"1.0\" encoding=\"utf-8\"?>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-language" content="cs" />
<title><?php echo $_SESSION["lng"]["title"]; ?></title>
<style type="text/css">
/* <![CDATA[ */
@import url("style.css");
/* ]]> */
</style>
</head>
<body>
<div class="main">
<?php
echo "<a href='index.php'><h1>".$_SESSION["lng"]["welcome_in"]." ".$_SESSION["lng"]["title"]."</h1></a>";
echo "<div class='content'>";
// odhlasni
if ($_GET["action"] == "sign_off"){
$_COOKIE["lang"] = $_SESSION["lang"];
// session_unset();
// session_destroy();
unset($_SESSION["DB_server"]);
unset($_SESSION["DB_user_name"]);
unset($_SESSION["DB_password"]);
unset($_SESSION["pripojeno"]);;
// mysql_close($spojeni);
unset($spojeni);
echo "<strong> <img src='icons/tick.png' /> ".$_SESSION["lng"]["sign_off_ok"]." </strong>";
}

// odstraneni nebezpecnych znaku -> funkce input()
function input($a){
$a = trim($a);
$a = strip_tags($a);
$a = htmlspecialchars($a);
return $a;
}

// udaje k pripojeni k DB
if (isset($_POST["submit"])){
$DB_server = input($_POST["server"]);
$DB_user_name = input($_POST["user_name"]);
$DB_password = input($_POST["password"]);

$_SESSION["DB_server"] = input($_POST["server"]);
$_SESSION["DB_user_name"] = input($_POST["user_name"]);
$_SESSION["DB_password"] = input($_POST["password"]);

// pripojeni k DB
$spojeni = @mysql_pconnect($DB_server, $DB_user_name, $DB_password);
@mysql_query("SET CHARACTER SET 'utf8'");
@mysql_query("SET NAMES 'utf8'");
if ($spojeni){
echo "<strong> <img src='icons/tick.png' /> ".$_SESSION["lng"]["connect_ok"]." </strong>";
$_SESSION["pripojeno"] = 1;
// $_SESSION["spojeni"] = $spojeni;
}else{
echo "<strong> <img src='icons/stop.png' /> ".$_SESSION["lng"]["connect_er"]." </strong>";
$_SESSION["pripojeno"] = 0;
}
}

// formular napripojeni k DB "server", "jemno", "heslo"
if ($_SESSION["pripojeno"] == 0){
echo "<form method='POST' action='index.php'>
<table border='0'>
<tr> <td> ".$_SESSION["lng"]["server"].": </td><td> <img src='icons/server_database.png' border='0' /> <input name='server' type='text' value='localhost' size='20'> </td> </tr>
<tr> <td> ".$_SESSION["lng"]["user_name"].": </td><td> <img src='icons/user.png' border='0' /> <input name='user_name' type='text' value='root' size='20'> </td> </tr>
<tr> <td> ".$_SESSION["lng"]["password"].": </td><td> <img src='icons/database_key.png' border='0' /> <input name='password' type='password' value='' size='20'> </td> </tr>
<tr><td> </td><td> <input name='submit' type='submit' value='".$_SESSION["lng"]["connect"]."'> <img  src='icons/server_connect.png' border='0' /> </td></tr>
</table>
</form>";
}


if ($_SESSION["pripojeno"] == 1){
$spojeni = @mysql_pconnect($_SESSION["DB_server"], $_SESSION["DB_user_name"], $_SESSION["DB_password"]);

// Location
echo "<br />".$_SESSION["lng"]["location"].": <img  src='icons/server.png' border='0' /> ".$_SESSION["lng"]["server"].": <a href='?action=server_info'>".$_SESSION["DB_server"]."</a>";

if (isset($_GET["database_name"])){
echo " <img  src='icons/resultset_next.png' border='0' /> <img  src='icons/database.png' border='0' /> ".$_SESSION["lng"]["database"].": <a href='?action=view_database&database_name=".$_GET["database_name"]."'>".$_GET["database_name"]."</a>";
}

if (isset($_GET["table_name"])){
echo " <img  src='icons/resultset_next.png' border='0' /> <img  src='icons/table.png' border='0' /> ".$_SESSION["lng"]["table"].": <a href='?action=view_table&database_name=".$_GET["database_name"]."&table_name=".$_GET["table_name"]."'>".$_GET["table_name"]."</a> <br /><br />";
}

// Menu
echo "<p>".$_SESSION["lng"]["menu"].": <a href='?action=server_info'> <img  src='icons/server.png' border='0' /> ".$_SESSION["lng"]["server_info"]."</a> | <a href='?action=view_all_databases'> <img  src='icons/database.png' border='0' /> ".$_SESSION["lng"]["all_dbs"]."</a> | <a href='?action=sign_off'> <img  src='icons/disconnect.png' border='0' /> ".$_SESSION["lng"]["sign_off"]."</a> | <a href='?action=about'> <img  src='icons/user.png' border='0' /> ".$_SESSION["lng"]["about"]."</a> </p>";

$action_menu = array("view_table", "truncate_table", "drop_table", "rename_table");

if (in_array($_GET["action"], $action_menu)){
// actions
echo "".$_SESSION["lng"]["actions"].": <a href='?action=truncate_table&table_name=".$_GET["table_name"]."&database_name=".$_GET["database_name"]."'><img src='icons/table_go.png' border='0' /> ".$_SESSION["lng"]["truncate_table"]."</a> | <a href='?action=drop_table&table_name=".$_GET["table_name"]."&database_name=".$_GET["database_name"]."'><img src='icons/table_delete.png' border='0' /> ".$_SESSION["lng"]["drop_table"]."</a> | <a href='?action=rename_table&table_name=".$_GET["table_name"]."&database_name=".$_GET["database_name"]."'><img src='icons/table_edit.png' border='0' /> ".$_SESSION["lng"]["rename_table"]."</a> <br /><br />";
}


if ($_GET["action"] == "view_all_databases"){

// ---------------------------------------------

echo "<a href='?action=add_new_database'><img src='icons/database_add.png' border='0' /> ".$_SESSION["lng"]["add_db"]."</a> <br /><br />";

$sql = mysql_query("SHOW DATABASES");
// echo "<h2>".mysql_error()."</h2>";

while($pole = mysql_fetch_row($sql)){
// `
$sql1 = mysql_query("USE `".$pole[0]."` ");
$sql2 = mysql_query("SHOW TABLES FROM `".$pole[0]."` ");

$num2 = mysql_num_rows($sql2);

if ($num2 == 0){
$num2_x = "-";
}else{
$num2_x = $num2;}

echo "<a href='?action=view_database&database_name=".$pole[0]."' title='".$db_title."'><img src='icons/database.png' border='0' /> ".$pole[0]." (".$num2_x.")</a> <br />";

if($num2 == 0){
echo "<i>- There are no tables.</i><br />"; }

while($pole2 = mysql_fetch_row($sql2)){

$sql3 = mysql_query("SELECT * FROM ".$pole2[0]."");

$num3 = mysql_num_rows($sql3);


echo " &nbsp; &nbsp; <a  title='".$num3." ".$_SESSION["lng"]["table_rows"]."' href='?action=view_table&database_name=".$pole[0]."&table_name=".$pole2[0]."'><img src='icons/table.png' border='0' /> ".$pole2[0]."</a> <br />";
}
}
// ---------------------------------------------
// $soubor = mysql_query("SELECT * INTO OUTFILE 'SOUBOR_hovinko.TXT' FIELDS TERMINATED BY ';' FROM ".$pole2[0]."");
// FLAT BETA -> NOW VOL 42.
}
elseif ($_GET["action"] == "view_database"){
// mysql_select_db($_GET["database_name"], $spojeni);

$database = input($_GET["database_name"]);
echo "Tables in database \"".$database."\". <br />";
echo "<table border='0'>";
echo "<tr> <td>1</td> <td>2</td> <td>3</td> <td>4</td> <td>5</td> <td>6</td> </tr>";
$sql5 = mysql_query("SHOW TABLES FROM ".$database."");
while($pole5 = mysql_fetch_array($sql5)){
echo "<tr><td><b>".$pole5[0]."</b></td>";

$sql6 = mysql_query("DESCRIBE ".$pole5[0]."");
while($pole6 = mysql_fetch_array($sql6)){
echo "<tr><td>".$pole6[0]."</td><td>".$pole6[1]."</td><td>".$pole6[2]."</td><td>".$pole6[3]."</td><td>".$pole6[4]."</tr>";
}

unset($sql6);
unset($pole6);
}
echo "</table>";
}


elseif ($_GET["action"] == "view_table"){
$table = input($_GET["table_name"]);
$database = input($_GET["database_name"]);

// odstranit DB -> $sql = 'DROP DATABASE `ip_adresy`';

// vyprazddnit -> $sql = 'TRUNCATE TABLE `ip_adresy`';
// odstranit TBL -> $sql = 'DROP TABLE `ip_adresy`';
// prejmenovat TBL -> $sql = 'ALTER TABLE `ip_adresy` RENAME `ip_adresy2`';


echo "<br /> Info about table \"".$table."\" in database \"".$database."\". <br />";

echo "<table class='tabulka'>";
mysql_select_db(input($_GET["database_name"]), $spojeni);
$result = mysql_query("select * from ".$table."");
# vrátí informace o sloupci
$i = 0;
echo "<tr> <td>name</td><td>blob</td><td>max_length</td><td>multiple_key</td><td>not_null</td><td>numeric</td><td>primary_key</td><td>type</td><td>unique_key</td><td>unsigned</td><td>zerofill</td> </tr>";


while ($i < mysql_num_fields($result)) {

$meta = mysql_fetch_field($result);
if (!$meta) {
echo "-";
}
echo "<tr>
<td>".$meta->name."</td>
<td>".$meta->blob."</td>
<td>".$meta->max_length."</td>
<td>".$meta->multiple_key."</td>
<td>".$meta->not_null."</td>
<td>".$meta->numeric."</td>
<td>".$meta->primary_key."</td>
<td>".$meta->type."</td>
<td>".$meta->unique_key."</td>
<td>".$meta->unsigned."</td>
<td>".$meta->zerofill."</td>
</tr>";

$i++;
}

echo "</table>";
}

elseif ($_GET["action"] == "about"){
echo "<p> <img  src='icons/user.png' border='0' /> ".$_SESSION["lng"]["producted_by"]." <strong>Marek Javůrek</strong> <br />";
echo "<img  src='icons/telephone.png' border='0' /> +420 776016713 <br />";
echo "<img  src='icons/world.png' border='0' /> <a href='http://www.peane.cz/' target='_blank'>http://www.peane.cz/</a> <br />";
echo "<img  src='icons/email.png' border='0' /> <a href='mailto:info@peane.cz'>info@peane.cz</a></p>";
echo "<p>".$_SESSION["lng"]["producted_info"]."</p>";


echo "<span class='language'> ".$_SESSION["lng"]["chose_language"]." ";
$adresar = opendir("./lng");
while ($soubor = readdir($adresar)){
if ($soubor !== "." AND $soubor !== ".."){
// $soubor = str_replace(".", "_", $soubor);
$part = explode(".", $soubor);
echo " / <a href='?lang=".$soubor."'>".$part[0]."</a>";
}}
echo "</span>";


}


elseif ($_GET["action"] == "truncate_table"){
$table = input($_GET["table_name"]);
$database = input($_GET["database_name"]);

if(isset($_POST["yesno1"]) OR isset($_POST["yesno2"])){

if(isset($_POST["yesno1"])){
$sql = mysql_query("TRUNCATE TABLE ".$table."");
if($sql){
echo "".$_SESSION["lng"]["truncate_ok_1"]." \"".$table."\" ".$_SESSION["lng"]["truncate_ok_2"].".";
}else{
echo "ERROR \"".$table."\"? ";
}

}else{
echo "<a href='?action=view_database&database_name=".$database."'>Back to database \"".$database."\".</a>";
}

}else{
echo "".$_SESSION["lng"]["truncate_ask"]." \"".$table."\"? <br />
<form method='POST' action='?action=truncate_table&table_name=".$table."&database_name=".$database."'>
<input type='submit' name='yesno1' value='".$_SESSION["lng"]["confirm_yes"]."' />
<input type='submit' name='yesno2' value=' ".$_SESSION["lng"]["confirm_no"]." ' />
<form>";
}
}


elseif ($_GET["action"] == "drop_table"){
echo "drop_table";
}

elseif ($_GET["action"] == "rename_table"){
// ----------- RENAME TABLE  ----------------------


if (isset($_GET["complete"])){

$rename_table = input($_POST["name_of_new_table"]);
$old_table = $_GET["table_name"];
$cur_database = $_GET["table_name"];

$sql0 = mysql_query("USE `".$cur_database."` ");
$sql = mysql_query("ALTER TABLE ".$old_table." RENAME ".$rename_table."");

$aaa = "MESSAGE";

if ($sql) {
echo "<img src='icons/tick.png' /> ".$aaa." \"".$old_table."\" ".$aaa;
$_GET["table_name"] = $rename_table;
} else {
echo "<img src='icons/database_error.png' /> ".$aaa." <br />";
}



}else{
echo "<form method='POST' action='?action=rename_table&table_name=".$_GET["table_name"]."&database_name=".$_GET["database_name"]."&complete'>
<img  src='icons/table_edit.png' border='0' />
".$_SESSION["lng"]["rename_table_ok"]." \"".$_GET["table_name"]."\" ".$_SESSION["lng"]["rename_table_2"].": <input name='name_of_new_table' type='text' value='' size='20' maxlength='65'> (".$_SESSION["lng"]["db_create_form2"].") <br />
<input type='submit' value='".$_SESSION["lng"]["rename_table_ok"]."'>
</form>";
}

// --------------------------------------------
}
elseif ($_GET["action"] == "add_new_database"){

if (isset($_GET["complete"])){

$new_db = input($_POST["name_of_new_db"]);

$sql = mysql_query("CREATE DATABASE '".$new_db."'");
if (!$sql) {
$sql = mysql_create_db($new_db, $spojeni);
}


if ($sql) {
echo "<img src='icons/tick.png' /> ".$_SESSION["lng"]["db_create_ok1"]." \"".$new_db ."\" ".$_SESSION["lng"]["db_create_ok2"];
} else {
echo "<img src='icons/database_error.png' /> ".$_SESSION["lng"]["db_create_er1"]." <br />";
echo $_SESSION["lng"]["db_create_er2"];
}



}else{
echo "<form method='POST' action='index.php?action=add_new_database&complete'>
<img  src='icons/database_add.png' border='0' />
".$_SESSION["lng"]["db_create_form1"].": <input name='name_of_new_db' type='text' value='' size='20' maxlength='65'> (".$_SESSION["lng"]["db_create_form2"].") <br />
<input type='submit' value='".$_SESSION["lng"]["db_create_form3"]."'>
</form>";
}

}else{

$table_count = 0;
$db_count = 0;

$sql = mysql_query("SHOW DATABASES");
while($pole = mysql_fetch_row($sql)){
$db_count++;

$sql1 = mysql_query("USE `".$pole[0]."` ");
$sql2 = mysql_query("SHOW TABLES FROM `".$pole[0]."` ");

while($pole2 = mysql_fetch_row($sql2)){
$table_count++;
}
}

echo "<table class='tabulka_info'>";
echo "<tr> <td>".$_SESSION["lng"]["info_Actual_server"].":</td> <td> <strong> ".$_SESSION["DB_server"]." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_login_user"].":</td> <td> <strong> ".$_SESSION["DB_user_name"]." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_actual_charset"].":</td> <td> <strong> ".mysql_client_encoding($spojeni)." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_number_of_databases"].":</td> <td> <strong> ".$db_count." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_number_of_tables"].":</td> <td> <strong> ".$table_count." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_mySQL_client_info"].":</td> <td> <strong> ".mysql_get_client_info()." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_mySQL_host_info"].":</td> <td> <strong> ".mysql_get_host_info()." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_version_of_MySQL_protocol"].":</td> <td> <strong> ".mysql_get_proto_info()." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_version_of_MySQL_server"].":</td> <td> <strong> ".mysql_get_server_info()." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_view_version_apache"].":</td> <td> <strong> ".apache_get_version()." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_view_version_PHP"].":</td> <td> <strong> ".phpversion()." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_view_info_about_PHP"].":</td> <td> <strong> <a href='?phpinfo'>PHP info</a> </strong> </td> </tr>";

echo "<tr> <td>".$_SESSION["lng"]["info_SERVER_ADDR"].":</td> <td> <strong> ".$_SERVER["SERVER_ADDR"]." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_SERVER_ADMIN"].":</td> <td> <strong> <a href='".$_SERVER["SERVER_ADMIN"]."'>".$_SERVER["SERVER_ADMIN"]."</a> </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_SERVER_NAME"].":</td> <td> <strong> ".$_SERVER["SERVER_NAME"]." </strong> </td> </tr>";
echo "<tr> <td>".$_SESSION["lng"]["info_SERVER_PORT"].":</td> <td> <strong> ".$_SERVER["SERVER_PORT"]." </strong> </td> </tr>";


echo "</table>";
// echo php_logo_guid();

}
}
?>
</div></div>
</body>
</html>
<?php } ?>