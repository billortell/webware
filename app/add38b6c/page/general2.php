<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->headtitle; ?></title>
    <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />
    <?php echo $this->headStylesheet; ?>
</head>
<body>

<div id="bodywrap"><div id="bodycontent">

<?php
$this->pagelet("header", null, 'w');
?>

<div id="instance-menu" class="wrapper clearboth">
  <ul>
    <?php
    $conf = Hooto_Config_Array::get($this->reqs->ins.'/global');

    if (isset($conf['menu'])) {
        $menu = $conf['menu'];
    } else {
        $menu = array();
    }

    $uid = uname2uid($this->reqs->uname);
    foreach ($menu as $key => $val) {
        
        if (isset($val['permission'])) {
        
            if (!user_session::isLogin($uid) 
                || !user_session::isAllow($this->reqs->ins, 'entry.edit')) {
                continue;
            }
        
        }
        
        $link = $this->siteurl('/'.$key, $this->reqs->ins);
        
        if ($this->reqs->act == $key) {
            echo "<li><a href=\"{$link}\" class=\"current\">{$val['title']}</a></li>";
        } else {
            echo "<li><a href=\"{$link}\">{$val['title']}</a></li>";
        }
    }
    ?>
  </ul>
</div>

<table class="wrapper clearboth">
  <tr>
    <?php if ($this->sidebar !== NULL) { ?>
    <td width="240px" valign="top"><?php print $this->sidebar;?></td>
    <td width="20px"></td>
    <?php } ?>
    <td valign="top"><?php print $this->content; ?></td>
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
