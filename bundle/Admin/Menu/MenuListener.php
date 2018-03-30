<?php

namespace Netgen\Bundle\InformationCollectionBundle\Admin\Menu;

use EzSystems\EzPlatformAdminUi\Menu\Event\ConfigureMenuEvent;
use EzSystems\EzPlatformAdminUi\Menu\MainMenuBuilder;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use JMS\TranslationBundle\Model\Message;

class MenuListener implements TranslationContainerInterface
{
    const ITEM_INFORMATION_COLLECTION = 'main_information_collection';
    /**
     * @param ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        $contentMenu = $menu->getChild(MainMenuBuilder::ITEM_ADMIN);
        $contentMenu->addChild(
            self::ITEM_INFORMATION_COLLECTION, [
                'route' => 'netgen_information_collection.admin.list'
            ]
        );
    }
    /**
     * @return array
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_INFORMATION_COLLECTION, 'messages'))->setDesc('Information collection'),
        ];
    }
}
