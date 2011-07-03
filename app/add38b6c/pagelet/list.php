<?php
//print_r($this->reqs);


if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);

$uid = uname2uid($this->reqs->uname);

$session = user_session::getInstance();

$urlpager = '';
$page = 0;
if (isset($this->reqs->page)) {
    $page = intval($this->reqs->page);
}
$page = $page < 1 ? 1 : $page;
$limitcount = 5;

$query  = hdata_entry::select()
    ->where('uid = ?', $uid)
    ->order('created', 'desc')
    ->limit($limitcount, ($page - 1) * $limitcount);

//print_r($query);
if (isset($this->reqs->q)) {
    $query->where("title LIKE '%{$this->reqs->q}%'");
    $urlpager .= "&q=".$this->reqs->q;
}
if (isset($this->reqs->cat)) {
    $query->where("category = ?", $this->reqs->cat);
    $urlpager .= "&cat=".$this->reqs->cat;
}
if (isset($this->reqs->tag)) {
    $query->where("tag LIKE '%{$this->reqs->tag}%'");
    $urlpager .= "&tag=".$this->reqs->tag;
}

if (isset($this->reqs->date)) {
    
    if (preg_match('/^([0-9]{4})-([0-9]{2})$/', $this->reqs->date)) {
        $start = strtotime($this->reqs->date);
        $query->where("created > ?", date("Y-m", $start));
        $query->where("created < ?", date("Y-m", strtotime("+1 Month", $start)));
    } else if (preg_match('/^([0-9]{4})$/', $this->reqs->date)) {
        $start = gmmktime(0,0,0,1,1,$this->reqs->date);
        $query->where("created > ?", date("Y", $start));
        $query->where("created < ?", date("Y", strtotime("+1 Year", $start)));
    }
    $urlpager .= "&date=".$this->reqs->date;
}

//print_r($query);

if (!user_session::isLogin($uid)) {
    $query->where('status = ?', add38b6c_entry::STATUS_PUBLISH);
} else {
    $query->where('status > ?', 0);
}

$feed = hdata_entry::query($query);

Hooto_Web_View::headStylesheet('/_w/css/cm.css');

$where = array('taxon' => hdata_entry::$metadata['taxonomy']['category']['id'], 'gid' => '0eb466');
$taxon_cats = hdata_taxonomy::fetchTerms($where);

foreach ($feed as $entry) {

    if (strlen($entry['tag']) > 0) {
        $entry['tag'] = explode(",", $entry['tag']);
    } else {
        $entry['tag'] = array();
    }
    
    $entry['href']  = "{$this->reqs->urlins}/entry?id={$entry['id']}";    
    
    if (strlen($entry['summary_auto']) == 0) {
        $entry['summary'] = Hooto_Util_Format::textHtmlFilter($entry['summary']);
    } else {
        //$entry['summary'] = wpautop(wptexturize( cutstr((($entry['content'])), 400) ));
        $entry['summary'] = Hooto_Util_Format::summary($entry['content'], 300);
    }
    
    if (isset($taxon_cats[$entry['category']])) {
        $entry['category_display'] = $taxon_cats[$entry['category']]['name'];
    } else {
        $entry['category_display'] = $entry['category'];
    }
    
    $entry['href_category']  = "{$this->reqs->urlins}/index?cat={$entry['category']}";
    
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
        <span><img src="/_w/img/fffam/date.png" align="absmiddle"/> <?php echo date('Y-m-d', strtotime($entry['created']));?></span>
        <span class="term"><img src="/_w/img/fffam/chart_organisation.png" align="absmiddle"/> <a href="<?=$entry['href_category']?>"><?=$entry['category_display']?></a></span>
        
        <?php 
        if (count($entry['tag']) > 0) {
            echo "<span class='term'><img src='/_w/img/fffam/tag_blue.png' align='absmiddle'/> ";
        }
        foreach ($entry['tag'] as $term) {
            echo "&nbsp;<a href=\"{$this->reqs->urlins}/index?tag={$term}\">{$term}</a>";
        }        
        ?>
        </span>
        <?php
        if (isset($entry['href_delete'])) {
            echo "<span><img src=\"/_w/img/fffam/page_white_delete.png\" align=\"absmiddle\"/> <a href=\"{$entry['href_delete']}\">Delete</a></span>";
        }
        if (isset($entry['href_edit'])) {
            echo "<span><img src=\"/_w/img/fffam/page_white_edit.png\" align=\"absmiddle\"/> <a href=\"{$entry['href_edit']}\">Edit</a></span>";
        }
        ?>
      </div>
    </div>
    
    <div class="content"><?=$entry['summary']?></div>
    
    <div class="infof">
      <span><img src="/_w/img/fffam/comment_add.png" align="absmiddle"/> <a href="<?=$entry['href']?>#comment-add">Leave a Reply</a></span>
      <span><img src="/_w/img/fffam/page_white_go.png" align="absmiddle"/> <a href="<?=$entry['href']?>">Read more</a></span>
    </div>
  </div>
</div>
<?php

}

$query = $query->select("count(id) as count")->reset(array('limit', 'order'));

$feed = hdata_entry::query($query);
$count = 0;
if (isset($feed[0]) && isset($feed[0]['count'])) {
    $count = $feed[0]['count'];
}
$pager = hwl_pager::get($page, $count, $limitcount);


$urlpager = $this->siteurl('/index?'.trim($urlpager, '&'), $this->reqs->ins);
?>

<ul class="pager">
    <li class="info">Items <?=$pager['itemFrom']?> - <?=$pager['itemTo']?> </li>
    
    <?php if (isset($pager['first'])) { ?>
    <li><a href="<?=$urlpager?>&page=<?=$pager['first']?>">First</a></li>
    <?php } ?>
    
    <?php if (isset($pager['previous'])) { ?>
    <li><a href="<?=$urlpager?>&page=<?=$pager['previous']?>">Previous</a></li>
    <?php } ?>
    
    <?php foreach ($pager['list'] as $page): ?>
    <li><a href="<?=$urlpager?>&page=<?=$page['page']?>" <?php if ($page['isCurrent']) {echo 'class="current"';}?>><?=$page['page']?></a></li>
    <?php endforeach; ?>
    
    <?php if (isset($pager['next'])) { ?>
    <li><a href="<?=$urlpager?>&page=<?=$pager['next']?>">Next</a></li>
    <?php } ?>
    
    <?php if (isset($pager['last'])) { ?>
    <li><a href="<?=$urlpager?>&page=<?=$pager['last']?>">Last</a></li>
    <?php } ?>
</ul>

