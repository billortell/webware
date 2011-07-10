<?php
/**
 *  Project     : SmartKit
 *  License     : http://www.gnu.org/copyleft/gpl.html
 *  Site        : http://smartkit.sourceforge.net
 *
 *  $Id: ManageNodeController.php 679 2009-01-29 16:27:29Z evorui $
 *  vim: expandtab shiftwidth=4 tabstop=4 softtabstop=4
 */

defined('SK_ENTRY') or die('Access Denied!');


require_once 'Kit/Controller/Action.php';
require_once 'Kit/Media.php';

class Media_ManageEditorpluginController extends Kit_Controller_Action
{

    protected $page = 1;

    protected $_cfg;

    public function init()
    {
        parent::init();

        $this->_cfg = new Zend_Config_Ini('config/global.ini.php');
        
        if (isset($this->_cfg->media->uploadDir) && strlen($this->_cfg->media->uploadDir)) {
            $this->_uploadDir = $this->_cfg->media->uploadDir;
        }


        $this->page = (int)$this->request->getParam('page');
        if ($this->page < 1) {
            $this->page = 1;
        }
    }
    
    public function indexAction()
    {
        $this->_redirect('/'.$this->subModule.'/manage-editorplugin/upload/');
    }
    
    public function listAction()
    {
       
        $session = self::getUserSession();
        $mediaid = (int)$this->request->getParam('mediaid');
        $page = (int)$this->request->getParam('page');
        if ($page < 1) {
            $page = 1;
        }
        $url = $this->moduleUri.'/manage-editorplugin/list';

        $oMedia = new Kit_Media();
        
        $where = array(
            'e.media.userid' => $session['userid'],
        );
        $order = array('media.mediaid DESC');
        $mediaList = $oMedia->getList($where, $order, 10, ($page - 1) * 10);

        

        foreach ($mediaList as $key => $value) {
            $mediaList[$key]['url'] = $this->moduleUri.'/image/view/?id='.$value['mediaid'];
            $mediaList[$key]['iconsrc'] = $this->moduleUri.'/image/view/?id='. $value['mediaid'] .'&style=thumb';

            $srcweb = $this->moduleUri.'/image/view/?id='. $value['mediaid'];
            $src = $this->_cfg->media->uploadDir .'/'. $value['media_dir'] .'/'. $value['media_stored'];
            
            // Thumbnail
            $im = preg_replace('/(.jpg|.png|.gif)/', '-thumb.jpg', $src);
            $mediaList[$key]['ims'] = array();
            
            if (file_exists($im)) {
                $ims = getimagesize($im);
                $mediaList[$key]['ims']['thumb'] = array(
                    'w' => $ims[0], 'h' => $ims[1], 
                    'name' => 'Thumbnail',
                    'value' => $srcweb.'&style=thumb'
                );
            }
            
            // Medium
            $im = preg_replace('/(.jpg|.png|.gif)/', '-medium.jpg', $src);
            if (file_exists($im)) {
                $ims = getimagesize($im);
                $mediaList[$key]['ims']['medium'] = array(
                    'w' => $ims[0], 'h' => $ims[1], 
                    'name' => 'Medium',
                    'value' => $srcweb.'&style=medium'
                );
            }
            
            // Large
            $im = preg_replace('/(.jpg|.png|.gif)/', '-large.jpg', $src);
            if (file_exists($im)) {
                $ims = getimagesize($im);
                $mediaList[$key]['ims']['large'] = array(
                    'w' => $ims[0], 'h' => $ims[1], 
                    'name' => 'Large',
                    'value' => $srcweb.'&style=large'
                );
            }
            
            // Full size
            if (file_exists($src)) {
                $ims = getimagesize($src);
                $mediaList[$key]['ims']['full'] = array(
                    'w' => $ims[0], 'h' => $ims[1], 
                    'name' => 'Full',
                    'value' => $srcweb.'&style=full'
                );
            }
            
            $mediaList[$key]['srcweb'] = $srcweb.'&style=full';
            
        }

        $this->view->assign('mediaList', $mediaList);
              
        $mediaCount = $oMedia->getCount($where);
        $mediaPager = getPager($page, $mediaCount, 10, 8);
        $this->view->assign('mediaPager', (array)$mediaPager);
        $this->view->assign('mediaUrl', $url);
        $this->view->assign('mediaid', $mediaid);
            
        $this->render('manage-editorplugin-list', null, true);
    }


    public function uploadAction()
    {
        $this->render('manage-editorplugin-upload', null, true);
    }
    
    public function douploadAction()
    {

        $params = $this->getRequest()->getParams();

		$media = new Kit_Util_Media();
		$image = new Kit_Util_Image();
        $oMedia = new Kit_Media();
        
        $session = self::getUserSession();

        $url = $this->moduleUri.'/manage-editorplugin/list/';

        if ($media->upload(array('userid' => $session['userid']), $ret)) {
        
            $image->resize($ret['stored'], 
                $this->_cfg->media->image->thumb->width,
                $this->_cfg->media->image->thumb->height,
                $this->_cfg->media->image->thumb->crop,
                'thumb'
            );
            
            $image->resize($ret['stored'], 
                $this->_cfg->media->image->medium->width,
                $this->_cfg->media->image->medium->height,
                $this->_cfg->media->image->medium->crop,
                'medium'
            );
            
            $image->resize($ret['stored'], 
                $this->_cfg->media->image->large->width,
                $this->_cfg->media->image->large->height,
                $this->_cfg->media->image->large->crop,
                'large'
            ); 
            
            $this->_redirect($url.'?mediaid='.$ret['mediaid']);
            
        } else {
            $this->setMessage('error', 'Error');
            return $this->uploadAction();
        }

    }
   
}
