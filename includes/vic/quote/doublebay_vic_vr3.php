<?php

// if($_POST['framework'] != "Double Bay VR2"){
// 	 error_log("incorrect vr2: ".$_POST['framework'], 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
// 	 //$framework = "Double Bay VR2"; //set default value for get default item in database.
// 	//return;
// }else{
// 	error_log("correct vr2: ".$_POST['framework'], 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
// } 

//echo "HERE";return;
//error_log(print_r($_POST,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
$next_increment = 0;
$qShowStatus = "SHOW TABLE STATUS LIKE 'ver_chronoforms_data_followup_vic'";
$qShowStatusResult = mysql_query($qShowStatus) or die ( "Query failed: " . mysql_error() . "<br/>" . $qShowStatus );
$row = mysql_fetch_assoc($qShowStatusResult);
$next_increment = $row['Auto_increment'];
$getprojectid = 'PRV'.$next_increment;

//$ReplaceCHAR = $QuoteID; 
// $resultb = mysql_query("SELECT * FROM ver_chronoforms_data_builderpersonal_vic WHERE builderid  = '$QuoteID'");
// $retrieveb = mysql_fetch_array($resultb);
// if (!$resultb) {die("Error: Data not found..");}
// $BuilderID = $retrieveb['builderid'];
// if($BuilderID == $QuoteID) {$PID = str_replace('BRV', '', $ReplaceCHAR); } 
// else {$PID = str_replace('CRV', '', $ReplaceCHAR); } 
// $salesrepb = $retrieveb['repname'];
// $rep_id = $retrieveb['repident'];

$resultc = mysql_query("SELECT * FROM ver_chronoforms_data_clientpersonal_vic WHERE clientid  = '$QuoteID'");
$retrievec = mysql_fetch_array($resultc);
if (!$resultc) {die("Error: Data not found..");}

$salesrep = $retrievec['repname'];
$rep_id = $retrievec['repident'];

$user = JFactory::getUser();
$rep_id = $user->RepID;
// if($salesrepb != "") {$salesrep = $salesrepb; $rep_id = $retrieveb['repident']; }
// elseif($salesrepc != ""){$salesrep = $salesrepc; $rep_id = $retrievec['repident']; }


// if(isset($_POST['quotedate'])){ 
// $date =  $_POST['quotedate']; 
// $timestamp = date('Y-m-d H:i:s', strtotime($date)); 
// $DateLodged = $timestamp;
// }
//Use the client date created as the quoted date.
if($retrieveb){
	$DateLodged = $retrieveb['datelodged'];
}else{
	$DateLodged = $retrievec['datelodged'];
}

 
$cbeam200ID = "1";
$cbeam250ID = "3";
$Inter250ID = "111";

//print_r($_POST);return;
//error_log(print_r($_POST,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');


