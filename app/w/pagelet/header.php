<div class="bodytop"></div>

<div class="centerbox" id="header">

  <div id="logo">
    <a href="#"><img src="/_w/img/hooto-gray-30.png" /></a>
  </div>

  <div id="userbar">
    <div>
    <?php
    if (user_session::isLogin()) {
        echo "<b>[".user_session::getInstance()->uname."]</b> &nbsp;&nbsp;";
        echo "<a href=\"/user/manage/\">Account Settings</a> &nbsp;&nbsp;";
        echo "<a href=\"/user/logout/\">Logout</a>";
    } else if ($this->reqs->act != 'logout') {
        echo "<a href=\"/user/login?cburl={$this->reqs->url}\">Login / Register</a>";
    } else {
        echo "<a href=\"/user/login/\">Login / Register</a>";
    }
    ?>
    </div>
  </div>

  <div id="menu">
    <ul>
    <?php
    if (isset($this->reqs->uname)) {
        $uname  = $this->reqs->uname;
        $uid    = uname2uid($this->reqs->uname);
    } else {
        $uname  = user_session::getInstance()->uname;
        $uid    = user_session::getInstance()->uid;
    }
    $menus = user_menu::getList(4, $uid);
    foreach ($menus as $val) {
        $link = $this->siteurl($val['link'], $val['instance'], array(':uname' => $uname));
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

</div>

