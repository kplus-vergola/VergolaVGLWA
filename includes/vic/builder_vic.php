<?php
/*  
  // connect to database
  include 'database.php';
  if (!$con) { echo "Error"; }
  $dbname = 'vergola_quotedb_v4_as_live';
  mysql_select_db($dbname);
  $initialBuildersArray = array( );
  $result = mysql_query("SELECT cf_id, suburbid, builder_name, builder_contact, builder_address1, builder_address2, builder_suburb, builder_state, builder_postcode, builder_wkphone, builder_mobile, builder_fax, builder_email FROM ver_chronoforms_data_builder_vic",$con) or die (mysql_error());
  while( $row = mysql_fetch_assoc( $result ) ) {
      $initialBuildersArray[] = $row;
  }
  $builders = $initialBuildersArray;
  // Cleaning up the term
  $term = trim(strip_tags($_GET['term']));
  // get match
  $matches = array();
  foreach($builders as $builder){
if(stripos($builder['builder_name'], $term) !== false){
    // Adding the necessary "value" and "label" fields and appending to result set
    $builder['value'] = $builder['builder_name'];
    $builder['label'] = "{$builder['builder_name']}, {$builder['builder_address1']} {$builder['builder_suburb']} {$builder['builder_state']} {$builder['builder_postcode']}";
    $matches[] = $builder;
    }
  } 
  // Truncate, encode and return the results
  $matches = array_slice($matches, 0, 5);
  print json_encode($matches);
  mysql_close($con);
  */

   ini_set('display_errors', 0);

   $system_config_file_path = $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';
   $config = array();
   if (file_exists($system_config_file_path)) {
       include($system_config_file_path);
       $system_config = new JConfig();
       $config['db']['host_name'] = $system_config->host;
       $config['db']['db_name'] = $system_config->db;
       $config['db']['user_name'] = $system_config->user;
       $config['db']['password'] = $system_config->password;
       $config['path']['log_folder'] = $system_config->log_path . '\\';

       $config['db']['table_name']['builders'] = "ver_chronoforms_data_clientpersonal_vic";
   }

   $matches = array(
       array(
           'id' => 'Error', 
           'builder_name' => 'Error'         
       )
   );
   if (count($config) > 0) {
       $con = mysql_connect(
           $config['db']['host_name'], 
           $config['db']['user_name'], 
           $config['db']['password']
       );

  if ($con) {
    mysql_select_db($config['db']['db_name']);
    $state_table = $config['db']['table_name']['builders'];

    $dbname = $config['db']['table_name']['builders'];
    mysql_select_db($dbname);
   
    $data = array();
    $initialSuburbsArray = array( );
    $term = trim(strip_tags($_GET['term']));
    /*$result1 = mysql_query("
              SELECT 
                  `pid`,
                  `builder_name`,
                  `builder_contact`,                 
                  `builder_contact_title` AS btitle,
                  `builder_contact_firstname` AS contact_firstname,
                  `builder_contact_lastname` AS contact_lastname,
                  `client_streetno` AS streetno,
                  `client_streetname` AS streetname,
                  `client_address1` AS address1,
                  `client_address2` AS address2,
                  `client_suburb` AS suburb,
                  `client_state` AS state,
                  `client_postcode` AS postcode,
                  `client_wkphone` AS workphone,
                  `client_mobile` AS mobilephone,
                  `client_hmphone` AS homephone,
                  `client_other` AS other,
                  `client_email` AS email,
                  
                  `clientid`
              FROM 
                  $state_table
              WHERE 
                  builder_name LIKE '%{$term}%' 
                  AND !ISNULL(builder_name) 
                  AND builder_name != ''
              GROUP BY 
                  `builder_name`
              ORDER BY 
                  `pid` DESC 
              -- LIMIT 1 
              ",$con) or die (mysql_error()); */

    $result = mysql_query("
      SELECT                
        c.pid,
        c.builder_name,
        c.builder_contact,
        c.builder_contact_title AS btitle,
        c.builder_contact_firstname AS contact_firstname,
        c.builder_contact_lastname AS contact_lastname,
        c.client_streetno AS streetno,
        c.client_streetname AS streetname,
        c.client_address1 AS builder_address1,
        c.client_address2 AS builder_address2,
        c.client_suburb AS builder_suburb,
        c.client_state AS builder_state,
        c.client_postcode AS builder_postcode,
        c.client_wkphone AS builder_wkphone,
        c.client_mobile AS builder_mobile,
        c.client_hmphone AS homephone,
        c.client_other AS builder_fax,
        c.client_email AS builder_email,
        c.clientid 
      FROM
        (SELECT * FROM ver_chronoforms_data_clientpersonal_vic 
            WHERE 
              builder_name LIKE '%{$term}%' 
              AND !ISNULL(builder_name)             
              AND builder_name != ''
            GROUP BY builder_name, pid ORDER BY builder_name, pid DESC ) c1
            JOIN ver_chronoforms_data_clientpersonal_vic AS c ON c1.pid = c.pid 
          GROUP BY
            c.builder_name
    ",$con) or die (mysql_error()); 


    while( $row = mysql_fetch_assoc( $result ) ) {

      // Check if builder contact is not empty and populate the lastname and firstname using the splitted value of the builder contact, would be better to automatically notify a popup msg for the user the save and apply the changes
        if($row['builder_contact']!='' && $row['contact_firstname']==null && $row['contact_lastname']==null){        
          $name = $row['builder_contact'];
          $name = explode(' ', $name);     
          $row['contact_firstname'] = $name[0];
          $row['contact_lastname'] = (isset($name[count($name)-1])) ? $name[count($name)-1] : '';
        }

      $row['value'] = $row['builder_name'];
      // $row['label'] = $row['builder_name'];
      $row['label'] = "{$row['builder_name']}, {$row['streetno']} {$row['streetname']} {$row['address1']} {$row['address2']},  {$row['suburb']} {$row['state']} {$row['postcode']}";

      array_push($data, $row);
    }
    
  }
    //error_log(print_r($data,true), 3,'C:\\xampp\htdocs\\vergola_contract_system_v4_sa\\my-error.log');
    $data = array_slice($data, 0, 10);  
    echo json_encode ($data);   
    mysql_close($con);
  }


  ?>