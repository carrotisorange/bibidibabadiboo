<?php
namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Authentication\AuthenticationService;

use Base\Acl\Acl;
 
/**
 * This view helper class displays a menu bar.
 */
class Menu extends AbstractHelper 
{
    /**
     * Menu list array.
     * @var array 
     */
    protected $menus = [];
     
    /**
     * @var Zend\Authentication\AuthenticationService
     */
    private $serviceAuth;
    
    /**
     * @var  Base\Acl\Acl
     */
    private $acl;
     /**
     * Constructor.
     * @param array $items Menu items.
     * @param object $serviceAuth AuthenticationService.
     * @param array $acl Acl.
     */
    public function __construct(Array $menus = [], AuthenticationService $serviceAuth, Acl $acl)
    {
        $this->menus = $menus;
        $this->serviceAuth = $serviceAuth;
        $this->acl = $acl;
    }
    
    /**
     * Renders the menu.
     * @return string HTML code of the menu.
     */
    public function getMenus()
    {
        if (empty($this->menus)) {
            return ''; // Do nothing if there are no items.
        }
        
        $userInfo = $this->serviceAuth->getIdentity();
        $userrole = $userInfo->role;

        $escapeHtml = $this->getView()->plugin('escapeHtml');
        $result = '<ul class="navigation">';
        foreach ($this->menus as $item) {
            $link       =  isset($item['link']) ? $item['link'] : '#';
            $label      =  isset($item['label']) ? $item['label'] : '';
            $rel        =  isset($item['rel']) ? $item['rel'] : '';
            $controller =  isset($item['controller']) ? $item['controller'] : '';
            $action     =  isset($item['action']) ? $item['action'] : 'index';
           
           if ($this->acl->isAllowed($userrole, $controller, $action)) {
                $result .= '<li>';
                $result .= '<a rel="' . $escapeHtml($rel) . '" href="' . $escapeHtml($link) . '">' . $escapeHtml($label) . '</a>';
                
                $subMenus = '<ul id="' . $rel . '" class="dropmenudiv">';
                    if (isset($item['sub_menus'])) {
                        $subMenus .= $this->renderItem($item['sub_menus']); 
                    }
                $subMenus .= '</ul>'; 
                
                $result .=  $subMenus.'</li>';
            }
        }
        $result .= '</ul>';
        return $result;
    }
    
    /**
     * Renders an item.
     * @param array $item The menu item info.
     * @return string HTML code of the item.
     */
    protected function renderItem($submenus)
    {
        $result = '';
        $escapeHtml = $this->getView()->plugin('escapeHtml');
        foreach ($submenus as $item) {
            $link = isset($item['link']) ? $item['link'] : '#';
            $label = isset($item['label']) ? $item['label'] : '';
            $result .= '<li>';
            $result .= '<a href="' . $escapeHtml($link) . '">' . $escapeHtml($label) . '</a>';
            $result .= '</li>';
        }
        
        return $result;
    }
}