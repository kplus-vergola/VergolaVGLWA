<?php
//error_log("INSIDE cbeam_vic from multiple bay: ".$ReLen[0], 3,'C:\\xampp\htdocs\\vergola_contract_system_v3\\my-error.log'); 
$resultcbeam = mysql_query("SELECT cf_id, inventoryid, description, uom, rrp, cost FROM ver_chronoforms_data_inventory_vic WHERE section = 'Frame' and category = 'CBeams' or category = 'Beams' or category = 'Timber'");
// ***************************** Cbeam 200 Deep by 2.4mm ***********************************************//
echo "<tr><td colspan=\"8\" class=\"subheading\">Framework</td></tr>";
echo "<tr><td><input type=\"hidden\" name=\"id[0]\" value=\"{$getCFID[0]}\" /><select class=\"desclist\" name=\"desclist[]\" >";	
	    $cbeams = array();
		while ($cbeam = mysql_fetch_array($resultcbeam)) {
		$cbeams[] = $cbeam;
		$heading = isset($cbeam[0]) ? $cbeam[0] : null;
		$BeamIDArrayPhp .= isset($cbeam[1]) ? 'BeamIDArray["'.$heading.'"]="'.$cbeam[1].'";' : null;	
		$BeamDESCArrayPhp .= isset($cbeam[2]) ? 'BeamDESCArray["'.$heading.'"]="'.$cbeam[2].'";' : null;
		$BeamRRPArrayPhp .= isset($cbeam[4]) ? 'BeamRRPArray["'.$heading.'"]="'.$cbeam[4].'";' : null;
		$BeamCOSTArrayPhp .= isset($cbeam[5]) ? 'BeamCOSTArray["'.$heading.'"]="'.$cbeam[5].'";' : null;
		}
		foreach($cbeams as $cbeam){
      	echo "<option value=\"".$cbeam['cf_id']."\""; 
		if($cbeam['cf_id'] == $getID[0]) { echo "selected=\"selected\""; } else { echo ""; }
		echo ">".$cbeam['description']."</option>";	
		}
echo "</select>";

foreach ($cbeams as $cbeam)
{	if ($cbeam['cf_id'] == $getID[0]) {
	echo "<input type=\"hidden\" class=\"desc\" name=\"desc[0]\" value=\"".$cbeam['description']."\" readonly=\"readonly\" />". 
    "<input type=\"hidden\" class=\"invent\" name=\"invent[0]\" value=\"".$cbeam['inventoryid']."\" readonly=\"readonly\" />". 
    "<input type=\"hidden\" class=\"descrrp\" name=\"descrrp\" value=\"".$cbeam['rrp']."\" readonly=\"readonly\" />".
    "<input type=\"hidden\" class=\"desccost\" name=\"desccost\" value=\"".$cbeam['cost']."\" readonly=\"readonly\" />"; } 
	else { echo "";}
}
echo "</td>";
	
// Webbing
$querywebbing="SELECT cf_id, rrp, cost FROM ver_chronoforms_data_inventory_vic WHERE   cf_id = '5'";
$resultweb = mysql_query($querywebbing);
if(!$resultweb){die ("Could not query the database: <br />" . mysql_error());}
//create selection list				
while($rweb = mysql_fetch_row($resultweb)){
$heading = isset($rweb[0]) ? $rweb[0] : null;	
$BeamRRPArrayPhp .= isset($rweb[1]) ? 'BeamRRPArray["'.$heading.'"]="'.$rweb[1].'";' : null;
$BeamCOSTArrayPhp .= isset($rweb[2]) ? 'BeamCOSTArray["'.$heading.'"]="'.$rweb[2].'";' : null;
	
if ($getWeb[0] == 'Yes') {
echo "<td><select class=\"webbing-list\" name=\"webname\" >".
          "<option value=\"No\">No</option>".
          "<option value=\"5\" selected=\"selected\">Yes</option>".
     "</select>"; 
 
echo "<input type=\"hidden\" class=\"webbing\" name=\"webbing[0]\" value=\"Yes\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"webrrp\" name=\"webrrp[]\" value=\"{$rweb[1]}\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"webcost\" name=\"webcost[]\" value=\"{$rweb[2]}\" readonly=\"readonly\" /></td>";
	 } else {		 
		 echo "<td><select class=\"webbing-list\" name=\"webname\" >".
          "<option value=\"No\" selected=\"selected\">No</option>".
          "<option value=\"5\">Yes</option>".
     "</select>"; 
	 echo "<input type=\"hidden\" class=\"webbing\" name=\"webbing[0]\" value=\"No\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"webrrp\" name=\"webrrp[]\" value=\"0\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"webcost\" name=\"webcost[]\" value=\"0\" readonly=\"readonly\" /></td>";
	 }
	 }

