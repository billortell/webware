<?php
$this->headtitle = "Menu Settings";
Hooto_Web_View::headStylesheet('/_user/css/manage.css');

$msg = null;

$session = user_session::getInstance();

if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}


$menu_type = 4;


$_menu = Hooto_Data_Sql::getTable('menu_link');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vars = get_object_vars($this->reqs);

    try {
        
        foreach ($vars['title'] as $key => $val) {
            
            if (strlen(trim($val)) == 0) {
                if ($key == 0) {
                    continue;
                }
                throw new Exception('Title can not be null');
            }
            
            $set = array('title' => $val);
            
            if (isset($vars['status'][$key])) {
                $set['status'] = intval($vars['status'][$key]);
            } else {
                $set['status'] = 0;
            }
            if (isset($vars['weight'][$key])) {
                $weight = intval($vars['weight'][$key]);
                $set['weight'] = $weight > 99 ? 99 : $weight;
            }
            if (isset($vars['link'][$key])) {
                $set['link'] = strip_tags($vars['link'][$key]);
            }
            
            $menu = $_menu->fetch($key);
            
            if (isset($menu['id'])) {
            
                $_menu->update($set, array('id' => $key));
            
            } else {
            
                $set['instance'] = 'user-main';
                $set['type'] = 4;
                $set['uid'] = $session->uid;
                
                $_menu->insert($set);
            }        
        }
        
        $msg = w_msg::simple('success', 'Success');
        
    } catch (Exception $e) {
        $msg = w_msg::simple('error', $e->getMessage());
    }

}

$query = $_menu->select()
    ->where('type = ?', 4)
    ->where('uid = ?', $session->uid)
    ->order('weight', 'asc')
    ->limit(50);
$menus = $_menu->query($query);

echo "<div><a href=\"/user/manage/\">Go Back</a></div><div class=\"clearhr\"></div>";
echo $msg;
?>
<fieldset class="editlet">
<legend class="titletab">Menu Settings</legend>
<form id="general_form" name="general_form" action="/user/menu?type=<?php echo $menu_type?>" method="post" >
  <table class="box" width="100%" border="0" cellpadding="0" cellspacing="10" >
    <tr>
      <td><b>Title</b></td>
      <td><b>Link</b></td>
      <td align="center"><b>Enable</b></td>
      <td><b>Product</b></td>
      <td><b>Weight</b></td>
    </tr>
    <?php
    //$conf = Hooto_Config_Array::get('global');
    foreach ($menus as $val) {
    $check = '';
    if ($val['status'] == 1) {
        $check = "checked=\"checked\" ";
    }
    if ($val['instance'] == 'user-main') {
        $link = "<input name=\"link[{$val['id']}]\" type=\"text\" size=\"40\" value=\"{$val['link']}\" />";
    } else {
        $link = $this->siteurl($val['link'], $val['instance'], array(':uname' => $session->uname));
    }
    ?>
    <tr>
      <td><input name="title[<?php echo $val['id']?>]" type="text" size="15" value="<?php echo $val['title']?>" /></td>
      <td><?php echo $link?></td>
      <td align="center"><input name="status[<?php echo $val['id']?>]" type="checkbox" value="1" <?php echo $check?> /></td>
      <td><?php echo $val['instance']?></td>
      <td><input name="weight[<?php echo $val['id']?>]" type="text" size="3" value="<?php echo $val['weight']?>" /></td>
    </tr>
    <?php 
    } 
    if (user_session::isAllow($this->reqs->ins, 'menu.custom')) {
    ?>
    <tr>
      <td><input name="title[0]" type="text" size="15" value="" /></td>
      <td><input name="link[0]" type="text" size="40" value="" /></td>
      <td align="center"><input name="status[0]" type="checkbox" value="1" checked="checked" /></td>
      <td>user-main</td>
      <td><input name="weight[0]" type="text" size="3" value="10" /></td>
    </tr>
    <?php } ?>
    <tr>
      <td><input type="submit" name="submit" value="Save" class="input_button" /></td>
      <td></td>
      <td></td>
      <td></td>
      <td></td>
    </tr>
  </table>
</form>
</fieldset>