if(isset($_POST['save-doublebay-vr3']) || isset($_POST['save-close-doublebay-vr3'])){	

	//$ProjectName = $_POST['projectsite'];
	$ProjectName = mysql_real_escape_string($_POST['projectsite']);
	$DateLodged = date('Y-m-d H:i:s');
	$FrameworkTYP = $_POST['frameworktype'];
	$Framework = $_POST['framework'];
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

	mysql_query("START TRANSACTION");

	if ($cnt6 > 0) { // && $cnt == $cnt2 && $cnt2 == $cnt3 && $cnt3 == $cnt4 && $cnt4 == $cnt5 && $cnt5 == $cnt6 && $cnt6 == $cnt7 && $cnt7 == $cnt8 && $cnt8 == $cnt9
	    $insertArr = array();
	    
		for ($i=0; $i<$cnt6; $i++) { 
			$insertArr[] = "('$QuoteID', '" . mysql_real_escape_string($_POST['invent'][$i]) . "', '$getprojectid', '$ProjectName', '$FrameworkTYP', '$Framework', '" . mysql_real_escape_string($_POST['slength'][$i]) . "', '" . mysql_real_escape_string($_POST['desc'][$i]) . "', '" . mysql_real_escape_string($_POST['colour'][$i]) . "', '" . mysql_real_escape_string($_POST['qty'][$i]) . "', '" . mysql_real_escape_string($_POST['webbing'][$i]) . "', '" . mysql_real_escape_string($_POST['finish'][$i]) . "', '" . mysql_real_escape_string($_POST['uom'][$i]) . "', '" . mysql_real_escape_string($_POST['rrp'][$i]) . "', '" . mysql_real_escape_string($_POST['cst'][$i]) . "', '" . mysql_real_escape_string($_POST['is_additional'][$i]) . "')";
		}

		$query = "INSERT INTO ver_chronoforms_data_quote_vic (quoteid, inventoryid, projectid, project_name, framework_type, framework, length, description, colour, qty, webbing, finish, uom, rrp, cost, is_additional) VALUES " . implode(", ", $insertArr);
		//echo $queryn; return;
		//error_log($query, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  //exit(); 
		$i1 = mysql_query($query) or trigger_error("Insert failed: " . mysql_error());

	}

	//Old Computation
	// $SubtotalVergola = $_POST['subtotalvergola'];
	// $SubtotalDisbursement = $_POST['subtotaldisd'];
	// $Totalrrp = $_POST['totalrrp'];
	// $Totalgst = $_POST['totalgst'];
	// $Totalrrpgst = $_POST['totalrrpgst'];
	// $Totalcost = $_POST['totalcost'];
	// $Totalcostgst = $_POST['totalcostgst'];
	// $GSTPercent = $_POST['gst'];
	// $CommPercent = $_POST['commision'];
	// $SalesCost = $_POST['salescost'];
	// $InstallCost = $_POST['installercost'];

	//New computation
	$SubtotalVergola = $_POST['total_vergola'];
	$SubtotalDisbursement = $_POST['total_disbursement'];
	$Totalrrp = $_POST['total_rrp'];
	$Totalgst = $_POST['total_gst'];
	$Totalrrpgst = $_POST['total_sum'];

	$Totalcost = $_POST['sub_total'];
	$Totalcostgst = $_POST['total_gst'];
	$GSTPercent = $_POST['gst'];
	$CommPercent = $_POST['commision'];
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
	NOW(),
	'$default_color')";
	
	//error_log($queryp, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  
	$i2 = mysql_query($queryp) or trigger_error("Insert failed: " . mysql_error());
	 
	$Bay = "";
	$cnt = count($_POST['dblength']);
	$cnt2 = count($_POST['dbwidth']); 
	 

	if ($cnt > 0) {
	    $insertArr = array();
	    
		for ($i=0; $i<$cnt; $i++) { 
			$insertArr[] = "('$getprojectid', '$FrameworkTYP', '" . mysql_real_escape_string($_POST['dbwidth'][0]) . "', '" . mysql_real_escape_string($_POST['dblength'][$i]) . "', '$Bay')";
		}

		$query = "INSERT INTO ver_chronoforms_data_measurement_vic (projectid, framework_type, width, length, bay) VALUES " . implode(", ", $insertArr);
		//error_log($query, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		$i3 = mysql_query($query) or trigger_error("Insert failed: " . mysql_error()); 
	}


	$db_result = 0;
	$note = null;

	if ($i1 and $i2 and $i3) {
		 mysql_query("COMMIT");
	    $db_result = 1;

		//Save the quotation report
		$clientid=$QuoteID;
		$projectid=$getprojectid;
		$title=$getprojectid;
		$content=generateHtmlReport($projectid,$title); 
	    
		$sql = "INSERT INTO ver_chronoforms_data_letters_vic (clientid, template_name, datecreated, template_content) 
			 VALUES ('$clientid','$title', NOW(), '{$content}')";  
		mysql_query($sql); 
		    
		if(isset($_POST['save-close-doublebay-vr3'])){

			//header('Location:'.JURI::base().'client-listing-vic/client-folder-vic?cid='.$QuoteID);
			if($retrievec['is_builder']){
				header('Location:'.JURI::base().'builder-listing-vic/builder-folder-vic?cid='.$QuoteID);
			}else{
				header('Location:'.JURI::base().'client-listing-vic/client-folder-vic?cid='.$QuoteID);
			}
			
		}else{

			header('Location:'.JURI::base().'view-quote-vic?projectid='.$getprojectid);    
		}   
		exit();
		
	}else{ 
		mysql_query("ROLLBACK");
	    $db_result = 0;
	    //error_log("DB Failed: i1:".$i1 .' i2:'. $i2 .' i3:'. $i3 .' i4:'. $i4 .' i5:'. $i5, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

	    $note = array();
	    $note['content'] = "Error while saving Client ID: {$clientid} Project ID: {$projectid} ";
  
	}		
	// if($BuilderID == $QuoteID) {header('Location:'.JURI::base().'builder-listing-vic/builder-folder-vic?pid='.$PID);} 
	// else {header('Location:'.JURI::base().'client-listing-vic/client-folder-vic?pid='.$PID);}
	
	$ProjectName = stripslashes($ProjectName);
}

 

 


