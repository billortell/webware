<?php
$this->headtitle = "My Profile";
Hooto_Web_View::headStylesheet('/_user/css/manage.css');

$session = user_session::getInstance();
$msg = null;
if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $entry = new Hooto_Object($_POST);
    
    if (user_profile::isValid($entry, $msgstr)) {
        
        try {
        
            $_profile = Hooto_Data_Sql::getTable('user_profile');
            $entry0 = $_profile->fetch($session->uid);
   
            $entry0 = new Hooto_Object($entry0);
            
            $set = array();
            if ($entry->name != $entry0->name) {
                $set['name'] = $entry->name;
            }
            if ($entry->gender != $entry0->gender) {
                $set['gender'] = $entry->gender;
            }
            if ($entry->birthday != $entry0->birthday) {
                $set['birthday'] = $entry->birthday;
            }
            if ($entry->address != $entry0->address) {
                $set['address'] = $entry->address;
            }
            if ($entry->content != $entry0->content) {
                $set['content'] = $entry->content;//Hooto_Util_Format::stripScript($entry->content);
            }
            if ($entry->sitename != $entry0->sitename) {
                $set['sitename'] = $entry->sitename;
            }
            $set['updated'] = $_SERVER['REQUEST_TIME'];
            
            if (count($set) > 0 && isset($entry0->id)) {   
                $_profile->update($set, array('id' => $session->uid));
            } else {
                $set['id'] = $session->uid;
                $set['created'] = $_SERVER['REQUEST_TIME'];
                $_profile->insert($set);
            }

            if (isset($set['name'])) {

                $_user = Hooto_Data_Sql::getTable('user');
                
                $_user->update(array('name' => $set['name']), array('id' => $session->uid));
            }

            $msg = w_msg::simple('success', 'Success <a href="/user/manage/">Go Back</a>');
            
        } catch (Exception $e) {
        
            $msg = w_msg::simple('error', $e->getMessage());
        }
        
    } else {
        $msg = w_msg::simple('error', $msgstr);
    } 

} else {
    $_profile = Hooto_Data_Sql::getTable('user_profile');
    $entry = $_profile->fetch($session->uid);
   
    $entry = new Hooto_Object($entry);
    
    
    //$entry->content = htmlspecialchars($entry->content, ENT_NOQUOTES);
}

$entry->content = Hooto_Util_Format::richEditFilter($entry->content);

echo "<div><a href=\"/user/manage/\">Go Back</a></div><div class=\"clearhr\"></div>";
print $msg;
?>

<!-- TinyMCE/ -->
<script type="text/javascript" src="/_default/tinymce/tiny_mce.js"></script>
<script type="text/javascript" src="/_default/js/editor.js"></script>
<script type="text/javascript">
  tinymceInitOptions['content_css'] = "/_default/js/editor.css";
  tinyMCE.init(tinymceInitOptions);
</script>
<!-- /TinyMCE -->

<fieldset class="editlet">
<legend class="titletab">Edit Profile</legend>

<form id="user_profile" name="user_profile" action="/user/profile-edit" method="post" >
  <input id="uid" name="id" type="hidden" value="<?php echo $entry->id?>" />
  <table class="box" width="100%" border="0" cellpadding="0" cellspacing="8" >
    <tr>
      <td width="120px" align="right" ><b>Name</b></td>
      <td width="20px"></td>
      <td><input id="name" name="name" type="text" size="20" value="<?php echo $entry->name?>" /></td>
    </tr>
    <tr>
      <td align="right" ><b>Gender</b></td>
      <td></td>
      <td>
      	Male <input id="gender" name="gender" type="radio" value="1" <?php if ($entry->gender == 1) { echo 'checked="checked"'; } ?> />
        Female <input name="gender" type="radio" value="0" <?php if ($entry->gender == 0) { echo 'checked="checked"'; } ?> />   
      </td>
    </tr>
    <tr>
      <td align="right" ><b>Birthday</b></td>
      <td></td>
      <td ><input id="birthday" name="birthday" type="text" size="20" value="<?php echo $entry->birthday?>" /> Example : 1970-01-01</td>
    </tr>
    <tr>
      <td align="right" ><b>Address</b></td>
      <td></td>
      <td ><input id="address" name="address" type="text" size="40" value="<?php echo $entry->address?>" /></td>
    </tr>
    <tr>
      <td width="120px" align="right" ><b>Site Name</b></td>
      <td width="20px"></td>
      <td><input id="sitename" name="sitename" type="text" size="40" value="<?php echo $entry->sitename?>" /></td>
    </tr>
    <tr>
      <td align="right" valign="top" ><b>About me</b></td>
      <td></td>
      <td>
        <div id="switchEditorsBar" class="hideifnojs">	
	      <a href="javascript:;" onclick="richEditor.go('content', 'tinymce');">[Visual]</a>
          <a href="javascript:;" onclick="richEditor.go('content', 'html');">[HTML]</a>
        </div>
        <textarea id="content" name="content" style="width:100%;" rows="20" ><?php echo $entry->content?></textarea>
      </td>
    </tr>
    <tr>
      <td></td>
      <td></td>
      <td><input class="input_button" type="submit" name="submit" value="Save" /></td>
    </tr>
  </table>
</form>

</fieldset>

<script>
  document.getElementById('switchEditorsBar').className = '';
  tinyMCE.execCommand("mceAddControl", false, 'content');
</script>