// Colours
echo "<td><select name=\"colour[0]\">";
    
		 foreach($colours as $colour){
      	echo "<option value=\"".$colour."\"";
		if($colour == $getColour[0]){ echo "selected=\"selected\""; 
		} else {echo ""; }
		echo ">".$colour."</option>";
		 }		 	
echo"</select></td>";

// Paint and Powder
echo "<td><select class=\"paint-list\" name=\"paintselect\" >"; 
	    foreach($paints as $paint){
      	echo "<option value=\"".$paint['cf_id']."\""; 
		if($paint['category'] == $getFinish[0]){ echo "selected=\"selected\"";
		} else {echo "";}
		echo">".$paint['category']."</option>";	
		}
echo "</select>";

foreach($paints as $paint) {
if ($paint['category'] == $getFinish[0]){
echo "<input type=\"hidden\" class=\"paintdesc\" name=\"paint[0]\" value=\"".$paint['category']."\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"paintrrp\" name=\"paintrrp[]\" value=\"".$paint['rrp']."\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"paintcost\" name=\"paintcost[]\" value=\"".$paint['cost']."\" readonly=\"readonly\" />";
} else {echo ""; }
     }
	 echo "</td>";

// Unit of Measurement
echo "<td><input type=\"text\" name=\"uom[0]\" readonly=\"readonly\" value=\"{$getUOM[0]}\"></td>";

// Unit Quantity
echo "<td><input type=\"text\" class=\"qtylen\" name=\"qty[0]\" value=\"{$getQty[0]}\"></td>";
error_log("ReLen: ".$ReLen[0], 3,'C:\\xampp\htdocs\\vergola_contract_system_v3\\my-error.log');
// Length or Width
echo "<td><input type=\"text\"  name=\"slength[0]\" class=\"length\" value=\"{$ReLen[0]}\"></td>";

// Total RRP
echo "<td><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[0]\" value=\"{$getRRP[0]}\"><input type=\"hidden\" class=\"cst\" name=\"cst[0]\" value=\"{$getCost[0]}\"></td></tr>";


// ***************************** Cbeam 200 Deep by 2.4mm ********************************************//

echo "<tr><td><input type=\"hidden\" name=\"id[1]\" value=\"{$getCFID[1]}\" /><select class=\"desclist\" name=\"desclist[]\" >";

		foreach($cbeams as $cbeam) {
      	echo "<option value=\"".$cbeam['cf_id']."\"";
		if($cbeam['cf_id'] == $getID[1]) {
		echo "selected=\"selected\""; 
		} else {echo "";}
		echo ">".$cbeam['description']."</option>";	
		}
echo "</select>";

//echo "<span>GET - {$getID[1]}</span>";

foreach($cbeams as $cbeam){
if ($cbeam['cf_id'] == $getID[1]) {
echo "<input type=\"hidden\" class=\"desc\" name=\"desc[1]\" value=\"".$cbeam['description']."\" readonly=\"readonly\" />". 
     "<input type=\"hidden\" class=\"invent\" name=\"invent[1]\" value=\"".$cbeam['inventoryid']."\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"descrrp\" name=\"descrrp\" value=\"".$cbeam['rrp']."\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"desccost\" name=\"desccost\" value=\"".$cbeam['cost']."\" readonly=\"readonly\" />"; } 
else { echo "";}
      }	 
	 echo "</td>";
	 
// Webbing
$querywebbing="SELECT cf_id, rrp, cost FROM ver_chronoforms_data_inventory_vic WHERE  cf_id = '5'";
      $resultweb = mysql_query($querywebbing);
      if(!$resultweb){die ("Could not query the database: <br />" . mysql_error());}
      //create selection list				
	   while($rweb = mysql_fetch_row($resultweb))
	{
		$heading = isset($rweb[0]) ? $rweb[0] : null;	
		$BeamRRPArrayPhp .= isset($rweb[1]) ? 'BeamRRPArray["'.$heading.'"]="'.$rweb[1].'";' : null;
		$BeamCOSTArrayPhp .= isset($rweb[2]) ? 'BeamCOSTArray["'.$heading.'"]="'.$rweb[2].'";' : null;
	
	if ($getWeb[1] == 'Yes') {
echo "<td><select class=\"webbing-list\" name=\"webname\" >".
          "<option value=\"No\">No</option>".
          "<option value=\"5\" selected=\"selected\">Yes</option>".
     "</select>"; 
 
echo "<input type=\"hidden\" class=\"webbing\" name=\"webbing[1]\" value=\"Yes\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"webrrp\" name=\"webrrp[]\" value=\"{$rweb[1]}\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"webcost\" name=\"webcost[]\" value=\"{$rweb[2]}\" readonly=\"readonly\" /></td>";
	 } else {		 
		 echo "<td><select class=\"webbing-list\" name=\"webname\" >".
          "<option value=\"No\" selected=\"selected\">No</option>".
          "<option value=\"5\">Yes</option>".
     "</select>"; 
	 echo "<input type=\"hidden\" class=\"webbing\" name=\"webbing[1]\" value=\"No\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"webrrp\" name=\"webrrp[]\" value=\"0\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"webcost\" name=\"webcost[]\" value=\"0\" readonly=\"readonly\" /></td>";
	 }
	 }

