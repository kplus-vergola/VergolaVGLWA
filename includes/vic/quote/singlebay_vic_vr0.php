<?php 
//error_log("Call singlebay.php", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  //exit; 
$next_increment = 0;
// $qShowStatus = "SHOW TABLE STATUS LIKE 'ver_chronoforms_data_followup_vic'";
// $qShowStatusResult = mysql_query($qShowStatus) or die ( "Query failed: " . mysql_error() . "<br/>" . $qShowStatus );
// $row = mysql_fetch_assoc($qShowStatusResult);
// $next_increment = $row['Auto_increment'];
// $getprojectid = 'PRV'.$next_increment;
// error_log("1: ".print_r($row,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

$qShowStatus = "SELECT cf_id+1 AS Auto_increment FROM ver_chronoforms_data_followup_vic ORDER BY cf_id DESC LIMIT 1";
$qShowStatusResult = mysql_query($qShowStatus) or die ( "Query failed: " . mysql_error() . "<br/>" . $qShowStatus );
$row = mysql_fetch_assoc($qShowStatusResult);
$next_increment = $row['Auto_increment'];
$getprojectid = 'PRV'.$next_increment;
//error_log("2: ".print_r($row,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
 
$retrieveb = null;
 
$resultc = mysql_query("SELECT * FROM ver_chronoforms_data_clientpersonal_vic WHERE clientid  = '$QuoteID'");
$retrievec = mysql_fetch_array($resultc);
if (!$resultc) {die("Error: Data not found..");}

$salesrep = $retrievec['repname']; 
$rep_id = $retrievec['repident'];
$DateLodged = $retrievec['datelodged'];
  
