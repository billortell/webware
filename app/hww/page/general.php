<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $this->headtitle; ?></title>
  <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/_w/css/common.css" type="text/css" media="all" />
  <?php echo $this->headStylesheet."\n".$this->headJavascript; ?>
</head>
<body>

<div id="hww_header">
  <div><a href="#">WebWare</a></div>
</div>

<?php print $this->content; ?>


</body>
</html>
