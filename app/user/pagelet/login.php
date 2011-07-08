<?php
Hooto_Web_View::headStylesheet('/_user/css/sign.css');

$msg = null;
if (isset($_REQUEST['cburl']) && strlen($_REQUEST['cburl']) > 10) {
    echo "<div><a href=\"{$_REQUEST['cburl']}\">Go Back</a></div>";
    $cburl = strip_tags($_REQUEST['cburl']);
} else {
    echo "<div><a href=\"javascript:history.go(-1)\">Go Back</a></div>";
    $cburl = '';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $params = $_POST;

    try {
    
        if (!user_sign::isValid($params, $msgstr)) {
            throw new Exception($msgstr);
        }
        
        user_sign::in($params);

        if (strlen($cburl) > 1) {
            header('Location: '.$cburl);die();
        } else {
            header('Location: /user/manage/');die();
        }
            
    } catch (Exception $e) {
        
        $msg = w_msg::simple('error', $e->getMessage());
    }
}

if ($msg === null) {
    $msg = w_msg::simple('notice', 'Please enter your user name and password');
}
?>
<div class="clearhr"></div>

<fieldset class="signlet">
  <legend class="titletab">Sign in to System</legend>
  <form name="signinform" action="/user/login" method="post">
  <input type="hidden" name="cburl" value="<?=$cburl?>" />
  <?php print $msg;?>
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td width="160" align="left"><b>Username</b></td>
      <td>
        <input id="uname" name="uname" type="text" value="<?=$this->uname?>" />
        <script>document.signinform.uname.focus();</script>
      </td>
    </tr>
    <tr>
      <td align="left"><b>Password</b></td>
      <td><input id="pass" name="pass" type="password" /></td>
    </tr>
    <tr>
      <td align="right"></td>
      <td>
        <input type="checkbox" id="persistent" name="persistent" value="1" checked="1" /> 
        Stay signed in <br />(Uncheck if on a shared computer)      
      </td>
    </tr>
    <tr>
      <td></td>
      <td><input type="submit" name="Submit" value="Sign in" class="input_button" /></td>
    </tr>
    <tr>
      <td align="right"></td>
      <td>
        <a href="#">Forget your Username or Password ? (Developing)</a>
      </td>
    </tr>
  </table>
  </form>
</fieldset>

