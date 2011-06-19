<?php
$this->headtitle = "Profile";

//Hooto_Web_View::headStylesheet('/_user/css/common.css');

if (preg_match('#^(.+)/profile/(.+)$#i', $this->reqs->uri, $regs)) {
    
    $uname  = $regs[2];
    $uid    = uname2uid($uname);
    
    try {

        $_user = Hooto_Data_Sql::getTable('user');    
        $user = $_user->fetch($uid);
        $user = new Hooto_Object($user);
        
        if (isset($user->id)) {
            $des = str_split($user->id);            
            $path = '/data/user/'.$des['0'].'/'.$des['1'].'/'.$des['2'].'/'.$user->id;
    
            if (!file_exists(SYS_ROOT.$path."/w100.png")) {
                $path = '/data/user';
            }

            $photo_path = $path.'/w100.png';
        } else {
            print w_msg::simple('error', 'Profile not found');
            return;
        }
        
        $_profile = Hooto_Data_Sql::getTable('user_profile');
        $pf = $_profile->fetch($uid);
        if (isset($pf['id'])) {
            $content = Hooto_Util_Format::textHtmlFilter($pf['content']);
        } else {
            $content = 'No Profile Found';
            print w_msg::simple('error', 'Profile not found');
        }
        
    } catch (Exception $e) {
        print w_msg::simple('error', 'Profile not found');
        return;
    }
} else {
    print w_msg::simple('error', 'Profile not found');
    return;
}
?>

<div class="profilepublic">

  <table class="box" width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="160px" valign="top">
        <div><img src="<?php echo $photo_path?>" /></div>
        <div><b><?php echo $user->name?></b></div>
        <?php
        if (user_session::isLogin($uid)) {
            echo "<div><b><a href=\"/user/profile-edit/\">Edit</a></b></div>";
        }
        ?>
      </td>
      <td valign="top"><?php echo $content?></td>
    </tr>
  </table>
</div>

