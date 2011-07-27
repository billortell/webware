<?php
$msg = '';

//$this->headtitle = $is->sitename;

?>
<div class="bodytop"></div>

<div class="centerbox" id="header">

  <div id="logo">
    <?php
    echo "<a href='/'><img src='/_w/img/hooto-gray-30.png' /></a>";
    ?>
  </div>

  <div id="userbar">
    <div>
    <?php
    $cfg = Hooto_Config_Array::get('global');
    $siteurl_user = $cfg['siteurl_user'];
    $session = user_session::getInstance();
    if (user_session::isLogin()) {
        echo "<b>[".user_session::getInstance()->uname."]</b> &nbsp;&nbsp;";
        echo "<a href=\"".$this->siteurl('', 'blog', array(':uname' => user_session::getInstance()->uname))."\">iSite</a>  &nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<a href=\"{$siteurl_user}/user/manage/\">Account</a> &nbsp;&nbsp;&nbsp;&nbsp;";
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
    $menus = user_menu::getList(5);

    foreach ($menus as $val) {

        $link = $this->siteurl($val['link'], $val['instance']);
        $class = "";
        if ($val['instance'] == $this->reqs->ins) {
            $class = "class=\"current\"";
        }
        echo "<li><a href=\"{$link}\" {$class}>{$val['title']}</a></li>";
    }
    ?>
    </ul>
    
    <ul class="sr">

    </ul>

  </div>
  
  <div id="submenu">

  </div>

  <?=$msg?>
  
</div>

