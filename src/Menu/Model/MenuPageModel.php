<?php

namespace Menu\Model;

use Core\Model\DefaultModel;
use Core\Entity\Menu;
use Translate\Model\TranslateModel;

class MenuPageModel extends DefaultModel {

    public function getMenuPages(Menu $menu) {
        $menu_id = $menu->getId();
        $sql = 'SELECT parent.id,parent.uri,child.id AS page_id,child.uri AS page_uri,child.icon_class AS page_icon_class,parent.icon_class AS icon_class FROM menu_page parent
                LEFT JOIN menu_page child ON (parent.id = child.page_id)
                WHERE parent.menu_id = :menu_id AND parent.page_id IS NULL';
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindParam('menu_id', $menu_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $this->formatMenuPages($stmt->fetchAll(\PDO::FETCH_ASSOC)? : array());
    }

    protected function formatMenuPages(array $pages) {
        $return = [];
        foreach ($pages AS $page) {
            $return[$page['id']]['id'] = $page['id'];
            $return[$page['id']]['uri'] = $page['uri'];
            $return[$page['id']]['icon_class'] = $page['icon_class'];
            $return[$page['id']]['label'] = $this->translateMenuPage($page['id']);
            if ($page['page_id']) {
                $return[$page['id']]['pages'][] = array(
                    'id' => $page['page_id'],
                    'uri' => $page['page_uri'],
                    'icon_class' => $page['page_icon_class'],
                    'label' => $this->translateMenuPage($page['page_id'])
                );
            }
        }
        return $return;
    }

    protected function translateMenuPage($menu_page_id) {
        $translateModel = new TranslateModel();
        $translateModel->initialize($this->serviceLocator);
        $translateModel->setEntity('Core\Entity\TranslateMenuPage');
        return $translateModel->translate($menu_page_id);
    }

}
