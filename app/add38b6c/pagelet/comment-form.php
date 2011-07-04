<?php

Hooto_Web_View::headStylesheet('/_w/css/cm.css');



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    
    if (!isset($hdata_instance)) {
        return;
    }

    if (!isset($this->reqs->pid) || !isset($this->reqs->puid) || !isset($this->reqs->pinstance)) {
       return;
    }
    

    hdata_entry::setInstance($hdata_instance);
    
    $entry = new Hooto_Object($_POST);
    $entry->uid = 0;
    $entry->created = date("Y-m-d H:i:s");
    $entry->updated = date("Y-m-d H:i:s");

    try {
        
        if (!hcaptcha_api::isValid($entry->captcha_word)) {
            throw new Exception('Captcha value is wrong');
        }
    
        $entry->instance = $hdata_instance;
            
        if ($entry->id == "") {
            $entry->id = Core_Util_Uuid::create(); 
                //print_r($entry);
                //$db->insert($entry);
        } else {
            unset($entry->created);
        }
            //print_r($entry);
           // hdata_entry::replaceEntry($entry);
            
        $links[] = array('url' => $_GET['url'], 'title' => 'Back');
        
        $this->msg = w_msg::get('success', 'Success', $links);
            
    } catch (Exception $e) {
        
        $this->msg = w_msg::get('error', $e->getMessage());
            //return $this->editAction();
    }
    
    print $this->pagelet('msg-inter');
    
    return;
} else {

    if (!isset($this->reqs->id)) {
       return;
    }
}

if (!Hooto_Registry::isRegistered('entry')) {
    return;
}
$entry = Hooto_Registry::get('entry');


if (isset($entry['comment']) && $entry['comment'] == 1) {

$captchaurl = hcaptcha_api::getImgUrl();
?>
<div class="comment-form">
  <a name="comment-add"></a>
  <h3 class="comment-form-htitle">Leave a Comment</h3>
  <form id="form_comment_submit" action="<?=$this->reqs->urlins?>/comment?url=<?php echo $this->reqs->url?>" method="post">
  <input type="hidden" name="pid" value="<?=$entry['id']?>" />
  <input type="hidden" name="pinstance" value="<?=$entry['instance']?>" />
  <input type="hidden" name="puid" value="<?=$entry['uid']?>" />
  <input type="hidden" name="status" value="1" />
  <table width="100%" border="0" cellpadding="0" cellspacing="10">
    <tr>
      <td width="160px" align="right"><b>Name</b></td>
      <td><input type="text" name="uname" value="<?=$this->reqs->uname?>" /></td>
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

