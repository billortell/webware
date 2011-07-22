<?php
$msg = '';

if (isset($this->reqs->uname)) {
    $uname  = $this->reqs->uname;
    $uid    = uname2uid($this->reqs->uname);
} else {
    $uname  = user_session::getInstance()->uname;
    $uid    = user_session::getInstance()->uid;
}

$is = new Hooto_Object();;
try {
    $is = user_profile::fetch($uname);
    $is = new Hooto_Object($is);
} catch (Exception $e) {
    $msg = w_msg::simple("error", "Page Not Found");
}

$this->headtitle = $is->sitename;

?>
<div class="bodytop"></div>

<div class="centerbox" id="header">

  <div id="logo">
    <?php
    if (strlen($is->sitelogo) > 1) {
        echo "<a href='#'><img src='{$is->sitelogo}' /></a>";
    } else {
        echo "<a href='#'>{$is->sitename}</a>";
    }
    ?>
  </div>

  <div id="userbar">
    <div>
    <?php
    $cfg = Hooto_Config_Array::get('global');
    $siteurl_user = $cfg['siteurl_user'];
    if (user_session::isLogin()) {
        echo "<b>[".user_session::getInstance()->uname."]</b> &nbsp;&nbsp;";
        echo "<a href=\"{$siteurl_user}/user/manage/\">Account Settings</a> &nbsp;&nbsp;";
        echo "<a href=\"{$siteurl_user}/user/logout/\">Logout</a>";
    } else if ($this->reqs->act != 'logout') {
        echo "<a href=\"{$siteurl_user}/user/login?cburl={$this->reqs->url}\">Login / Register</a>";
    } else {
        echo "<a href=\"{$siteurl_user}/user/login/\">Login / Register</a>";
    }
    ?>
    </div>
  </div>

  <div id="menu">
    <ul>
    <?php
    $menus = user_menu::getList(4, $uid);
    foreach ($menus as $val) {
        if (strlen($val['permission']) 
            && !user_session::isAllow($val['instance'], $val['permission'])) {
            continue;
        }
        $link = $this->siteurl($val['link'], $val['instance'], array(':uname' => $uname), $is->url_personal);
        $class = "";
        if ($val['instance'] == $this->reqs->ins) {
            $class = "class=\"current\"";
        }
        echo "<li><a href=\"{$link}\" {$class}>{$val['title']}</a></li>";
    }
    ?>
    </ul>
    
    <ul class="sr">
      <li><a href="/user/profile/<?php echo $uname?>">About</a></li>      
    </ul>

  </div>
  
  <div id="submenu">

  </div>

  <?=$msg?>
  
</div>

