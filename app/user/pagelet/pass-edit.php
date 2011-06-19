<?php
$this->headtitle = "Change password";
Hooto_Web_View::headStylesheet('/_user/css/manage.css');

$msg = null;

$session = user_session::getInstance();

if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}


$user = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    try {
    
        $vars = get_object_vars($this->reqs);
        
        if (!user_pass::isValid($vars, $msgret)) {
            throw new Exception($msgret);
        }
       
        $_user = Hooto_Data_Sql::getTable('user');
        
        $user = $_user->fetch($session->uid);

        if (!isset($user['id'])) {
            throw new Exception('Access Denied');
        }
        
        if (md5($vars['pass_current']) != $user['pass']) {
            throw new Exception('Current password do not match');
        }

        $_user->update(array('pass' => md5($vars['pass'])), 
        array('id' => $session->uid));

        $msg = w_msg::simple('success', 'Success');

    } catch (Exception $e) {
       $msg = w_msg::simple('error', $e->getMessage());
    }
}
    
echo "<div><a href=\"/user/manage/\">Go Back</a></div><div class=\"clearhr\"></div>";
echo $msg;

?>
<fieldset class="editlet">
<legend class="titletab">Change password</legend>
<form name="user_accountpwd" action="/user/pass-edit/" method="post" >
  <table class="box" width="100%" border="0" cellpadding="0" cellspacing="10" >
    <tr>
      <td width="200px" align="right" >Current password</td>
      <td ><input id="pass_current" name="pass_current" size="30" type="password" /></td>
    </tr>
    <tr>
      <td align="right" >New password</td>
      <td ><input id="pass" name="pass" size="30" type="password" /></td>
    </tr>
    <tr>
      <td align="right" >Confirm new password</td>
      <td ><input id="pass_confirm" name="pass_confirm" size="30" type="password" /></td>
    </tr>
    <tr>
      <td></td>
      <td ><input type="submit" name="submit" value="Submit" /></td>
    </tr>
  </table>
</form>
</fieldset>
<script>document.user_accountpwd.pass_current.focus();</script>