$cbeam200ID = "1";
$cbeam250ID = "3"; 
//error_log(print_r($_POST,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
if(isset($_POST['save-singlebay-vr0']) || isset($_POST['save-close-singlebay-vr0']))
{	
//error_log("Run in : save-singlebay-vr1", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
$ProjectName = mysql_real_escape_string($_POST['projectsite']);
$DateLodged = date('Y-m-d H:i:s');
$FrameworkTYP = $_POST['frameworktype'];
$Framework = $_POST['framework']; 
$Length = $_POST['length'];
$Width = $_POST['width'];
$Bay = "";
$default_color = $_POST['paintselect'];

$qty = implode(", ", $_POST['qty']);
$cnt = count($_POST['colour']);
$cnt2 = count($_POST['uom']);
$cnt3 = count($_POST['qty']);
$cnt4 = count($_POST['webbing']);
$cnt5 = count($_POST['paint']);
$cnt6 = count($_POST['invent']);
$cnt7 = count($_POST['slength']);
$cnt8 = count($_POST['rrp']);
$cnt9 = count($_POST['cst']);

//error_log('1) '.$cnt.' 2)'.$cnt2.' 3)'.$cnt3.' 4)'.$cnt4.' 5)'.$cnt5.' 6)'.$cnt6.' 7)'.$cnt7.' 8)'.$cnt8.' 9)'.$cnt9, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
//error_log(print_r($_POST,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
//error_log(print_r($_POST['invent'],true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
 
$insertArr = array();

for ($i=0; $i<$cnt6; $i++) { 
	$insertArr[] = "('$QuoteID', '" . mysql_real_escape_string($_POST['invent'][$i]) . "', '$getprojectid', '$ProjectName', '$FrameworkTYP', '$Framework', '" . mysql_real_escape_string($_POST['slength'][$i]) . "', '" . mysql_real_escape_string($_POST['desc'][$i]) . "', '" . mysql_real_escape_string($_POST['colour'][$i]) . "', '" . mysql_real_escape_string($_POST['qty'][$i]) . "', '" . mysql_real_escape_string($_POST['webbing'][$i]) . "', '" . mysql_real_escape_string($_POST['finish'][$i]) . "', '" . mysql_real_escape_string($_POST['uom'][$i]) . "', '" . mysql_real_escape_string($_POST['rrp'][$i]) . "', '" . mysql_real_escape_string($_POST['cst'][$i]) . "', '" . mysql_real_escape_string($_POST['is_additional'][$i]) . "')";
}

$queryn = "INSERT INTO ver_chronoforms_data_quote_vic (quoteid, inventoryid, projectid, project_name, framework_type, framework, length, description, colour, qty, webbing, finish, uom, rrp, cost, is_additional) VALUES " . implode(", ", $insertArr);
	//error_log($queryn, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  exit();
	mysql_query($queryn) or trigger_error("Insert failed: " . mysql_error());

 
 
//New computation
$SubtotalVergola = $_POST['total_vergola'];
$SubtotalDisbursement = $_POST['total_disbursement'];
$Totalrrp = $_POST['total_rrp'];
$Totalgst = $_POST['total_gst'];
$Totalrrpgst = $_POST['total_sum'];

$Totalcost = $_POST['sub_total'];
$Totalcostgst = $_POST['total_gst'];
$GSTPercent = $_POST['gst'];
$CommPercent = $_POST['commission'];
$SalesCost = $_POST['com_sales_commission'];
$InstallCost = $_POST['com_installer_payment'];

$payment_deposit = $_POST['payment_deposit'];
$payment_progress = $_POST['payment_progress'];
$payment_final = $_POST['payment_final'];
$SalesComm = $_POST['com_sales_commission'];
$com_pay1 = $_POST['com_pay1'];
$com_pay2 = $_POST['com_pay2'];
$com_final = $_POST['com_final'];
$is_builder_project = $_POST['is_builder'];
 

$queryp = "INSERT INTO ver_chronoforms_data_followup_vic 
(sales_rep, 
quoteid, 
projectid, 
quotedate, 
project_name, 
framework_type, 
subtotal_vergola, 
subtotal_disbursement, 
total_rrp, 
total_gst, 
total_rrp_gst, 
total_cost, 
total_cost_gst, 
gst_percent, 
comm_percent, 
sales_comm, 
install_comm, 
sales_comm_cost, 
install_comm_cost, 
status,
payment_deposit,
payment_progress,
payment_final,
com_pay1,
com_pay2,
com_final,
rep_id,
is_builder_project,
length,
width,
updated_at,
default_color) 

VALUES 
('$salesrep', 
'$QuoteID', 
'$getprojectid', 
'$DateLodged', 
'$ProjectName', 
'$FrameworkTYP',
'$SubtotalVergola',
'$SubtotalDisbursement', 
'$Totalrrp',
'$Totalgst', 
'$Totalrrpgst',
'$Totalcost', 
'$Totalcostgst',  
'$GSTPercent', 
'$CommPercent', 
'$SalesComm', 
'$InstallComm', 
'$SalesComm', 
'$InstallCost', 'Quoted',
'$payment_deposit',  
'$payment_progress',  
'$payment_final', 
'$com_pay1', 
'$com_pay2',  
'$com_final',
'$rep_id',
'$is_builder_project',
$Length,
$Width,
NOW(),
'$default_color')";
 

//error_log($queryp, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  exit();
mysql_query($queryp) or trigger_error("Insert failed: " . mysql_error());
 
$querym = "INSERT INTO ver_chronoforms_data_measurement_vic (projectid, framework_type, width, length, bay) VALUES ('$getprojectid', '$FrameworkTYP', '$Width', '$Length', '$Bay')";
 
 mysql_query($querym) or trigger_error("Insert failed: " . mysql_error());


//Save the quotation report
$clientid=$QuoteID;  
$projectid=$getprojectid;
$title=$getprojectid; 
 
$content=generateHtmlReport($projectid,$title); 

$sql = "INSERT INTO ver_chronoforms_data_letters_vic (clientid, template_name, datecreated, template_content) 
	 VALUES ('$clientid','$title', NOW(), '{$content}')";  

mysql_query($sql); 
//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  

	if(isset($_POST['save-close-singlebay-vr1']))
	{	
		if($retrievec['is_builder']){
			header('Location:'.JURI::base().'builder-listing-vic/builder-folder-vic?cid='.$QuoteID);
		}else{
			header('Location:'.JURI::base().'client-listing-vic/client-folder-vic?cid='.$QuoteID);
		}
	    //header('Location:'.JURI::base().'client-listing-vic/client-folder-vic?cid='.$QuoteID);
	}else{
		header('Location:'.JURI::base().'view-quote-vic?projectid='.$getprojectid);
	}
	exit(); 

}
 

//error_log("HERE 1", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
if(isset($VergolaType) && $VergolaType=="Single Bay VR1"){
	$projectId = $ProjectID;
}else{
	$projectId = null;
}


