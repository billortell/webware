<?php
$msg = '';

$links[] = array('url' => $this->reqs->url, 'title' => 'Back');

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (!isset($this->reqs->pid) || !isset($this->reqs->puid) || !isset($this->reqs->pinstance)) {
        throw new Exception('400');
    }
    
    $item = new Hooto_Object($_POST);
    $item->uid = 0;
    $item->created = date("Y-m-d H:i:s");
    $item->updated = date("Y-m-d H:i:s");

    try {
        
        if (!hcaptcha_api::isValid($item->captcha_word)) {
            throw new Exception('Captcha value is wrong');
        }
    
        $item->instance = $hdata_instance;
            
        if ($item->id == "") {
            $item->id = hwl_string::rand(12);
        } else {
            unset($item->created);
        }
        hdata_entry::replaceEntry($item);
            
        $msg = w_msg::simple('success', 'Success', $links);
        
        unset($this->reqs->pid);
            
    } catch (Exception $e) {
        
        $msg = w_msg::simple('error', $e->getMessage(), $links);
        //return $this->editAction();
    }
}

$entry = array();

if (Hooto_Registry::isRegistered('entry')) {
    $entry = Hooto_Registry::get('entry');
}

if (!Hooto_Registry::isRegistered('entry') && isset($this->reqs->pid)) {
    hdata_entry::setInstance($hdata_pinstance);
    $entry = hdata_entry::fetchEntry($this->reqs->pid);
}

if (is_array($entry)) {
    $entry = new Hooto_Object($entry);   
}

echo $msg;

$session = user_session::getInstance();
//print_r($session);

if (isset($entry->comment) && $entry->comment == 1) {

  $captchaurl = hcaptcha_api::getImgUrl();
  
?>
<div class="comment-form">
  <a name="comment-add"></a>
  <h3 class="comment-form-htitle">Leave a Comment</h3>
  <form id="form_comment_submit" action="<?=$this->siteurl("/comment?url={$this->reqs->url}")?>" method="post">
  <input type="hidden" name="pid" value="<?=$entry->id?>" />
  <input type="hidden" name="pinstance" value="<?=$entry->instance?>" />
  <input type="hidden" name="puid" value="<?=$entry->uid?>" />
  <input type="hidden" name="status" value="1" />
  <table width="100%" border="0" cellpadding="0" cellspacing="10">
    <tr>
      <td width="160px" align="right"><b>Name</b></td>
      <td><input type="text" name="uname" value="<?=$session->uname?>" /></td>
    </tr>
    <tr>
      <td align="right" valign="top"><b>Content</b></td>
      <td><textarea name="content" cols="50" rows="5"><?=$this->reqs->content?></textarea></td>
    </tr>
    <tr>
      <td align="right" valign="top"><b>Verification</b></td>
      <td>
        <input type="text" name="captcha_word" value="" size="6" />Type the characters you see in the picture below<br />
        <img src="<?=$captchaurl?>" />
      </td>
    </tr>
    <tr>
      <td></td>
      <td><input class="input_button" type="submit" value="Submit" /></td>
    </tr>
  </table>
  </form>
</div>
<?php } ?>

