<?php
$this->headtitle = "Change email";
Hooto_Web_View::headStylesheet('/_user/css/manage.css');

$msg = null;

$session = user_session::getInstance();

if ($session->uid == "0") {
    print w_msg::simple('error', 'Access Denied');
    return;
}

$_user = Hooto_Data_Sql::getTable('user');
$user = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vars = get_object_vars($this->reqs);
        
    if (!user_email::isValid($vars, $msgret)) {
        
        $msg = w_msg::simple('error', $msgret);
        
    } else {

        try {
            
            $user = $_user->fetch($session->uid);
            
            if (!isset($user['id'])) {
                throw new Exception('Access Denied');
            }
            
            if (md5($vars['pass']) != $user['pass']) {
                throw new Exception('Password do not match');
            }
                
            if ($user['email'] == $vars['email']) {
                throw new Exception('Nothing changed');
            }
                
            $_user->update(array('email' => $vars['email']), 
                array('id' => $session->uid));
                
            $user['email'] = $vars['email'];
                
            $msg = w_msg::simple('success', 'Success');
            
        } catch (Exception $e) {
            $msg = w_msg::simple('error', $e->getMessage());
        }
    }
}

if (!isset($user['email'])) {
    $user = $_user->fetch($session->uid);
}

echo "<div><a href=\"/user/manage/\">Go Back</a></div><div class=\"clearhr\"></div>";
echo $msg;
?>
<fieldset class="editlet">
<legend class="titletab">Change email</legend>
<form id="account_email" name="account_email" action="/user/email-edit/" method="post" >
  <table class="box" width="100%" border="0" cellpadding="0" cellspacing="10" >
    <tr>
      <td width="200px" align="right" >Email</td>
      <td ><input id="email" name="email" type="text" size="30" value="<?php echo $user['email']?>" /></td>
    </tr>
     <tr>
      <td width="200px" align="right" >Password</td>
      <td ><input id="pass" name="pass" size="30" type="password" /></td>
    </tr>
    <tr>
      <td></td>
      <td ><input type="submit" name="submit" value="Submit" class="input_button" /></td>
    </tr>
  </table>
</form>
</fieldset>
