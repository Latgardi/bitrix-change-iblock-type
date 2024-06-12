<?php

use Bitrix\ {
    Main\EventManager,
    Main\ModuleManager,
    Main\Localization\Loc
};
use Latgardi\ChangeIblockType\EventHandler;

class latgardi_changeiblocktype extends CModule
{
    public function __construct()
    {
        $version = include __DIR__ . '/version.php' ?? null;
        if ($version !== null) {
            $this->MODULE_VERSION = $version['number'];
            $this->MODULE_VERSION_DATE = $version['date'];
        }
        $this->MODULE_NAME = Loc::getMessage('MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_DESCRIPTION');
        $this->MODULE_ID = basename(dirname(__DIR__));
    }

    public function doInstall(): void
    {
        try {
            ModuleManager::registerModule($this->MODULE_ID);
            EventManager::getInstance()->registerEventHandler(
                'main',
                'OnAdminContextMenuShow',
                $this->MODULE_ID,
                EventHandler::class,
                'onAdminContextMenuShowHandler'
            );
            EventManager::getInstance()->registerEventHandler(
                'main',
                'OnEndBufferContent',
                $this->MODULE_ID,
                EventHandler::class,
                'onEndBufferContentHandler'
            );
        } catch (Exception $e) {
            ShowError($e);
        }
    }

    public function doUninstall(): void
    {
        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnAdminContextMenuShow',
            $this->MODULE_ID,
            EventHandler::class,
            'onAdminContextMenuShowHandler'
        );
        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnEndBufferContent',
            $this->MODULE_ID,
            EventHandler::class,
            'onEndBufferContentHandler'
        );

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}