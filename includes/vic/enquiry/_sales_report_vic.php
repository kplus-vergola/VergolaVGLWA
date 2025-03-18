<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo JURI::base().'jscript/jquery-ui-1.11.4/jquery-ui.min.css'; ?>" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo JURI::base().'jscript/jquery-ui-1.11.4/jquery-ui.theme.min.css'; ?>" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo JURI::base().'jscript/client-folder.css'; ?>" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo JURI::base().'jscript/Chart.js/css/demo.css'; ?>" />

 
<link rel="stylesheet" href="<?php echo JURI::base().'components/com_chronoforms/css/datepicker/datepicker_dashboard.css'; ?> " type="text/css" />
<link rel="stylesheet" href="<?php echo JURI::base().'components/com_chronoforms/css/formcheck/theme/classic/formcheck.css'; ?> " type="text/css" />
 
<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo JURI::base().'jscript/datetime/css/bootstrap.min.css'; ?>" />
<link rel="stylesheet" type="text/css" media="screen,projection" href="<?php echo JURI::base().'jscript/datetime/css/bootstrap-datetimepicker.min.css'; ?>" /> 
 
<script charset="UTF-8" type="text/javascript" src="<?php echo JURI::base().'jscript/datetime/js/jquery-1.8.3.min.js'; ?>"></script> 
<script src="<?php echo JURI::base().'jscript/jquery-ui-1.11.4/jquery-ui.min.js'; ?>"></script>
<script src="<?php echo JURI::base().'jscript/jquery-dateFormat.min.js'; ?>"></script>
<script src="<?php echo JURI::base().'jscript/Chart.js/Chart.min.js'; ?>"></script>
<script src="<?php echo JURI::base().'jscript/Chart.js/legend.js'; ?>"></script>

<script charset="UTF-8" type="text/javascript" src="<?php echo JURI::base().'jscript/datetime/js/bootstrap-datetimepicker.js'; ?>"></script> 
<script charset="UTF-8" type="text/javascript" src="<?php echo JURI::base().'components/com_chronoforms/js/datepicker/datepicker.js'; ?>"></script>  


<?php 
//$user->groups['10'] // is victoria sales manager/admin user 
//$user->groups['26'] //  is victoria office manager
//$user->groups['27'] //  is victoria sales manager
//$user->groups['9'] //9 is consultants general user
//top_admin is Jit user $user->groups['10']
$user = JFactory::getUser();

$is_admin = 0; $is_manager = 0; $is_office = 0; $is_top_admin = 0; $is_user = 0;
if(isset($user->groups['10'])){
	$is_top_admin = 1;
	$is_admin = 1;
}else if(isset($user->groups['26']) ){
	$is_office = 1;
	$is_admin = 1;
}else if( isset($user->groups['27'])){
	$is_manager = 1;
	$is_admin = 1;
}else{
	$is_user = 1;
}
  
//Set the filter of the selected consultant
//error_log("is_top_admin:".$is_top_admin."is_admin:".$is_admin."is_manager:".$is_manager."is_user:".$is_user, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

if(isset($_POST['command']) && $_POST['command']=="save_status" ){
	$cf_id = mysql_real_escape_string($_POST['cf_id']);
	$status = mysql_real_escape_string($_POST['status']);
	$_followup_date = mysql_real_escape_string($_POST['followup_date']);

	$timestamp = date('Y-m-d H:i:s', strtotime($_followup_date));
		$followup_date = $timestamp;

	$sql = "SELECT * FROM ver_chronoforms_data_followup_vic WHERE cf_id = {$cf_id}";
	$result = mysql_query($sql);

	$r = mysql_fetch_assoc($result);
	// error_log(print_r($r,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
	$followup_field = "";

	//$sql = "UPDATE ver_chronoforms_data_followup_vic SET status='{$status}', ffdate1='{$followup_date}' WHERE cf_id = {$cf_id}"; 
	
	mysql_query($sql);  
	 
	if($status == "considering"){ //In progress
		$sql = "UPDATE ver_chronoforms_data_followup_vic SET status='{$status}', ffdate1='{$followup_date}' WHERE cf_id = {$cf_id}";
		$r = mysql_query($sql);

		$sql2 = "UPDATE ver_chronoforms_data_clientpersonal_vic SET appointmentdate='{$followup_date}' WHERE quoteid = {$r['clientid']} ORDER BY pid DESC LIMIT 1";
		mysql_query($sql2);

	}else if($status == "Quoted"){
		$sql = "UPDATE ver_chronoforms_data_followup_vic SET status='{$status}', qdelivered='{$followup_date}' WHERE cf_id = {$cf_id}"; 
		mysql_query($sql);
	}else if($status == "Lost"){
		$sql = "UPDATE ver_chronoforms_data_followup_vic SET status='{$status}', date_lost='{$followup_date}' WHERE cf_id = {$cf_id}"; 
		mysql_query($sql);
	}else if($status == "Won"){
		$sql = "UPDATE ver_chronoforms_data_followup_vic SET status='{$status}', date_won='{$followup_date}' WHERE cf_id = {$cf_id}"; 
		mysql_query($sql);
	}    
	 
}

if(isset($_POST['command'])&& $_POST['command']=="save_is_done" ){
	//error_log(print_r($_POST,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
	$cf_id = mysql_real_escape_string($_POST['cf_id']);
	$is_done = mysql_real_escape_string($_POST['is_done']);

	$sql = "UPDATE ver_chronoforms_data_followup_vic SET is_done={$is_done} WHERE cf_id = {$cf_id}";
	//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
	mysql_query($sql);
	echo "";
	exit();

}


if(isset($user->groups['10']) || isset($user->groups['26']) || isset($user->groups['27'])){ 

	$rep_id = "all";
	$rep_filter = "";
	
	if(isset($_REQUEST['consultant_id'])){
		$rep_id = mysql_real_escape_string($_REQUEST['consultant_id']);
	} 

	if($rep_id == "all"){
		$rep_filter = "";
	}else{
		//$rep_filter = "";
	}
	//$qry_filter = " rep_id IN (SELECT RepID FROM ver_users WHERE usertype='{$user->usertype}') AND "; 
	
	
} 

//error_log($rep_id, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

define("NUMBER_PER_PAGE", 20); //number of records per page of the search results
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page-1) * NUMBER_PER_PAGE;
	 
//our pagination function is now in this file
function pagination($current_page_number, $total_records_found, $query_string = null)
{ 	
	$page = 1; 

	$paging = "";

	if($total_records_found)  $paging = "Page: ";
	
	for ($total_pages = ($total_records_found/NUMBER_PER_PAGE); $total_pages > 0; $total_pages--)
	{
		if ($page != $current_page_number)
			$paging .= "<a href=\"". JURI::base() . "?page=$page" . (($query_string) ? "&$query_string" : "") . "\">";

		if ($page == $current_page_number) {$paging .= "<span class=\"current\">$page</span>";} else {$paging .= "$page";}


		if ($page != $current_page_number)
			$paging .= "</a>";

		$page++;
	}

	return $paging;
  
}

 
 


//---------------- PREV YEAR Sales
$year = date('Y');  
$cMonth = date('m');
if($cMonth<7){
	$year = $year - 2;
}else{
	$year = $year - 1;
}

$qry_filter = "";
$qry_filter2 = "";
$user =& JFactory::getUser();
 
if($is_user){  
	 $qry_filter = " rep_id='{$user->RepID}' AND ";

}else if($rep_id=="all" && $is_user==0){ 

	//$qry_filter = " rep_id IN (SELECT RepID FROM ver_users WHERE usertype='{$user->usertype}') AND "; 
	$sql = "SELECT RepID FROM ver_users WHERE usertype='{$user->usertype}' and block=0 ";
	$qResult = mysql_query($sql);
	$i=0; $ar_rep_id=array();
	while ($r = mysql_fetch_assoc($qResult)){ 
		$ar_rep_id[$i] = $r['RepID'];
		$i++;
	}
	 
	//error_log(print_r($ar_rep_id,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); //exit(); 
	$qry_filter = "  rep_id IN ('".implode("','", $ar_rep_id)."') AND "; 
	//error_log("rep_id: ".$rep_id, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
}else if($rep_id!="all" && $is_user==0){ 
	$qry_filter = " rep_id='{$rep_id}' AND ";

}else{ return; } 

if($is_user==1){
	$qry_filter2 = " repident='{$user->RepID}' AND   ";

}else if($rep_id=="all"){ 
	$qry_filter2 = " repident IN ('".implode("','", $ar_rep_id)."') AND  ";  
}else{ 
	$qry_filter2 = " repident='{$rep_id}' AND   "; 
}

//error_log("qry_filter: ".$qry_filter, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth, SUM(target_amount) as target_amount FROM ver_rep_sales_target WHERE {$qry_filter}  year={$year} GROUP BY yearMonth"; //rep_id='{$user->RepID}' AND
//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
$qResult = mysql_query($sql); 
if(mysql_num_rows($qResult)<1){
	//error_log("INSIDE", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
	$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth, SUM(target_amount) as target_amount FROM ver_rep_sales_target WHERE  rep_id='Default Target' AND year={$year} GROUP BY yearMonth"; //rep_id='{$user->RepID}' AND
	//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
	$qResult = mysql_query($sql); 
}
$r = mysql_fetch_assoc($qResult);
//error_log($qry_filter, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();

$tFrom = substr($r['dateFromTo'], 0,10);
$tTo = substr($r['dateFromTo'], -10,10);

//add filter to find the query from the start of the target date.

//error_log($qry_filter, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
//echo $dFrom;return; 
$dFrom = date("Y-m-01", strtotime($tFrom));
$dTo = date("Y-m-t", strtotime($tTo));
//echo $dFrom." ".$dTo;return;	

//echo $dTo;return; 
$sales_amount2_assoc = array(); 
$sales_amount2 = array();
$sales_period2 = array();
$sales_target2 = array();

//echo $dTo;return;

//error_log(print_r($user,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 


$sql = "SELECT rep_id, SUM(total_cost) as sales_amount, DATE_FORMAT(contractdate,'%Y-%m') as yearMonth  FROM ver_chronoforms_data_contract_list_vic AS c WHERE {$qry_filter} contractdate BETWEEN '{$dFrom}' AND '{$dTo}' GROUP BY YEAR(c.contractdate), MONTH(c.contractdate) ";
//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();	
// }else{
// 	$qry_filter = " rep_id='{$user->RepID}' AND ";
// 	$sql = "SELECT rep_id, SUM(total_rrp) as sales_amount, DATE_FORMAT(contractdate,'%Y-%m') as yearMonth FROM ver_chronoforms_data_contract_list_vic AS c WHERE {$qry_filter}  contractdate BETWEEN '{$dFrom}' AND '{$dTo}' GROUP BY YEAR(c.contractdate), MONTH(c.contractdate)";
// }

//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
$qSales = mysql_query($sql); 
//print_r($qResult);return; 

	$i=0;$sales_amount_total=0;$target_amount_total=0; $diffAmountTotal = 0; $runningDiffAmount = 0; 
	 
	mysql_data_seek($qResult, 0);   

while ($r = mysql_fetch_assoc($qResult)) { 
	$sales = array();  
	while ($s = mysql_fetch_assoc($qSales)) { 
		if($s["yearMonth"]==$r["yearMonth"]){
			$sales = $s;
			break;
		} 
	} 
	
	$mDate = date_format(date_create($r["target_date"]),"F");
	$mDateShort = date_format(date_create($r["target_date"]),"M");
	$yDate = date_format(date_create($r["target_date"]),"Y");
	$fDate = date_format(date_create($r["target_date"]),"Y-m-d");


	array_push($sales_target2,$r['target_amount']);
	if($sales['sales_amount']>0){
		array_push($sales_amount2,$sales['sales_amount']);
		array_push($sales_amount2_assoc,array("yearMonth"=>$r["yearMonth"],"sales_amount"=>$sales['sales_amount']));
	}else{
		array_push($sales_amount2,0.00);
	}
		
	array_push($sales_period2,$mDateShort);

}	


$kpi_table = "";
$kpi_table_prev_yr = "";
$kpi_graph = "";
$kpi_graph_prev_yr = "";
$sales_table = "";
$note_table = "";	  

?>

 <!-- CURRENT KPI TABLE -->