function generateRowItemVR3($section, $fw=null){ //type = double bay vr2, double bay vr3, double bay vr2 - gutter
	global $inventory_table;
	$tr = "";
	
	if($section=="frame"){
		 
	 	//error_log("Inside generateRowItem2 framework: ".$framework, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
	 	//error_log("FRAMEWORK: ".$framework, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
	 	// $framework = mysql_query("SELECT  * FROM ver_vergola_default_framework_items WHERE framework = 'Double Bay VR3'  AND (LOWER(section) = 'frame' OR LOWER(section) = 'posts') ORDER BY FIELD(inventoryid, 'IRV13','IRV26','IRV25','IRV24','IRV23','IRV15','IRV121','IRV3') DESC  " ); //  ORIG
	 	$framework = mysql_query("SELECT  * FROM ver_vergola_default_framework_items WHERE framework = 'Double Bay VR3'  AND (LOWER(section) = 'frame' OR LOWER(section) = 'posts') ORDER BY FIELD(inventoryid, 'IRV122','IRV25','IRV23','IRV15','IRV109','IRV111','IRV120','IRV3') DESC  " );  //ORDER BY FIELD(inventoryid, 'IRV122','IRV25','IRV23','IRV15','IRV111','IRV3') DESC  
 
		//error_log("SELECT  * FROM ver_vergola_default_framework_items WHERE framework = '{$filter_framework}' AND LOWER(section) = 'frame'  ORDER BY FIELD(category, 'Beams', 'Beam Fixings', 'Posts')", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
		$isFirst = 1;$isSecond = 0;$isThird = 0;$isFourth = 0;//should be in order based on the list from the pulled record from database, beam(L), beam(L[2nd]), 
		while ($r = mysql_fetch_assoc($framework)) {

			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}'" )); 
			//error_log(print_r($item,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
			$tr .= "<tr>"; 
					 
					$lw = ""; 
					if($isFirst==1){$lw = "length";  $isSecond=1; }
					else if($isSecond==1){$lw = "width"; $isSecond=0; $isThird=1; }
					else if($isThird==1){$lw = "width"; $isThird=0; $isFourth=1; }
					else{$lw = "length";} 
					
					// else if($isFourth==1){$lw = "width"; $isFourth=0;   }
					// error_log(print_r($item,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
					//error_log(print_r($item,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
					if(strtolower($item["category"]) == "beams" || strtolower($item["category"])=="posts" || strtolower($item["category"])=="beam fixings" || strtolower($item["category"])=="intermediate"){ //
						//error_log($item['inventoryid'].'-'.$item["category"], 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
						$tr .= "<td class=\"td-item\">".listItem(strtolower($item["section"]),$item,strtolower($item["category"]))." <input type=\"hidden\" class=\"price select\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
						if(strtolower($item["category"])=="posts" || strtolower($item["category"])=="beam fixings"){
							$tr .= "<td> <input type=\"hidden\" name=\"webbing[]\" />  </td>"; 
						}
						else{
							$tr .= "<td class=\"td-webbing\">".listWebbing("")."</td>"; 
						}
						$tr .= "<td>".listColours()."</td>";   
						$tr .= "<td class=\"td-finish-color\">".listColourBond(null,$item)."</td>";  
						$tr .= "<td class=\"td-uom\">{$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
						$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
						if(strtolower($item["category"]) == "beams" && $isFirst ==1 ){ 
							$tr .= "<td class=\"td-len\"><input type=\"text\"  id=\"cbeam_length\" name=\"slength[]\" class=\"input-size input-in\" value=\"\"></td>";  
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"  id=\"cbeam_length_ft\" class=\"input-size input-ft\" value=\"\"></td>"; 
							}	
							$isFirst = 0;
						}else if($isThird ==1 || strtolower($item["category"])=="intermediate"){ 
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"input-size width input-in\" value=\"\"></td>";
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-size width input-ft\" value=\"\"></td>";
							}   

						}else if($item["category"] == "Posts"){ //IRV15 = post 90 x 90
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"input-size input-in\" value=\"".(METRIC_SYSTEM=="inch"?"157.48":"4")."\"></td>";  
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\" class=\"input-size input-ft\" value=\"13'1\"></td>";  
							}
								
						}else if(strtolower($item["category"])=="beam fixings"){ //IRV15 = post 90 x 90
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"input-size input-in\" value=\"\" style=\"display:none\" ></td>";  
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"   class=\"input-size input-ft\" value=\"\" style=\"display:none\" ></td>";  
							}	
						}else{
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"{$lw} input-size input-in\" value=\"0\"></td>";  
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"{$lw} input-size input-ft\" value=\"0\"></td>";  
							}
							}


						//$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"{$lw}\" value=\"1\"></td>";  
						$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"> </td>";  

					}else{
						$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
						$tr .= "<td> <input type=\"hidden\" name=\"webbing[]\" /> </td>"; 
						$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
						$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
						$tr .= "<td  class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
						$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\" ></td>"; 
						$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"0\" style=\"display:none\"></td>";  	 
						if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"length input-ft\" value=\"0\" style=\"display:none\"></td>";  
						}	
						$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"> </td>"; 
  
					}

					if($isFirst==1){
						$isFirst=0;
						$isSecond = 1;
					} 
					// if($item['inventoryid']=="IRV4"){
					// 	error_log(print_r($item,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
					// }

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
			$tr .= "<td class=\"td-rrp\"> </td>";  
	 		 

	 	$tr .= "</tr>";
	 	
	 	
	 }else if($section=="fixings"){
	 	$order_by = "";
	 	if(HOST_SERVER=="LA"){
	 		$order_by = " ORDER BY FIELD(inventoryid, 'IRV22', 'IRV501', 'IRV21', 'IRV20') DESC ";
	 	}
	 	$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items  WHERE LOWER(framework) = 'Double Bay VR3' AND LOWER(section) = 'fixings' {$order_by}" ); // 
		
		while ($r = mysql_fetch_assoc($framework)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$tr .= "<tr>";  

			$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
			$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
			$tr .= "<td class=\"td-uom\"> {$item["uom"]}  <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
			$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
			$tr .= "<td class=\"td-len\"> <input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"".(METRIC_SYSTEM=="inch"?"12":"1")."\" style=\"display:none\"> </td>";
			if(METRIC_SYSTEM=="inch"){
				$tr .= "<td class=\"td-ft\"> <input type=\"text\"   class=\"length input-ft\" value=\"1\" style=\"display:none\"> </td>";
			}

			$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"><input type=\"hidden\" class=\"cst\" name=\"cst[]\" value=\"\"></td>";  
	    
	  		$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
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
	  
	 	if($fw == "Double Bay VR3"){ 
	 		$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Double Bay VR3' AND LOWER(section) = 'guttering' ORDER BY FIELD(inventoryid, 'IRV27','IRV28', 'IRV29', 'IRV30', 'IRV31')" ); // 
	 		//$isFirst = 1;$isSecond = 0;$isThird = 0;$isFourth = 0;//should be in order based on the list from the pulled record from database, beam(L), beam(L[2nd]), 
	 	
	 	}else{ 
	 		//error_log("NOT INSIDE VR3 - gutter - section:".$section, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
	 		//error_log("INSIDE VR3 - gutter - framework:".$fw, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
	 		$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Double Bay VR3 - Gutter' AND LOWER(section) = 'guttering' " ); // 
			
			//$isFirst = 1;$isSecond = 0;$isThird = 0;$isFourth = 0;//should be in order based on the list from the pulled record from database, beam(L), beam(L[2nd]), 
	 	}
	 	
	 	$isFirst = 1;$isSecond = 0;$isThird = 0;$isFourth = 0;//should be in order based on the list from the pulled record from database, beam(L), beam(L[2nd]), 
		$_isFirst = 1;  

		while ($r = mysql_fetch_assoc($framework)) {
			
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			//error_log(" Item: ".$item['inventoryid'], 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$tr .= "<tr>"; 

					$lw = ""; 
					if($isFirst==1){$lw = "width"; $isFirst=0; $isSecond=1; }
					else if($isSecond==1){$lw = "width"; $isSecond=0; $isThird=1; } 
					else if($isThird==1){$lw = "width"; $isThird=0; $isFourth=1; }
					else if($isFourth==1){$lw = "width"; $isFourth=0;   }
					 
					$tr .= "<td class=\"td-item\">".listItem("guttering",$item)." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\"  category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
					$tr .= "<td></td>"; 
					$tr .= "<td>".listColours()."</td>";   
					$tr .= "<td class=\"td-finish-color\">".listColourBond(null,null,"guttering")."</td>";  
					$tr .= "<td class=\"td-uom\">{$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>"; 
					if($item["inventoryid"] == "IRV31"){ 
						$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";
					}else{ 
					$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen gutter-qty\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";
					}

					if($item["inventoryid"] == "" && $isSecond==1){ 
						$tr .= "<td class=\"td-len\"><input type=\"text\"  id=\"\" name=\"slength[]\" class=\"{$lw} gutter-length\" value=\"0\" ></td>"; 
						if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  id=\"\"  class=\"{$lw}\" value=\"0\" ></td>";
						}	
					}else if($item["inventoryid"] == "IRV29" || $item["inventoryid"] == "IRV30"){ 
						if($_isFirst == 1){ 
							$tr .= "<td class=\"td-len\"><input type=\"text\"   name=\"slength[]\" class=\"length input-in gutter-length\" value=\"0\"></td>";  
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"length input-ft gutter-length\" value=\"0\"></td>";
							}	
							$_isFirst = 0;
						}else{

							$tr .= "<td class=\"td-len\"><input type=\"text\"   name=\"slength[]\" class=\"length2 input-in gutter-length\" value=\"0\"></td>";
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"length2 input-ft gutter-length\" value=\"0\"></td>";
							}	
							$_isFirst = 1;
						}

						
					}else if($item["inventoryid"] == "IRV31"){ 
						$tr .= "<td class=\"td-len\"><input type=\"text\"  id=\"gutterLiningLength\" name=\"slength[]\" class=\"input-in\" value=\"0\"></td>";  
						if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  id=\"gutterLiningLength_ft\"  class=\"input-ft\" value=\"0\"></td>";  
						}	
					}else{
						$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"{$lw} gutter-length input-in\" value=\"0\" irv=\"{$item["inventoryid"]}\"></td>";
						if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\" class=\"{$lw} gutter-length input-ft\" value=\"0\" irv=\"{$item["inventoryid"]}\"></td>";
						}	
					}
					//$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"{$lw}\" value=\"0\"></td>";  
					$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"><input type=\"hidden\" class=\"cst\" name=\"cst[]\" value=\"\"></td>";  
 
					$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
							"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";		
		   	$tr .= "</tr>";
	 	} 

	 	$tr .= "<tr id=\"gutter_last_row\" >";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Non-Standard Gutter\" onclick=\"add_new_non_standard_gutter()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
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
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	}else if($section=="flashings"){
	  	 
	 	$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Double Bay VR3' AND LOWER(section) = 'flashings'  ORDER BY field(inventoryid,'IRV43','IRV44','IRV280','IRV45','IRV46','IRV47') " ); // 
		$isFirst = 1; $isSecond = 0; $isThird=0;$isFourth=0; $isFirst_IRV44=1; $isFirst_IRV280=1;

		while ($r = mysql_fetch_assoc($framework)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " ));  
			//error_log($item['inventoryid']."-", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
			if($isFirst==1){$lw = ""; $isFirst=0; $isSecond=1; }
			else if($isSecond==1){$lw = ""; $isSecond=0; $isThird=1; } 
			else if($isThird==1){$lw = ""; $isThird=0; $isFourth=1; }
			else if($isFourth==1){$lw = ""; $isFourth=0;   }

			$tr .= "<tr>"; 
					 
					 
					$tr .= "<td class=\"td-item\"> {$item['description']} <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
					 
					$tr .= "<td></td>"; 
					$tr .= "<td>".listColours()."</td>";   
					$tr .= "<td class=\"td-finish-color\">".listColourBond(null,null,"Guttering")."</td>";  
					$tr .= "<td class=\"td-uom\">{$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
					$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  

					if($isSecond == 1 || $isThird == 1 || $isFourth == 1){ //($item["inventoryid"]=="IRV43" || $item["inventoryid"]=="IRV46" )  && 
						$input_id="";
						//if($item["inventoryid"]=="IRV43"){
							//$input_id="IRV43_length";
						// }else if($item["inventoryid"]=="IRV46"){
						// 	$input_id="IRV46_length";
						// }  
						if($isSecond){
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"0\" ></td>"; //id=\"{$input_id}\"
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"length input-ft\" value=\"0\" ></td>";
							}	
						}else if($isThird){
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"length2 input-in\" value=\"0\" ></td>"; //id=\"{$input_id}\"
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"   class=\"length2 input-ft\" value=\"0\" ></td>";
							}	
						}else if($isFourth){
							$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"width input-in\" value=\"0\" ></td>"; //id=\"{$input_id}\"
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"width input-ft\" value=\"0\" ></td>";
							}	
						}
						 
					}else if($item["inventoryid"]=="IRV44"  ){ //error_log("2nd", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
						 
						if($isFirst_IRV44){
							$tr .= "<td class=\"td-len\"><input id=\"IRV44_l1\" type=\"text\"  name=\"slength[]\" class=\"input-in\" value=\"\"></td>";
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input id=\"IRV44_l1_ft\" type=\"text\"   class=\"input-ft\" value=\"\"></td>";	
							}						 
						}
					 	else{
					 		$tr .= "<td class=\"td-len\"><input id=\"IRV44_l2\" type=\"text\"  name=\"slength[]\" class=\"input-in\" value=\"\"></td>";
					 		if(METRIC_SYSTEM=="inch"){
					 			$tr .= "<td class=\"td-ft\"><input id=\"IRV44_l2_ft\" type=\"text\"  class=\"input-ft\" value=\"\"></td>";
					 		}						 
					 	}

					 	$isFirst_IRV44 = 0;
					}else if( $item["inventoryid"]=="IRV280"){ //error_log("2nd", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
						 
						if($isFirst_IRV280){
							$tr .= "<td class=\"td-len\"><input id=\"IRV280_l1\" type=\"text\"  name=\"slength[]\" class=\"input-in\" value=\"\"></td>";
							if(METRIC_SYSTEM=="inch"){
								$tr .= "<td class=\"td-ft\"><input id=\"IRV280_ft\" type=\"text\"   class=\"input-ft\" value=\"\"></td>";	
							}						 
						}
					 	else{
					 		$tr .= "<td class=\"td-len\"><input id=\"IRV280_l2\" type=\"text\"  name=\"slength[]\" class=\"input-in\" value=\"\"></td>";
					 		if(METRIC_SYSTEM=="inch"){
					 			$tr .= "<td class=\"td-ft\"><input id=\"IRV44_l2_ft\" type=\"text\"  class=\"input-ft\" value=\"\"></td>";
					 		}						 
					 	}

					 	$isFirst_IRV280 = 0;
					}else if($item["inventoryid"]=="IRV45" ){ 
						$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"input-in\" value=\"0\" id=\"IRV45_length\"></td>";
						if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"input-ft\" value=\"0\" id=\"IRV45_length_ft\"></td>";
						}	
				 		$isFirst = 1; //set it back to equal 1 for IRV46 as 1st item.
				 		
					}else if($item["inventoryid"]=="IRV43" || $item["inventoryid"]=="IRV46"   || $item["inventoryid"]=="IRV47" ){ //error_log("2nd", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
						$input_id="";
						if($item["inventoryid"]=="IRV47"){
							$input_id="IRV47_length"; 
						} 
						$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"width input-in\" value=\"0\" id=\"{$input_id}\"></td>";
						if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"  class=\"width input-ft\" value=\"0\" id=\"{$input_id}_ft\"></td>";
						}						 
						
 
					}else{
						//error_log("here", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
						$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"0\"></td>"; 
						if(METRIC_SYSTEM=="inch"){
							$tr .= "<td class=\"td-ft\"><input type=\"text\"   class=\"length input-ft\" value=\"0\"></td>"; 
						}	
					}  
					
					//$tr .= "<td class=\"td-len\"><input type=\"text\"  name=\"slength[]\" class=\"length\" value=\"0\"></td>";  
					$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"> </td>";  
  
  					$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";
		   	$tr .= "</tr>";
	 	} 

	 	$tr .= "<tr id=\"flashing_last_row\">";  
			$tr .= "<td class=\"td-item\"> <input type=\"button\" class=\"save-btn\" value=\"Add Flashing\" onclick=\"add_new_flashing()\" />   </td>";
			$tr .= "<td> </td>"; 
			$tr .= "<td> </td>";   
			$tr .= "<td> </td>";  
			$tr .= "<td class=\"td-uom\">  </td>";  
			$tr .= "<td class=\"td-qty\"> </td>";  
			$tr .= "<td class=\"td-len\"> </td>";  
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";
	 	
	}else if($section=="downpipe"){
	 	
	 	$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Double Bay VR3' AND LOWER(section) = 'downpipe'" ); // 
		
		while ($r = mysql_fetch_assoc($framework)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$tr .= "<tr>"; 
				$tr .= "<td class=\"td-item\">".addslashes($item["description"])." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"".addslashes($item["description"])."\" /> </td>";
				$tr .= "<td> </td>"; 
				$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
				$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
				$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
				$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				$tr .= "<td class=\"td-len\"> <input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"".(METRIC_SYSTEM=="inch"?"12":"1")."\" style=\"display:none\"> </td>";  
				if(METRIC_SYSTEM=="inch"){
					$tr .= "<td class=\"td-ft\"> <input type=\"text\"    class=\"length input-ft\" value=\"1\" style=\"display:none\"> </td>"; 
				}	
				$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"> </td>";  

				$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".addslashes($item["description"])."\" readonly=\"readonly\" />".
						"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

			$tr .= "</tr>";
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
	 	
	 	$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Double Bay VR3' AND LOWER(section) = 'vergola' ORDER BY FIELD(inventoryid, 'IRV64','IRV63','IRV62','IRV60','IRV61','IRV59','IRV58','IRV54') DESC" );  
		
		$isFirst = 1; $isSecond = 0;$isThird = 0;$isFourth = 0; $isFifth = 0;$isSixth=0;$isSeventh=0;  $isEighth=0;
		$colorSelection = ""; $listColourBondSelection = "";

		while ($r = mysql_fetch_assoc($framework)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); //ORDER BY FIELD(inventoryid, 'IRV54','IRV58', 'IRV59', 'IRV61', 'IRV60', 'IRV62', 'IRV63', 'IRV64')
			
			$itemName = ""; $lengthInput = ""; $lengthInput_ft = "";
			$itemQty = "";
			$itemLen = ""; $itemLen_ft = "";
			if($isFirst==1){ 
				$isSecond=1; 
				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",null,"Vergola");
				$itemQty = 'id="louvres-qty-1"'; //1st Louvres Poly in ever meter.
				$itemLen = 'id="louvres-len-1"'; 

				if(METRIC_SYSTEM=="inch"){
					$itemLen_ft = 'id="louvres-len-1_ft"'; 
				}

			}else if($isSecond==1){ 
				$isSecond=0; $isThird=1; 
				$itemQty = 'id="louvres-qty-2"'; //2nd Louvres Poly in ever meter.
				$itemLen = 'id="louvres-len-2"';  

				if(METRIC_SYSTEM=="inch"){
					$itemLen_ft = 'id="louvres-len-2_ft"'; 
				}
			}
			else if($isThird==1){ 
				$isThird=0; $isFourth=1; 
				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",null,"Vergola");
				 
			}else if($isFourth==1){ 
				$isFourth=0; $isFifth=1;
				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",null,"Vergola");

				$itemQty = 'id="pivot-qty-1"'; // 1st Pivot strip 
				$itemLen = 'id="pivot-len-1"'; 

			}else if($isFifth==1){ 
				$isFifth=0;  $isSixth=1;
				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",null,"Vergola");

				$itemQty = 'id="pivot-qty-2"'; // 2nd Pivot strip 
				$itemLen = 'id="pivot-len-2"'; 
			}else if($isSixth==1){ //  Link Bar 
			 	$isSixth=0;$isSeventh=1;
			 	$colorSelection = "<input type=\"hidden\" name=\"colour[]\" />";
				$listColourBondSelection = "<input type=\"hidden\" name=\"finish[]\" />"; 

			}else{
				$colorSelection = "<input type=\"hidden\" name=\"colour[]\" />";
				$listColourBondSelection = "<input type=\"hidden\" name=\"finish[]\" />"; 
			}

			
			if($item["category"]=="Louvers"){ //5 Louvres Poly in ever meter.
			
			}else if($item["inventoryid"]=="IRV58"){ //2 Endcap  
				$itemQty = 'id="endcap-qty"';
				$itemLen = 'id="endcap-len"'; 
			}else if($item["inventoryid"]=="IRV59"){ //  Pivot strip 
				
			}else if($item["inventoryid"]=="IRV60" && $isSeventh==1){ //  Link Bar 
				$isSeventh=0; $isEighth=1;

				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",null,"Vergola");
				$itemQty = 'id="linkBar-qty-1"';
				$itemLen = 'id="linkBar-len-1"'; 
			}else if($item["inventoryid"]=="IRV60" && $isEighth==1){ //  Link Bar 
				$isEighth=0;

				$colorSelection = listColours("",null,"vergola-colour");
				$listColourBondSelection = listColourBond("",null,"Vergola");
				$itemQty = 'id="linkBar-qty-2"';
				$itemLen = 'id="linkBar-len-2"'; 
			}

			$lengthInput_ft = "";
			if($isFirst || $isThird){
				$itemName = listItem("louvres");
				$lengthInput = "<input type=\"text\"  {$itemLen} name=\"slength[]\" class=\"  input-in\" value=\"0\" />";
				if(METRIC_SYSTEM=="inch"){
					$lengthInput_ft = "<input type=\"text\"  {$itemLen_ft}  class=\"  input-ft\" value=\"0\" />";
				}	
				//error_log("lengthInput :".$lengthInput, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
			}
			else{
				$itemName = $item["description"];
				$lengthInput = "<input type=\"text\"  {$itemLen} name=\"slength[]\" class=\"length input-in\" value=\"0\"  style=\"display:none\" />";
				if(METRIC_SYSTEM=="inch"){
					$lengthInput_ft = "<input type=\"text\"  {$itemLen_ft}   class=\"length input-ft\" value=\"0\"  style=\"display:none\" />";
				}	
			}

			$tr .= "<tr class=\"tr-vergola\">"; 
				$tr .= "<td class=\"td-item\"> {$itemName} <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
				$tr .= "<td> </td>"; 
				$tr .= "<td> ".$colorSelection." </td>";  
				$tr .= "<td class=\"td-finish-color\">".$listColourBondSelection."</td>";   
				$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
				if($item["inventoryid"] == "IRV61"){
					$tr .= "<td class=\"td-qty\"><input type=\"text\" {$itemQty} class=\"qtylen\" id=\"IRV61_qty\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				}else if($item["inventoryid"] == "IRV64"){
					$tr .= "<td class=\"td-qty\"><input type=\"text\" {$itemQty} class=\"qtylen\" id=\"IRV64_qty\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				}else{
					$tr .= "<td class=\"td-qty\"><input type=\"text\" {$itemQty} class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				}
					// $tr .= "<td class=\"td-qty\"><input type=\"text\" {$itemQty} class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				$tr .= "<td class=\"td-len\"> {$lengthInput} </td>";
				if(METRIC_SYSTEM=="inch"){
					$tr .= "<td class=\"td-ft\"> {$lengthInput_ft} </td>";
				}  
				$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"> </td>";  

				$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
						"<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

			$tr .= "</tr>";
			$isFirst = 0;
	 	} 
	}else if($section=="misc"){
	 	
	 	$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Double Bay VR3' AND LOWER(section) = 'misc'" ); // 
		
		while ($r = mysql_fetch_assoc($framework)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$tr .= "<tr>"; 
				$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
				$tr .= "<td> </td>"; 
				$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
				$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
				$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
				
				if($item["inventoryid"] == "IRV66"){
					$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" id=\"IRV66_qty\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				}else{
					$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				}
					//$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				$tr .= "<td class=\"td-len\"> <input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"".(METRIC_SYSTEM=="inch"?"12":"1")."\" style=\"display:none\"> </td>";  
				if(METRIC_SYSTEM=="inch"){
					$tr .= "<td class=\"td-ft\"> <input type=\"text\"  class=\"length input-ft\" value=\"1\" style=\"display:none\"> </td>";  
				}	
				$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"> </td>";  

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
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	}else if($section=="extras"){
	 	
	 	$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Double Bay VR3' AND LOWER(section) = 'extras'" ); // 
		
		while ($r = mysql_fetch_assoc($framework)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$tr .= "<tr>"; 
				$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
				$tr .= "<td> </td>"; 
				$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
				$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
				$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
				$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				$tr .= "<td class=\"td-len\"> <input type=\"text\"  name=\"slength[]\" class=\"length input-in\" value=\"".(METRIC_SYSTEM=="inch"?"12":"1")."\" style=\"display:none\"> </td>";  
				if(METRIC_SYSTEM=="inch"){
					$tr .= "<td class=\"td-ft\"> <input type=\"text\"    class=\"length input-ft\" value=\"1\" style=\"display:none\"> </td>";  
				}	
				$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"> </td>";  

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
			$tr .= "<td class=\"td-rrp\"> </td>";   
	 	$tr .= "</tr>";

	}else if($section=="disbursements"){
	 	
	 	$framework = mysql_query("SELECT * FROM ver_vergola_default_framework_items   WHERE  framework = 'Double Bay VR3' AND LOWER(section) = 'disbursements'" ); // 
		
		while ($r = mysql_fetch_assoc($framework)) { 
			$item = mysql_fetch_assoc(mysql_query("SELECT * FROM  {$inventory_table} AS i  WHERE inventoryid='{$r["inventoryid"]}' " )); 
			
			$tr .= "<tr>"; 
				$tr .= "<td class=\"td-item\">".$item["description"]." <input type=\"hidden\" class=\"price\" value=\"{$item['rrp']}\" category=\"{$item['category']}\" inventoryid=\"{$item['inventoryid']}\" desc=\"{$item['description']}\" /> </td>";
				$tr .= "<td> </td>"; 
				$tr .= "<td> <input type=\"hidden\" name=\"colour[]\" /> </td>";  
				$tr .= "<td> <input type=\"hidden\" name=\"finish[]\" /> </td>";   
				$tr .= "<td class=\"td-uom\"> {$item["uom"]} <input type=\"hidden\" name=\"uom[]\" value=\"{$item["uom"]}\" \> </td>";  
				$tr .= "<td class=\"td-qty\"><input type=\"text\" class=\"qtylen qtylen-disbursements\" name=\"qty[]\" value=\"{$r['qty']}\"></td>";  
				$tr .= "<td class=\"td-len\"> <input type=\"text\"  name=\"slength[]\" class=\"length\" value=\"1\" style=\"display:none\"> </td>";  
				if(METRIC_SYSTEM=="inch"){
					$tr .= "<td class=\"td-ft\"> <input type=\"text\"  name=\"slength[]\" class=\"length\" value=\"".(METRIC_SYSTEM=="inch"?"12":"1")."\" style=\"display:none\"> </td>";  
				}	
				$tr .= "<td class=\"td-rrp\"><input type=\"text\" class=\"rrp rrp-disbursement\" readonly=\"readonly\" name=\"rrp[]\" value=\"\"> </td>";  

				$tr .= "<input type=\"hidden\" class=\"desc\" name=\"desc[]\" value=\"".$item['description']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"invent\" name=\"invent[]\" value=\"".$item['inventoryid']."\" readonly=\"readonly\" />".
					       "<input type=\"hidden\" class=\"\" name=\"is_additional[]\" value=\"\" readonly=\"readonly\" />";

			$tr .= "</tr>";
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




 

if(isset($_POST['framework'])){
	$framework = $_POST['framework'];
}else{
	$framework = "Double Bay VR3";//set default value.
}

//error_log("selected framework: ".$framework, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
echo "<table class=\"listing-table\">";

// ***************************** Generate Framework Row ***********************************************//


// ***************************** Cbeam 200 Deep by 2.4mm ***********************************************//
echo "<tbody class=\"tbody_framework\">";
echo "<tr><th>Description</th><th>Webbing</th><th>Colour</th><th>Finish</th><th>UOM</th><th>QTY</th><th>Length</th><th>RRP</th></tr>";

echo "<tr><td colspan=\"8\" class=\"subheading\">Framework</td></tr>"; 
echo generateRowItemVR3("frame"); 
 
  
// ***************************** First Post 90 x 90 - 2mm Galv ********************************************//


 
// ***************************** Fixing to Wall - Solid Brick ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Fittings</td></tr>"; 
echo generateRowItemVR3("fixings"); 
 

echo "</tbody>";
echo "<tbody class=\"tbody_non_framework\">";
echo "<tr><th>Description</th><th>Webbing</th><th>Colour</th><th>Finish</th><th>UOM</th><th>QTY</th><th>Length</th><th>RRP</th></tr>";


// ***************************** First Standard Vergola Gutter Lip Out 200 x 200 ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Gutters</td></tr>"; 
echo generateRowItemVR3("guttering",$framework); 
 

// ***************************** Cbeam Face Flashing Z al ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Flashing</td></tr>"; 
echo generateRowItemVR3("flashings"); 
//error_log("Inside2 ", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
 
 

// ***************************** Downpipe Plastic 3m ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Downpipe</td></tr>";
echo generateRowItemVR3("downpipe"); 
 

// ***************************** Louvres Poly or Square ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Vergola System</td></tr>"; 
echo generateRowItemVR3("vergola"); 
 

// ***************************** Opaque Enclosure ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Misc Items</td></tr>"; 
echo generateRowItemVR3("misc"); 
 

// ***************************** Misc Cost ********************************************//
 
// ***************************** Add Extra 1 ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Extras</td></tr>";
echo generateRowItemVR3("extras"); 
 
// ***************************** Shop Drawings ********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Disbursements</td></tr>";
 echo generateRowItemVR3("disbursements"); 
 
 
echo "</tbody>";
// End of Table
echo "</table>";
// error_log("output vr2: ".$framework, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
?>
