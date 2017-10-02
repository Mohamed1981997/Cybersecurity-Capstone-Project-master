<?php require_once('../Connections/Master.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "new_message")) {
	$crypt=base64_encode($_POST['texto']);
  $insertSQL = sprintf("INSERT INTO datos (id_sender, id_for, texto, subject) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['sender'], "int"),
                       GetSQLValueString($_POST['para'], "int"),
                       GetSQLValueString($crypt, "text"),
                       GetSQLValueString($_POST['asunto'], "text"));

  mysql_select_db($database_Master, $Master);
  $Result1 = mysql_query($insertSQL, $Master) or die(mysql_error());

  $insertGoTo = "principal.php?successSend";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_user = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_user = $_SESSION['MM_Username'];
}
mysql_select_db($database_Master, $Master);
$query_user = sprintf("SELECT * FROM usuarios WHERE Email = %s", GetSQLValueString($colname_user, "text"));
$user = mysql_query($query_user, $Master) or die(mysql_error());
$row_user = mysql_fetch_assoc($user);
$totalRows_user = mysql_num_rows($user);
$maybe=$row_user['iduser'];

mysql_select_db($database_Master, $Master);
$query_list = "SELECT iduser, FirstName, LastName, Email FROM usuarios";
$list = mysql_query($query_list, $Master) or die(mysql_error());
$row_list = mysql_fetch_assoc($list);
$totalRows_list = mysql_num_rows($list);

mysql_select_db($database_Master, $Master);
$query_inbox = "SELECT * FROM datos WHERE id_for = $row_user[iduser]";
$inbox = mysql_query($query_inbox, $Master) or die(mysql_error());
$row_inbox = mysql_fetch_assoc($inbox);
$totalRows_inbox = mysql_num_rows($inbox);

mysql_select_db($database_Master, $Master);
$query_sent = "SELECT * FROM datos WHERE id_sender = $row_user[iduser]";
$sent = mysql_query($query_sent, $Master) or die(mysql_error());
$row_sent = mysql_fetch_assoc($sent);
$totalRows_sent = mysql_num_rows($sent);



?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Cybersecurity Capstone Project</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/shop-item.css" rel="stylesheet">

  </head>

  <body>

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#">Cybersecurity Capstone Project</a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
       
        
      </ul>
    
      <ul class="nav navbar-nav navbar-right">
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
<font color="#990000">You are logged like:</font> <?php echo $row_user['FirstName']; ?> <?php echo $row_user['LastName']; ?><span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="#">Change Password</a></li>
            <li><a href="<?php echo $logoutAction ?>">LogOut</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

    <!-- Page Content -->
    <div class="container">

      <div class="row">

        <div class="col-lg-4">
          <h1 class="my-4">New Message</h1>
          <div class="panel panel-default">
          <form method="POST" action="<?php echo $editFormAction; ?>" class="form-horizontal" name="new_message">
  <div class="panel-body">
      <div class="form-group">
      <label for="select" class="col-lg-2 control-label">To:</label>
      <div class="col-lg-10">
       
          <select name="para" class="form-control" id="select">
          <option value="">Select...</option>
           <?php do { ?>
            <option value="<?php echo $row_list['iduser']; ?>"><?php echo $row_list['FirstName']; ?> <?php echo $row_list['LastName']; ?></option>
         <?php } while ($row_list = mysql_fetch_assoc($list)); ?>
          </select>
          
      </div>
    </div>
    <div class="form-group">
      <label for="inputEmail" class="col-lg-3 control-label">Subject:</label>
      <div class="col-lg-9">
        <input name="asunto" type="text" class="form-control" id="inputEmail" >
      </div>
    </div>
     <div class="form-group">
      <label for="textArea" class="col-lg-3 control-label">Text</label>
      <div class="col-lg-9">
        <textarea name="texto" rows="3" class="form-control" id="textArea"></textarea>
     
      </div>
    </div>
    <div class="form-group">
      <div class="col-lg-12">
      <input type="hidden" name="sender" value="<?php echo $maybe; ?>">
        <button type="submit" class="btn btn-default btn-lg btn-block">Send</button>
      </div>
    </div>
  </div>
  <input type="hidden" name="MM_insert" value="new_message">
          </form>
