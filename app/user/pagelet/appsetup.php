<?php
$this->headtitle = "Product Setup";
Hooto_Web_View::headStylesheet('/_user/css/manage.css');

$msg = null;

if (!isset($this->reqs->instance)) {
    print w_msg::simple('error', 'Access Denied');
    return;
}
$instance = $this->reqs->instance;

if (!file_exists(SYS_ROOT."conf/".SITE_NAME."/{$this->reqs->instance}/global.php")) {
    print w_msg::simple('error', 'Access Denied');
    return;
}
$cins = require SYS_ROOT."conf/".SITE_NAME."/{$this->reqs->instance}/global.php";


$session = user_session::getInstance();

if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}

$_user = Hooto_Data_Sql::getTable('user');
$user = null;

if (isset($this->reqs->status) && $this->reqs->status == 0) {
    $action_button = 'Disable';
    $status = 0;
} else {
    $action_button = 'Active';
    $status = 1;
}

$_apps = Hooto_Data_Sql::getTable('user_apps');
$q = $_apps->select()
    ->where('uid = ?', $session->uid)
    ->where('instance = ?', $instance);
$apps = $_apps->query($q);
if (count($apps) > 0 && isset($apps[0]['id'])) {
    $app = $apps[0];
    $cins['name'] = $app['title'];
} else {
    $app = null;
}
        
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $title = strip_tags(trim($this->reqs->title));
    
    if (strlen($title) < 1) {
        $title = $cins['name'];
    }
    
    try {

        $_menu = Hooto_Data_Sql::getTable('menu_link');

        // APP INIT
        $set = array(
            'status' => $status,
            'uid' => $session->uid,
            'title' => $title,
            'instance' => $instance
        );
            
        if ($app === null) {            
            $_apps->insert($set);
        } else {
            
            $_apps->update($set, array('id' => $app['id']));
        }
        
        // MENU INIT
        $q = $_menu->select()
            ->where('type = ?', 4)
            ->where('uid = ?', $session->uid)
            ->where('instance = ?', $instance);        
        $menu = $_menu->query($q);

        if (isset($cins['permission'])) {
            $set['permission'] = $cins['permission'];
        }

        if (count($menu) == 0) {
        
            $set['type'] = 4; // type: user main-menu
            //$set['link'] = "/$instance/";

            $_menu->insert($set);
            
        } else {
            $_menu->update($set, array('id' => $menu[0]['id']));
        }
                        
        $msg = w_msg::simple('success', 'Success');
          
    } catch (Exception $e) {
        $msg = w_msg::simple('error', $e->getMessage());
    }
    
}

    
echo "<div><a href=\"/user/manage/\">Go Back</a></div><div class=\"clearhr\"></div>";
echo $msg;


?>
<fieldset class="editlet">
<legend class="titletab">Product Setup</legend>
<form id="general_form" name="general_form" action="/user/appsetup?instance=<?php echo $instance; ?>" method="post" >
  <input id="status" name="status" type="hidden" value="<?php echo $status; ?>" />
  <table class="box" width="100%" border="0" cellpadding="0" cellspacing="10" >
    <tr>
      <td width="200px" align="right" >NAME</td>
      <td ><input id="title" name="title" type="text" size="30" value="<?php echo $cins['name']?>" /></td>
    </tr>
    <tr>
      <td></td>
      <td ><input type="submit" name="submit" class="input_button" value="<?php echo $action_button;?>" /></td>
    </tr>
  </table>
</form>
</fieldset>