<?php 

	$cbo_consultant = "";

	if(isset($user->groups['10']) || isset($user->groups['27']) ){

		if(isset($user->groups['10'])){ //27 is victoria sales manager
          $querysub2="SELECT * FROM ver_users WHERE (id IN (SELECT user_id FROM ver_user_usergroup_map WHERE group_id=27 || group_id=9 ) || id={$user->id}) and block=0 ORDER BY name ASC";
        }else if(isset($user->groups['27'])){ //26 is victoria office
          $querysub2="SELECT * FROM ver_users WHERE  (id IN (SELECT user_id FROM ver_user_usergroup_map WHERE group_id=9 ) || id={$user->id})  and block=0 ORDER BY name ASC";
        } 

		//$sql = "SELECT id, RepID, name  FROM ver_users WHERE id IN (SELECT user_id FROM ver_user_usergroup_map WHERE group_id=9)";
		//error_log($querysub2, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
	  	$qResult = mysql_query($querysub2);
		 
		$cbo_consultant = "<select id='cbo_consultant' onchange='request_sel_consultant()' style='padding:3px 0;'> "; 
		 $cbo_consultant .= "<option value='all' selected>All consultant</option>";
		while ($r = mysql_fetch_assoc($qResult)) { 
			$cbo_consultant .= "<option".($r['RepID']==$rep_id ? " selected ":"")." value='{$r['RepID']}'>{$r['name']}</option>";
		} 
		$cbo_consultant .= "</select>";

		$sales_table .= "<div style='width:100%; margin-bottom:10px;'> 
				<span><b>Consultant :</b> &nbsp;</span>{$cbo_consultant}
			</div>";
 		} 

 		
		$sales_table .= "<table id='tblSalesTarget' class='update-table' style='width:50%; display:inline-block;vertical-align: top; font-size:12px; text-align:center; '>";
   		
		//print_r($user);return;
		//echo $user->RepID;return;
		//error_log("SELECT * FROM ver_rep_sales_target WHERE rep_id='".$user["RepID"]."'", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
		//echo $user->RepID;
		$year = date('Y');
		$cMonth = date('m');
		if($cMonth<7){
			$year = $year - 1;
		}

		
		$sql = "SELECT id, rep_id, sales_amount, target_date, dateFromTo, year, DATE_FORMAT(target_date,'%Y-%m') as yearMonth, SUM(target_amount) as target_amount FROM ver_rep_sales_target WHERE  ".((strlen($qry_filter)>0)? $qry_filter:"rep_id='Default Target' AND ")."   year={$year} GROUP BY yearMonth"; //
		 
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		$qResult = mysql_query($sql);
		
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
		if(mysql_num_rows($qResult)<1){
			//error_log("INSIDE", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth, SUM(target_amount) as target_amount FROM ver_rep_sales_target WHERE  rep_id='Default Target' AND year={$year} GROUP BY yearMonth"; //rep_id='{$user->RepID}' AND
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); //exit();
			$qResult = mysql_query($sql); 
		}

		$r = mysql_fetch_assoc($qResult);
		 
		$dFrom = substr($r['dateFromTo'], 0,10);
		$dTo = substr($r['dateFromTo'], -10,10);
		//error_log($dFrom, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		//set the date to the start and end of the month
		$dFrom = date("Y-m-01", strtotime($dFrom));
		$dTo = date("Y-m-t", strtotime($dTo));

		$sales_target = array();
		$sales_amount = array();
		$sales_period = array();
		
		//echo $dTo;return;
		if($is_manager){
			$sql = "SELECT rep_id, IF(cv.job_end_date IS NULL,SUM(total_cost),false)  as project_amount_ready, IF(job_end_date IS NOT NULL,SUM(total_cost),false) as project_amount_finish, IF(cv.job_end_date IS NULL,count(total_cost),false)  as project_count_ready, IF(job_end_date IS NOT NULL,count(total_cost),false) as project_count_finish, IF(job_end_date IS NOT NULL,SUM(total_cost),false) as sales_amount, DATE_FORMAT(c.contractdate,'%Y-%m') as yearMonth FROM ver_chronoforms_data_contract_list_vic AS c JOIN ver_chronoforms_data_contract_vergola_vic AS cv ON cv.projectid=c.projectid WHERE c.contractdate BETWEEN '{$dFrom}' AND '{$dTo}' GROUP BY YEAR(c.contractdate), MONTH(c.contractdate)"; // rep_id='{$user->RepID}' AND 
		}else{
			$sql = "SELECT rep_id, SUM(total_cost) as sales_amount, DATE_FORMAT(contractdate,'%Y-%m') as yearMonth FROM ver_chronoforms_data_contract_list_vic AS c WHERE  {$qry_filter} contractdate BETWEEN '{$dFrom}' AND '{$dTo}' GROUP BY YEAR(c.contractdate), MONTH(c.contractdate)"; // rep_id='{$user->RepID}' AND 
		}	
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); //exit();
		$qSales = mysql_query($sql); 
		 
		//$r = mysql_fetch_assoc($qSales);
		//error_log(print_r($r,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
	 	$i=0;$sales_amount_total=0;$target_amount_total=0; $diffAmountTotal = 0; $runningDiffAmount = 0; $prevYearMonthSalesTotal = 0; 
	 	mysql_data_seek($qResult, 0);   //$r = mysql_fetch_assoc($qResult); print_r($r);return;

		while ($r = mysql_fetch_assoc($qResult)) { 
			//error_log(print_r($r,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  
			$sales = array();  
			if(!empty($qSales) && mysql_num_rows($qSales)){
				mysql_data_seek($qSales, 0); 

				while ($s = mysql_fetch_assoc($qSales)) { 
					if($s["yearMonth"]==$r["yearMonth"]){
						$sales = $s;
						//print_r($sales);
						break;
					} 
				} 

			}else{
				$sales["yearMonth"] = $r["yearMonth"];
				$sales["sales_amount"] = 0;
				$sales["project_amount_ready"] = 0;
				$sales["project_amount_finish"] = 0;

				$sales["project_count_ready"] = 0;
				$sales["project_count_finish"] = 0;
			}

			$prevYearMonthSales = 0;
			//echo "no sales_amount2: ".count($sales_amount2);
			//print_r($sales_amount2_assoc);
			
			for($j=0;$j<count($sales_amount2_assoc);$j++){ 
				//echo $sales_amount2_assoc[$j]["yearMonth"];
				$prevYear = substr($r["yearMonth"],0,4);
				$prevYear = (int)$prevYear-1;
				//echo $prevYear;
				$prevYearMonth = $prevYear ."-". substr($r["yearMonth"],-2,2);
				
				if($sales_amount2_assoc[$j]["yearMonth"]==$prevYearMonth){
					$prevYearMonthSales = $sales_amount2_assoc[$j]["sales_amount"];
					//print_r($sales);
					break;
				} 
			} 

			$prevYearMonthSalesTotal += $prevYearMonthSales;
 		
			$mDate = date_format(date_create($r["target_date"]),"F");
			$mDateShort = date_format(date_create($r["target_date"]),"M");
			$yDate = date_format(date_create($r["target_date"]),"Y");
			$fDate = date_format(date_create($r["target_date"]),"Y-m-d");

			$diffAmount = $sales['sales_amount'] - $r['target_amount'];
			$runningDiffAmount += $diffAmount; 

			if($i==0 && $is_manager){
				$sales_table .= "<tr><th >Month</th><th width='100'>Jobs Ready to Build</th><th width='100'>Jobs Built</th><th>Target</th><th>Monthly Excess/Difference</th><th>YTD Excess/Difference</th></tr> ";
			}
			else if($i==0){
				$sales_table .= "<tr><th >Month</th> <th>Target</th> <th width='100'>Sales This Year</th><th>Monthly Excess/Difference</th><th>YTD Excess/Difference</th></tr> ";
			}

			if($is_manager){
				$sales_table .= "<tr>";
				$sales_table .= "<td>{$mDate}</td><td>".($sales['project_count_ready']>0 ? "{$sales['project_count_ready']} / $". number_format($sales['project_amount_ready'],2)."":"-") ."</td><td>".($sales['project_count_finish']>0 ? "{$sales['project_count_finish']} / $".number_format($sales['project_amount_finish'],2)."":"-")."</td><td>$".number_format($r['target_amount'],2)."</td><td>$". ($sales['sales_amount']>0 ? number_format($diffAmount,2):0.00) ."</td><td>$". ($sales['sales_amount']>0 ? number_format($runningDiffAmount,2):0.00) ."</td>";	
				$sales_table .= "</tr>"; 

			}else{ 
				
				$sales_table .= "<tr>";
				$sales_table .= "<td>{$mDate}</td> <td>$".number_format($r['target_amount'],2)."</td> <td>$".number_format($sales['sales_amount'],2)."</td><td>$". ($sales['sales_amount']>0 ? number_format($diffAmount,2):0.00) ."</td><td>$". ($sales['sales_amount']>0 ? number_format($runningDiffAmount,2):0.00) ."</td>";	
				$sales_table .= "</tr>"; 

			}

			$i++;
			$sales_amount_total+=$sales['sales_amount'];
			$target_amount_total+=$r['target_amount'];

			array_push($sales_target,$r['target_amount']);
			if($sales['sales_amount']>0){
				array_push($sales_amount,$sales['sales_amount']);
			}else{
				array_push($sales_amount,0.00);
			}
			
			array_push($sales_period,$mDateShort);

		}
		$sales_table .= "<tr>"; 
		$sales_table .= "<td><b>Total</b></td> <td><b>$".number_format($target_amount_total,2)."</b></td> <td><b>$".number_format($sales_amount_total,2)."</b></td><td> </td><td> </td>";	
		$sales_table .= "</tr>";
		$sales_table .= "</table>";
 
 		//echo $sales_table;
		//error_log("sales_amount: ".print_r($sales_amount,true)."  sales_target: ".print_r($sales_target,true). "  "."sales_amount2: ".print_r($sales_amount2,true)."  sales_target2: ".print_r($sales_target2,true) , 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
 	
	?> 

	<?php   
		$advertising_chart = "<div style='display:inline-block; width:45%;'>
			<h3 style='margin:0; text-decoration:underline;'>Advertising of ".date('Y')."</h3>
		 	<canvas id='advertising_chart' width='700' height='400' style='margin:0 0 0 0px;'></canvas>
		 	<div id='advertising_chart_placeholder'></div>
		</div>";
   
		$suburb_lead_chart = "<div style='display:inline-block; width:45%;'>
			<h3 style='text-decoration:underline;'>Suburb Lead of ".date('Y')."</h3>
		 	<canvas id='suburb_lead_chart' width='700' height='400' style='margin:0 0 0 0px;'></canvas>
		 	<div id='suburb_lead_chart_placeholder'></div>
		</div>";
 
 	  
		$kpi_graph = "<div style='display:inline-block; width:45%;'>
			<h3 style='margin:15px 0 10px 70px; text-decoration:underline;'>Current Sales Vs Target</h3>
		 	<canvas id='myChart' width='700' height='400' style='margin:0 0 0 0px;'></canvas>
		 	<div id='placeholder'></div>
		</div>";
  
		$construction_analysis_graph = "
		<div style='display:inline-block; margin:0px 0 0 0; width:45%; '>
			<h3 style='margin:0px 0 10px 70px; text-decoration:underline;'>Construction Analysis of ".date('Y')." </h3>
		 	<canvas id='construction_analysis_chart' width='700' height='400' style='margin:0 0 0 0px;'></canvas>
		 	<div id='construction_analysis_chart_placeholder'></div>
		</div>"; 
		 
	?>


	<?php  
		//----------- KPI Table Previous Year --------------
		$kpi_table_prev_yr .= "	
		<h3 style='margin:10px 0 0 0; text-decoration:underline; '>Previous Year KPI</h3>
		<ul  class='list-table kpi-table'  style='margin:0 0; width:".(isset($user->groups['9']) ? "65%;":"50%")."; display:inline-block;vertical-align: top; font-size:12px;'> 
		"; 
	  	 
		$year = date('Y'); 
		$cMonth = date('m');
		if($cMonth<7){
			$year = $year - 2;
		}else{
			$year = $year - 1;
		}

		$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth FROM ver_rep_sales_target WHERE rep_id='".$user->RepID."' AND year={$year}";
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
		$qResult = mysql_query($sql); 

		if(mysql_num_rows($qResult)<1){
			//error_log("INSIDE", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth  FROM ver_rep_sales_target WHERE  rep_id='Default Target' AND year={$year} "; //rep_id='{$user->RepID}' AND
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
			$qResult = mysql_query($sql); 
		}

		$r = mysql_fetch_assoc($qResult);
		$tFrom = substr($r['dateFromTo'], 0,10);
		$tTo = substr($r['dateFromTo'], -10,10);

		//set the date to the start and end of the month
		$dFrom = date("Y-m-01", strtotime($tFrom));
		$dTo = date("Y-m-t", strtotime($tTo));
 
	 	$i=0; $j=0; $kpi_prev = array(); 
	 	
	 	mysql_data_seek($qResult, 0);  
		while ($r = mysql_fetch_assoc($qResult)) {  
			 
			$mDate = date_format(date_create($r["target_date"]),"F");
	 
		  	$sql = "SELECT SUM(num_enquiries) AS num_enquiries FROM ( 
		  				SELECT count(pid) as num_enquiries FROM ver_chronoforms_data_clientpersonal_vic where {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' 
		  				UNION ALL
		  				SELECT count(pid) as num_enquiries FROM ver_chronoforms_data_builderpersonal_vic where  {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' ) AS t1 ";
			 
			$qEnquiries = mysql_query($sql);

			if($qEnquiries){
				$rEnquiries = mysql_fetch_assoc($qEnquiries);
			}
			
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
			//Quotes Created
			// $_qry_filter = $qry_filter." quotedate BETWEEN '{$tFrom}' AND '{$tTo}' "; 
			// $sql = " SELECT COUNT(quoteid) AS num_quotes FROM ( SELECT  quoteid, quotedate  FROM ver_chronoforms_data_followup_vic WHERE {$_qry_filter} GROUP BY quoteid ORDER BY cf_id DESC, sales_rep) AS t WHERE DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}'      ";  
			$sql = "  SELECT count(quoteid) AS num_quotes  FROM ( SELECT  quoteid, quotedate  FROM ver_chronoforms_data_followup_vic WHERE  {$qry_filter}  DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}'   GROUP BY quoteid ORDER BY cf_id DESC, sales_rep) AS t JOIN (SELECT ClientID FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' ) AS c ON c.ClientID=t.quoteid  ";  
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
			$qQuotes = mysql_query($sql); 
			if($qQuotes){
				$rQuotes = mysql_fetch_assoc($qQuotes);
			}
			 
			// $sql = "SELECT count(cf_id) as num_contracts FROM ver_chronoforms_data_contract_list_vic WHERE {$qry_filter} DATE_FORMAT(contractdate,'%Y-%m') = '{$r["yearMonth"]}'";
			$sql = "  SELECT count(quoteid) AS num_contracts  FROM ( SELECT  quoteid, quotedate  FROM ver_chronoforms_data_followup_vic WHERE  {$qry_filter}  DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}' AND status='Won'  GROUP BY quoteid ORDER BY cf_id DESC, sales_rep) AS t JOIN (SELECT ClientID FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' ) AS c ON c.ClientID=t.quoteid  ";  
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');   exit();
			$qContracts = mysql_query($sql);
			if($qContracts){
				$rContracts = mysql_fetch_assoc($qContracts); 
			}
			 
			
			$leadP = 0; $quotesP = 0; $contractP = 0;
			if($rQuotes["num_quotes"] && $rEnquiries["num_enquiries"]){
				$leadP = number_format(($rQuotes["num_quotes"]/$rEnquiries["num_enquiries"])*100);
			}

			if($rContracts["num_contracts"] && $rQuotes["num_quotes"]){
				$quotesP = number_format(($rContracts["num_contracts"]/$rQuotes["num_quotes"])*100);
			}
			
			if($rContracts["num_contracts"] && $rEnquiries["num_enquiries"]){
				$contractP = number_format(($rContracts["num_contracts"]/$rEnquiries["num_enquiries"])*100);
			}

			if($i==0){$kpi_table_prev_yr .= "<li class='li-header'><span>Month 1</span><span>Enquiry allocated</span><span width='100'>Quotes created </span><span>Contracts won</span><span>% Lead conversion</span><span>% quotes conversion</span><span>% Enquiry to Contracts</span></li>  ";}
			
			$kpi_table_prev_yr .= "<li class=\"li-row-clickable\">";
				$kpi_table_prev_yr .= "<span>{$mDate}</span><span>".($rEnquiries["num_enquiries"]>0 ? $rEnquiries["num_enquiries"]:"-")."</span><span>".($rQuotes["num_quotes"]>0 ? $rQuotes["num_quotes"]:"-")."</span><span>".($rContracts["num_contracts"]>0 ? $rContracts["num_contracts"]:"-")."</span><span>". ($leadP>0 ? $leadP."":"-") ."</span><span>". ($quotesP>0 ? $quotesP."":"-") ."</span><span>". ($contractP>0 ? $contractP."":"-") ."</span>";	
			$kpi_table_prev_yr .= "</li>";
			//Store
			$kpi_prev[$j]['month'] = $mDate;
			$kpi_prev[$j]['enquiry_allocated'] = ($rEnquiries["num_enquiries"]>0 ? $rEnquiries["num_enquiries"]:0);
			$kpi_prev[$j]['quotes_created'] = ($rQuotes["num_quotes"]>0 ? $rQuotes["num_quotes"]:0);
			$kpi_prev[$j]['contracts_won'] = ($rContracts["num_contracts"]>0 ? $rContracts["num_contracts"]:0);
			$kpi_prev[$j]['lead_conversion'] = ($leadP>0 ? $leadP."":"-");
			$kpi_prev[$j]['quotes_conversion'] = ($quotesP>0 ? $quotesP."":"-");
			$kpi_prev[$j]['enquiry_contracts'] = ($contractP>0 ? $contractP."":"-");
  
  		 
			$i++;
			$j++;

		}

		
 
	
		//$kpi_table_prev_yr .= "<tr>";  
		$kpi_table_prev_yr .= "</ul>"; 
		//change the layout
		$kpi_table_prev_yr = "<ul> </ul> "; 
	//----------- KPI Table Previous Year --------------	
 
	//echo print_r($kpi_prev,true);
 
 	  
  
 	if($is_top_admin){  
		//------------------  Year KPI V2 -------------- --------------
		//error_log("INSIDE..", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
		$kpi_table_last_yr ="<h3 style='margin:0px 0 0 0; text-decoration:underline; '>Lead / Enquiry Summary</h3><br/><h3 style='  text-decoration:underline; '><span>Last Year</span><span style='margin:0 0 0 50%'>This Year</span></h3>
		<ul  class='list-table kpi-table'  style='margin:0 0; width:".(isset($user->groups['9']) ? "100%;":"49%")."; display:inline-block;vertical-align: top; font-size:12px; padding-right: 1%;'>";
  
		$year = date('Y');
		$cMonth = date('m');
		if($cMonth<7){
			$year = $year - 2;
		}
		//$sql = "SELECT rep_id, SUM(total_rrp) as sales_amount, DATE_FORMAT(contractdate,'%Y-%m') as yearMonth FROM ver_chronoforms_data_contract_list_vic AS c WHERE rep_id='{$user->RepID}' AND contractdate BETWEEN '{$dFrom}' AND '{$dTo}' GROUP BY YEAR(c.contractdate), MONTH(c.contractdate)";
		$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth FROM ver_rep_sales_target WHERE rep_id='".$user->RepID."' AND year={$year}";
		$qResult = mysql_query($sql); 
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); //exit();
		// if($qResult==false){
		// 	$year = $year-1;
		// 	$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth FROM ver_rep_sales_target WHERE rep_id='".$user->RepID."' AND year={$year}";
		// 	//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  
		// 	$qResult = mysql_query($sql); 
		// }

		if(mysql_num_rows($qResult)<1){
			//error_log("INSIDE", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth  FROM ver_rep_sales_target WHERE  rep_id='Default Target' AND year={$year} "; //rep_id='{$user->RepID}' AND
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
			$qResult = mysql_query($sql); 
		}

		$r = mysql_fetch_assoc($qResult);
		$tFrom = substr($r['dateFromTo'], 0,10);
		$tTo = substr($r['dateFromTo'], -10,10);

		//set the date to the start and end of the month
		$dFrom = date("Y-m-01", strtotime($tFrom));
		$dTo = date("Y-m-t", strtotime($tTo));

		if(isset($user->groups['9'])){
			$qry_filter2 = " repident='{$user->RepID}' AND   ";

		}else if($rep_id=="all"){ 
			$qry_filter2 = " repident IN ('".implode("','", $ar_rep_id)."') AND  ";  
		}else{ 
			$qry_filter2 = " repident='{$rep_id}' AND   "; 
		}

 
	 	$i=0; $j=0; 
	 	mysql_data_seek($qResult, 0); 
	 	$consultant_filter1 = "";  
	 	$tot_enquiry=0; $tot_quote=0; $tot_contract_won=0; $av_lead=0; $av_quote=0; $av_enquiry_w=0;
	 	$sum_lead=0; $sum_quote=0; $sum_enquiry_w=0;

		while ($r = mysql_fetch_assoc($qResult)) {  
			 
			$mDate = date_format(date_create($r["target_date"]),"F");
	 
	 		//Count Enquiry new client added
		  	// $sql = "SELECT SUM(num_enquiries) AS num_enquiries FROM ( 
		  	// 			SELECT count(pid) as num_enquiries FROM ver_chronoforms_data_clientpersonal_vic where {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' 
		  	// 			UNION ALL
		  	// 			SELECT count(pid) as num_enquiries FROM ver_chronoforms_data_builderpersonal_vic where  {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' ) AS t1 ";
		  	$sql = "SELECT count(pid) as num_enquiries FROM ver_chronoforms_data_clientpersonal_vic where {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}'  "; 
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  exit();
			$qEnquiries = mysql_query($sql); 
			//$rEnquiries = mysql_fetch_assoc($qEnquiries);
			if($qEnquiries){
				$rEnquiries = mysql_fetch_assoc($qEnquiries); 
			}
 
 			//Quotes Created
 			$_qry_filter = $qry_filter." quotedate>= '{$tFrom}' AND ";
			// $sql = " SELECT count(quoteid) AS num_quotes FROM ( SELECT  quoteid, quotedate  FROM ver_chronoforms_data_followup_vic WHERE {$_qry_filter} 1=1 GROUP BY quoteid ORDER BY cf_id DESC, sales_rep) AS t WHERE DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}'      ";  
			$sql = "  SELECT count(quoteid) AS num_quotes  FROM ( SELECT  quoteid, quotedate  FROM ver_chronoforms_data_followup_vic WHERE  quotedate>= DATE_SUB('{$tFrom}',INTERVAL 1 YEAR)   GROUP BY quoteid ORDER BY cf_id DESC) AS t JOIN (SELECT ClientID FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' ) AS c ON c.ClientID=t.quoteid  ";      
			// error_log("HERE 2".$sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  exit();
			$qQuotes = mysql_query($sql); 
			if($qQuotes){
				$rQuotes = mysql_fetch_assoc($qQuotes);
			}

			//Contracts Created
			// $sql = "SELECT count(cf_id) as num_contracts FROM ver_chronoforms_data_contract_list_vic WHERE {$qry_filter} DATE_FORMAT(contractdate,'%Y-%m') = '{$r["yearMonth"]}'";
			$sql = "  SELECT count(quoteid) AS num_contracts  FROM ( SELECT  quoteid  FROM ver_chronoforms_data_contract_list_vic WHERE   DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}' ORDER BY cf_id DESC, sales_rep) AS t JOIN (SELECT ClientID FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2}  DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}') AS c ON c.ClientID=t.quoteid  ";   
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');   exit();
			$qContracts = mysql_query($sql);
			if($qContracts){
				$rContracts = mysql_fetch_assoc($qContracts);
			}

			if($i==0){$kpi_table_last_yr .= "<li class='li-header'><span style=>Month</span><span>Enquiry allocated</span><span >Quotes created </span><span>Contracts won</span><span>% Lead conversion</span><span>% quotes conversion</span><span>% Enquiry to Contracts</span></li>  ";}

			
			$leadP = 0; $quotesP = 0; $contractP = 0;
			if($rQuotes["num_quotes"] && $rEnquiries["num_enquiries"]){
				$leadP = number_format(($rQuotes["num_quotes"]/$rEnquiries["num_enquiries"])*100);
			}

			if($rContracts["num_contracts"] && $rQuotes["num_quotes"]){
				$quotesP = number_format(($rContracts["num_contracts"]/$rQuotes["num_quotes"])*100);
			}
			
			if($rContracts["num_contracts"] && $rEnquiries["num_enquiries"]){
				$contractP = number_format(($rContracts["num_contracts"]/$rEnquiries["num_enquiries"])*100);
			}
 
			$kpi_table_last_yr .= "<li class=\"li-row-clickable\">";
			$kpi_table_last_yr .= "<span>{$mDate}</span><span>".($kpi_prev[$j]['enquiry_allocated']>0 ? $kpi_prev[$j]['enquiry_allocated']:"0")." </span><span>".($kpi_prev[$j]['quotes_created']>0 ? $kpi_prev[$j]['quotes_created']:"0")." </span><span>".($kpi_prev[$j]['contracts_won']>0 ? $kpi_prev[$j]['contracts_won']:"0")." </span><span>". ($kpi_prev[$j]['lead_conversion']>0 ? $kpi_prev[$j]['lead_conversion']."%":"0") ." </span><span>". ($kpi_prev[$j]['quotes_conversion']>0 ? $kpi_prev[$j]['quotes_conversion']."%":"0") ." </span><span>". ($kpi_prev[$j]['enquiry_contracts']>0 ? $kpi_prev[$j]['enquiry_contracts']."%":"0") ." </span>";	
			$kpi_table_last_yr .= "</li>"; 
				$kpi_table_last_yr .= "<li style='display:none;'>";
						$kpi_table_last_yr .= "<table class='update-table' style='margin: 0 0 0 0px; display:inline-block;vertical-align: top; font-size:11px; line-height: 1.3em;'>";
			
					//Search clients entry details
					//$is_manager = 1;
					if(isset($user->groups['9'])){
						//$qry_filter = " rep_id='{$user->RepID}' AND ";
						$qry_filter3 = "f.rep_id='{$user->RepID}' AND  f.quotedate>= '{$tFrom}' AND  "; 
						//$is_manager = 0;
					}else if($rep_id=="all"){ 
						$qry_filter3 = " f.rep_id IN ('".implode("','", $ar_rep_id)."') AND f.quotedate>= '{$tFrom}' AND ";  
					}else if($rep_id!="all"){ 
						$qry_filter3 = " f.rep_id='{$rep_id}' AND f.quotedate>= '{$tFrom}' AND  "; 
					} 	

					$_qry_filter = $qry_filter." DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}' AND ";
					if($is_manager==0)
						$consultant_filter_client = ", (SELECT CONCAT(DATE_FORMAT(datenotes,'".SQL_DFORMAT."'),' ', SUBSTRING(content,1,50)) AS note FROM ver_chronoforms_data_notes_vic WHERE clientid=c.clientid ORDER BY cf_id DESC LIMIT 1) AS last_followup ";
					$sql = " SELECT * FROM (SELECT f.quoteid AS client_id, f.projectid, f.rep_id, c.pid, CONCAT(c.client_firstname,' ',c.client_lastname) AS client_name,  c.client_suburb, c.client_wkphone AS phone, f.quotedate, DATE_FORMAT(f.quotedate,'".SQL_DFORMAT."') AS fquotedate, DATE_FORMAT(c.datelodged,'".SQL_DFORMAT."') AS fdatelodged, DATE_FORMAT(c.datelodged,'".SQL_DFORMAT."')  AS enquiry_date, DATEDIFF(f.quotedate, c.datelodged) as q_no_days_pass, DATEDIFF(f.date_won, f.quotedate) as c_no_days_pass, f.total_cost, DATE_FORMAT(f.date_won,'".SQL_DFORMAT."')  AS date_won, f.status, u.name AS rep_name {$consultant_filter_client} FROM (SELECT * FROM (SELECT  *  FROM ver_chronoforms_data_followup_vic WHERE is_builder_project=0 ORDER BY cf_id DESC) AS f0  WHERE quotedate BETWEEN '{$dFrom}' AND '{$dTo}' AND 1=1 GROUP BY quoteid) AS f JOIN (SELECT * FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' AND is_builder=0 ) AS c ON c.clientid=f.quoteid LEFT JOIN ver_users as u ON u.RepID=f.rep_id ORDER BY f.cf_id DESC, rep_name ) AS t  ";
					//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); //exit();
					//error_log($sql, 3,'/home/vergola/public_html/quote-system/my-error.log');	 
					if(HOST_SERVER=="LA"){
						$sql = "";
					}
					 

					$fResult = mysql_query($sql);
					 
					$i=0; $kpi_table_last_yr1 = "";
					

					while ($c = mysql_fetch_assoc($fResult)) { 
						
						if($i==0){$kpi_table_last_yr .= "<tr>".($is_manager ? "<th>Rep. Name</th>":"")."<th width='10%'>Client ID</th><th width='13%'>Client name</th><th width='10%'> Address</th><th width='8%'> Phone</th><th width='10%'>Enquiry Date</th><th width='10%'>Quote Date (No. Days Pass)</th><th width='8%'>Quote $$</th><th width='10%'>Contract Date (No. Days Pass)</th><th width='8%'>Status</th>".($is_manager==0 ? "<th  width='8%' >Last Followup</th>":"")."".($is_manager==0 ? "<th  width='15%' >Note</th>":"")."</tr> ";}
	 					$kpi_table_last_yr .= "<tr>";
							$kpi_table_last_yr .= ($is_manager ? "<td>{$c['rep_name']}</td>":"")."<td> {$c['client_id']} </td><td>{$c['client_name']}</td><td>{$c['client_suburb']}</td><td>{$c['phone']}</td><td>{$c['fdatelodged']}</td><td>{$c['fquotedate']} ({$c['q_no_days_pass']})</td><td>{$c['total_cost']}</td><td>".(strtolower($c['status'])=="won"?$c['date_won']." (".$c['c_no_days_pass'].")":"")."</td><td>{$c['status']}</td><td>".substr($c['last_followup'], 0,10)."</td><td>".substr($c['last_followup'], 12)."</td>";	
						$kpi_table_last_yr .= "</tr>"; 
						$i++;
					}

					//Search builder entry details
					//error_log("kpi_table_last_yr1:".$kpi_table_last_yr1, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
					if($is_manager==0)
						$consultant_filter_builder = ", (SELECT CONCAT(DATE_FORMAT(datenotes,'".SQL_DFORMAT."'),' ', SUBSTRING(content,1,50)) AS note FROM ver_chronoforms_data_notes_vic WHERE clientid=b.clientid ORDER BY cf_id DESC LIMIT 1) AS last_followup ";
					 
					$sql = "SELECT * FROM (SELECT f.quoteid AS client_id, f.projectid, f.rep_id, b.pid, b.builder_name AS client_name, b.client_suburb, f.quotedate, DATE_FORMAT(f.quotedate,'".SQL_DFORMAT."') AS fquotedate , DATE_FORMAT(b.datelodged,'".SQL_DFORMAT."')  AS enquiry_date, DATE_FORMAT(b.datelodged,'".SQL_DFORMAT."')  AS fenquiry_date, DATEDIFF(f.quotedate, b.datelodged) as q_no_days_pass, DATEDIFF(f.date_won, f.quotedate) as c_no_days_pass, f.total_cost, DATE_FORMAT(f.date_won,'".SQL_DFORMAT."')  AS date_won, f.status, u.name AS rep_name {$consultant_filter_builder}    FROM (SELECT * FROM (SELECT  *  FROM ver_chronoforms_data_followup_vic WHERE is_builder_project=1 ORDER BY cf_id DESC) AS f0 WHERE quotedate BETWEEN '{$dFrom}' AND '{$dTo}' GROUP BY quoteid ) AS f JOIN (SELECT * FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' AND  is_builder=1) as b ON b.clientid=f.quoteid  LEFT JOIN ver_users as u ON u.RepID=f.rep_id ORDER BY f.cf_id DESC, rep_name ) AS t   ";
					 
					//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
					if(HOST_SERVER=="LA"){ $sql = ""; } 

					$bResult = mysql_query($sql); 
					 
					$i=0;
					while ($b = mysql_fetch_assoc($bResult)) { 
						if($i==0){$kpi_table_last_yr .= "<tr>".($is_manager ? "<th  width='10%' >Rep. Name</th>":"")."<th width='8%'>Builder ID</th><th width='10%'>Builder name</th><th width='10%'> Address</th><th width='8%'>Phone</th><th width='10%'>Quote Date (No. Days Pass)</th><th width='8%'>Quote $$</th><th width='10%'>Contract Date (No. Days Pass)</th><th width='8%'>Status</th>".($is_manager==0 ? "<th  width='7%' >Last Followup</th>":"")."".($is_manager==0 ? "<th  width='15%' >Note</th>":"")."</tr> ";}
	 					$kpi_table_last_yr .= "<tr>";
							$kpi_table_last_yr .= ($is_manager ? "<td>{$b['rep_name']}</td>":"")."<td><a href=\"".JURI::base()."client-listing-vic/client-folder-vic?pid={$b['pid']}\">{$b['client_id']}</a></td><td>{$b['client_name']}</td><td>{$b['client_suburb']}</td><td>{$c['phone']}</td><td>{$b['fquotedate']} ({$c['q_no_days_pass']})</td><td>{$b['total_cost']}</td><td>".(strtolower($c['status'])=="won"?$c['date_won']." (".$c['c_no_days_pass'].")":"")."</td><td>{$b['status']}</td><td>".substr($b['last_followup'], 0,10)."</td><td>".substr($b['last_followup'], 12)."</td>";	
						$kpi_table_last_yr .= "</tr>"; 
						$i++;
					}

				$kpi_table_last_yr .= "</table>";
			$kpi_table_last_yr .= "</li>";

			$tot_enquiry+=$rEnquiries["num_enquiries"]; $tot_quote+=$rQuotes["num_quotes"]; $tot_contract_won+=$rContracts["num_contracts"]; 
  			$sum_lead+=$kpi_prev[$j]['lead_conversion']; $sum_quote+=$kpi_prev[$j]['quotes_conversion']; $sum_enquiry_w+=$kpi_prev[$j]['enquiry_contracts'];

			//error_log("lead_conversion: ".$kpi_prev[$j]['lead_conversion']." sum_lead:".$sum_lead, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$i++;
			$j++;

			

		}

		$av_lead=number_format(($sum_lead/12)); $av_quote=number_format(($sum_quote/12)); $av_enquiry_w=number_format(($sum_enquiry_w/12)); 

		$kpi_table_last_yr .= "<li  class=\"li-header\" style=\"border:none;\" >";
				$kpi_table_last_yr .= "<span>Total</span><span>".$tot_enquiry."</span><span>".$tot_quote."</span><span>".$tot_contract_won."</span><span>".$av_lead."%</span><span>".$av_quote."%</span><span>".$av_enquiry_w."%</span>";	
			$kpi_table_last_yr .= "</li>";

		$kpi_table_last_yr .= "<li>";  
		$kpi_table_last_yr .= "</li>"; 
		$kpi_table_last_yr .= "</ul>";
		//error_log($kpi_table_last_yr, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		//echo $kpi_table;
		//----------- END of Year KPI V2 --------------
  



		//------------------  Year KPI Current Year V2 -------------- --------------
		//error_log("INSIDE..", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
		$kpi_table_this_yr ="
		<ul  class='list-table kpi-table'  style='margin:0 0; width:".(isset($user->groups['9']) ? "100%;":"48%")."; display:inline-block;vertical-align: top; font-size:12px; padding-right: 1%;'>";
  
		$year = date('Y');
		$cMonth = date('m');
		if($cMonth<7){
			$year = $year - 1;
		}
		//$sql = "SELECT rep_id, SUM(total_rrp) as sales_amount, DATE_FORMAT(contractdate,'%Y-%m') as yearMonth FROM ver_chronoforms_data_contract_list_vic AS c WHERE rep_id='{$user->RepID}' AND contractdate BETWEEN '{$dFrom}' AND '{$dTo}' GROUP BY YEAR(c.contractdate), MONTH(c.contractdate)";
		$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth FROM ver_rep_sales_target WHERE rep_id='".$user->RepID."' AND year={$year}";
		$qResult = mysql_query($sql); 
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');// exit();
		// if($qResult==false){
		// 	 $year = $year-1;
		// 	 $sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth FROM ver_rep_sales_target WHERE rep_id='".$user->RepID."' AND year={$year}";
		// 	 error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  
		// 	 $qResult = mysql_query($sql);
		//}

		if(mysql_num_rows($qResult)<1){
			//error_log("INSIDE", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth  FROM ver_rep_sales_target WHERE  rep_id='Default Target' AND year={$year} "; //rep_id='{$user->RepID}' AND //error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
			$qResult = mysql_query($sql);
		}

		$r = mysql_fetch_assoc($qResult);
		$tFrom = substr($r['dateFromTo'], 0,10);
		$tTo = substr($r['dateFromTo'], -10,10);

		//set the date to the start and end of the month
		$dFrom = date("Y-m-01", strtotime($tFrom));
		$dTo = date("Y-m-t", strtotime($tTo));

		if(isset($user->groups['9'])){
			$qry_filter2 = " repident='{$user->RepID}' AND   ";
		}else if($rep_id=="all"){ 
			$qry_filter2 = " repident IN ('".implode("','", $ar_rep_id)."') AND  ";  
		}else{ 
			$qry_filter2 = " repident='{$rep_id}' AND   "; 
		}

 
	 	$i=0; $j=0; 
	 	mysql_data_seek($qResult, 0); 
	 	$consultant_filter1 = "";  
	 	$tot_enquiry=0; $tot_quote=0; $tot_contract_won=0; $av_lead=0; $av_quote=0; $av_enquiry_w=0;
	 	$sum_lead=0; $sum_quote=0; $sum_enquiry_w=0;
	 	
		while ($r = mysql_fetch_assoc($qResult)) {  
			 
			$mDate = date_format(date_create($r["target_date"]),"F");
	 
	 		//Count Enquiry new client added
		  	// $sql = "SELECT SUM(num_enquiries) AS num_enquiries FROM ( 
		  	// 			SELECT count(pid) as num_enquiries FROM ver_chronoforms_data_clientpersonal_vic where {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' 
		  	// 			UNION ALL
		  	// 			SELECT count(pid) as num_enquiries FROM ver_chronoforms_data_builderpersonal_vic where  {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' ) AS t1 ";
		  	$sql = "SELECT count(pid) as num_enquiries FROM ver_chronoforms_data_clientpersonal_vic where {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}'  "; 
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  exit();
			$qEnquiries = mysql_query($sql); 
			//$rEnquiries = mysql_fetch_assoc($qEnquiries);
			if($qEnquiries){
				$rEnquiries = mysql_fetch_assoc($qEnquiries); 
			}
 
 			//Quotes Created
 			$_qry_filter = $qry_filter." quotedate>= '{$tFrom}' AND ";
			// $sql = " SELECT count(quoteid) AS num_quotes FROM ( SELECT  quoteid, quotedate  FROM ver_chronoforms_data_followup_vic WHERE {$_qry_filter} 1=1 GROUP BY quoteid ORDER BY cf_id DESC, sales_rep) AS t WHERE DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}'      ";  
			$sql = "  SELECT count(quoteid) AS num_quotes  FROM ( SELECT  quoteid, quotedate  FROM ver_chronoforms_data_followup_vic WHERE  quotedate>= DATE_SUB('{$tFrom}',INTERVAL 1 YEAR)   GROUP BY quoteid ORDER BY cf_id DESC) AS t JOIN (SELECT ClientID FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' ) AS c ON c.ClientID=t.quoteid  ";      
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  exit();
			$qQuotes = mysql_query($sql); 
			if($qQuotes){
				$rQuotes = mysql_fetch_assoc($qQuotes);
			}

			//Contracts Created
			// $sql = "SELECT count(cf_id) as num_contracts FROM ver_chronoforms_data_contract_list_vic WHERE {$qry_filter} DATE_FORMAT(contractdate,'%Y-%m') = '{$r["yearMonth"]}'";
			$sql = "  SELECT count(quoteid) AS num_contracts  FROM ( SELECT  quoteid  FROM ver_chronoforms_data_contract_list_vic WHERE   DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}' ORDER BY cf_id DESC, sales_rep) AS t JOIN (SELECT ClientID FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2}  DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}') AS c ON c.ClientID=t.quoteid  ";   
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');   exit();
			$qContracts = mysql_query($sql);
			if($qContracts){
				$rContracts = mysql_fetch_assoc($qContracts);
			}

			if($i==0){$kpi_table_this_yr .= "<li class='li-header'><span style=>Month</span><span>Enquiry allocated</span><span >Quotes created </span><span>Contracts won</span><span>% Lead conversion</span><span>% quotes conversion</span><span>% Enquiry to Contracts</span></li>  ";} 

			
			$leadP = 0; $quotesP = 0; $contractP = 0;
			if($rQuotes["num_quotes"] && $rEnquiries["num_enquiries"]){
				$leadP = number_format(($rQuotes["num_quotes"]/$rEnquiries["num_enquiries"])*100);
			}

			if($rContracts["num_contracts"] && $rQuotes["num_quotes"]){
				$quotesP = number_format(($rContracts["num_contracts"]/$rQuotes["num_quotes"])*100);
			}
			
			if($rContracts["num_contracts"] && $rEnquiries["num_enquiries"]){
				$contractP = number_format(($rContracts["num_contracts"]/$rEnquiries["num_enquiries"])*100);
			}
 
			$kpi_table_this_yr .= "<li class=\"li-row-clickable\">";
			$kpi_table_this_yr .= "<span>{$mDate}</span><span>".($rEnquiries["num_enquiries"]>0 ? $rEnquiries["num_enquiries"]:"0")." </span><span>".($rQuotes["num_quotes"]>0 ? $rQuotes["num_quotes"]:"0")."  </span><span>".($rContracts["num_contracts"]>0 ? $rContracts["num_contracts"]:"0")."  </span><span>". ($leadP>0 ? $leadP."%":"0") ."  </span><span>". ($quotesP>0 ? $quotesP."%":"0") ." </span><span>". ($contractP>0 ? $contractP."%":"0") ."  </span>";	
			$kpi_table_this_yr .= "</li>"; 
				$kpi_table_this_yr .= "<li style='display:none;'>";
						$kpi_table_this_yr .= "<table class='update-table' style='margin: 0 0 0 0px; display:inline-block;vertical-align: top; font-size:11px; line-height: 1.3em;'>";
			
					//Search clients entry details
					//$is_manager = 1;
					if(isset($user->groups['9'])){
						//$qry_filter = " rep_id='{$user->RepID}' AND ";
						$qry_filter3 = "f.rep_id='{$user->RepID}' AND  f.quotedate>= '{$tFrom}' AND  "; 
						//$is_manager = 0;
					}else if($rep_id=="all"){ 
						$qry_filter3 = " f.rep_id IN ('".implode("','", $ar_rep_id)."') AND f.quotedate>= '{$tFrom}' AND ";  
					}else if($rep_id!="all"){ 
						$qry_filter3 = " f.rep_id='{$rep_id}' AND f.quotedate>= '{$tFrom}' AND  "; 
					} 	

					$_qry_filter = $qry_filter." DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}' AND ";
					if($is_manager==0)
						$consultant_filter_client = ", (SELECT CONCAT(DATE_FORMAT(datenotes,'".SQL_DFORMAT."'),' ', SUBSTRING(content,1,50)) AS note FROM ver_chronoforms_data_notes_vic WHERE clientid=c.clientid ORDER BY cf_id DESC LIMIT 1) AS last_followup ";
					$sql = " SELECT * FROM (SELECT f.quoteid AS client_id, f.projectid, f.rep_id, c.pid, CONCAT(c.client_firstname,' ',c.client_lastname) AS client_name,  c.client_suburb, c.client_wkphone AS phone, f.quotedate, DATE_FORMAT(f.quotedate,'".SQL_DFORMAT."') AS fquotedate, DATE_FORMAT(c.datelodged,'".SQL_DFORMAT."') AS fdatelodged, DATE_FORMAT(c.datelodged,'".SQL_DFORMAT."')  AS enquiry_date, DATEDIFF(f.quotedate, c.datelodged) as q_no_days_pass, DATEDIFF(f.date_won, f.quotedate) as c_no_days_pass, f.total_cost, DATE_FORMAT(f.date_won,'".SQL_DFORMAT."')  AS date_won, f.status, u.name AS rep_name {$consultant_filter_client} FROM (SELECT * FROM (SELECT  *  FROM ver_chronoforms_data_followup_vic WHERE is_builder_project=0 ORDER BY cf_id DESC) AS f0  WHERE quotedate BETWEEN '{$dFrom}' AND '{$dTo}' AND 1=1 GROUP BY quoteid) AS f JOIN (SELECT * FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' AND is_builder=0 ) AS c ON c.clientid=f.quoteid LEFT JOIN ver_users as u ON u.RepID=f.rep_id ORDER BY f.cf_id DESC, rep_name ) AS t  ";
					//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
					//error_log($sql, 3,'/home/vergola/public_html/quote-system/my-error.log');	 

					if(HOST_SERVER=="LA"){ $sql = ""; } 

					$fResult = mysql_query($sql);
					 
					$i=0;
					while ($c = mysql_fetch_assoc($fResult)) { 
						
						if($i==0){$kpi_table_this_yr .= "<tr>".($is_manager ? "<th>Rep. Name</th>":"")."<th width='10%'>Client ID</th><th width='13%'>Client name</th><th width='10%'> Address</th><th width='8%'> Phone</th><th width='10%'>Enquiry Date</th><th width='10%'>Quote Date (No. Days Pass)</th><th width='8%'>Quote $$</th><th width='10%'>Contract Date (No. Days Pass)</th><th width='8%'>Status</th>".($is_manager==0 ? "<th  width='8%' >Last Followup</th>":"")."".($is_manager==0 ? "<th  width='15%' >Note</th>":"")."</tr> ";}
	 					$kpi_table_this_yr .= "<tr>";
							$kpi_table_this_yr .= ($is_manager ? "<td>{$c['rep_name']}</td>":"")."<td><a href=\"".JURI::base()."client-listing-vic/client-folder-vic?pid={$c['pid']}\">{$c['client_id']}</a></td><td>{$c['client_name']}</td><td>{$c['client_suburb']}</td><td>{$c['phone']}</td><td>{$c['fdatelodged']}</td><td>{$c['fquotedate']} ({$c['q_no_days_pass']})</td><td>{$c['total_cost']}</td><td>".(strtolower($c['status'])=="won"?$c['date_won']." (".$c['c_no_days_pass'].")":"")."</td><td>{$c['status']}</td><td>".substr($c['last_followup'], 0,10)."</td><td>".substr($c['last_followup'], 12)."</td>";	
						$kpi_table_this_yr .= "</tr>"; 
						$i++;
					}

					//Search builder entry details
					//error_log("DATE:".$r['target_date'], 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
					if($is_manager==0)
						$consultant_filter_builder = ", (SELECT CONCAT(DATE_FORMAT(datenotes,'".SQL_DFORMAT."'),' ', SUBSTRING(content,1,50)) AS note FROM ver_chronoforms_data_notes_vic WHERE clientid=b.clientid ORDER BY cf_id DESC LIMIT 1) AS last_followup ";
					 
					$sql = "SELECT * FROM (SELECT f.quoteid AS client_id, f.projectid, f.rep_id, b.pid, b.builder_name AS client_name, b.client_suburb, f.quotedate, DATE_FORMAT(f.quotedate,'".SQL_DFORMAT."') AS fquotedate , DATE_FORMAT(b.datelodged,'".SQL_DFORMAT."')  AS enquiry_date, DATE_FORMAT(b.datelodged,'".SQL_DFORMAT."')  AS fenquiry_date, DATEDIFF(f.quotedate, b.datelodged) as q_no_days_pass, DATEDIFF(f.date_won, f.quotedate) as c_no_days_pass, f.total_cost, DATE_FORMAT(f.date_won,'".SQL_DFORMAT."')  AS date_won, f.status, u.name AS rep_name {$consultant_filter_builder}    FROM (SELECT * FROM (SELECT  *  FROM ver_chronoforms_data_followup_vic WHERE is_builder_project=1 ORDER BY cf_id DESC) AS f0 WHERE quotedate BETWEEN '{$dFrom}' AND '{$dTo}' GROUP BY quoteid ) AS f JOIN (SELECT * FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2} DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}' AND  is_builder=1) as b ON b.clientid=f.quoteid  LEFT JOIN ver_users as u ON u.RepID=f.rep_id ORDER BY f.cf_id DESC, rep_name ) AS t   ";
					//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
					if(HOST_SERVER=="LA"){ $sql = ""; } 
					$bResult = mysql_query($sql); 
					 
					$i=0;
					while ($b = mysql_fetch_assoc($bResult)) { 
						if($i==0){$kpi_table_this_yr .= "<tr>".($is_manager ? "<th  width='10%' >Rep. Name</th>":"")."<th width='8%'>Builder ID</th><th width='10%'>Builder name</th><th width='10%'> Address</th><th width='8%'>Phone</th><th width='10%'>Quote Date (No. Days Pass)</th><th width='8%'>Quote $$</th><th width='10%'>Contract Date (No. Days Pass)</th><th width='8%'>Status</th>".($is_manager==0 ? "<th  width='7%' >Last Followup</th>":"")."".($is_manager==0 ? "<th  width='15%' >Note</th>":"")."</tr> ";}
	 					$kpi_table_this_yr .= "<tr>";
							$kpi_table_this_yr .= ($is_manager ? "<td>{$b['rep_name']}</td>":"")."<td><a href=\"".JURI::base()."client-listing-vic/client-folder-vic?pid={$b['pid']}\">{$b['client_id']}</a></td><td>{$b['client_name']}</td><td>{$b['client_suburb']}</td><td>{$c['phone']}</td><td>{$b['fquotedate']} ({$c['q_no_days_pass']})</td><td>{$b['total_cost']}</td><td>".(strtolower($c['status'])=="won"?$c['date_won']." (".$c['c_no_days_pass'].")":"")."</td><td>{$b['status']}</td><td>".substr($b['last_followup'], 0,10)."</td><td>".substr($b['last_followup'], 12)."</td>";	
						$kpi_table_this_yr .= "</tr>"; 
						$i++;
					}

				$kpi_table_this_yr .= "</table>";
			$kpi_table_this_yr .= "</li>";

			$tot_enquiry+=$rEnquiries["num_enquiries"]; $tot_quote+=$rQuotes["num_quotes"]; $tot_contract_won+=$rContracts["num_contracts"]; 
  			$sum_lead+=$leadP; $sum_quote+=$quotesP; $sum_enquiry_w+=0;

			$i++;
			$j++;

		}

		$av_lead=number_format(($sum_lead/12)); $av_quote=number_format(($sum_quote/12)); $av_enquiry_w=number_format(($sum_enquiry_w/12)); 

		$kpi_table_this_yr .= "<li class=\"li-header\" style=\"border:none;\" >";
				$kpi_table_this_yr .= "<span>Total</span><span>".$tot_enquiry."</span><span>".$tot_quote."</span><span>".$tot_contract_won."</span><span>".$av_lead."%</span><span>".$av_quote."%</span><span>".$av_enquiry_w."%</span>";	
			$kpi_table_this_yr .= "</li>";


		$kpi_table_this_yr .= "<li>";  
		$kpi_table_this_yr .= "</li>"; 
		$kpi_table_this_yr .= "</ul>";
		//error_log($kpi_table_this_yr, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
		//echo $kpi_table;
		//----------- END of Current Year KPI V2 --------------
 	}

 






 	if($is_manager || $is_top_admin){
 		//----------- KPI Table Previous Year --------------
 		$kpi_table_manager = "";
		$kpi_table_manager .= "	
		<h3 style='margin:10px 0 0 0; text-decoration:underline; '>KPI Summary</h3>
		<ul  class='list-table kpi-table'  style='margin:0 0; width:50%; display:inline-block;vertical-align: top; font-size:12px;'> 
		"; 
	  	 
		$year = date('Y'); 
		$cMonth = date('m');
		if($cMonth<7){
			$year = $year - 1;
		} 

		$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth FROM ver_rep_sales_target WHERE rep_id='".$user->RepID."' AND year={$year}";
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
		$qResult = mysql_query($sql); 

		if(mysql_num_rows($qResult)<1){
			//error_log("INSIDE", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth  FROM ver_rep_sales_target WHERE  rep_id='Default Target' AND year={$year} "; //rep_id='{$user->RepID}' AND
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
			$qResult = mysql_query($sql); 
		}

		$r = mysql_fetch_assoc($qResult);
		$tFrom = substr($r['dateFromTo'], 0,10);
		$tTo = substr($r['dateFromTo'], -10,10);

		//set the date to the start and end of the month
		$dFrom = date("Y-m-01", strtotime($tFrom));
		$dTo = date("Y-m-t", strtotime($tTo));
 
	 	$i=0;   $kpi_prev = array();
	 	$tot_contract=0; $tot_drawing=0; $tot_council=0; $tot_production=0; $tot_job=0; $tot_built=0;

	 	mysql_data_seek($qResult, 0);  
		while ($r = mysql_fetch_assoc($qResult)){
			$mDate = date_format(date_create($r["target_date"]),"F");
	  
			//Contracts Created 
			//$sql = "  SELECT count(quoteid) AS num_contracts  FROM ( SELECT  quoteid  FROM ver_chronoforms_data_contract_list_vic WHERE   DATE_FORMAT(quotedate,'%Y-%m') = '{$r["yearMonth"]}' ORDER BY cf_id DESC, sales_rep) AS t JOIN (SELECT ClientID FROM ver_chronoforms_data_clientpersonal_vic WHERE {$qry_filter2}  DATE_FORMAT(datelodged,'%Y-%m') = '{$r["yearMonth"]}') AS c ON c.ClientID=t.quoteid  ";   
			$sql = " SELECT COUNT(quoteid) AS num_contracts, IF(drawing_approve_date = NULL,false,COUNT(drawing_approve_date)) AS count_drawing_approve, IF(permit_approved_date = NULL,false,COUNT(permit_approved_date)) AS count_permit_approved_date, IF(production_start_date = NULL,false,COUNT(production_start_date)) AS count_production_start_date,  IF(job_start_date = NULL,false,COUNT(job_start_date)) AS count_job_start_date,  IF(job_end_date = NULL,false,COUNT(job_end_date)) AS count_job_built    FROM ( SELECT  c.quoteid, cv.drawing_approve_date, cs.permit_approved_date, cv.production_start_date, cv.job_start_date, cv.job_end_date FROM ver_chronoforms_data_contract_list_vic AS c JOIN ver_chronoforms_data_contract_vergola_vic AS cv ON cv.projectid=c.projectid JOIN ver_chronoforms_data_contract_statutory_vic AS cs ON cs.projectid=c.projectid WHERE {$qry_filter}  DATE_FORMAT(c.contractdate,'%Y-%m') = '{$r["yearMonth"]}' ORDER BY c.cf_id DESC, sales_rep) AS t; ";
			// error_log("MAN. KPI:  ".$sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  exit();
			$qContracts = mysql_query($sql); 
			if($qContracts){
				$rContracts = mysql_fetch_assoc($qContracts);  
				//error_log(" i=".$i." yearMonth: ".$r["yearMonth"]." sql:".$sql." PRINT_R:".print_r($rContracts,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
			}

			 
			if($i==0){$kpi_table_manager .= "<li class='li-header'><span style=>Month</span><span>No. of Contracts</span><span >No. of Drawing Waiting for Approval </span><span> No. of Council Waiting for Approval</span><span>Contracts Waiting for Production Start</span><span>Contracts Waiting for Job Start</span><span>No. Job Built</span></li>  ";}

			 
			$kpi_table_manager .= "<li class=\"li-row-clickable\">";
			$kpi_table_manager .= "<span>{$mDate}</span><span>".($rContracts["num_contracts"]>0 ? $rContracts["num_contracts"]:"0")."  </span><span>".($rContracts["count_drawing_approve"]>0 ? $rContracts["count_drawing_approve"]:"0")." </span><span>".($rContracts["count_permit_approved_date"]>0 ? $rContracts["count_permit_approved_date"]:"0")."  </span><span>". ($rContracts["count_production_start_date"]>0 ? $rContracts["count_production_start_date"]:"0") ."  </span><span>". ($rContracts["count_job_start_date"]>0 ? $rContracts["count_job_start_date"]:"0") ."  </span><span>". ($rContracts["count_job_built"]>0 ? $rContracts["count_job_built"]:"0") ." </span>";
			$kpi_table_manager .= "</li>"; 

			$kpi_table_manager .= "<li style='display:none;  '>";
						$kpi_table_manager .= "<table class='update-table' style='margin: 0 0 25px 30px; vertical-align: top; font-size:11px; line-height: 1.3em;'>";
			 
					$sql = " SELECT c.projectid, CONCAT(cp.client_firstname,' ',cp.client_lastname) AS client_name,  cp.client_suburb, cp.client_wkphone AS phone, c.cf_id, c.quoteid, cv.contractdate, DATEDIFF(cv.check_measure_date, cv.contractdate) AS n_check_measure_date, DATEDIFF(cv.drawing_approve_date, cv.contractdate) AS n_drawing_approve_date, DATEDIFF(cv.building_permit_issued, cv.contractdate) AS n_building_permit_issued, DATEDIFF(cv.job_start_date, cv.contractdate) AS n_job_start_date, DATEDIFF(cv.job_end_date, cv.contractdate) AS n_job_end_date 
			FROM ver_chronoforms_data_contract_list_vic AS c LEFT JOIN ver_chronoforms_data_contract_vergola_vic AS cv ON cv.projectid=c.projectid LEFT JOIN ver_chronoforms_data_contract_statutory_vic AS cs ON cs.projectid=c.projectid LEFT JOIN ver_chronoforms_data_clientpersonal_vic AS cp ON cp.clientid = c.quoteid
            WHERE {$qry_filter}   DATE_FORMAT(c.contractdate,'%Y-%m') = '{$r["yearMonth"]}' ORDER BY c.cf_id DESC ";
					//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); exit();
					//error_log($sql, 3,'/home/vergola/public_html/quote-system/my-error.log');	 
            		if(HOST_SERVER=="LA"){ $sql = ""; } 
					$fResult = mysql_query($sql);
					
					$j=0;
					while ($c = mysql_fetch_assoc($fResult)) { 
						// error_log(print_r($c,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v3\\my-error.log');
						if($j==0){$kpi_table_manager .= "<tr><th width='10%' style='text-align:left;'>Client Name</th><th width='10%'>Phone</th><th width='10%'>Check Measure</th><th width='10%'>Draw Approval</th><th width='10%'>Building Permit</th><th width='10%'>Job Start</th><th width='10%'>Job Complete</th> <th width='10%'>&nbsp;</th></tr> ";}

	 					$kpi_table_manager .= "<tr>";
							$kpi_table_manager .= "<td style='text-align:left;'><a  class='plain' href='".JURI::base()."contract-listing-vic/contract-folder-vic?projectid={$c['projectid']}&ref=/'   
				>{$c['client_name']}</a></td><td>{$c['phone']}</td><td>{$c['n_check_measure_date']}</td><td>{$c['n_drawing_approve_date']}</td><td>{$c['n_building_permit_issued']}</td>  <td>{$c['n_job_start_date']}</td>   <td>{$c['n_job_end_date']}</td>  ";	
						$kpi_table_manager .= "</tr>"; 
						$j++;

					}

					$kpi_table_manager .= "</table>";
			$kpi_table_manager .= "</li>";

			$tot_contract+=$rContracts["num_contracts"]; $tot_drawing+=$rContracts["count_drawing_approve"]; $tot_council+=$rContracts["count_permit_approved_date"]; $tot_production+=$rContracts["count_production_start_date"]; $tot_job+=$rContracts["count_job_start_date"]; $tot_built+=$rContracts["count_job_built"];
  
  			//error_log("tot_job:".$tot_job, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

			$i++;
		 
		}

		$kpi_table_manager .= "<li class=\"li-header\" style=\"border:none;\" >";
				$kpi_table_manager .= "<span>Total</span><span>".$tot_contract."</span><span>".$tot_drawing."</span><span>".$tot_council."</span><span>".$tot_production."</span><span>".$tot_job."</span><span>".$tot_built."</span>";	
			$kpi_table_manager .= "</li>";
		$kpi_table_manager .= "</ul>";




 	}


	?>


	
	 <!------------- Advertising Table SUMMARY ---------------------- -->
<?php 
	$advertising_table = "";
	if($is_top_admin){  
		$advertising_table .= "<table id='tblAdvertising' class='update-table' style='width:48%; display:inline-block;vertical-align: top; font-size:12px; text-align:center; '>";
   
		//print_r($user);return;
		//echo $user->RepID;return;
		//error_log("SELECT * FROM ver_rep_sales_target WHERE rep_id='".$user["RepID"]."'", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
		//echo $user->RepID;
		$year = date('Y');
		$prev_year = $year - 1;
		   
		$sql = "SELECT count(pid) as count, leadname FROM  ver_chronoforms_data_lead_vic   AS l RIGHT  JOIN ver_chronoforms_data_clientpersonal_vic  AS c ON c.leadname=l.lead WHERE leadname IS NOT NULL AND leadname != '' AND  DATE_FORMAT(datelodged,'%Y') = '{$year}' GROUP BY leadname ORDER BY count DESC"; //
		 
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		$qResult = mysql_query($sql);

		$sql = "SELECT count(pid) as count, leadname FROM  ver_chronoforms_data_lead_vic   AS l RIGHT  JOIN ver_chronoforms_data_clientpersonal_vic  AS c ON c.leadname=l.lead WHERE leadname IS NOT NULL AND leadname != '' AND  DATE_FORMAT(datelodged,'%Y') = '{$prev_year}' GROUP BY leadname ORDER BY count DESC";
		 
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		$qResult2 = mysql_query($sql);
		
		$advertising_list = array();
		$advertising_number_list = array();
		 
		//$r = mysql_fetch_assoc($qSales);
		//error_log(print_r($r,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
	 	$i=0; //$sales_amount_total=0;$target_amount_total=0; $diffAmountTotal = 0; $runningDiffAmount = 0; $prevYearMonthSalesTotal = 0; 
	 	//mysql_data_seek($qResult, 0);   //$r = mysql_fetch_assoc($qResult); print_r($r);return; 
		while ($r = mysql_fetch_assoc($qResult)) { 
			//error_log(print_r($r["yearMonth"],true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$advertising_prev_yr = array();  
			if(!empty($qResult) && mysql_num_rows($qResult)){
				  
				mysql_data_seek($qResult2, 0);  
				while ($s = mysql_fetch_assoc($qResult2)) { 
					if($s["leadname"]==$r["leadname"]){
						$advertising_prev_yr = $s;
						//print_r($sales);
						break;
					} 
				} 

			} 
  

			if($i==0){
				$advertising_table .= "<tr><th >Advertising</th><th width='100'>{$year}</th><th width='100'>{$prev_year}</th></tr> ";
			} 
			 
			$advertising_table .= "<tr>";
			$advertising_table .= "<td>{$r['leadname']}</td><td>{$r['count']}</td><td>{$advertising_prev_yr['count']}</td>";	
			$advertising_table .= "</tr>";
 	
 			array_push($advertising_list,$r['leadname']);
			array_push($advertising_number_list,$r['count']);
			$i++;
			 

		}

		 
		$advertising_table .= "</table>";
  
 	 
 
	 //------------- END OF Advertising Table -->

	//------------- SUBURB LEAD SUMMARY ---------------------- -->
 
 	
	$suburb_lead_table = "";
  
		$suburb_lead_table .= "<table id='tblSuburLead' class='update-table' style='width:48%;  display:inline-block;vertical-align: top; font-size:12px; text-align:center; '>";
   
		//print_r($user);return;
		//echo $user->RepID;return;
		//error_log("SELECT * FROM ver_rep_sales_target WHERE rep_id='".$user["RepID"]."'", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
		//echo $user->RepID;
		$year = date('Y');
		$prev_year = $year - 1;
		   
		$sql = "SELECT count(pid) as count, s.suburb FROM  ver_chronoforms_data_contract_list_vic  AS cl JOIN ver_chronoforms_data_clientpersonal_vic AS c ON c.clientid=cl.quoteid  JOIN ver_chronoforms_data_suburbs_vic AS s ON c.client_suburb=s.suburb WHERE  s.suburb IS NOT NULL AND  s.suburb != '' AND c.client_suburb IS NOT NULL AND  c.client_suburb != '' AND DATE_FORMAT(contractdate,'%Y') = '{$year}' GROUP BY suburb ORDER BY count desc limit 15";
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		$qResult = mysql_query($sql);
		$tqResult = mysql_num_rows($qResult);

		$sql = "SELECT count(pid) as count, s.suburb FROM ver_chronoforms_data_contract_list_vic  AS cl JOIN ver_chronoforms_data_clientpersonal_vic AS c ON c.clientid=cl.quoteid  JOIN ver_chronoforms_data_suburbs_vic AS s ON c.client_suburb=s.suburb WHERE DATE_FORMAT(contractdate,'%Y') = '{$prev_year}' GROUP BY suburb ORDER BY count desc limit 15";
		 //s.suburb IS NOT NULL AND  s.suburb != '' AND c.client_suburb IS NOT NULL AND  c.client_suburb != '' AND
		//error_log("error 963: ".$sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		$qResult2 = mysql_query($sql);
		$tqResult2 = mysql_num_rows($qResult2);
		//error_log($tRows, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
		//error_log(print_r($qResult2,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

		$suburb_list = array();
		$suburb_number_list = array();  
		//$r = mysql_fetch_assoc($qSales);
		//error_log(print_r($r,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
	 	$i=0; $j=0; //$sales_amount_total=0;$target_amount_total=0; $diffAmountTotal = 0; $runningDiffAmount = 0; $prevYearMonthSalesTotal = 0; 
	 	//mysql_data_seek($qResult, 0);   //$r = mysql_fetch_assoc($qResult); print_r($r);return; 
		while ($r = mysql_fetch_assoc($qResult)) { 
			
			if(empty($r)) break; 

			$advertising_prev_yr = array();  
			if($tqResult>0){ 
				 
				if($tqResult2>0)
					mysql_data_seek($qResult2, 0);  

				while ($s = mysql_fetch_assoc($qResult2)) { 
					if($s["suburb"]==$r["suburb"]){
						$advertising_prev_yr = $s;
						//print_r($sales);
						break;
					} 
				} 
 
			} 
  

			if($i==0){
				$suburb_lead_table .= "<tr><th >Suburb Analysis</th><th width='100'>{$year}</th><th width='100'>Suburb</th><th width='100'>{$prev_year}</th></tr> ";
			} 

			if($tqResult2>0){
				mysql_data_seek($qResult2, $i); 
				$s = mysql_fetch_assoc($qResult2);
			}else{
				$s['suburb'] = "";
				$s['count'] = null;
			}

			$suburb_lead_table .= "<tr>";
			$suburb_lead_table .= "<td>{$r['suburb']}</td><td>{$r['count']}</td><td>{$s['suburb']}</td><td>{$s['count']}</td>";	
			$suburb_lead_table .= "</tr>";

			array_push($suburb_list,$r['suburb']);
			array_push($suburb_number_list,$r['count']);

			$i++;
			 

		}

		 
		$suburb_lead_table .= "</table>";



		//------------ Construction Analysis
		$year = date('Y');
		$cMonth = date('m');
		if($cMonth<7){
			$year = $year - 1;
		}

		
		$sql = "SELECT *, DATE_FORMAT(target_date,'%Y-%m') as yearMonth, SUM(target_amount) as target_amount FROM ver_rep_sales_target WHERE  rep_id='Default Target' AND year={$year} GROUP BY yearMonth"; //rep_id='{$user->RepID}' AND
		 
		//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		$qResult = mysql_query($sql); 


		$r = mysql_fetch_assoc($qResult);
		 
		$dFrom = substr($r['dateFromTo'], 0,10);
		$dTo = substr($r['dateFromTo'], -10,10);
		//error_log($dFrom, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
		//set the date to the start and end of the month
		$dFrom = date("Y-m-01", strtotime($dFrom));
		$dTo = date("Y-m-t", strtotime($dTo));


		$construction_record_list = array();
		$construction_built_list = array();

		mysql_data_seek($qResult, 0);   //$r = mysql_fetch_assoc($qResult); print_r($r);return; 
		while ($r = mysql_fetch_assoc($qResult)) { 
			//print_r($r);
			$sql = "SELECT count(cl.cf_id) as count,  IF(job_end_date = NULL,false,COUNT(job_end_date)) AS count_job_built, DATE_FORMAT(cl.contractdate,'%Y-%m') as yearMonth  FROM   ver_chronoforms_data_contract_list_vic  AS cl   JOIN ver_chronoforms_data_contract_vergola_vic AS cv ON cv.projectid=cl.projectid WHERE    DATE_FORMAT(cl.contractdate,'%Y') = '{$year}' GROUP BY YEAR(cl.contractdate), MONTH(cl.contractdate) ORDER BY count desc limit 15";
			$qSales = mysql_query($sql);
			//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log'); 
			$construction = array(); 

			if(!empty($qSales) && mysql_num_rows($qSales)){
				//mysql_data_seek($qSales, 0); 

				while ($s = mysql_fetch_assoc($qSales)){ 

					if($s["yearMonth"]==$r["yearMonth"]){
						$construction = $s;
						 
						break;
					}else{
						$construction["yearMonth"] = $r["yearMonth"];
						$construction["count"] = 0;
						$construction["count_job_built"] = 0;
					} 
				} 

			}else{
				$construction["yearMonth"] = $r["yearMonth"];
				$construction["count"] = 0;
				$construction["count_job_built"] = 0;
				 
			}





			array_push($construction_record_list,$construction['count']);
			array_push($construction_built_list,$construction['count_job_built']);

		}	
		//------------ End of contruction analysis
 	}
 	 
 	 //print_r($construction_record_list);
	?>
	 

	
 
	<?php  
 
	if($is_user){ //To do list for sales consultant users

	$to_do_list = ""; $i = 0;
 
	$to_do_list .= " 
		<div style='width:100%; display: inline-block;'>		

		<h3 style='margin:10px 0 0 0; text-decoration:underline; '>To Do List</h3>
		
		<ul id='to_do_list' class='list-table'  style='margin:0 0; width: 100%;  display:inline-block;vertical-align: top; font-size:12px;'> 
		";   
	 
			$sql = "SELECT c.datelodged, c.pid, c.clientid, repident AS rep_id,  c.appointmentdate,  c.client_firstname, c.client_lastname, c.qdelivered, c.next_followup, c.is_first_appointment, n.content AS note FROM ver_chronoforms_data_clientpersonal_vic  AS c 
 LEFT JOIN (SELECT * FROM (SELECT * FROM ver_chronoforms_data_notes_vic ORDER BY cf_id desc) as t GROUP BY clientid  ) as n ON n.clientid=c.clientid  
 WHERE repident='{$user->RepID}' AND (appointmentdate IS NOT NULL OR next_followup IS NOT NULL) AND if(next_followup IS NULL,DATE(appointmentdate)<=DATE(NOW()), DATE(next_followup)<=DATE(NOW())) AND (c.status!='Won' OR c.status!='Lost') AND c.date_contract_signed IS NULL ";

 			$fResult = mysql_query($sql); 
			$total_records1 = mysql_num_rows($fResult); 

 $sql .= " ORDER BY IF(DATE(appointmentdate) = DATE(NOW()), 0, 1), IF(DATE(next_followup) = DATE(NOW()), 0, 1), appointmentdate DESC, c.next_followup DESC LIMIT {$start}, ".NUMBER_PER_PAGE." ";
	  
			$fResult = mysql_query($sql); 
			$i=0; 
			while ($l = mysql_fetch_assoc($fResult)) {  

				if($i==0){$to_do_list .= "<li class='li-header'>	
					<span class='col-date'>Due Date</span><span class='col-client-id'>Client ID</span><span class='col-name'> Name </span><span class='col-date'> Mobile </span><span class='col-note'>Last notes</span><span class='col-status'>Status</span> </li>  ";
				}
  
				$is_overdue = false;
				$client_status = "Initial Appointment";
				$due_date = ""; 

				if(empty($l['qdelivered'])==true){
					$client_status = "Initial Appointment";
					$phpdate = strtotime( $l['appointmentdate'] );
					$due_date = date( PHP_DFORMAT, $phpdate );
					//error_log("inside 1..", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

				}else if(empty($l['next_followup'])==true){
					//break;
					$client_status = "Follow-up";
					//$phpdate = strtotime( $l['next_followup'] );
					//$due_date = date( PHP_DFORMAT, $phpdate );
					$due_date = "Need appointment";
					//error_log("inside 2..", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
				}else{
					$client_status = "Quote Delivered";
					$phpdate = strtotime( $l['next_followup'] );
					$due_date = date( PHP_DFORMAT, $phpdate );
					//error_log("inside 3..", 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');


				}
			 	//error_log("due_date  :".$due_date, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');

				$_due_date = date( 'Y-m-d', $phpdate ); 
				$is_overdue = 0;
				if($_due_date<date('Y-m-d')){
					$is_overdue = 1; 
				}else{
					$is_overdue = 0; 
				}
 				 
				$input_followup = "<input type='text' name='followup_date' value=''  autocomplete='off' class='datepicker' onchange='' />";
				  
			 
				$chk_done = "<input type='button' name='' value='update'  onclick='save_status(event,this)' />";
  	 
				// $link_client = "<a  class='template_link' onclick=\"window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=auto,height=auto,directories=no,location=no'); return false;\"  href='".JURI::base()."?pid={$l['pid']}&tmpl=component&chronoform=Dialog_Client_Folder&ref=".JURI::base()."' style='margin-right:5px;'  
				// >{$l['clientid']}</a>"; 

				$link_client = "<a  class='' href='".JURI::base()."client-listing-vic/client-folder-vic?pid={$l['pid']}&ref=/'   
				>{$l['clientid']}</a>"; 

				$to_do_list .= "<li class=\"li-row ".($is_overdue?'highligh-red':'')." \">".
								"<input type='hidden'  class='cf_id' value='' /> ".
					 			"<input type='hidden'  class='pid' value='{$l['pid']}' /> ";

				$to_do_list .= "<span class='col-date'> {$due_date} </span>".
								"<span  class='col-client-id'>  {$link_client} </span>".
								"<span class='col-name'> ". ($l['is_builder']==1 ? $l['builder_name'] : $l['client_firstname'].' '.$l['client_lastname'])."</span>".
								"<span class='col-date'> {$l['mobile']} </span>".
								"<span class='col-note'> {$l['note']} </span>". 
								"<span class='col-status'> {$client_status}  </span>"; 
				$to_do_list .= "</li>"; 
				$i++; 

		 	}
 
	 
		$to_do_list .= "</ul>"; 

		$to_do_list .= "<div class='pagination-layer'>";
		$to_do_list .= pagination($page, $total_records, "");
		$to_do_list .= "</div>"; 	

	}
	else if($is_manager ){ //To do list for office users

	$to_do_list_manager = ""; $i = 0;
 
	$to_do_list_manager .= " 
		<div style='width:100%; display: inline-block;'>		
			
			<h3 style='margin:0; text-decoration:underline; '>Reminder List</h3>
			
			<ul id='to_do_list' class='list-table'  style='margin:0 0; width: 100%;  display:inline-block;vertical-align: top; font-size:12px;'> 
			";  
	 
			$sql = " SELECT f.cf_id, c.datelodged, pid, c.clientid, f.projectid, c.repident AS rep_id, c.client_firstname, c.client_lastname, c.is_builder, c.builder_name, f.status, f.date_won, f.date_contract_signed, f.date_contract_system_created, n.content 
 FROM ver_chronoforms_data_followup_vic AS f JOIN  ver_chronoforms_data_clientpersonal_vic  AS c ON c.clientid=f.quoteid  
 LEFT JOIN (SELECT * FROM (SELECT * FROM ver_chronoforms_data_notes_vic ORDER BY cf_id desc) as t GROUP BY clientid  ) as n ON n.clientid=c.clientid 
 WHERE (f.cf_id>5534 AND f.status='Won')"; //f.cf_id>5534 is the next number the new system generate a new quotations.

 			$fResult = mysql_query($sql); 
			$total_records1 = mysql_num_rows($fResult); 

 $sql .= " ORDER BY date_won DESC, f.date_contract_signed DESC LIMIT {$start}, ".NUMBER_PER_PAGE." ";
	   
			$fResult = mysql_query($sql); 
			$i=0; $is_contract_generated = 0;
			while ($l = mysql_fetch_assoc($fResult)) {  

				if($i==0){$to_do_list_manager .= "<li class='li-header'>	
					<span class='col-date'>Date</span><span class='col-client-id'>Project ID</span><span class='col-name'> Client Name </span> <span class='col-note'>Last notes</span><span class='col-name'>Follow-up</span> </li>  ";
				}

				$c = null;
				if(!empty($l['date_contract_system_created'])){
					$sql = "SELECT cv.drawing_prepare_date, cv.drawing_prepare_date_followup, cv.drawing_approve_date, cv.drawing_approve_date_followup, cv.job_start_date, cv.job_start_date_followup, cs.stat_req_easement_waterboard_approval_date, cs.stat_req_easement_waterboard_followup, cs.stat_req_easement_council_approval_date, cs.stat_req_easement_council_followup, cs.m_o_d_followup  
 FROM  ver_chronoforms_data_contract_list_vic AS cl   JOIN ver_chronoforms_data_contract_vergola_vic AS cv ON cv.projectid=cl.projectid 
 JOIN ver_chronoforms_data_contract_statutory_vic AS cs ON cs.projectid=cl.projectid 
 WHERE cl.projectid='{$l['projectid']}' AND ((drawing_prepare_date IS NULL OR DATE(cv.drawing_prepare_date_followup)<=DATE(NOW()) )
 OR (cv.drawing_approve_date IS NULL AND DATE(cv.drawing_approve_date_followup)<=DATE(NOW()) )
 OR (cv.job_start_date IS NULL AND DATE(cv.job_start_date_followup)<=DATE(NOW()) )
 OR (cs.stat_req_easement_waterboard_approval_date IS NULL AND DATE(cs.stat_req_easement_waterboard_followup)<=DATE(NOW()) )
 OR (cs.stat_req_easement_council_approval_date IS NULL AND DATE(cs.stat_req_easement_council_followup)<=DATE(NOW()) )
 OR (cs.m_o_d IS NULL AND DATE(cs.m_o_d_followup)<=DATE(NOW())))";
					
					$fResult1 = mysql_query($sql); 
					$c = mysql_fetch_assoc($fResult1);
					$is_contract_generated = 1;

					//error_log($sql, 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');  exit();
				}
  
				$is_overdue = false;
				$status = "";
				$date = ""; 
				//error_log(print_r($c,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_us\\my-error.log');
				// if(strtolower($l['status'])=="won" && empty($l['date_won'])==false && empty($l['date_contract_signed'])==true){
				// 	$status = "Contract for delivery";
				// 	$phpdate = strtotime( $l['date_won'] );
				// 	$date = date( PHP_DFORMAT, $phpdate ); 
				$status="";$status1="";$status2="";$status3="";$status4="";$status5="";$status6="";$status7="";$status8="";

 
				if(empty($l['date_contract_signed'])==false && empty($l['date_contract_system_created']) ){
					$status1 = "Ready for Contract";
					$status .= $status1;
					$phpdate = strtotime( $l['date_contract_signed'] );
					$date = date( PHP_DFORMAT, $phpdate ); 
				}

				if($is_contract_generated && empty($c['drawing_approve_date']) && !empty($c['drawing_prepare_date_followup'])){
					
					$phpdate = strtotime( $c['drawing_prepare_date_followup'] );
					$date = date( PHP_DFORMAT, $phpdate ); 
					if(date( 'Y-m-d', $phpdate )<=date('Y-m-d')){ 
						$status2 = (!empty($status)?"<br/>":"")."Drawing & Prepare";
						$status .= $status2;
					}
  
				}

				if($is_contract_generated && empty($c['drawing_approve_date']) && !empty($c['drawing_approve_date_followup'])){
					
					$phpdate = strtotime( $c['drawing_approve_date_followup'] );
					$date = date( PHP_DFORMAT, $phpdate ); 

					if(date( 'Y-m-d', $phpdate )<=date('Y-m-d')){ 
						$status3 = (!empty($status)?"<br/>":"")."Drawing Approval";
						$status .= $status3;
					}
				}

				if($is_contract_generated && empty($c['job_start_date']) && !empty($c['job_start_date_followup'])){
					
					$phpdate = strtotime( $c['job_start_date_followup'] );
					$date = date( PHP_DFORMAT, $phpdate ); 

					if(date( 'Y-m-d', $phpdate )<=date('Y-m-d')){ 
						$status4 = (!empty($status)?"<br/>":"")."Job Start";
						$status .= $status4;
					}
				}

				if($is_contract_generated && empty($c['stat_req_easement_waterboard_approval_date']) && !empty($c['stat_req_easement_waterboard_followup'])){  
					$phpdate = strtotime( $c['stat_req_easement_waterboard_followup'] );
					$date = date( PHP_DFORMAT, $phpdate );

					if(date( 'Y-m-d', $phpdate )<=date('Y-m-d')){ 
						$status5 = (!empty($status)?"<br/>":"")."Waterboard Approval";
						$status .= $status5;
					}
				}

				if($is_contract_generated && empty($c['stat_req_easement_council_approval_date']) && !empty($c['stat_req_easement_council_followup'])){ 
					
					$phpdate = strtotime( $c['stat_req_easement_council_followup'] );
					$date = date( PHP_DFORMAT, $phpdate );

					if(date( 'Y-m-d', $phpdate )<=date('Y-m-d')){ 
						$status6 = (!empty($status)?"<br/>":"")."Council Approval";
						$status .= $status6;
					}
				}

				 
				if($is_contract_generated && !empty($c['m_o_d']) && !empty($c['m_o_d_followup'])){ 
					$phpdate = strtotime( $c['m_o_d_followup'] );
					$date = date( PHP_DFORMAT, $phpdate ); 

					if(date( 'Y-m-d', $phpdate )<=date('Y-m-d')){ 
						$status7 = (!empty($status)?"<br/>":"")."Modifications";
						$status .= $status7;
					}
				}

				if($is_contract_generated && empty($c['engineering_approved_date']) && !empty($c['engineering_approved_date_followup'])){ 
					
					$phpdate = strtotime( $c['engineering_approved_date_followup'] );
					$date = date( PHP_DFORMAT, $phpdate );

					if(date( 'Y-m-d', $phpdate )<=date('Y-m-d')){ 
						$status8 = (!empty($status)?"<br/>":"")."Engineering Epproval";
						$status .= $status8;
					}
				}

				if(empty($status)){
					$i++; 
					continue;
				}

				$_due_date = date( 'Y-m-d', $phpdate ); 
				$is_overdue = 0;
				if($_due_date<date('Y-m-d')){
					$is_overdue = 1;
				}else{
					$is_overdue = 0;
				}
			  
  
				$input_followup = "<input type='text' name='followup_date' value=''  autocomplete='off' class='datepicker' onchange='' />";
				  
			 
				$chk_done = "<input type='button' name='' value='update'  onclick='save_status(event,this)' />";
  	  
  	  			if(!empty($status1)){
  	  				$link_client = "<a  class='' href='".JURI::base()."client-listing-vic/client-folder-vic?cid={$l['clientid']}&cf_id={$l['cf_id']}&ref=/'   
				>{$l['projectid']}</a>";  
  	  			}else{ 
					$link_client = "<a  class='' href='".JURI::base()."contract-listing-vic/contract-folder-vic?projectid={$l['projectid']}&ref=/'   
				>{$l['projectid']}</a>";  
  	  			}

				$to_do_list_manager .= "<li class=\"li-row ".($is_overdue?'highligh-red':'')." \">".
								"<input type='hidden'  class='cf_id' value='' /> ".
					 			"<input type='hidden'  class='pid' value='{$l['pid']}' /> ";

				$to_do_list_manager .= "<span class='col-date'> {$date} </span>".
								"<span  class='col-client-id'>  {$link_client} </span>".
								"<span class='col-name'> ". ($l['is_builder']==1 ? $l['builder_name'] : $l['client_firstname'].' '.$l['client_lastname'])."</span>". 
								"<span class='col-note'> {$l['note']} </span>". 
								"<span class='col-status'> {$status1}{$status2}{$status3}{$status4}{$status5}{$status6}{$status7}{$status8} </span>"; 
				$to_do_list_manager .= "</li>"; 
				$i++; 

		 	}
 
	 
		$to_do_list_manager .= "</ul>"; 

		$to_do_list_manager .= "<div class='pagination-layer'>";
		$to_do_list_manager .= pagination($page, $total_records, "");
		$to_do_list_manager .= "</div>"; 	
 

	}	


	if($is_user){

		echo "<div style='width:100%;  '>";  
				echo $to_do_list;   

				echo $kpi_table;
				echo $kpi_table_prev_yr;
			echo "</div>";	
			//echo $notes;
 
		echo "</div>";
	 
		echo "<div style='width:100%;  margin:15px; 0;'>";
			echo "<h3 style='margin:30px 0 10px 0; text-decoration:underline; '>Sales Summary</h3>";
			echo $sales_table;

			echo $kpi_graph; 

		echo "</div>";

		echo "<div style='width:50%; display:inline-block '>";
			echo "&nbsp;";
		echo "</div>";
		//echo $kpi_graph_prev_yr;
	 
		
	  
	}else if($is_manager){

		echo "<div style='width:65%;  '>";  
			echo $to_do_list_manager;    
		echo "</div>";	
	 
		echo $kpi_table_manager;
		  	
		echo "</div>";
		
		echo "<h3 style='margin:30px 0 10px 0; text-decoration:underline; '>Sales Summary</h3>";
		echo $sales_table;
			//echo $notes;
		echo $kpi_graph;

		//echo $kpi_graph_prev_yr;
 
		

	}else if($is_top_admin){

		echo "<div style='width:100%; margin:0;'>";
			//echo $kpi_table;
			echo "<h3 style='margin:30px 0 10px 0; text-decoration:underline; '>Sales Summary</h3>";
			echo $sales_table; 
			echo $kpi_graph;

			echo "<br/><br/><br/>"; 
			echo $kpi_table_last_yr; 
			echo $kpi_table_this_yr;
			 

			echo "<br/><br/><br/>";
			echo $kpi_table_manager;
			//echo "<br/><br/><br/><div style='display:inline-block; width:50%;'></div>";
			echo $construction_analysis_graph;


			echo "<br/><br/><br/><br/>";
			echo $advertising_table;
			echo $advertising_chart;

			//echo "<br/><br/><br/>";
			echo $suburb_lead_table; 
			echo $suburb_lead_chart;

			

			
  			


		echo "</div>  ";
  
		  
		
	}

	echo "<form id='update_to_do_list_form' method='post' action=''  >
		<input type='hidden' id='cf_id' name='cf_id' value='' />
		<input type='hidden' id='followup_date' name='followup_date' value='' />
		<input type='hidden' id='status'  name='status' value='' />
		<input type='hidden' id='command'  name='command' value='' />
		<input type='submit' value='Submit' id='send_update_to_do_list_form' style='display:none'>
	</form>";

	

	?>
	 

<script type="text/javascript">
	$(document).ready(function(){ 

		window.addEvent('load', function() {
				new DatePicker('.datepicker,date_time', {pickerClass: 'datepicker_dashboard', format: PHP_DFORMAT, inputOutputFormat: PHP_DFORMAT, allowEmpty: 1, timePicker: 0, timePickerOnly: 0});
			});

		// Get the context of the canvas element we want to select
		//var ctx = document.getElementById("myChart").getContext("2d");
		//var myNewChart = new Chart(ctx).PolarArea(data);
		//--------- INIT KPI BAR CHART ----------
		
		var sales_amount = [<?php echo implode(",", $sales_amount); ?>];
		var sales_target = [<?php echo implode(",", $sales_target); ?>];
		//var sales_amount_prev = [<?php echo implode(",", $sales_amount2); ?>];
 
		var data = {
		    labels: [<?php echo "'".implode("','", $sales_period)."'"; ?>],
		    datasets: [
		    	 {
		            label: "Target Sales",
		            fillColor: "rgba(31, 105, 165,0.9)",
		            strokeColor: "rgba(31, 105, 165,1)",
		            pointColor: "rgba(31, 105, 165,1)",
		            pointStrokeColor: "#fff",
		            pointHighlightFill: "#fff",
		            pointHighlightStroke: "rgba(31, 105, 165,1)", 
		            data: sales_target
		        },
		        {
		            label: "Monthly Sales",
		            fillColor: "rgba(254, 191, 1,0.8)",
		            strokeColor: "rgba(254, 191, 1,0.6)",
		            pointColor: "rgba(254, 191, 1,0.6)",
		            pointStrokeColor: "#111",
		            pointHighlightFill: "#111",
		            pointHighlightStroke: "rgba(254, 191, 1,1)", 
		            data: sales_amount
		        }
		       
		    ]
		};

		var options = {
			
		 	//Boolean - Show a backdrop to the scale label
		    scaleShowLabelBackdrop : true,

		    //String - The colour of the label backdrop
		    scaleBackdropColor : "rgba(255,255,255,0.75)",

		    // Boolean - Whether the scale should begin at zero
		    scaleBeginAtZero : true,

		    //Number - The backdrop padding above & below the label in pixels
		    scaleBackdropPaddingY : 2,

		    //Number - The backdrop padding to the side of the label in pixels
		    scaleBackdropPaddingX : 2,

		    //Boolean - Show line for each value in the scale
		    scaleShowLine : true,

		    //Boolean - Stroke a line around each segment in the chart
		    segmentShowStroke : true,

		    //String - The colour of the stroke on each segement.
		    segmentStrokeColor : "#111",

		    //Number - The width of the stroke value in pixels
		    segmentStrokeWidth : 2,

		    //Number - Amount of animation steps
		    animationSteps : 70,

		    //String - Animation easing effect.
		    animationEasing : "easeOutBounce",

		    //Boolean - Whether to animate the rotation of the chart
		    animateRotate : true,

		    //Boolean - Whether to animate scaling the chart from the centre
		    animateScale : false,

		    //String - A legend template
		    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",

		    animation: false,

		    scaleLabel: function (valuePayload) {
					    return '$'+Number(valuePayload.value).toFixed(0).replace(/./g, function(c, i, a) {
    return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
});
					}


		};

		 

		// This will get the first returned node in the jQuery collection.
		//var myNewChart = new Chart(ctx);
		var ctx = $("#myChart").get(0).getContext("2d");
		var myLineChart = new Chart(ctx).Bar(data, options);
		legend(document.getElementById('placeholder'), data);

		//--------- END KPI BAR CHART ----------

<?php 
	if($is_top_admin){
?>
		//--------- INIT CONSTRUCTION ANALYSIS BAR CHART ----------
		
		var construction_record_list = [<?php echo implode(",", $construction_record_list); ?>];
		var construction_built_list = [<?php echo implode(",", $construction_built_list); ?>]; 
		 
 
		var data_construct = {
		    labels: [<?php echo "'".implode("','", $sales_period)."'"; ?>],
		    datasets: [
		        {
		            label: "Close Contracts",
		            fillColor: "rgba(255,255,0,0.6)",
		            strokeColor: "rgba(255,255,0,0.3)",
		            pointColor: "rgba(255,255,0,0.3)",
		            pointStrokeColor: "#fff",
		            pointHighlightFill: "#fff",
		            pointHighlightStroke: "rgba(0,255,0,1)", 
		            data: construction_record_list
		        },
		        {
		            label: "Built Contracts",
		            fillColor: "rgba(151,187,205,0.9)",
		            strokeColor: "rgba(151,187,205,1)",
		            pointColor: "rgba(151,187,205,1)",
		            pointStrokeColor: "#fff",
		            pointHighlightFill: "#fff",
		            pointHighlightStroke: "rgba(151,187,205,1)",
		            data: construction_built_list
		        } 
		    ]
		};


		var ctx_construct = $("#construction_analysis_chart").get(0).getContext("2d");
		var myLineChart_construct = new Chart(ctx_construct).Bar(data_construct, options2);
		legend(document.getElementById('construction_analysis_chart_placeholder'), data_construct);
	 
		//--------- END CONSTRUCTION ANALYSIS BAR CHART ----------


		//--------- INIT ADVERTISING PIE CHART ----------
		
		var advertising_list = [<?php echo "'".implode("','", $advertising_list)."'"; ?>];
		var advertising_number_list = [<?php echo implode(",", $advertising_number_list); ?>]; 
		var color_list = ["#F7464A","#1a237e","#FDB45C","#90a4ae","#26a69a","#ec407a","#3f51b5","#a5d6a7","#ff1144","#4e342e","#880e4f","#4caf50","#827717","#212121","#e65100"];
		  
		var data2 = [
		    {
		        value: advertising_number_list[0],
		        color:color_list[0], 
		        label: advertising_list[0],
                labelColor : 'white',
                labelFontSize : '16' 
		    },
		    {
		        value: advertising_number_list[1],
		        color:color_list[1], 
		        label: advertising_list[1],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[2],
		        color:color_list[2], 
		        label: advertising_list[2],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[3],
		        color:color_list[3], 
		        label: advertising_list[3],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[4],
		        color:color_list[4], 
		        label: advertising_list[4],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[5],
		        color:color_list[5], 
		        label: advertising_list[5],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[6],
		        color:color_list[6], 
		        label: advertising_list[6],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[7],
		        color:color_list[7], 
		        label: advertising_list[7],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[8],
		        color:color_list[8], 
		        label: advertising_list[8],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[9],
		        color:color_list[9], 
		        label: advertising_list[9],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[10],
		        color:color_list[10], 
		        label: advertising_list[10],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[11],
		        color:color_list[11], 
		        label: advertising_list[11],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[12],
		        color:color_list[12], 
		        label: advertising_list[12],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[13],
		        color:color_list[13], 
		        label: advertising_list[13],
                labelColor : 'white',
                labelFontSize : '16'
		    },
		    {
		        value: advertising_number_list[14],
		        color:color_list[14], 
		        label: advertising_list[14],
                labelColor : 'white',
                labelFontSize : '16'
		    }
		];

		var options2 = {
			tooltipTemplate: "<%= value %>",

		    tooltipEvents: [],

		    showTooltips: true,
					//Boolean - Whether we should show a stroke on each segment
		    segmentShowStroke : false,

		    //String - The colour of each segment stroke
		    segmentStrokeColor : "#fff",

		    //Number - The width of each segment stroke
		    segmentStrokeWidth : 2,

		    //Number - The percentage of the chart that we cut out of the middle
		    percentageInnerCutout : 50, // This is 0 for Pie charts

		    //Number - Amount of animation steps
		    animationSteps : 100,

		    //String - Animation easing effect
		    animationEasing : "easeOutBounce",

		    //Boolean - Whether we animate the rotation of the Doughnut
		    animateRotate : false,

		    //Boolean - Whether we animate scaling the Doughnut from the centre
		    animateScale : false,
		     
		    //String - A legend template
		    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"><%if(segments[i].label){%><%=segments[i].label%><%}%></span></li><%}%></ul>",
 
		};

		var _options = {
      //Boolean - Show a backdrop to the scale label
      scaleShowLabelBackdrop : true,
      //Boolean - Whether to show labels on the scale
      scaleShowLabels : true,
      // Boolean - Whether the scale should begin at zero
      scaleBeginAtZero : true,
      scaleLabel : "<%%= Number(value) + ' %'%>",
      legendTemplate: "<ul class=\"<%%=name.toLowerCase()%>-legend\"><%% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%%=datasets[i].strokeColor%>\"></span><%%if(datasets[i].label){%><%%=datasets[i].label%> <strong><%%=datasets[i].value%></strong><%%}%></li><%%}%></ul>",
      tooltipTemplate: "<%%= value %> Label"
    }

		// var ctx2 = $("#advertising_chart").get(0).getContext("2d");
		// var myPieChart = new Chart(ctx2[0]).Pie(data2,options2);
	
	
		var ctx2 = document.getElementById("advertising_chart").getContext("2d");
		var advertising_chart = new Chart(ctx2).Pie(data2,{
	        tooltipEvents: [],
	        showTooltips: true,
	        onAnimationComplete: function() {
	            this.showTooltip(this.segments, true);
	        },
	        tooltipTemplate: "<%= label %> - <%= value %>",
	        animationSteps : 100,

		    //String - Animation easing effect
		    animationEasing : "easeInOutQuart"
	    }); 

	 
//--------- END ADVERTISING PIE CHART ----------



	//--------- INIT SUBURB LEAD PIE CHART ----------
		
		var suburb_list = [<?php echo "'".implode("','", $suburb_list)."'"; ?>];
		var suburb_number_list = [<?php echo implode(",", $suburb_number_list); ?>];
		var color_list2 = ["#F7464A","#6633BD","#FDB45C","#90a4ae","#26a69a","#fff59d","#3f51b5","#a5d6a7","#ff1144","#ef9a9a","#26a69a","#4caf50","#d1c4e9","#6d4c41"];
		  
		var data3 = [
		    {
		        value: suburb_number_list[0],
		        color:color_list[0], 
		        label: suburb_list[0]
		    },
		    {
		        value: suburb_number_list[1],
		        color:color_list[1], 
		        label: suburb_list[1]
		    },
		    {
		        value: suburb_number_list[2],
		        color:color_list[2], 
		        label: suburb_list[2]
		    },
		    {
		        value: suburb_number_list[3],
		        color:color_list[3], 
		        label: suburb_list[3]
		    },
		    {
		        value: suburb_number_list[4],
		        color:color_list[4], 
		        label: suburb_list[4]
		    },
		    {
		        value: suburb_number_list[5],
		        color:color_list[5], 
		        label: suburb_list[5]
		    },
		    {
		        value: suburb_number_list[6],
		        color:color_list[6], 
		        label: suburb_list[6]
		    },
		    {
		        value: suburb_number_list[7],
		        color:color_list[7], 
		        label: suburb_list[7]
		    },
		    {
		        value: suburb_number_list[8],
		        color:color_list[8], 
		        label: suburb_list[8]
		    },
		    {
		        value: suburb_number_list[9],
		        color:color_list[9], 
		        label: suburb_list[9]
		    },
		    {
		        value: suburb_number_list[10],
		        color:color_list[10], 
		        label: suburb_list[10]
		    },
		    {
		        value: suburb_number_list[11],
		        color:color_list[11], 
		        label: suburb_list[11]
		    },
		    {
		        value: suburb_number_list[12],
		        color:color_list[12], 
		        label: suburb_list[12]
		    },
		    {
		        value: suburb_number_list[13],
		        color:color_list[13], 
		        label: suburb_list[13]
		    },
		    {
		        value: suburb_number_list[14],
		        color:color_list[14], 
		        label: suburb_list[14]
		    }
		];

		 

		// var ctx2 = $("#advertising_chart").get(0).getContext("2d");
		// var myPieChart = new Chart(ctx2[0]).Pie(data2,options2);
	
	
		var ctx3 = document.getElementById("suburb_lead_chart").getContext("2d");
		var suburb_lead_chart = new Chart(ctx3).Pie(data3,{
	        tooltipEvents: [],
	        showTooltips: true,
	        onAnimationComplete: function() {
	            this.showTooltip(this.segments, true);
	        },
	        tooltipTemplate: "<%= label %> - <%= value %>",
	        animationSteps : 100,

		    //String - Animation easing effect
		    animationEasing : "easeInOutQuart"
	    });

//--------- END SUBURB LEAD PIE CHART ----------
<?php 
	}
?>


 
		$(document).on("click",".li-row-clickable",function(e){
			//alert($(this).next().html());
			$(this).next().toggle(); 

		});

 

		$('.form_datetime').datetimepicker({
        //language:  'en',
        weekStart: 1,
        todayBtn:  0,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		forceParse: 0,
        showMeridian: 0
    });


	});

 
 	function request_sel_consultant(){
 		var sel_consultant = $("#cbo_consultant").children("option:selected").val();
 		//alert(sel_consultant);
 		//console.log("sel_consultant "+sel_consultant);

 		window.location.assign("<?php echo JURI::base().'?consultant_id='; ?>"+sel_consultant);
 		 


 	}

 	function save_status(event,o){

			var action = "";
			var status = "";
			var followup_date = "";

			
			var cf_id =  $(o).closest('li').children("input.cf_id").val(); //.children("table").children("tbody").children("tr").find("input.cf_id").val();
			 
			status =  $(o).closest('li').children("span.col-status").children("select").children("option:selected").val();
			followup_date =  $(o).closest('li').children("span.followup-date").children("input[name='followup_date']").val();
			 
			 

			//var data = { command: 'save_status', cf_id : cf_id, status: status, followup_date: followup_date }

		 	$("#cf_id").val(cf_id);
		 	$("#command ").val('save_status');
		 	$("#followup_date").val(followup_date);
		 	$("#status").val(status);

		 	$("#send_update_to_do_list_form").click();
			//location.reload(); 
		}

	function save_is_done(event,o){

			var action = "";
			
			var cf_id =  $(o).closest('li').children("input.cf_id").val(); //.children("table").children("tbody").children("tr").find("input.cf_id").val();
			var is_done = 0;
			if($(o).is(':checked')){
				is_done = 1;
			}
			//alert(cf_id);

			var data = { command: 'save_is_done', cf_id : cf_id, is_done: is_done }

			 
			$.ajax({
				type: "POST",
				url: action,
				dataType: 'json', 	
				data: data,	
				success: function(data) {					
					//alert(data['message']); 
				}		
			});
		}
 

</script> 

 