function generateRowItem($section,$projectId=null){
	global $inventory_table;
	$tr = "";
	 
	if($section=="frame"){
	 	
	 	if($projectId==null){
	 		$fw = mysql_query("SELECT  * FROM ver_vergola_default_framework_items WHERE framework = 'Single Bay VR1' AND (LOWER(section) = 'frame' OR LOWER(section) = 'posts') ORDER BY  FIELD(inventoryid, 'IRV122','IRV121','IRV26','IRV25','IRV24','IRV23','IRV15','IRV120','IRV3') DESC" );
	 	}else{
	 		$fw = mysql_query("SELECT  * FROM ver_chronoforms_data_quote_vic AS q LEFT JOIN {$inventory_table} AS i ON i.inventoryid=q.inventoryid WHERE projectid='{$projectId}' " );  
	 	}

	 	//error_log($fw, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  
	 	
		$isFirst = 1;
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			//error_log(print_r($item,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$tr .= "<tr>"; 
					if(strtolower($item["category"])=="beams" || strtolower($item["category"])=="posts" || strtolower($item["category"])=="beam fixings"){ 
						
						$lw = ""; $isFirst==1?$lw = "length":$lw ="width";
						$tr .= "<td class=\"td-item\">".listItem(strtolower($item["section"]),$item,strtolower($item["category"]))." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\"  inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" />   </td>";
						 
						if(strtolower($item["category"])=="posts" || strtolower($item["category"])=="beam fixings"){
							$tr .= "<td> <input type=\"hidden\" name=\"webbing[]\" />  </td>"; 
						}
						else{
							$tr .= "<td class=\"td-webbing\">".listWebbing("",$r)."</td>"; 
						}
						$tr .= "<td>".listColours()."</td>";   
						$tr .= "<td class=\"td-finish-color\">".listColourBond(null,$item,"Frame")."</td>";  
						$tr .= "<td class=\"td-uom\">{$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
						$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"\"></td>"; 
						 
						
						if($item["category"] == "Posts"){
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"input-size input-in\" value=\"".(METRIC_SYSTEM=="inch"?"":"")."\" ></td>";  
						}else if($item["uom"]=="Ea"){
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"input-in\" value=\"\"   style=\"display:none;\"></td>";
						}else{
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"{$lw} input-size input-in\" value=\"\"  ></td>";
						}  

						if(METRIC_SYSTEM=="inch" && $item["category"]=="Posts" ){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\"  ></td>";  
						}else if(METRIC_SYSTEM=="inch" && $item["uom"]=="Ea"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" style=\"display:none;\" value=\"\" ></td>";  
						}else if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\" ></td>";  
						}

						$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"></td>";  
						$isFirst = 0;
					}else{
						$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\"  class=\"price\"  value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
						$tr .= "<td> <input type=\"hidden\" name=\"webbing[]\" /> </td>"; 
						$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
						$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
						$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
						$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"\"></td>";  
						$tr .= "<td class=\"td-len\"> <input type=\"text\"  name=\"slength[]\" class=\"input-in\" value=\"\" style=\"display:none\"> </td>";  
						if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\" style=\"".($item["uom"]=="Ea"?"display:none;":"")."\" ></td>";  
						} 
						$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"></td>";  
					}

					$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";
					
		   	$tr .= "</tr>"; 
	 	}

	 	 
	 	$tr .= "<tr id=\"framework_last_row\">";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Beam / Post\" onclick=\"add_new_post()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";
 
	 	
	 }else if($section=="fixings"){
	 	$order_by = "";
	 	if(HOST_SERVER=="LA"){
	 		$order_by = " ORDER BY FIELD(inventoryid, 'IRV22', 'IRV501', 'IRV21', 'IRV20') DESC ";
	 	}
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items  WHERE LOWER(framework) = 'single bay vr1' AND LOWER(section) = 'fixings'    {$order_by}" ); // 
		
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
				
			$tr .= "<tr>";  
			$tr .= "<td class=\"td-item\">".addslashes($item["description"])." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" desc=\"".addslashes($item["description"])."\" /> </td>";
			$tr .= "<td>  </td>"; 
			$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
			$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /></td>";   
			$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";
			
			$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"\"></td>";  
			 		 
			$tr .= "<td class=\"td-len\"> <input type=\"text\"  name=\"slength[]\"  value=\"\" class=\"input-in\" style=\"display:none\" > </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\" style=\"display:none\" ></td>";  
			}
			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"><input type=\"hidden\" class=\"cst\" name=\"cst[]\" value=\"\"></td>";  
		    
	  		$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".addslashes($item["description"])."\" readonly=\"readonly\" />".
				   "<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					"<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

		   	$tr .= "</tr>";
		}
		   	
	   	$tr .= "<tr id=\"fixing_last_row\" >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Fixing\" onclick=\"add_new_fixing()\" />   </td>";
			$tr .= "<td> </td>";  
			$tr .= "<td> </td>";  
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	 	
	 }else if($section=="guttering"){
	 	
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Single Bay VR1' AND LOWER(section) = 'guttering'" ); // 
		
		$isFirstTwo = 1;$k=0; 

		
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
 
 			if($item["inventoryid"] == "IRV31" ){ continue;}


			$lw = ""; $isFirstTwo==1?$lw = "length":$lw ="width";
			$k=$k+1;
			$tr .= "<tr>";  
			$tr .= "<td class=\"td-item\">".listItem("guttering",$item)." <input type=\"hidden\"  class=\"price\" value=\"{$item['rrp']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
			$tr .= "<td></td>"; 
			$tr .= "<td>".listColours()."</td>";   
			$tr .= "<td class=\"td-finish-color\">".listColourBond(null,$item,"Guttering")."</td>";  
			$tr .= "<td class=\"td-uom\">{$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>"; 
			if($item["inventoryid"] == "IRV31" ){  
				$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen \" name=\"qty[]\" value=\"\"></td>";  // Don't include gutter-qty so it won't be included in event every gutter item.
			}else{
				$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen gutter-qty\" name=\"qty[]\" value=\"\"></td>";  
			}
			
			//Remove gutter lining in the part
			if($item["inventoryid"] == "IRV31" ){ 
				$tr .= "<td class=\"td-len\"><input type=\"text\"  id=\"gutterLiningLength\" name=\"slength[]\" class=\"input-in \" value=\"\"></td>";  
			}else{
				$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"{$lw} gutter-length input-in\" value=\"\"></td>";
			}

			if(METRIC_SYSTEM=="inch"){ 
				$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\" gutter-length input-ft\" value=\"\"  ></td>";  
			} 

			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"></td>";  
			
			$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";
			$tr .= "</tr>";
			
			if($k==2)$isFirstTwo=0;  
	 	} 


	 	//Repeat the sequence of gutter just for VR0
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Single Bay VR0' AND LOWER(section) = 'guttering extra'" );
	 	$isFirstTwo = 1;$k=0;   
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$lw = ""; $isFirstTwo==1?$lw = "length":$lw ="width";
			$k=$k+1;
			$tr .= "<tr>";  
			$tr .= "<td class=\"td-item\">".listItem("guttering",$item)." <input type=\"hidden\"  class=\"price\" value=\"{$item['rrp']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
			$tr .= "<td></td>"; 
			$tr .= "<td>".listColours()."</td>";   
			$tr .= "<td class=\"td-finish-color\">".listColourBond(null,$item,"Guttering")."</td>";  
			$tr .= "<td class=\"td-uom\">{$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";   
			
			
			if($item["inventoryid"] == "IRV31"){ 
				$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"\"></td>";  
			}else{
				$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen gutter-qty\" name=\"qty[]\" value=\"\"></td>";  
			}	

			if($item["inventoryid"] == "IRV31"){ 
				$tr .= "<td class=\"td-len\"><input type=\"text\"  id=\"gutterLiningLength\" name=\"slength[]\" class=\"input-in\" value=\"\"></td>";  
			}else{
				$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"{$lw} gutter-length input-in\" value=\"\"></td>";
			}

			if(METRIC_SYSTEM=="inch"){
				if($item["inventoryid"] == "IRV31"){
					$tr .= "<td class=\"td-ft\"><input type=\"text\"  id=\"gutterLiningLength_ft\" class=\"input-ft\" value=\"\"  ></td>";  
				}else{
					$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"gutter-length input-ft\" value=\"\"  ></td>";  
				}
			} 

			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"></td>";  
			
			$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";
			$tr .= "</tr>";
			
			if($k==2)$isFirstTwo=0;  
	 	}

	 	$tr .= "<tr id=\"gutter_last_row\" >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Non-Standard Gutter\" onclick=\"add_new_non_standard_gutter()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

 		$tr .= "<tr >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Standard Gutter\" onclick=\"add_new_gutter()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	 	


	}else if($section=="flashings"){
	 	
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Single Bay VR1' AND LOWER(section) = 'flashings' ORDER BY  FIELD(inventoryid,'IRV46','IRV46','IRV45','IRV280','IRV44','IRV43') DESC " ); // 
		
		$isFirst = 1;
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			$tr .= "<tr>"; 
					 
					if($item["inventoryid"]=="IRV48" ){ 
						$tr .= "<td class=\"td-item\">".listItem("flashing",$item)." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\"  category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\"  desc=\"{$item['description']}\" /> </td>";
					}else{ 
						$tr .= "<td class=\"td-item\"> {$item['description']} <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
					}
						
					$tr .= "<td></td>";
					$tr .= "<td>".listColours()."</td>";
					$tr .= "<td class=\"td-finish-color\">".listColourBond(null,$item,"Guttering")."</td>";
					$tr .= "<td class=\"td-uom\">{$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";
					$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"\"></td>";
					 

					if(($item["inventoryid"]=="IRV43" || $item["inventoryid"]=="IRV46" )  && $isFirst == 1 ){  
						$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"\"></td>"; 
						$isFirst = 0; 

					}elseif(($item["inventoryid"]=="IRV43" || $item["inventoryid"]=="IRV46" ) || ($item["inventoryid"]=="IRV44")){ //error_log("2nd", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
						$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"width input-in\" value=\"\"></td>";						 
						$isFirst = 1; //set it back to equal 1 for IRV46 as 1st item.
					}else{
						//error_log("here", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
						$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"\"></td>"; 
					}

					if(METRIC_SYSTEM=="inch"){
						$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\"  ></td>";  
					} 
					  
					$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"></td>";  
  
  					$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
							"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

		   	$tr .= "</tr>";  
	 	} 

	 	$tr .= "<tr id=\"flashing_last_row\">";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Flashing\" onclick=\"add_new_flashing()\"  id=\"btn_add_flashing\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	}else if($section=="downpipe"){
	 	
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Single Bay VR1' AND LOWER(section) = 'downpipe'" ); // 
		
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$tr .= "<tr>"; 
			$tr .= "<td class=\"td-item\">".addslashes($item["description"])." <input type=\"hidden\" class=\"price\"  value=\"{$item['rrp']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"".addslashes($item["description"])."\" /> </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
			$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
			$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
			$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"\"></td>";  
			$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"input-in\" value=\"\" style=\"display:none\" ></td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\"  style=\"display:none\"  ></td>";  
			} 

			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"><input type=\"hidden\" class=\"cst\" name=\"cst[]\" value=\"\"></td>";  
			
			$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".addslashes($item["description"])."\" readonly=\"readonly\" />".
					"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";
		} 	
				       
		$tr .= "</tr>";

			$tr .= "<tr id=\"downpipe_last_row\" >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Downpipe\" onclick=\"add_new_downpipe()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	 	
	}else if($section=="vergola"){
	 	
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Single Bay VR1' AND LOWER(section) = 'vergola' ORDER BY FIELD(inventoryid, 'IRV66','IRV64','IRV63','IRV62','IRV60','IRV61','IRV59','IRV58','IRV54') DESC " ); 
		
		$isFirst = 1; $isSecond = 0;$isThird = 0;$isFourth = 0; $isFifth=0;
		$colorSelection = ""; $listColourBondSelection = "";
	 
		
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			//error_log(print_r($item,true));
			
			if($isFirst==1){ 
				$isSecond=1; 
				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",$item,"Vergola");

			}else if($isSecond==1){ 
				$isSecond=0; $isThird=1; 
				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",$item,"Vergola");
				 
			}
			else if($isThird==1){ 
				$isThird=0; $isFourth=1; 
				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",$item,"Vergola");
				 
			}else if($isFourth==1){ 
				$isFourth=0; $isFifth=1;  
				$colorSelection = "<input type=\"hidden\" name=\"colour[]\" />";
				$listColourBondSelection = "<input type=\"hidden\" name=\"finish[]\" />";

			}else if($isFifth==1){ 
				$isFifth=0;
				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",$item,"Vergola");  

			}else{
				$colorSelection = "<input type=\"hidden\" name=\"colour[]\" />";
				$listColourBondSelection = "<input type=\"hidden\" name=\"finish[]\" />";

			}
			

			$itemQty = ""; 
			$itemLen = ""; 
			$itemLen_ft = "";
			$className = "qtylen";
			if($item["category"]=="Louvers"){ //5 Louvres Poly in ever meter.
				$itemQty = 'id="louvres-qty"';
				$itemLen = 'id="louvres-len"'; 
				if(METRIC_SYSTEM=="inch"){ 
					$itemLen_ft = 'id="louvres-len_ft"'; 
				}	
				//$className = "";
			}else if($item["inventoryid"]=="IRV58"){ //2 Endcap every Louveres
				$itemQty = 'id="endcap-qty"';
				$itemLen = 'id="endcap-len"'; 
				if(METRIC_SYSTEM=="inch"){ 
					$itemLen_ft = 'id="endcap-len_ft"'; 
				}	
				//$className = "";
			}else if($item["inventoryid"]=="IRV59"){ //2 Endcap every Louveres
				$itemQty = 'id="pivot-qty"';
				$itemLen = 'id="pivot-len"'; 
				if(METRIC_SYSTEM=="inch"){ 
					$itemLen_ft = 'id="pivot-len_ft"'; 
				}	
				//$className = "";
			}else if($item["inventoryid"]=="IRV60"){ //2 Endcap every Louveres
				$itemQty = 'id="linkBar-qty"';
				$itemLen = 'id="linkBar-len"'; 
				if(METRIC_SYSTEM=="inch"){ 
					$itemLen_ft = 'id="pivot-len_ft"'; 
				}	 
			}else if($item["inventoryid"]=="IRV64"){ //2 Endcap every Louveres
				$itemQty = 'id="IRV64_qty"';
				  
				 
			}else if($item["inventoryid"]=="IRV66"){ //2 Endcap every Louveres
				$itemQty = 'id="IRV66_qty"';
				  
				  
			}else if($item["inventoryid"]=="IRV61"){ //2 Endcap every Louveres
				$itemQty = 'id="IRV61_qty"'; 
			}


			$itemName = ""; $lengthInput = ""; $lengthInput_ft = "";
			if($isFirst){
				$itemName = listItem("louvers");
				$lengthInput = "<input type=\"text\"  {$itemLen} name=\"slength[]\" class=\"width input-in louver-item-len\" value=\"\" />";
				if(METRIC_SYSTEM=="inch"){
					$lengthInput_ft = "<input type=\"text\"  {$itemLen_ft}   class=\"width input-ft louver-item-len\" value=\"\" />";
				}
			}
			else{
				$itemName = $item["description"];
				$lengthInput = "<input type=\"text\"  {$itemLen} name=\"slength[]\" class=\"input-in\"  value=\"\" style=\"display:none\" />";
				if(METRIC_SYSTEM=="inch"){
					$lengthInput_ft = "<input type=\"text\"  {$itemLen_ft}  class=\"input-ft\"  value=\"\" style=\"display:none\" />";
				}
			}

			$tr .= "<tr class=\"tr-vergola\">"; 
			$tr .= "<td class=\"td-item\"> {$itemName} <input type=\"hidden\" class=\"price\"  value=\"{$item['rrp']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td>".$colorSelection."</td>";  
			$tr .= "<td class=\"td-finish-color\">".$listColourBondSelection."</td>";   
			$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>"; 
			  
			$tr .= "<td class=\"td-qty\"><input type=\"text\" {$itemQty} class=\"{$className} ".($isFirst?"louver-item-qty":"")."\" name=\"qty[]\" value=\"\"></td>";  
			 
			 
			$tr .= "<td class=\"td-len\"> {$lengthInput} </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\">{$lengthInput_ft}</td>";  
			} 

			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp rrp-vergola\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"><input type=\"hidden\" class=\"cst\" name=\"cst[]\" value=\"\"></td>";  
			
			$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

			$tr .= "</tr>";
			$isFirst = 0;
	 	}


	   	$tr .= "<tr id=\"vergola_last_row\" >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Louver\" onclick=\"add_new_louver()\" />   </td>";
			$tr .= "<td> </td>";  
			$tr .= "<td> </td>";  
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	}else if($section=="misc"){
	 	
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Single Bay VR1' AND LOWER(section) = 'misc'" ); // 
		
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}'  " )); 
			
			$tr .= "<tr>"; 
			$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\" class=\"price\"  value=\"{$item['rrp']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
			$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
			$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";   
			$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
			 
			
			$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\"  value=\"\" style=\"display:none\" class=\"input-in\"></td>"; 

			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\" style=\"display:none\"  ></td>";  
			}  

			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"><input type=\"hidden\" class=\"cst\" name=\"cst[]\" value=\"\"></td>";  
			
			$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

			$tr .= "</tr>";

	 	} 

	 	$tr .= "<tr id=\"misc_last_row\" >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Misc\" onclick=\"add_new_misc()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	}else if($section=="extra"){
	 	
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Single Bay VR1' AND LOWER(section) = 'extras'" ); // 
		
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$tr .= "<tr>";
			$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\" class=\"price\"  value=\"{$item['rrp']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
			$tr .= "<td> </td>";
			$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";
			$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /></td>";
			$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";
			$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"\"></td>";
			$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"input-in\"  value=\"\" style=\"display:none\" ></td>";
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\" style=\"display:none\" ></td>";  
			} 
			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"><input type=\"hidden\" class=\"cst\" name=\"cst[]\" value=\"\"></td>";
			
			$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

			$tr .= "</tr>";
	 	} 

	 	$tr .= "<tr id=\"extra_last_row\" >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Extra\" onclick=\"add_new_extra()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"></td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";


	}else if($section=="disbursements"){
	 	
	 	$fw = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Single Bay VR1' AND LOWER(section) = 'disbursements'" );
		
		while ($r = mysql_fetch_assoc($fw)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$tr .= "<tr class=\"tr-disbursements\">"; 
			$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\" class=\"price\"  value=\"{$item['rrp']}\"  inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
			$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
			$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
			$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen qtylen-disbursements\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
			$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\"  value=\"\" class=\"input-in\" style=\"display:none\" ></td>";
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"\" style=\"display:none\" ></td>";  
			}   
			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp rrp-disbursement\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"><input type=\"hidden\" class=\"cst\" name=\"cst[]\" value=\"\"></td>";  
			
			$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

			$tr .= "</tr>";
			//break;
		}
			
		$tr .= "<tr id=\"disbursement_last_row\" >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Disbursement\" onclick=\"add_new_disbursement()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> </td>";  
			} 
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";
	 	 
	}     


	return $tr;

}
 

