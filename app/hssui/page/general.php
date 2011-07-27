<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $this->headtitle; ?></title>
    <link rel="stylesheet" href="/_w/css/global.css" type="text/css" media="all" />
    <link rel="stylesheet" href="/_hssui/css/common.css" type="text/css" media="all" />
    <?php echo $this->headStylesheet; ?>
</head>
<body>

<div id="bodywrap"><div id="bodycontent">

<?php
$session = user_session::getInstance();

if (isset($this->reqs->uname) || $session->uid != '0') {
    $this->pagelet("header_user", null, 'w');
} else {
    $this->pagelet("header", null, 'w');
}
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

    $uid = $session->uid;

    foreach ($menu as $key => $val) {
        
        if (isset($val['permission'])) {
        
            if (!user_session::isLogin()
                || !user_session::isAllow($this->reqs->ins, $val['permission'])) {
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

<div class="wrapper clearboth">
<?php
if ($this->sidebar !== NULL) { 
?>
<div class="mainbody-leftbox" style="width:680px;">
<?php print $this->content; ?>
</div>
<div class="mainbody-rightbox" style="width:300px;">
<?php print $this->sidebar;?>
</div>
<?php
} else {
    print $this->content;
}
?>
</div>

</div></div>

<div class="centerbox" id="footer">
  <div class="box">
  
    <div class="sl">
      <p>Based on <b><a href="https://github.com/eryx/webware" target="_blank">Hooto WebWare <?php echo HWW_VERSION;?></a></b></p>
    </div>

    <div class="sr">
      <p id="htdebug"></p>
    </div>

  </div>
</div>

</body>
</html>
