<?php
/***********************************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

require __DIR__ . "/.init.php";

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;
use Bitrix\Main\Config\Option;

Loader::includeModule("iblock");

EventManager::getInstance()->addEventHandler(
	"iblock",
	"OnIBlockPropertyBuildList",
	array("\Rodzeta\Attribs\Customfield", "GetUserTypeDescription")
);

EventManager::getInstance()->addEventHandler(
	"iblock",
	"OnIBlockPropertyBuildList",
	array("\Rodzeta\Attribs\Attribsfield", "GetUserTypeDescription")
);
