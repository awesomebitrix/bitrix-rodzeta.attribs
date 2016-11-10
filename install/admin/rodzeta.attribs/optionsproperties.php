<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";

\CModule::IncludeModule("iblock");

$currentIblockId = $_GET["iblock_id"];

$resProps = \CIBlockProperty::GetList(
	["SORT" => "ASC"],
	[
		"IBLOCK_ID" => $currentIblockId,
		"ACTIVE" => "Y",
	]
);
$optionsProperties = [];
while ($prop = $resProps->GetNext()) {
  $optionsProperties[$prop["ID"]] = $prop["NAME"];
}

echo json_encode($optionsProperties);
