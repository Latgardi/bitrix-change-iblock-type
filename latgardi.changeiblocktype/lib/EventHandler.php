<?php

namespace Latgardi\ChangeIblockType;

use Bitrix\ {
    Iblock\ORM\Query,
    Iblock\TypeTable,
    Main\SystemException
};
use CModule;

class EventHandler
{
    public static function onAdminContextMenuShowHandler(array &$menu): bool
    {
        global $APPLICATION;
        global $USER;

        if (!CModule::IncludeModule("iblock") || !$USER->IsAdmin()) {
            return false;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $APPLICATION->GetCurPage() === '/bitrix/admin/iblock_edit.php') {
            $types = self::getIBlockTypeList($_GET['type']);
            self::menuAction($menu, $types);
        }

        return true;
    }

    public static function onEndBufferContentHandler(&$content): void
    {
        global $APPLICATION;

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $APPLICATION->GetCurPage() === '/bitrix/admin/iblock_edit.php') {
            $scriptSrc = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])) . '/script.js';
            $script = "<script src='$scriptSrc'></script>";
            $content .= $script;
            $content .= "<style>#bx-admin-prefix .bx-core-popup-menu-no-icons .bx-core-popup-menu-item-text { padding-left: 13px }</style>";
        }
    }

    private static function menuAction(array &$menu, ?array $types): void
    {
        if (!is_null($types)) {
            $button = [
                "ICON" => "btn_green",
                "TEXT" => "Изменить тип инфоблока",
                "MENU" => self::createMenuItems($types),
            ];
            if (count($menu) > 1) {
                array_splice($menu, 1, 0, [$button]);
            } else {
                $menu[] = $button;
            }
        }
    }

    private static function createMenuItems(array $types): array
    {
        $result = [];
        foreach ($types as $id => $type) {
            $title = "$type ($id)";
            $result[] = [
                "HTML" => "
                    <span onclick='changeType(\"$id\")'>
                        <span class='adm-submenu-item-link-icon iblock_menu_icon_types'></span>
                        <span class='change-iblock-type-link'>$title</span>
                    </span>
                    "
            ];
        }
        return $result;
    }

    private static function getIBlockTypeList(?string $excludeType = null): ?array
    {
        $types = [];
        try {
            $query = new Query(TypeTable::getEntity());
            $query
                ->setSelect(['ID', 'NAME' => 'LANG_MESSAGE.NAME'])
                ->where('LANG_MESSAGE.LANGUAGE_ID', 'ru');

            if ($excludeType) {
                $query->whereNot('ID', $excludeType);
            }

            $result = $query->exec();
            while ($type = $result->fetch()) {
                $types[$type['ID']] = $type['NAME'];
            }
        } catch (SystemException $e) {
            return null;
        }
        return $types;
    }
}