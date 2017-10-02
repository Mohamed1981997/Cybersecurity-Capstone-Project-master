<?php require_once('Connections/Master.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "Register")) {
	$contra=md5($_POST['Password']);
  $insertSQL = sprintf("INSERT INTO usuarios (FirstName, LastName, Email, Password) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['FirstName'], "text"),
                       GetSQLValueString($_POST['LastName'], "text"),
                       GetSQLValueString($_POST['Email'], "text"),
                       GetSQLValueString($contra, "text"));

  mysql_select_db($database_Master, $Master);
  $Result1 = mysql_query($insertSQL, $Master) or die(mysql_error());

  $insertGoTo = "index.php?successRegister";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['Email'])) {
  $loginUsername=$_POST['Email'];
  $password=$_POST['Password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "Archive/principal.php";
  $MM_redirectLoginFailed = "index.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_Master, $Master);
  $passencry=md5($password);
  $LoginRS__query=sprintf("SELECT Email, Password FROM usuarios WHERE Email=%s AND Password=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($passencry, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $Master) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	      

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Cybersecurity Capstone Project</title>
  <link href='https://fonts.googleapis.com/css?family=Titillium+Web:400,300,600' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

  
      <link rel="stylesheet" href="css/style.css">

  
</head>

<body>
  
  <div class="form">
  
    <div align="center">
  <img src="cybersecurity_capstone2.jpg" width="520" height="280">
  </div>
  

    <ul class="tab-group">
        <li class="tab active"><a href="#signup">Sign Up</a></li>
        <li class="tab"><a href="#login">Log In</a></li>
    </ul>
      
      <div class="tab-content">
        <div id="signup">   
          <h1>Sign Up</h1>
          
          <form action="<?php echo $editFormAction; ?>" name="Register" method="POST">
          
          <div class="top-row">
            <div class="field-wrap">
              <label>
                First Name
              </label>
              <input type="text" name="FirstName" required autocomplete="off" />
            </div>
        
            <div class="field-wrap">
              <label>
                Last Name
              </label>
              <input type="text"required name="LastName" autocomplete="off"/>
            </div>
          </div>

          <div class="field-wrap">
            <label>
              Email Address
            </label>
            <input type="email"required name="Email" autocomplete="off"/>
          </div>
          
          <div class="field-wrap">
            <label>
              Set A Password
            </label>
            <input type="password"required name="Password" pattern="(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" title="UpperCase, LowerCase, Number/SpecialChar and min 8 Chars" autocomplete="off"/>
          </div>
          <button class="button button-block"/>
Get Started
<input type="hidden" name="MM_insert" value="Register">
          </button>
          
          </form>

        </div>
        
        <div id="login">   
          <h1>Welcome Back!</h1>
          
          <form name="login" action="<?php echo $loginFormAction; ?>" method="POST">
          
            <div class="field-wrap">
            <label>
              Email Address
            </label>
            <input name="Email" type="email"required autocomplete="off"/>
          </div>
          
          <div class="field-wrap">
            <label>
              Password
            </label>
            <input name="Password" type="password"required autocomplete="off"/>
          </div>
          
          
          
          <button class="button button-block"/>LogIn</button>
          
          </form>

        </div>
        
      </div><!-- tab-content -->
      
</div> <!-- /form -->
  <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script  src="js/index.js"></script>

</body>
</html>
