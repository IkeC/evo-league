<?php
$page = "index";
$subpage = "sixstatistics";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

$left = $subNavText.getRaquo().getSubNavigation($subpage, null);

$days = 30;
if (isset($_POST['days'])) {
  $days = mysql_real_escape_string($_POST['days']);
} elseif (isset($_GET['days'])) {
  $days = mysql_real_escape_string($_GET['days']);
}

?>

<?= getOuterBoxTop($left, "") ?>
<table>
  <tr>
    <td width="900">
      <form method="post" name="formDays" action="<?= $_SERVER['PHP_SELF'] ?>">
      <select class="width50" name="days">
      <?= getOption(7, $days); ?>
      <?= getOption(14, $days); ?>
      <?= getOption(30, $days); ?>
      <?= getOption(60, $days); ?>
      <?= getOption(180, $days); ?>
      <?= getOption(365, $days); ?>
     </select>
     &nbsp;Days&nbsp;&nbsp;
     <input type="Submit" class="width100" name="submit2" value="Show" />
     </form>
    </td>
  </tr>
  <tr><td></td></tr>
  <tr>
    <td>
      <img width="800" height="200" src="chart.php?type=1&amp;days=<?= $days ?>" border="1">
    </td>
  </tr>
  <tr>
    <td>
      <img width="800" height="200" src="chart.php?type=2&amp;days=<?= $days ?>" border="1">
    </td>
  </tr>
  <tr>
    <td>
      <img width="800" height="300" src="chart.php?type=4&amp;days=<?= $days ?>" border="1">
    </td>
  </tr>
  <tr>
    <td>
      <img width="800" height="300" src="chart.php?type=5&amp;days=<?= $days ?>" border="1">
    </td>
  </tr>
  <tr>
    <td>
      <img width="800" height="200" src="chart.php?type=3&amp;days=<?= $days ?>" border="1">
    </td>
  </tr>
</table>
<?= getOuterBoxBottom() ?>
<?php
	require ('../bottom.php');

  function getOption($val, $days) {
    $selected = "";
    if ($val == $days) {
      $selected = 'selected="selected"';
    }
    $result .= '<option '.$selected.'>'.$val.'</option>';
    return $result;
  }
  ?>

