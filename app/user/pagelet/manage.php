<?php
$this->headtitle = "Account Settings";

Hooto_Web_View::headStylesheet('/_user/css/manage.css');

$session = user_session::getInstance();
           
if ($session->uid != "0") {

    $_user = Hooto_Data_Sql::getTable('user');
    $user = $_user->fetch($session->uid);
                
    $des = str_split($session->uid);            
    $path = '/data/user/'.$des['0'].'/'.$des['1'].'/'.$des['2'].'/'.$session->uid;
    
    if (!file_exists(SYS_ROOT.$path."/w100.png")) {
        $path = '/data/user';
    }
    
    $user['photo_path'] = $path;

} else {
    print w_msg::simple('error', 'Access Denied');
    return;
}

?>
<div class="usermanage">

<div class="profile">
  <h2>Profile</h2>
  <div class="info">
    <img src="<?php echo $user['photo_path']?>/w100.png" />
    <ul>
      <li>ID: <b><?php echo $session->uname?></b></li>
      <li>NAME: <b><?php echo $user['name']?></b></li>
      <li></li>
      <li><a href="/user/profile-edit/">Edit profile</a>  - <a class="light" href="/user/profile/<?php echo $session->uname?>" target="_blank">Preview</a></li>
      <li><a href="/user/photo-edit/">Change photo</a></li>
    </ul>
  </div>
</div>

<div class="personal">
  <h2>Personal Settings</h2>
  <table> 
    <tr> 
      <td class="headings">Security</td> 
      <td> 
        <ul> 
          <li><a href="/user/pass-edit/">Change password</a></li>
        </ul>
      </td> 
    </tr> 
    <tr> 
      <td class="headings">Email addresses</td> 
      <td>
        <ul> 
          <li><?php echo $user['email']?></li> 
          <li><a href="/user/email-edit/">Edit</a></li> 
        </ul>
      </td>
    </tr> 
    <tr> 
      <td class="headings">Main Menu</td> 
      <td>
        <ul> 
          <li><a href="/user/menu?type=4">Settings</a></li> 
        </ul>
      </td>
    </tr>
  </table> 
</div>

<?php

$_apps = Hooto_Data_Sql::getTable('user_apps');
$q = $_apps->select()->where('uid = ?', $session->uid)->limit(100);
$apps = $_apps->query($q);


$patt = SYS_ROOT.'conf/'.SITE_NAME.'/*';
    
$myProducts = "";
$tryProducts = "";

$instances = array();

foreach (glob($patt, GLOB_ONLYDIR) as $st) {

    if (file_exists($st."/global.php")) {
    
        $cfg = require $st."/global.php";
        
        if (!in_array(10, $cfg['type'])) {
            continue;
        }
        
        $instances[$cfg['instance']] = $cfg;
    }
    
}

foreach ($apps as $val) {
    
    if ($val['status'] == 1 && isset($instances[$val['instance']])) {
    
        $ins = $instances[$val['instance']];

        $oper = '';
        if (file_exists(SYS_ROOT."app/{$ins['appid']}/permission.php")) {
            $oper .= ' - ';
            //$oper .= "<a href='#' class='light'>Settings</a>&nbsp;";
            $oper .= "<a href='/user/appsetup?instance={$ins['instance']}&status=0' class='light'>Disable</a>&nbsp;";
        }
        $appurl = $this->siteurl('', $val['instance'], array(':uname' => $session->uname));
        
        $myProducts .= "<li>";
        $myProducts .= "<img src='/_default/img/application.png' />";
        $myProducts .= "<span>";
        $myProducts .= "<a href='{$appurl}' target='_blank'>{$val['title']}</a>";
        $myProducts .= $oper;
        $myProducts .= "</span>";
        $myProducts .= "</li>";
        
        unset($instances[$val['instance']]);
    }

}

foreach ($instances as $instance => $ins) {

    $tryProducts .= "<li>";
    $tryProducts .= "<img src='/_default/img/application.png' />";
    $tryProducts .= "<span>";
    $tryProducts .= "<b>{$ins['name']}</b> - ";
    $tryProducts .= "<a href='/user/appsetup?instance={$instance}' class='light'>Install</a>&nbsp;";
    $tryProducts .= "</span>";
    $tryProducts .= "</li>";
}

if (strlen($myProducts) > 1) {
    echo "<div class='products'>
  <h2>My products</h2>
  <ul>{$myProducts}</ul> 
</div>";
}

if (strlen($tryProducts) > 1) {
    echo "
<div class='products'>
  <h2>Try something new</h2>
  <ul>{$tryProducts}</ul> 
</div>";
}
?>
</div>
