<?php

if (!isset($hdata_instance) || !isset($this->reqs->id)) {
    return;
}

if (!Hooto_Registry::isRegistered('entry')) {
    return;
}

hdata_entry::setInstance($hdata_instance);

$query = hdata_entry::select()
    ->where('pid = ?', $this->reqs->id)
    ->where('status = ?', 1)
    ->order('created', 'asc')
    ->limit(50);

$feed = hdata_entry::query($query);

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

?>
<div class="comments">
  <h3>Comments</h3><a name="comment-view"></a>
  <?php
  foreach ($feed as $val) {
    $val['content'] = Hooto_Util_Format::textHtmlFilter($val['content']);
  ?>      
  <div class="view" id="entry-comment-<?=$val['id']?>">
    <div><img src="/_w/img/fffam/comment.png" align="absmiddle" /> <b><?=$val['uname']?></b></div>
    <p><?=$val['content']?></p>
    <div class="info">
      <?=$val['created']?>
      <?php if (1 || isset($val['allowDelete'])) { ?>
      <a href="<?=$this->reqs->urlins?>/comment-delete/?id=<?=$val['id']?>&url=<?php echo $this->reqs->url?>">Delete</a>
      <?php } ?>
    </div>
  </div>
  <?php } ?>
</div>
