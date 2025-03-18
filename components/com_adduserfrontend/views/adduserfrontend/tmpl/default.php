<?php 
/*
* Copyright Copyright (C) 2012 - Kim Pittoors
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html 
*/
defined('_JEXEC') or die('Restricted access'); 
// Access check.
if (!JFactory::getUser()->authorise('adduserfrontend.createuser', 'com_adduserfrontend')) 
{
	return JError::raiseWarning(404, JText::_('')); // Display nothing because controller already does show that message also
}
?>
<?php
// Get DB acces
$db =& JFactory::getDBO();

// Get joomla component system params
$itemid = JRequest::getInt('Itemid', 0); 
$app = JFactory::getApplication('site');
$params =  & $app->getParams('com_adduserfrontend');
$operationmode = $params->get( 'operationmode', 0);
$namemode = $params->get( 'namemode', 0);  

// Check if the group is chosen from frontend or backend
$usertypemode = $params->get( 'usertypemode', 0); 	
if($usertypemode == '0'){
$setusertype = $params->get( 'setusertype', 2); 
$hiddenusertype = "0"; // Initialize
} else {
$setusertype = "FRONTEND";
$hiddenusertype = $params->get( 'hiddenusertype', 1);
}
// END - Check if the group is chosen from frontend or backend

$notificationemail = $params->get( 'notificationemail', 0);
$adminnotificationemail = $params->get( 'adminnotificationemail', 0);
$usernamemode = $params->get( 'usernamemode', 0 );
if($usernamemode !== '1'){
$unameexist = '0';
} else {
$unameexist = $params->get( 'unameexist', 0);
}

// Get fieldsettings
$emailexist = $params->get( 'emailexist', 1);
$passwordmode = $params->get( 'passwordmode', 0);
$genericemail = $params->get( 'genericemail', 0);  

// Get parent_id of usergroup
function get_parent_id($id)
{
	
// Get DB acces
$db =& JFactory::getDBO();

// Get the parent id of a custom usergroup
$query = "SELECT parent_id FROM #__usergroups WHERE id = '$id' order by parent_id DESC";	
$db->setQuery($query); // Set query
$result = $db->loadResult(); // Load result

return $result;
} 
// End -  Get the parent id

// Make addition to username if username exists
function MakeAddition($fusername) {
// Get DB acces
$db =& JFactory::getDBO();
// Going into a loop
$finished = false;  // We're not finished loop yet (we just started the loop)
$i = 1; // Counting
while(!$finished) {                          // While not finished
$sql = "SELECT COUNT(*) ".$db->nameQuote('username')." FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('username')." = ".$db->quote($fusername.$i).""; // Check in DB if the alternative username doesnt exist
$db->setQuery($sql);
$num_rows_add = $db->loadResult();	    
if ($num_rows_add == "0") {        // If username DOES NOT exist...
$finished = true;                    // We are finished stop loop
} 
$i++;
}
return $i-1;
}
// END - Make addition to username if username exists

