<?php

if(isset($_REQUEST['pid'])){
	$id =$_REQUEST['pid'];
	$result = mysql_query("SELECT * FROM ver_chronoforms_data_letters_vic WHERE cf_id  = '$id'");
}else{
	$template_name =$_REQUEST['titleID'];	
	$sql = "SELECT * FROM ver_chronoforms_data_letters_vic WHERE template_name  = '$template_name' ";
	//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_sa\\my-error.log');
	$result = mysql_query($sql);
}

 

$retrieve = mysql_fetch_array($result);
if (!$result) 
		{
		die("Error: Data not found..");
		}
	//$htmlcontent = stripcslashes($retrieve['template_content']);
	$htmlcontent = $retrieve['template_content'];
	//error_log("IN download_pdf.php -> ".$htmlcontent, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_sa\\my-error.log');
?>



<?php echo $htmlcontent; ?>