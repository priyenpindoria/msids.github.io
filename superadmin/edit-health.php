<?php require_once('../Connections/connMISDS.php'); ?>
<?php
// Load the common classes
require_once('../includes/common/KT_common.php');

// Load the tNG classes
require_once('../includes/tng/tNG.inc.php');

// Load the KT_back class
require_once('../includes/nxt/KT_back.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("../");

// Make unified connection variable
$conn_connMISDS = new KT_connection($connMISDS, $database_connMISDS);

// Start trigger
$formValidation = new tNG_FormValidation();
$formValidation->addField("subsector", true, "text", "", "", "", "");
$formValidation->addField("title", true, "text", "", "", "", "");
$formValidation->addField("description", true, "text", "", "", "", "");
$formValidation->addField("location", true, "text", "", "", "", "");
$formValidation->addField("date", true, "date", "date", "", "", "");
$tNGs->prepareValidation($formValidation);
// End trigger

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

mysql_select_db($database_connMISDS, $connMISDS);
$query_rsDropdown = "SELECT * FROM health_subsector";
$rsDropdown = mysql_query($query_rsDropdown, $connMISDS) or die(mysql_error());
$row_rsDropdown = mysql_fetch_assoc($rsDropdown);
$totalRows_rsDropdown = mysql_num_rows($rsDropdown);

// Make an insert transaction instance
$ins_health = new tNG_multipleInsert($conn_connMISDS);
$tNGs->addTransaction($ins_health);
// Register triggers
$ins_health->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_health->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_health->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$ins_health->setTable("health");
$ins_health->addColumn("subsector", "STRING_TYPE", "POST", "subsector");
$ins_health->addColumn("title", "STRING_TYPE", "POST", "title");
$ins_health->addColumn("description", "STRING_TYPE", "POST", "description");
$ins_health->addColumn("location", "STRING_TYPE", "POST", "location");
$ins_health->addColumn("date", "DATE_TYPE", "POST", "date");
$ins_health->setPrimaryKey("newsid", "NUMERIC_TYPE");

// Make an update transaction instance
$upd_health = new tNG_multipleUpdate($conn_connMISDS);
$tNGs->addTransaction($upd_health);
// Register triggers
$upd_health->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_health->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_health->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$upd_health->setTable("health");
$upd_health->addColumn("subsector", "STRING_TYPE", "POST", "subsector");
$upd_health->addColumn("title", "STRING_TYPE", "POST", "title");
$upd_health->addColumn("description", "STRING_TYPE", "POST", "description");
$upd_health->addColumn("location", "STRING_TYPE", "POST", "location");
$upd_health->addColumn("date", "DATE_TYPE", "POST", "date");
$upd_health->setPrimaryKey("newsid", "NUMERIC_TYPE", "GET", "newsid");

// Make an instance of the transaction object
$del_health = new tNG_multipleDelete($conn_connMISDS);
$tNGs->addTransaction($del_health);
// Register triggers
$del_health->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Delete1");
$del_health->registerTrigger("END", "Trigger_Default_Redirect", 99, "../includes/nxt/back.php");
// Add columns
$del_health->setTable("health");
$del_health->setPrimaryKey("newsid", "NUMERIC_TYPE", "GET", "newsid");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rshealth = $tNGs->getRecordset("health");
$row_rshealth = mysql_fetch_assoc($rshealth);
$totalRows_rshealth = mysql_num_rows($rshealth);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="../includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="../includes/common/js/base.js" type="text/javascript"></script>
<script src="../includes/common/js/utility.js" type="text/javascript"></script>
<script src="../includes/skins/style.js" type="text/javascript"></script>
<?php echo $tNGs->displayValidationRules();?>
<script src="../includes/nxt/scripts/form.js" type="text/javascript"></script>
<script src="../includes/nxt/scripts/form.js.php" type="text/javascript"></script>
<script type="text/javascript">
$NXT_FORM_SETTINGS = {
  duplicate_buttons: false,
  show_as_grid: false,
  merge_down_value: false
}
</script>
</head>
<style>
.header {
	font-family: Georgia, "Times New Roman", Times, serif;
	font-size: 18px;
	color: #00F;
	text-align: center;
	border-bottom-width: thin;
	border-bottom-style: solid;
	border-bottom-color: #7E80B4;
	margin-left: 10px;
	margin-right: 10px;
}
a:link {
	color: #000;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #000;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}

body {
	background-color: #DDDDDD;
	background-repeat: repeat-x;

}

h1 {
	text-align: center;

}

.KT_tngform {
	margin-left: 400px;
	border-radius: 50px;
	padding: 20px;
}

.KT_bottombuttons {
	text-align: center;

	}

</style>

<body>
<?php
	echo $tNGs->getErrorMsg();
?>
<div class="KT_tng">
  <h1>
    <?php 
// Show IF Conditional region1 
if (@$_GET['newsid'] == "") {
?>
      <?php echo NXT_getResource("Insert_FH"); ?>
      <?php 
// else Conditional region1
} else { ?>
      <?php echo NXT_getResource("Update_FH"); ?>
      <?php } 
// endif Conditional region1
?>
    Health </h1>
  <div class="KT_tngform">
    <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>">
      <?php $cnt1 = 0; ?>
      <?php do { ?>
        <?php $cnt1++; ?>
        <?php 
// Show IF Conditional region1 
if (@$totalRows_rshealth > 1) {
?>
          <h2><?php echo NXT_getResource("Record_FH"); ?> <?php echo $cnt1; ?></h2>
          <?php } 
// endif Conditional region1
?>
        <table cellpadding="2" cellspacing="0" class="KT_tngtable">
          <tr>
            <td width="146" class="KT_th"><label for="subsector_<?php echo $cnt1; ?>"><br />
            Subsector:</label></td>
            <td width="338"><p>
              <select name="subsector_<?php echo $cnt1; ?>" id="subsector_<?php echo $cnt1; ?>">
                <option value=""><?php echo NXT_getResource("Select one..."); ?></option>
                <?php 
do {  
?>
                <option value="<?php echo $row_rsDropdown['subsector']?>"<?php if (!(strcmp($row_rsDropdown['subsector'], $row_rshealth['subsector']))) {echo "SELECTED";} ?>><?php echo $row_rsDropdown['subsector']?></option>
                <?php
} while ($row_rsDropdown = mysql_fetch_assoc($rsDropdown));
  $rows = mysql_num_rows($rsDropdown);
  if($rows > 0) {
      mysql_data_seek($rsDropdown, 0);
	  $row_rsDropdown = mysql_fetch_assoc($rsDropdown);
  }
?>
              </select>
                <?php echo $tNGs->displayFieldError("health", "subsector", $cnt1); ?></p></td>
          </tr>
          <tr>
            <td class="KT_th"><label for="title_<?php echo $cnt1; ?>"><br />
            Title:</label></td>
            <td><p>
              <input type="text" name="title_<?php echo $cnt1; ?>" id="title_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rshealth['title']); ?>" size="32" maxlength="100" />
                <?php echo $tNGs->displayFieldHint("title");?> <?php echo $tNGs->displayFieldError("health", "title", $cnt1); ?></p></td>
          </tr>
          <tr>
            <td class="KT_th"><label for="description_<?php echo $cnt1; ?>"><br />
            Description:</label></td>
            <td><p>
              <textarea name="description_<?php echo $cnt1; ?>" id="description_<?php echo $cnt1; ?>" cols="50" rows="5"><?php echo KT_escapeAttribute($row_rshealth['description']); ?></textarea>
                <?php echo $tNGs->displayFieldHint("description");?> <?php echo $tNGs->displayFieldError("health", "description", $cnt1); ?></p></td>
          </tr>
          <tr>
            <td class="KT_th"><label for="location_<?php echo $cnt1; ?>"><br />
            Location:</label></td>
            <td><p>
              <input type="text" name="location_<?php echo $cnt1; ?>" id="location_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rshealth['location']); ?>" size="32" maxlength="100" />
                <?php echo $tNGs->displayFieldHint("location");?> <?php echo $tNGs->displayFieldError("health", "location", $cnt1); ?></p></td>
          </tr>
          <tr>
            <td class="KT_th"><label for="date_<?php echo $cnt1; ?>"><br />
            Date:</label></td>
            <td><p>
              <input type="text" name="date_<?php echo $cnt1; ?>" id="date_<?php echo $cnt1; ?>" value="<?php echo KT_formatDate($row_rshealth['date']); ?>" size="10" maxlength="22" />
                <?php echo $tNGs->displayFieldHint("date");?> <?php echo $tNGs->displayFieldError("health", "date", $cnt1); ?></p></td>
          </tr>
        </table>
        <input type="hidden" name="kt_pk_health_<?php echo $cnt1; ?>" class="id_field" value="<?php echo KT_escapeAttribute($row_rshealth['kt_pk_health']); ?>" />
        <?php } while ($row_rshealth = mysql_fetch_assoc($rshealth)); ?>
      <div class="KT_bottombuttons">
        <div>
          <?php 
      // Show IF Conditional region1
      if (@$_GET['newsid'] == "") {
      ?>
            <input type="submit" name="KT_Insert1" id="KT_Insert1" value="<?php echo NXT_getResource("Insert_FB"); ?>" />
            <?php 
      // else Conditional region1
      } else { ?>
            <input type="submit" name="KT_Update1" value="<?php echo NXT_getResource("Update_FB"); ?>" />
            <input type="submit" name="KT_Delete1" value="<?php echo NXT_getResource("Delete_FB"); ?>" onclick="return confirm('<?php echo NXT_getResource("Are you sure?"); ?>');" />
            <?php }
      // endif Conditional region1
      ?>
          <input type="button" name="KT_Cancel1" value="<?php echo NXT_getResource("Cancel_FB"); ?>" onclick="return UNI_navigateCancel(event, '../includes/nxt/back.php')" />
        </div>
      </div>
    </form>
  </div>
  <br class="clearfixplain" />
</div>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsDropdown);
?>
