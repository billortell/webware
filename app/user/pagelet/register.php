<?php
Hooto_Web_View::headStylesheet('/_user/css/sign.css');

$message = null;
if (isset($_REQUEST['url']) && strlen($_REQUEST['url']) > 10) {
    echo "<div><a href=\"{$_REQUEST['url']}\">Go Back</a></div>";
    $url = urlencode($_REQUEST['url']);
} else {
    echo "<div><a href=\"javascript:history.go(-1)\">Go Back</a></div>";
    $url = '';
}

$params = new Hooto_Object($_POST);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    
    if (user_register::isValid($params, $msgstr)) {
        
        try {
        
            user_register::register($params);
            
            if (isset($params->url) && strlen($params->url) > 1) {
                header('Location: '.$params->url);
            } else {
                header('Location: /user/login/');
            }
            //$message = w_msg::simple('success', 'Success');
            
        } catch (Exception $e) {
        
            $message = w_msg::simple('error', $e->getMessage());
        }
        
    } else {
        $message = w_msg::simple('error', $msgstr);
    }
}

?>
<div class="clearhr"></div>

<fieldset class="signlet">
  <legend class="titletab">User Registration</legend>
  <?php print $message;?>
  <form id="signup" name="signup" action="/user/register" method="post">
  <input type="hidden" name="url" value="<?=$url?>" />
  <table width="100%" border="0" cellpadding="0" cellspacing="10">
    <tr>
      <td width="160px" align="right" ><b>Username</b></td>
      <td>
        <input id="uname" name="uname" type="text" size="20" value="<?=$params->uname?>" />
      </td>
    </tr>
    <tr>
      <td align="right"><b>Email</b></td>
      <td><input id="email" name="email" type="text" size="30" maxlength="50" value="<?=$params->email?>" /></td>
    </tr>
    <tr>
      <td align="right"><b>Password</b></td>
      <td>
        <input id="pass" name="pass" type="password" size="20" value="<?=$params->pass?>" />
      </td>
    </tr>
    <tr>
      <td align="right"><b>Re-type Password</b></td>
      <td><input id="repass" name="repass" type="password" size="20" value="<?=$params->repass?>" /></td>
    </tr>
    <tr>
      <td align="right"></td>
      <td><input type="submit" value="Sign up" class="input_button" /></td>
    </tr>
  </table>
  </form>
</fieldset>

