<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->headtitle; ?></title>
    <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />
    <?php echo $this->headlink; ?>
</head>
<body>

<div id="bodywrap"><div id="bodycontent">

<?php
$this->pagelet("header");
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

<div class="centerbox" id="footer">
  <div class="box">
  
    <div class="sl">
      <p>Based on <b><a href="#">Hooto WebWare</a></b></p>
    </div>

    <div class="sr">
      <p id="htdebug"></p>
    </div>

  </div>
</div>

</body>
</html>