</div>
		<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">Extract Dump (Only for Testing)</h3>
  </div>
  <div class="panel-body">
    <strong>Main table</strong>
    <a href="maintable.php" class="btn btn-default btn-lg btn-block">Download</a>
    <hr>
    <strong>User Table</strong>
     <a href="usertable.php" class="btn btn-default btn-lg btn-block">Download</a>
  </div>
</div>

        </div>
        <!-- /.col-lg-3 -->

        <div class="col-lg-8">

          <div class="card mt-4">
            <img src="../cybersecurity_capstone2.jpg" width="800" height="400">
            
          </div>
         <?php if($totalRows_inbox>=1){  ?> 
         <div class="panel panel-danger">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Inbox</strong></h3>
          </div>
          <div class="panel-body">
            <div class="list-group">
              <?php do {mysql_select_db($database_Master, $Master);
$query_senderis = "SELECT * FROM usuarios WHERE iduser = $row_inbox[id_sender]";
$senderis = mysql_query($query_senderis, $Master) or die(mysql_error());
$row_senderis = mysql_fetch_assoc($senderis);
$totalRows_senderis = mysql_num_rows($senderis); ?>
              
              <a href="#" class="list-group-item">
              <h3 class="list-group-item-heading"><strong>Sender: </strong><?php echo $row_senderis['FirstName']; ?> <?php echo $row_senderis['LastName']; ?></h3>
              <h4 class="list-group-item-heading"><strong>Subject:</strong> <?php echo $row_inbox['subject']; ?></h4>
              <h5 class="list-group-item-heading"><strong>Time: </strong><?php echo $row_inbox['date']; ?></h5>
              <p class="list-group-item-text"><?php echo base64_decode($row_inbox['texto']); ?></p>
                     </a>
                <?php } while ($row_inbox = mysql_fetch_assoc($inbox)); ?>
            </div>
          </div>
        </div>
        <?php }else{ ?>
        <div class="alert alert-dismissible alert-danger">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Oh snap!</strong> <a href="#" class="alert-link">u dont have any message to read
</div>
<?php } ?>

 <?php if($totalRows_sent>=1){  ?> 
         <div class="panel panel-warning">
          <div class="panel-heading">
            <h3 class="panel-title"><strong>Send Messages</strong></h3>
          </div>
          <div class="panel-body">
            <div class="list-group">
    
              
              <?php do { 
			  mysql_select_db($database_Master, $Master);
$query_whosent = "SELECT * FROM usuarios WHERE iduser = $row_sent[id_for]";
$whosent = mysql_query($query_whosent, $Master) or die(mysql_error());
$row_whosent = mysql_fetch_assoc($whosent);
$totalRows_whosent = mysql_num_rows($whosent);
			  ?>
                <a href="#" class="list-group-item">
                  <h3 class="list-group-item-heading"><strong>To: </strong> <?php echo $row_whosent['FirstName']; ?> <?php echo $row_whosent['LastName']; ?></h3>
                  <h4 class="list-group-item-heading"><strong>Subject:</strong> <?php echo $row_sent['subject']; ?></h4>
                  <h5 class="list-group-item-heading"><strong>Time: </strong><?php echo $row_sent['date']; ?></h5>
                  <p class="list-group-item-text"><?php echo base64_decode($row_sent['texto']); ?></p>
              </a>
                <?php } while ($row_sent = mysql_fetch_assoc($sent)); ?>

            </div>
          </div>
        </div>
<?php }else{ ?>
        <div class="alert alert-dismissible alert-warning">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Oh snap!</strong> <a href="#" class="alert-link">u dont send any message still
<?php } ?>

        </div>
        <!-- /.col-lg-9 -->

      </div>
		
  </div>
    <!-- /.container -->

    <!-- Footer -->
    <footer class="py-5 bg-dark">
      <div class="container">
        <p class="m-0 text-center text-white">Cybersecurity Capstone Project &copy; Miguel Angel Zabala</p>
      </div>
      <!-- /.container -->
    </footer>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/popper/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

  </body>

</html>
<?php
mysql_free_result($user);

mysql_free_result($list);

mysql_free_result($inbox);

mysql_free_result($sent);

mysql_free_result($whosent);

mysql_free_result($senderis);
?>