// Clean special chars
function clean_now($text)
{
$text=strtolower($text);
$code_entities_match = array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','+','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','/','*','+','~','`','=');  
$code_entities_replace = array('-','-','','','','','','','','','','','','','','','','','','','','','','','');
$text = str_replace($code_entities_match, $code_entities_replace, $text);
return $text;
} 
// End clean special chars

// Function to create a random password
function createRandomPassword() {
$chars = "abcdefghijkmnopqrstuvwxyz0123456789";
srand((double)microtime()*1000000);
$i = 0;
$pass = '' ;
while ($i < 10) {
$num = rand() % 33;
$tmp = substr($chars, $num, 1);
$pass = $pass . $tmp;
$i++;
}	
return $pass;
} // End - Function to create a random password

// Encrypt password for Joomla
function getCryptedPassword($plaintext, $salt = '', $encryption = 'md5-hex', $show_encrypt = false)
{

// Get the salt to use.
$salt = JUserHelper::getSalt($encryption, $salt, $plaintext);
$encrypted = ($salt) ? md5($plaintext.$salt) : md5($plaintext);
return ($show_encrypt) ? '{MD5}'.$encrypted : $encrypted;        
} // END - getCryptedPassword

// Get user and Groupid
$user   = &JFactory::getUser();
$uid    = $user->get('id');

// Initialize some variables
$custumgroupparentids = ""; // Initialize variable
$normalgroupids = ""; // Initialize variable
$normalgroupidsstring = ""; // Initialize variable
$custumgroupparentidsstring = ""; // Initialize variable

// Get the highest group id (not heigest id but with the most permisions)
$user = JFactory::getUser(); // get user data
$usergroups = $user->getAuthorisedGroups(); // get all usergroups for this user

foreach ($usergroups as $usergroup) { // For each usergroup do something
if($usergroup > "8") { // If the usergroup ID is higher then 8 and therefore is a custum usergroup

// Get the parent id of this custum usergroup
$result = get_parent_id($usergroup);

// If the resulting parent_id is also a custom usergroup
while($result > 8) { // Loop to get parent_id untill we find a parent id of the standard joomla usergroups
$result = get_parent_id($result);
}

// Put results in comma seperated string
$custumgroupparentidsstring .= $result.","; // Make comma seperated string out of results

} else { // Else of: if($usergroup > "8") { - (usergroup is not higher then 8 and therefore is a 'normal' usergroup)
$normalgroupidsstring .= $usergroup.","; // Make comma seperated string out of results
}

} // END - (foreach ($usergroups as $usergroup) { - For each usergroup do something)

$custumgroupparentidsstring = substr($custumgroupparentidsstring, 0, -1); // Delete not needed comma's
$custumgroupparentids = explode(",", $custumgroupparentidsstring); // Explode comma seperated string to array

$normalgroupidsstring = substr($normalgroupidsstring, 0, -1); // Delete not needed comma's
$normalgroupids = explode(",", $normalgroupidsstring); // Explode comma seperated string to array

$allgroupids = array_merge($custumgroupparentids,$normalgroupids); // Merge the 2 arrays
$allgroupids = array_unique($allgroupids); // Remove duplicate value's from array
sort($allgroupids, SORT_NUMERIC); // Sort all groups numeric
$highestgroup = max($allgroupids); // Get highest groupid or parent groupid
$groupid = $highestgroup;
// End - Get highest groupid

		
// Handle form
if(isset($_POST['import'])) {
// User helper 
jimport( 'joomla.user.helper' );
if($passwordmode == 0){
$createpassword = createRandomPassword();
$password = getCryptedPassword($createpassword, $salt= '', $encryption= 'md5-hex', $show_encrypt=false);
$showpass = $createpassword;
} else {	
$postpassword  = trim($_POST['password']);
$password = getCryptedPassword($postpassword, $salt= '', $encryption= 'md5-hex', $show_encrypt=false);
$showpass = $postpassword;
}

// Getting name from form
if($namemode == 1){
$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
} else { // Else of: if($namemode == 1){
$name = trim($_POST['name']);

// Get firstname and lastname
$xname = explode(" ", $name);
$firstname = $xname[0];

// Make lastname
if(!empty($xname[1])) {	
$lastname = $xname[1];	
}
if(!empty($xname[2])) {	
$lastname = $xname[1].' '.$xname[2];	
}
if(!empty($xname[3])) {	
$lastname = $xname[1].' '.$xname[2].' '.$xname[3];	
}
if(!empty($xname[4])) {	
$lastname = $xname[1].' '.$xname[2].' '.$xname[3].' '.$xname[4];	
}
if(!empty($xname[5])) {	
$lastname = $xname[1].' '.$xname[2].' '.$xname[3].' '.$xname[4].' '.$xname[5];	
}
if(!empty($xname[6])) {	
$lastname = $xname[1].' '.$xname[2].' '.$xname[3].' '.$xname[4].' '.$xname[5].' '.$xname[6];	
}
}
$divider = ' ';
if(!empty($lastname)){ // Complete name
$name = $firstname.$divider.$lastname; 
} else {
$name = $firstname;  // If name is one word
}

// Loading group ID (if chosen at frontend)
if($usertypemode == '1'){
$group = $_POST['group'];
} else {
$group = ""; // Initialize variable we will not be using because he group is not chosen from the frontend
}

if($group > 8) {
$parentgrouporgroup = get_parent_id($group);
while($parentgrouporgroup > 8) { // Loop to get parent_id untill we find a parent_id of the standard joomla usergroups
$parentgrouporgroup = get_parent_id($parentgrouporgroup);
}
} else {
$parentgrouporgroup = $group;
}
// END - Loading group ID (if chosen at frontend)

// Getting the username from the form or creating one based on the name
if($usernamemode == 1){
$username  = trim($_POST['username']);
$username1 = clean_now($username);
$username = $username1;
} elseif ($usernamemode == 2) {
$username = trim($_POST['email']);
$username1 = $username;
} else {
if(empty($lastname)){ 
$username1 = $firstname; 
} else {
$lastnamesign = mb_substr ($lastname, 0, 1);
$username1 = $firstname . '-' . $lastnamesign;
}
$username1 = str_replace (" ", "-", $username1);
$username1 = strtolower($username1); 
$username = $username1;
}

// We have found a free alternative username lets add it to the $addition string
$addition = MakeAddition($username); 
// End - Make addition to username if username exists

// Get usertype 
// Check if the usertype ID that is provided in the settings is an existing usergroup
if($setusertype == "FRONTEND"){
$usertype = $group;
$query = "SELECT ".$db->nameQuote('title')." FROM ".$db->nameQuote('#__usergroups')." WHERE id = ".$db->quote($usertype)."";
$debugq = $query;
$db->setQuery($query);
$usertypename = $db->loadResult();
if($usertypename == ""){
echo '<p><font color="red">The group-<b>ID</b> you provided in your settings doesnt exist in the #__usergroups table! Fix this in your settings.</font></p>';							
}
}
// END - Check if the usertype ID that is provided in the settings is an existing usergroup   


if($setusertype == "2"){
$usertype = '2'; 
$usertypename = 'Registered';
}
if($setusertype == "3"){
$usertype = '3'; 
$usertypename = 'Author';
}
if($setusertype == "4"){
$usertype = '4'; 
$usertypename = 'Editor';
}
if($setusertype == "5"){
$usertype = '5'; 
$usertypename = 'Publisher';
}
if($setusertype == "6"){
$usertype = '6'; 
$usertypename = 'Manager';
}
if($setusertype == "7"){
$usertype = '7'; 
$usertypename = 'Administrator';
}

// Custum usergroup
if($setusertype == "100"){
$custumgroup = $params->get( 'custumgroup' ); 
$usertype = $custumgroup; 

// Check if the usertype ID that is provided in the settings is an existing usergroup
$query = "SELECT ".$db->nameQuote('title')." FROM ".$db->nameQuote('#__usergroups')." WHERE id = ".$db->quote($usertype)."";
$db->setQuery($query);
$usertypename = $db->loadResult();
if($usertypename == ""){
echo '<p><font color="red">The group-<b>ID</b> you provided in your settings doesnt exist in the #__usergroups table! Fix this in your settings.</font></p>';
}
}
// End - Get usertype from config

// Check if username exists
$sql = "SELECT COUNT(*) ".$db->nameQuote('username')." FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('username')." = ".$db->quote($username)."";
$db->setQuery($sql);
$num_rows = $db->loadResult();
if($num_rows == 0){
$username = $username;
$usernameexists = "0";
} else {
if ($unameexist == "0") {
$username = $username.$addition;
$usernameline = "" . JText::_('THEUSERNAME') . " <strong>" . $username1 . "</strong> " . JText::_('USERCHANGENAME') . " <strong>" . $username . "</strong><br>";
echo $usernameline;
$usernameexists = "0";
} else {	
$usernameexists = "1";	
}
}

// Create generic emailadress (faking an emailadress)
if( $genericemail == "1" ) {
// Get Domain
$domain = $_SERVER['HTTP_HOST']; 
$domain = str_replace ("www.", "", $domain);
// Make generic email
$email = $username . '@' . $domain;
$emaildoesexist = "0";  // We dont want a double email check when using this option
} else {
$email = trim($_POST['email']);
}

// Check if email exists 
if ($emailexist == "1") {	
$sql = "SELECT COUNT(*) ".$db->nameQuote('email')." FROM ".$db->nameQuote('#__users')." WHERE ".$db->nameQuote('email')." = ".$db->quote($email)."";
$db->setQuery($sql);
$num_rows = $db->loadResult();
if($num_rows == 0){
$email = $email;
$emaildoesexist = "0"; 
} else {	 
$emaildoesexist = "1"; // This email already exists in the joomla user db
}
} else {
$emaildoesexist = "0"; // This email already exists in the joomla user db but we dont check for double mails so we let it pass
}	
	
// Save data in cookies if we are sending back the user to the form because of double username or email
if($usernameexists == "1" || $emaildoesexist == "1") { 
if($namemode == "1"){
setcookie("firstname", $firstname, time()+30); 
setcookie("lastname", $lastname, time()+30); 
} else {
setcookie("name", $name, time()+30); 
}
if( $genericemail !== "1" ) {	
setcookie("email", $email, time()+30); 
}
if( $passwordmode == "1" ) {
setcookie("showpass", $showpass, time()+30); 
}
if($usernamemode == "1"){
setcookie("username", $username, time()+30); 
}
}
setcookie("group", $group, time()+30); 

if($emaildoesexist == "1") { // Email exists - Send message to user and then send user back to the form
echo '<script language="JavaScript">
alert ("'.JText::_("EMAILEXISTS").'")
history.go(-1);
</script>';
} else {
if($usernameexists == "1") { // Username exists and automatic renaming is off - Send message to user and then send user back to the form
echo '<script language="JavaScript">
alert ("'.JText::_("USERNAMEEXISTS").'")
history.go(-1);
</script>';
} else {
// When javascript is turned off there is no input field validation //
if($name == "" || $email == "" || $username == "" || $showpass == "") {

// Message when $name, $email, $username or $showpass are empty //
echo JText::_("SPAMBOT");

// Check if the group ID of the user which is added is not bigger or equal to the groupid of the user who is adding the new user.
} else if (($hiddenusertype == "1")&&($group < "9")&&($group != "") || ($parentgrouporgroup >= $groupid)) {echo JText::_("ERR_GROUP");
// END - Check if the group ID of the user which is added is not bigger or equal to the groupid of the user who is adding the new user.

} else {
if($groupid > 2) { // If at least an author

// Some data for the query
$block = '0';
$sendmail = '0';

//$db =& JFactory::getDBO();
//$query = $db->getQuery(true);
$next_increment = 0;
$qShowStatus = "SHOW TABLE STATUS LIKE 'ver_users'";
$qShowStatusResult = mysql_query($qShowStatus) or die ( "Query failed: " . mysql_error() . "<br/>" . $qShowStatus );
$row = mysql_fetch_assoc($qShowStatusResult);
$next_increment = 'Rep'.$row['Auto_increment'];

// Insert record into users
$sql1 = "INSERT INTO ".$db->nameQuote('#__users')." SET
".$db->nameQuote('name')."            = ".$db->quote($name).",
".$db->nameQuote('username')."        = ".$db->quote($username).",
".$db->nameQuote('email')."           = ".$db->quote($email).",
".$db->nameQuote('password')."        = ".$db->quote($password).",
".$db->nameQuote('usertype')."        = ".$db->quote($usertypename).",
".$db->nameQuote('block')."           = ".$db->quote($block).",
".$db->nameQuote('sendEmail')."       = ".$db->quote($sendmail).",
".$db->nameQuote('RepID')."           = ".$db->quote($next_increment).",
".$db->nameQuote('registerDate')."    = NOW(),
".$db->nameQuote('lastvisitDate')."   = ".$db->quote('0000-00-00 00:00:00').",
".$db->nameQuote('activation')."      = '',
".$db->nameQuote('params')."          = ''
";
$db->setQuery($sql1);
$db->query();

// Get back user's ID
$user_id = $db->insertid();


// Insert record into #__user_usergroup_map
$sql2 = "INSERT INTO ".$db->nameQuote('#__user_usergroup_map')." SET
".$db->nameQuote('group_id')."        = ".$db->quote($usertype).",
".$db->nameQuote('user_id')."         = ".$db->quote($user_id)."
";
$db->setQuery($sql2);
$db->query();


if(!isset($lastname)) { // Initialize variable
$lastname = "";
}

// Insert record into Community Builder
if($operationmode == 1){
$sql3 = "INSERT INTO ".$db->nameQuote('#__comprofiler')." SET 
".$db->nameQuote('id')."                  = ".$db->quote($user_id).",
".$db->nameQuote('user_id')."             = ".$db->quote($user_id).",
".$db->nameQuote('firstname')."           = ".$db->quote($firstname).",
".$db->nameQuote('lastname')."            = ".$db->quote($lastname).",
".$db->nameQuote('hits')."                = ".$db->quote('0').",
".$db->nameQuote('message_last_sent')."   = ".$db->quote('0000-00-00 00:00:00').",
".$db->nameQuote('message_number_sent')." = ".$db->quote('0').",
".$db->nameQuote('approved')."            = ".$db->quote('1').",
".$db->nameQuote('confirmed')."           = ".$db->quote('1').",
".$db->nameQuote('lastupdatedate')."      = ".$db->quote('0000-00-00 00:00:00').",
".$db->nameQuote('banned')."              = ".$db->quote('0').",
".$db->nameQuote('acceptedterms')."       = ".$db->quote('1')."
";
$db->setQuery($sql3);
$db->query();
} // End - CB mode or not

// Get userdata for export (Is used for additional plugins and the onAfterStoreUser function in joomla)
$userdataexport = array (
"username" => "$username",
"email" => "$email",
"name" => "$name",
"password" => "$password",
"id" => "$user_id",
"group" => "$group",
);

// Fire the onAfterStoreUser trigger
JPluginHelper::importPlugin('user');
$dispatcher =& JDispatcher::getInstance();
$dispatcher->trigger('onUserAfterSave', array($userdataexport, true, true, $this->getError()));

// Start executing additional plugins
// Fire the onAfterStoreUserAuftoK2 function for K2 synchronization
$dispatcher->trigger('onAfterStoreUserAuftoK2', array($userdataexport, true, true, $this->getError()));
// End executing plugins

// Flush

flush();
// Show message to user if CB mode is ON
if($operationmode == 1){
if($id=10) {
echo "Hello Victoria";
//header('Location:'.JURI::base().'system-management-vic/rep-listing-vic');
}

elseif($id=11) {
echo "Hello Admin";
//header('Location:'.JURI::base().'member-list');

}
//echo '<br /><br /><strong>' . JText::_("ADDEDUSERTOJOOMLACB") . '!</strong><br><a href="index.php?option=com_comprofiler&task=userDetails&uid=' . $user_id . '"><strong>' . $username . '</strong></a> ' . JText::_("ADDEDUSERTOJOOMLACBTXT") . '';
}

// Show message to user if CB mode is OFF
if($operationmode == 0){
echo '<br /><br /><strong>' . JText::_("ADDEDUSERTOJOOMLA") . '</strong><br><strong>' . $username . '</strong> ' . JText::_("HASBEENADDEDTOJOOMLA") . '';
}

// Send notification email to added user
if($notificationemail == "1"){
$config =& JFactory::getConfig();
$fromname = $config->getValue( 'config.fromname' );
$from = $config->getValue( 'config.mailfrom' );
$recipient = $email;
$subject = "".JText::_("YOURDETAILFOR")." ".$_SERVER['HTTP_HOST']."";
$body   = "".JText::_("YOUHAVEBEENADDED")." http://".$_SERVER['HTTP_HOST']."<br>".JText::_("THISMAILCONT")." http://".$_SERVER['HTTP_HOST']."<br>".JText::_("USERNAME").": ".$username."<br>".JText::_("PASSWORD").": ".$showpass."<br>".JText::_("DONOTRESPOND")."
";
// Send notification email now!
JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode=1, $cc=null, $bcc=null, $attachment=null, $replyto=null, $replytoname=null);
} 

// Send notification email to admin
if($adminnotificationemail == "1"){
$config =& JFactory::getConfig();
$fromname = $config->getValue( 'config.fromname' );
$from = $config->getValue( 'config.mailfrom' );
$recipient = $from;
$subject = "A new user has been added to ".$_SERVER['HTTP_HOST']."";
$body   = "A new user has been added to ".$_SERVER['HTTP_HOST'].". This is a copy off the emailnotification that this user received:<br>".JText::_("YOUHAVEBEENADDED")." http://".$_SERVER['HTTP_HOST']."<br>".JText::_("THISMAILCONT")." http://".$_SERVER['HTTP_HOST']."<br>".JText::_("USERNAME").": ".$username."<br>".JText::_("PASSWORD").": xxx (hidden)<br>".JText::_("DONOTRESPOND")."
";
 
// Send notification email now!
JUtility::sendMail($from, $fromname, $recipient, $subject, $body, $mode=1, $cc=null, $bcc=null, $attachment, $replyto=null, $replytoname=null);
} 

} else {  // End at least an author
echo 'You are not authorised to view this resource. Because you are a registered user, you must be an author at least!';
} // End at least an author
} // End if-else security check -no input field 
} // End if-else double username check
} // End Check if email does exist
} else {
	
if($groupid > 2) { // If at least an author
	
// Show upload form

echo '<script type="text/javascript">  

function validate_required(field,alerttxt)
{ 
with (field)
  {
  if (value==null||value=="")
  {
  alert(alerttxt);return false;
   }
  else
   {
   return true;
   }
  }
}

function is_valid_email(email,alerttxt) {
   var reg = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-]{2,})+\.)+([a-zA-Z0-9]{2,})+$/;
   var address = email.value;
   if(!reg.test(address)) {
      alert(alerttxt);return false;
   } else {
    return true;
   }
}

function validate_form(thisform)
{
with (thisform)
 {'; 
 
if( $namemode == "1" ) { 
echo 'if (validate_required(firstname,"'. JText::_( 'N0_FIRSTNAME').'")==false) {
firstname.focus();return false;}	
if (validate_required(lastname,"'.JText::_( 'N0_LASTNAME').'")==false) {
lastname.focus();return false;}';
} else {
echo 'if (validate_required(name,"'.JText::_("N0_NAME").'")==false) {
name.focus();return false;}';
}	

if( $genericemail !== "1" ) {	
echo 'if (validate_required(email,"'.JText::_( 'N0_EMAIL').'")==false) {
email.focus();return false;}';
}	
if( $genericemail !== "1" ) {	
echo 'if (is_valid_email(email,"'.JText::_( 'NO_VALID_EMAIL').'")==false) {
email.focus();return false;}';
} 
if( $usernamemode == "1" ) {
echo 'if (validate_required(username,"'.JText::_( 'N0_USERNAME').'")==false) {
username.focus();return false;}';
}	
if( $passwordmode == "1" ) {
echo'if (validate_required(password,"'.JText::_( 'N0_PASSWORD').'")==false) {
group.focus();return false;}';
}	
if( $usertypemode == "1" ) {
echo'if (validate_required(group,"'.JText::_( 'NO_GROUP').'")==false) {	
group.focus();return false;}';
}		
echo '}
}
</script>';
echo '<div>
<h1>'.JText::_( 'ADD_USER').':</h1>
<form class="adduser" onsubmit="return validate_form(this);"  action="'.JRoute::_('index.php?option=com_adduserfrontend&Itemid='.$itemid).'" method="post" enctype="multipart/form-data">
<input type="hidden" name="import" value="1" />

<table cellpadding="4px">';

// Getting data from cookies if used	 
if(isset($_COOKIE['firstname'])) {
$savedfirstname = $_COOKIE['firstname'];
} else {	
$savedfirstname ="";
}
if(isset($_COOKIE['lastname'])) {
$savedlastname = $_COOKIE['lastname'];
} else {	
$savedlastname ="";
}
if(isset($_COOKIE['name'])) {
$savedname = $_COOKIE['name'];
} else {	
$savedname ="";
}
if(isset($_COOKIE['email'])) {
$savedemail = $_COOKIE['email'];
} else {	
$savedemail ="";
}
if(isset($_COOKIE['username'])) {
$savedusername = $_COOKIE['username'];
} else {	
$savedusername ="";
}
if(isset($_COOKIE['group'])) {
$savedgroup = $_COOKIE['group'];
} else {	
$savedgroup = "";
}
if(isset($_COOKIE['showpass'])) {
$savedshowpass = $_COOKIE['showpass'];
} else {	
$savedshowpass ="";
}

// Show form inputfields according to params
// Read from database all joomla's groups and print result in a selectbox
if($usertypemode == '1'){
if($hiddenusertype == '1'){

// Initialize strings
$allowedcustomusergroups = "";
$sqladdition = "";

// Select usergroups where gid is bigger or equal too 9 (ONLY custum usergroups)
$query = "SELECT id,title,parent_id FROM #__usergroups WHERE id >= 9 order by title";
$db->setQuery($query);
$result = $db->loadRowList();

// Get the parentids of each custom usergroup
foreach ($result as $acustomusergroup) { // For each custom usergroup

// Get first parent_id of the custom usergroup
$tparentofcustomusergroup = get_parent_id($acustomusergroup[0]);

// If the resulting parent_id is also a custom usergroup
while($tparentofcustomusergroup > 8) { // Loop to get parent_id untill we find a parent id of the standard joomla usergroups
$tparentofcustomusergroup = get_parent_id($tparentofcustomusergroup);
}

// If the parent_id which is now always a custom usergroup is smaller then the group ID of the user
if($tparentofcustomusergroup < $groupid) {
$allowedcustomusergroups .= $acustomusergroup[0].","; // Make comma seperated string out of results
}

} 

// Create SQL query
$allowedcustomusergroups = substr($allowedcustomusergroups, 0, -1); // Delete not needed comma's
$allowedcustomusergroupsarray = explode(",", $allowedcustomusergroups); // Explode comma seperated string to array

foreach($allowedcustomusergroupsarray as $allowedcustomusergroup) {
$sqladdition .= "OR id = '$allowedcustomusergroup' "; 
}

// Do a new query to get the final results for the custom usergroups
$query = "SELECT id,title,parent_id FROM #__usergroups WHERE id = 'XXX' $sqladdition order by title";
$db->setQuery($query);
$result = $db->loadRowList();

} else { // Hidden usertype is not 1

// Initialize strings
$allowedcustomusergroups = "";
$sqladdition = "";

// Select usergroups where gid is smaller then the users own gid (ignoring the custum usergroups)
$query = "SELECT id,title,parent_id FROM #__usergroups WHERE id < $groupid AND id != '1' order by title";	
$db->setQuery($query);
$result = $db->loadRowList();

// Select usergroups where gid is bigger or equal too 9 (ONLY custum usergroups)
$query2 = "SELECT id,title,parent_id FROM #__usergroups WHERE id >= 9 order by title";
$db->setQuery($query2);
$result2 = $db->loadRowList();

// Get the parentids of each custom usergroup
foreach ($result2 as $acustomusergroup) { // For each custom usergroup

// Get first parent_id of the custom usergroup
$tparentofcustomusergroup = get_parent_id($acustomusergroup[0]);

// If the resulting parent_id is also a custom usergroup
while($tparentofcustomusergroup > 8) { // Loop to get parent_id untill we find a parent_id of the standard joomla usergroups
$tparentofcustomusergroup = get_parent_id($tparentofcustomusergroup);
}

// If the parent_id which is now always a custom usergroup is smaller then the group ID of the user
if($tparentofcustomusergroup < $groupid) {
$allowedcustomusergroups .= $acustomusergroup[0].","; // Make comma seperated string out of results 
}

} 

// Create SQL query
$allowedcustomusergroups = substr($allowedcustomusergroups, 0, -1); // Delete not needed comma's
$allowedcustomusergroupsarray = explode(",", $allowedcustomusergroups); // Explode comma seperated string to array

foreach($allowedcustomusergroupsarray as $allowedcustomusergroup) {
$sqladdition .= "OR id = '$allowedcustomusergroup' "; 
}

// Do a new query to get the final results for the custom usergroups
$query2 = "SELECT id,title,parent_id FROM #__usergroups WHERE id = 'XXX' $sqladdition order by title";
$db->setQuery($query2);
$result2 = $db->loadRowList();

// Merge the 2 arrays. Array 1 is the list of allowed 'normal' usergroups and array 2 is the list of allowed custum usergroups
$result = array_merge($result,$result2); // All allowed usergroups
sort($result); // Sort all groups numeric
}

// Echo the selectbox
echo '<tr>
<td>'.JText::_( 'GROUP').':</td>	
<td><select name="group" type="text" value="'.$savedgroup.'">
<option value=""> - - '.JText::_("SELECTGROUP").' - - </option>
';
foreach ($result as $line) {echo '<option value='.$line[0].'>'.$line[1].'</option>';}
echo '</select></td>
</tr>';
}

// Namemode
if( $namemode == "1" ) {
echo'<tr>
<td>'.JText::_( 'FIRSTNAME').':</td>
<td><input type="text" name="firstname" value="'.$savedfirstname.'" /></td>
</tr>
<tr>
<td>'.JText::_( 'LASTNAME').':</td>
<td><input type="text" name="lastname" value="'.$savedlastname.'" /></td>
         </tr>';
} else {
echo '<tr>
<td width="130">'.JText::_( 'NAME').':</td>
<td><input type="text" name="name" value="'.$savedname.'" /></td>
</tr>';
}
if( $genericemail !== "1" ) {
echo '<tr>
<td>'.JText::_( 'EMAIL').':</td>
<td><input type="text" name="email" value="'.$savedemail.'" /></td>
</tr>';
}
if( $usernamemode == "1" ) {
echo '<tr>
<td>'.JText::_( 'USERNAME').':</td>
<td><input type="text" name="username" value="'.$savedusername.'" /></td>
</tr>';
}
if( $passwordmode == "1" ) {
echo '<tr>
<td>'.JText::_( 'PASSWORD').':</td>
<td><input type="text" name="password" value="'.$savedshowpass.'" /></td>
</tr>';
}
   
echo '<tr>
<td></td>
<td><input class="addbtn" type="submit" name="submit" value="'.JText::_( 'ADDNOW').'" /></td>
</tr>
</table>
</form>
</div>';
} else {
echo 'You are not authorised to view this resource. Because you are a registered user, you must be an author at least!';
}
}
?>