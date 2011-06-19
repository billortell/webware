<?php
//print_r($this->reqs);

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);

$uid = uname2uid($this->reqs->uname);

$session = user_session::getInstance();

$query  = hdata_entry::select()
    ->where('uid = ?', $uid)
    ->order('created', 'desc')
    ->limit(10);
    
if (!user_session::isLogin($uid)) {
    $query->where('status = ?', add38b6c_entry::STATUS_PUBLISH);
}

$feed = hdata_entry::query($query);

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

$where = array('taxon' => 1, 'gid' => '0eb466');
$taxon_cats = hdata_taxonomy::fetchTerms($where);

foreach ($feed as $entry) {

    $entry['tag'] = explode(",", $entry['tag']);
    $entry['href']  = "{$this->reqs->urlins}/entry?id={$entry['id']}";    
    
    if (strlen($entry['summary']) > 1) {
        $entry['summary'] = Hooto_Util_Format::textHtmlFilter($entry['summary']);
    } else {
        //$entry['summary'] = wpautop(wptexturize( cutstr((($entry['content'])), 400) ));
        $entry['summary'] = Hooto_Util_Format::summary($entry['content'], 400);
    }
    
    if (isset($taxon_cats[$entry['category']])) {
        $entry['category_display'] = $taxon_cats[$entry['category']]['name'];
    } else {
        $entry['category_display'] = $entry['category'];
    }
    
    $entry['href_category']  = "{$this->reqs->urlins}/entry?term={$entry['category']}";
    
    if (user_session::isAllow($this->reqs->ins, 'entry.edit')) {
        $entry['href_edit']  = "{$this->reqs->urlins}/edit?id={$entry['id']}";
    }
    if (user_session::isAllow($this->reqs->ins, 'entry.delete')) {
        $entry['href_delete']  = "{$this->reqs->urlins}/delete?id={$entry['id']}";
    }
?>
<div class="entry-list">
  <div class="info">
    <div class="infoh">
      <h3 class="title"><a href="<?=$entry['href']?>"> <?=$entry['title']?></a></h3>
      <div>
        <span><img src="/_w/img/fffam/date.png"/> <?php echo date('Y-m-d', strtotime($entry['created']));?></span>
        <span class="term"><img src="/_w/img/fffam/folder.png"/> <a href="<?=$entry['href_category']?>"><?=$entry['category_display']?></a></span>
        <span class="term"><img src="/_w/img/fffam/tag_blue.png"/> 
        <?php 
        foreach ((array)$entry['tag'] as $term) {
            echo "&nbsp;<a href=\"#{$term}\">{$term}</a>";
        }        
        ?>
        </span>
      </div>
    </div>
    <div class="content"><?=$entry['summary']?></div>
    <div class="infof">
      <span>
        <?php
        if (isset($entry['href_edit'])) {
            echo " <a href=\"{$entry['href_edit']}\">Edit</a>";
        }
        if (isset($entry['href_delete'])) {
            echo " <a href=\"{$entry['href_delete']}\">Delete</a>";
        }
        ?>
      </span>
      <span><img src="/_w/img/fffam/comments.png"/> <a href="<?=$entry['href']?>#comment-list">Comments (?)</a></span>
    </div>
  </div>
</div>
<?php } ?>
