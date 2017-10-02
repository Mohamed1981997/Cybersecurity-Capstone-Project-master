<?php require_once('../Connections/Master.php'); ?>
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

mysql_select_db($database_Master, $Master);
$query_total = "SELECT * FROM datos";
$total = mysql_query($query_total, $Master) or die(mysql_error());
$row_total = mysql_fetch_assoc($total);
$totalRows_total = mysql_num_rows($total);


?>

<table border="1">
  <tr>
    <td>iddata</td>
    <td>id_sender</td>
    <td>id_for</td>
    <td>texto</td>
    <td>subject</td>
    <td>date</td>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_total['iddata']; ?></td>
      <td><?php echo $row_total['id_sender']; ?></td>
      <td><?php echo $row_total['id_for']; ?></td>
      <td><?php echo $row_total['texto']; ?></td>
      <td><?php echo $row_total['subject']; ?></td>
      <td><?php echo $row_total['date']; ?></td>
    </tr>
    <?php } while ($row_total = mysql_fetch_assoc($total)); ?>
</table>
<?php
mysql_free_result($total);
?>