echo "<table class=\"listing-table\">";

// ***************************** Generate Framework Row ***********************************************//


// ***************************** Cbeam 200 Deep by 2.4mm ***********************************************//
echo "<tbody class=\"tbody_framework\">";
echo "<tr><th>Description</th><th>Webbing</th><th>Colour</th><th>Finish</th><th>UOM</th><th>QTY</th><th>Length</th><th>RRP</th></tr>";
echo "<tr><td colspan=\"8\" class=\"subheading\">Framework</td></tr>";

 
echo generateRowItem("frame",$projectId); 
 
// ***************************** First Post 90 x 90 - 2mm Galv ********************************************//

echo "<tr><td colspan=\"8\" class=\"subheading\">Fittings</td></tr>";
 
// ***************************** Fixing to Wall - Solid Brick ********************************************//
echo generateRowItem("fixings",$projectId); 
  
echo "</tbody>";
echo "<tbody class=\"tbody_non_framework\">";
echo "<tr><th>Description</th><th>Webbing</th><th>Colour</th><th>Finish</th><th>UOM</th><th>QTY</th><th>Length</th><th>RRP</th></tr>";
echo "<tr><td colspan=\"8\" class=\"subheading\">Gutters</td></tr>";

// ***************************** First Standard Vergola Gutter Lip Out 200 x 200 ********************************************//

echo generateRowItem("guttering",$projectId); 
 

// ***************************** Cbeam Face Flashing Z al ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Flashing</td></tr>";
 
echo generateRowItem("flashings",$projectId); 

 

// ***************************** Downpipe Plastic 3m ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Downpipe</td></tr>";
echo generateRowItem("downpipe",$projectId);  

// ***************************** Louvers Poly or Square ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Vergola System</td></tr>";
echo generateRowItem("vergola",$projectId); 
// ***************************** Opaque Enclosure ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Misc Items</td></tr>";
echo generateRowItem("misc",$projectId); 

// ***************************** Misc Cost ********************************************//
 
// ***************************** Add Extra 1 ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Extras</td></tr>";
echo generateRowItem("extra",$projectId); 
 
// ***************************** Shop Drawings ********************************************//
echo "<tr id=\"tr_disbursements\"><td colspan=\"8\" class=\"subheading\">Disbursements</td></tr>";
echo generateRowItem("disbursements",$projectId); 

  
echo "</tbody>";
// End of Table
echo "</table>";
?>
