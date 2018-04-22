<?php

namespace Menu\Model;

use Core\Model\DefaultModel;
use Zend\Navigation\Navigation;
use Company\Model\CompanyModel;

class MenuModel extends DefaultModel {

    private static $_menus;

    public static function addMenu($menu) {
        self::$_menus[] = $menu;
    }

    protected function getPages($menu) {
        $companyModel = new CompanyModel();
        $companyModel->initialize($this->serviceLocator);
        $menuData = $this->entity->findOneBy(array('menu' => $menu, 'people' => $companyModel->getLoggedPeopleCompany()));
        if ($menuData) {
            $menuPageModel = new MenuPageModel();
            $menuPageModel->initialize($this->serviceLocator);
            $pages = $menuPageModel->getMenuPages($menuData);
        }
        return $pages ? : array();
    }

    public function getPagesFromPeople($admin = false) {
        $companyModel = new CompanyModel();
        $companyModel->initialize($this->serviceLocator);
        $menuData = $this->entity->findOneBy(
                array(
                    'admin' => $admin,
                    'people' => $companyModel->getDefaultCompany()
                )
        );
        if ($menuData) {
            $menuPageModel = new MenuPageModel();
            $menuPageModel->initialize($this->serviceLocator);
            $pages = $menuPageModel->getMenuPages($menuData);
        }
        return $pages ? : array();
    }

    protected function getMenus() {
        $menus = array();
        if (is_array(self::$_menus)) {
            foreach (self::$_menus AS $menu) {
                $menus[$menu] = $this->getPages($menu);
            }
        }
        return $menus;
    }

    public function __destruct() {
        /*
          $menus = $this->getMenus();
          foreach ($menus AS $key => $menu) {
          $navigation = new Navigation();
          $this->serviceLocator->setService($key, $navigation);
          $this->serviceLocator->get($key)->addPages($menu);
          }
         * 
         */
    }

}
