<?php
Hooto_Web_View::headStylesheet('/_user/css/sign.css');

$msg = null;
if (isset($_REQUEST['cburl']) && strlen($_REQUEST['cburl']) > 10) {
    $cburl = strip_tags($_REQUEST['cburl']);
} else {
    $cburl = '/';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $params = $_POST;

    try {
    
        if (!user_sign::isValid($params, $msgstr)) {
            throw new Exception($msgstr);
        }
        
        user_sign::in($params);

        if (strlen($cburl) > 1) {
            //header('Location: '.$cburl);die();
        } else {
            //header('Location: /user/manage/');die();
            $cburl = '/user/manage/';
        }
        
        /** $pu = parse_url($cburl);        
        if ($pu['host'] != $_SERVER['HTTP_HOST']) {
                   
            if (!preg_match("/\?/i", $cburl)) {
                $cburl .= '?';
            }
            
            $cburl .= "&.access_token=".$_COOKIE['sid'];
        }*/
        
        $go = "<html><head><title>redirecting</title></head><body>";
        $cfg = Hooto_Config_Array::get("user/global");
        if (isset($cfg['cookie_domain'])) {
            $access_token = registry("access_token");
            $expire = time() + 864000;
            foreach ($cfg['cookie_domain'] as $v) {
                $go .= "<script src=\"http://{$v}/setcookie.php?access_token={$access_token}&expire={$expire}\"></script>\n";
            }
        }
        
        $go .= '
        <div><b>Sign-on system success. Page redirecting</b> ... <a href=\"'.$cburl.'\">Goto</a></div>
        <script language="javascript">
        function reback() {
            top.location="'.$cburl.'";
        }
        window.setInterval(reback,3000);
        </script>
        <body></html>';
        
        echo $go;
        die();
            
    } catch (Exception $e) {
        
        $msg = w_msg::simple('error', $e->getMessage());
    }
}

echo "<div><a href=\"{$cburl}\">Go Back</a></div>";

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
        <a href="/user/register/">User Registration</a> <br />
        <a href="#">Forget your Username or Password ?</a> (Developing)
      </td>
    </tr>
  </table>
  </form>
</fieldset>

