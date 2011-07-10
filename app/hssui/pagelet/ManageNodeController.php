<?php
/**
 *  Project     : SmartKit
 *  License     : http://www.gnu.org/copyleft/gpl.html
 *  Site        : http://smartkit.sourceforge.net
 *
 *  $Id: ManageNodeController.php 680 2009-02-02 15:36:11Z evorui $
 *  vim: expandtab shiftwidth=4 tabstop=4 softtabstop=4
 */

defined('SK_ENTRY') or die('Access Denied!');


require_once 'Kit/Controller/Action.php';
require_once 'Kit/Media.php';

class Media_ManageNodeController extends Kit_Controller_Action
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
        $this->_redirect('/'.$this->subModule.'/manage-node/list/page/'.$this->page);
    }
    
    public function listAction()
    {
       
        $this->initLayout('layout-manage');
        $session = self::getUserSession();
        $page = (int)$this->request->getParam('page');
        if ($page < 1) {
            $page = 1;
        }
        $url = $this->moduleUri.'/manage-node/list';

        $oMedia = new Kit_Media();
        
        $where = array(
            'e.media.userid' => $session['userid'],
        );
        $order = array('media.mediaid DESC');
        $mediaList = $oMedia->getList($where, $order, 20, ($page - 1) * 20);

        foreach ($mediaList as $key => $value) {
            $mediaList[$key]['src_thumb'] = $this->moduleUri.'/image/view/?id='. $value['mediaid'] .'&style=thumb';
            $mediaList[$key]['created_formated'] = date("Y/m/d", strtotime($value['created']));
        }
        // echo $this->moduleUri.' '.$this->baseUrl;
        $this->view->assign('mediaList', $mediaList);
              
        $mediaCount = $oMedia->getCount($where);
        $mediaPager = getPager($page, $mediaCount, 20, 12);
        $this->view->assign('mediaPager', (array)$mediaPager);
        $this->view->assign('mediaUrl', $url);
    
        $this->render('manage-node-list', null, true);
    }
    
    public function newAction()
    {
        return $this->editAction();
    }

    public function editAction()
    {
        $this->initLayout('layout-manage');
        $this->render('manage-node-edit', null, true);
    }

    public function uploadAction()
    {
        $this->initLayout('layout-manage');

        $params = $this->getRequest()->getParams();

		$media = new Kit_Util_Media();
		$image = new Kit_Util_Image();
        $oMedia = new Kit_Media();
        
        $session = self::getUserSession();
        $page = (int)$this->request->getParam('page');
        if ($page < 1) {
            $page = 1;
        }
        $url = $this->moduleUri.'/manage-node/list';

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
             
            $this->setMessage('success', 'Success');
            return $this->listAction();     
        } else {
            $this->setMessage('error', 'Error');
            return $this->editAction();
        }

    }
    
    public function deleteAction()
    {
        $session = self::getUserSession();
        
        $where = array(
            'mediaid'  => $this->reqParams['mediaid'],
            'userid'   => $session['userid'],
        );

        $oMedia = new Kit_Media();
        
        if (!$oMedia->delete($where)) {
            $this->setMessage('error', 'Error');
        } else {
            $this->setMessage('success', 'Success');
        }

        return $this->listAction();
    }
    
}