// Colours
echo "<td><select name=\"colour[1]\">";
        foreach($colours as $colour) {
      	echo "<option value=\"".$colour."\"";
		if($colour == $getColour[1]){ echo "selected=\"selected\""; 
		} else {echo "";}
		echo ">".$colour."</option>";
		}		 	
echo"</select></td>";

// Paint and Powder
echo "<td><select class=\"paint-list\" name=\"paintselect\" >";  
	    foreach($paints as $paint) {
      	echo "<option value=\"".$paint['cf_id']."\""; 
		if($paint['category'] == $getFinish[1]){ echo "selected=\"selected\"";
		} else {echo "";}
		echo">".$paint['category']."</option>";	
		}
echo "</select>";

foreach($paints as $paint) {
if ($paint['category'] == $getFinish[1]){
echo "<input type=\"hidden\" class=\"paintdesc\" name=\"paint[1]\" value=\"".$paint['category']."\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"paintrrp\" name=\"paintrrp[]\" value=\"".$paint['rrp']."\" readonly=\"readonly\" />".
     "<input type=\"hidden\" class=\"paintcost\" name=\"paintcost[]\" value=\"".$paint['cost']."\" readonly=\"readonly\" />";
} else {echo ""; }
}
	 echo "</td>";

// Unit of Measurement
echo "<td><input type=\"text\" name=\"uom[1]\" readonly=\"readonly\" value=\"{$getUOM[1]}\"></td>";

// Unit Quantity
echo "<td><input type=\"text\" class=\"qtylen\" name=\"qty[1]\" value=\"{$getQty[1]}\"></td>";

// Length or Width
echo"<td><input type=\"text\"  name=\"slength[1]\" class=\"width\" value=\"{$getLength[1]}\"></td>";

// Total RRP
echo "<td><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[1]\" value=\"{$getRRP[1]}\"><input type=\"hidden\" class=\"cst\" name=\"cst[1]\" value=\"{$getCost[1]}\"></td></tr>";

// ***************************** CBeam Corner - IRV4 ********************************************//
$resultbeam = mysql_query("SELECT cf_id, inventoryid, description, uom, rrp, cost FROM ver_chronoforms_data_inventory_vic WHERE  cf_id = '$getID[2]'" );
echo "<tr><td><input type=\"hidden\" name=\"id[2]\" value=\"{$getCFID[2]}\" />";

		while ($beam = mysql_fetch_array($resultbeam)) { 
	      	echo $beam['description'];	
			echo "<input type=\"hidden\" class=\"desc\" name=\"desc[2]\" value=\"".$beam['description']."\" readonly=\"readonly\" />".
			"<input type=\"hidden\" class=\"invent\" name=\"invent[2]\" value=\"".$beam['inventoryid']."\" readonly=\"readonly\" />".
			"<input type=\"hidden\" class=\"descrrp\" name=\"descrrp\" value=\"".$beam['rrp']."\" readonly=\"readonly\" />".
	        "<input type=\"hidden\" class=\"desccost\" name=\"desccost\" value=\"".$beam['cost']."\" readonly=\"readonly\" /></td>";
		}	
 // Webbing
echo"<td><input type=\"hidden\" class=\"webbing\" name=\"webbing[2]\" value=\"\" readonly=\"readonly\" /></td>";    

// Colours
echo"<td><input type=\"hidden\" class=\"colour\" name=\"colour[2]\" value=\"\" readonly=\"readonly\" /></td>";
     	
// Paint and Powder
echo"<td><input type=\"hidden\" id=\"paint[]\" name=\"paint[2]\" value=\"\" readonly=\"readonly\" /></td>";

// Unit of Measurement
echo "<td><input type=\"text\" name=\"uom[2]\" readonly=\"readonly\" value=\"{$getUOM[2]}\"></td>";

// Unit Quantity
echo "<td><input type=\"text\" class=\"qtylen\" name=\"qty[2]\" value=\"{$getQty[2]}\"></td>";

// Length or Width
echo "<td><input type=\"hidden\" id=\"slength[]\" name=\"slength[2]\" value=\"\" readonly=\"readonly\" /></td>";

// Total RRP
echo "<td><input type=\"text\" class=\"rrp\" readonly=\"readonly\" name=\"rrp[2]\" value=\"{$getRRP[2]}\"><input type=\"hidden\" class=\"cst\" name=\"cst[2]\" value=\"{$getCost[2]}\"></td></tr>";

?>