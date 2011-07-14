<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $this->headtitle; ?></title>
  <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />
  <link rel="stylesheet" href="/_user/css/common.css" type="text/css" media="all" />
  <?php print $this->headStylesheet; ?>
</head>
<body>

<div id="bodywrap"><div id="bodycontent">

<?php
$this->pagelet("header", null, 'w');
?>

<div class="clearhr"></div>

<table class="wrapper clearboth">
  <tr>

    <td valign="top">
    <?php print $this->content; ?>
    </td>

    <?php if ($this->sidebar !== NULL) { ?>
    <td width="20px"></td>
    <td width="300px" valign="top">
    <?php print $this->sidebar; ?>
    </td>
    <?php } ?>

  </tr>
</table>

</div></div>

<?php
$this->pagelet("footer", null, 'w');
?>

</body>
</html>
