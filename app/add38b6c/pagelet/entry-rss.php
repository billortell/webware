<?php
//print_r($this->reqs);

if (!isset($hdata_instance)) {
    return;
}
hdata_entry::setInstance($hdata_instance);

$uid = uname2uid($this->reqs->uname);

try {
    $profile = user_profile::fetch($this->reqs->uname);
    if (!isset($profile['sitename'])) {
        throw new Exception('Profile Not Found');
    }
} catch (Exception $e) {
    $profile = array(
        'sitename' => $this->reqs->uname,
        'name' => $this->reqs->uname
    );
}

$query = hdata_entry::select()
    ->where('uid = ?', $uid)
    ->where('status = ?', add38b6c_entry::STATUS_PUBLISH)
    ->order('created', 'desc')
    ->limit(20);
$ret = hdata_entry::query($query);

$feed = new Zend_Feed_Writer_Feed;
$feed->setTitle($profile['sitename']);
$feed->setLink($this->siteurl('', $this->reqs->ins));
$feed->setFeedLink($this->siteurl('/rss', $this->reqs->ins), 'atom');
$feed->addAuthor(array('name' => $profile['name']));
$feed->setDateModified(time());
//print_r($ret);
foreach ($ret as $val) {
    
    $content = htmlspecialchars(Hooto_Util_Format::summaryPlainText($val['content']));
    
    $entry = $feed->createEntry();
    
    $entry->setTitle(htmlspecialchars($val['title']));
    $entry->setLink($this->siteurl("/view/{$val['id']}.html", $this->reqs->ins));
    $entry->addAuthor(array('name'  => $val['uname']));
    $entry->setDateModified(strtotime($val['updated']));
    $entry->setDateCreated(strtotime($val['created']));
    $entry->setDescription($content);
    $entry->setContent($content);
    
    $feed->addEntry($entry);
}

if (!headers_sent()) {
    header('Content-Type: application/atom+xml; charset=utf-8');
}
print $feed->export('atom');
die();
