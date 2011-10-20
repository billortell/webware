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
$cfg = Hooto_Config_Array::get("user/global");
$captcha_img = null;
if (isset($cfg['captcha'])) {
  if ($cfg['captcha']['type'] == 'hcaptcha') {
    $captcha_token = hwl_string::rand(32);
    $captcha_img = $cfg['captcha']['api'].'?token='.$captcha_token;
  }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    
    if (user_register::isValid($params, $msgstr)) {
        
        try {
        
            if ($captcha_img !== null) {
              $client = new hwl_httpclient("{$cfg['captcha']['api']}?word={$params->captcha_word}&token={$params->captcha_token}");
              $client->doGet();
              //echo $client->getBody(); 
              if ($client->getBody() != 'OK') {
                throw new Exception('Word Verification do not match');
              }
            }
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
    <?php if ($captcha_img !== null) { ?>
    <tr>
      <td align="right" valign="top"><b>Word Verification</b></td>
      <td>
        <img src="<?=$captcha_img?>" title="hcaptcha service"/><br />
        <input id="captcha_token" name="captcha_token" type="hidden" value="<?=$captcha_token?>" />
        <input id="captcha_word" name="captcha_word" type="text" size="12" value="" /> 
      </td>
    </tr>
    <?php } ?>
    <tr>
      <td align="right"></td>
      <td><input type="submit" value="Sign up" class="input_button" /></td>
    </tr>
    <tr>
      <td align="right"></td>
      <td>
        <a href="/user/login/">User Login</a> <br />
        <a href="#">Forget your Username or Password ?</a> (Developing)
      </td>
    </tr>
  </table>
  </form>
</fieldset>

