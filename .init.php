<?php
/***********************************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Attribs;

use Bitrix\Main\Config\Option;

define(__NAMESPACE__ . "\_APP", __DIR__ . "/");
define(__NAMESPACE__ . "\_LIB", __DIR__  . "/lib/");
define(__NAMESPACE__ . "\_FILE_ATTRIBS", "/upload/.rodzeta.attribs.php");
define(__NAMESPACE__ . "\_FILE_ATTRIBS_CSV", "/upload/.rodzeta.attribs.csv");

require _LIB . "encoding/csv.php";

function CreateCache() {
	$basePath = $_SERVER["DOCUMENT_ROOT"];
	$headers = array(
		"CODE",
		"NAME",
		"HINT",
		"ALIAS",
		"SORT",
		"NUMERIC",
		"FILTER",
		"COMPARE",
		"INPUT_TYPE",
		"COLS",
		"ROWS",
		"SECTIONS",
	);
	$attribs = array();
	$sefCodes = array();
	foreach (\Encoding\Csv\Read($basePath . _FILE_ATTRIBS_CSV) as $row) {
		$attribs[$row[0]] = array_combine($headers, $row);
		$attribs[$row[0]]["SORT"] = (int)$attribs[$row[0]]["SORT"];
		// collect sef codes
		if (!empty($attribs[$row[0]]["ALIAS"])) {
			$sefCodes[$attribs[$row[0]]["ALIAS"]] = $row[0];
		}
		// convert sections ids
		if (!empty($attribs[$row[0]]["SECTIONS"])) {
			$attribs[$row[0]]["SECTIONS"] = array_flip(explode(",", $attribs[$row[0]]["SECTIONS"]));
		}
	}

	// ordering by key SORT
	uasort($attribs, function ($a, $b) {
		if ($a["SORT"] == $b["SORT"]) {
			return 0;
		}
		return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
	});

	/*
	$attribs = array();
	$iblockId = Option::get("rodzeta.attribs", "sys_iblock_id", 3);
	$sectionCode = Option::get("rodzeta.attribs", "attribs_section_code");

	if ($sectionCode != "") {
		$res = \CIBlockSection::GetList(
			array("SORT" => "ASC"),
			array(
				"IBLOCK_ID" => $iblockId,
				"CODE" => $sectionCode,
				"ACTIVE" => "Y",
			),
			true,
			array("UF_*")
		);
		$sectionAttribs = $res->GetNext();
		if ($sectionAttribs) {
			$res = \CIBlockSection::GetList(
				array("SORT" => "ASC"),
				array(
					"IBLOCK_ID" => $iblockId,
					"SECTION_ID" => $sectionAttribs["ID"],
					"ACTIVE" => "Y",
				),
				true,
				array("UF_*")
			);
			while ($row = $res->GetNext()) {
				$attribs[$row["CODE"]] = array(
					"ID" => $row["ID"],
					"NAME" => $row["NAME"],
					"CODE" => $row["CODE"],
					"DESCRIPTION" => $row["DESCRIPTION"],
					"DETAIL_PICTURE" => $row["DETAIL_PICTURE"],
					"PICTURE" => $row["PICTURE"],
				);
				// add UF_ fields
				foreach ($row as $k => $v) {
					if (substr($k, 0, 3) == "UF_") {
						$attribs[$row["CODE"]][substr($k, 3)] = $row["~" . $k];
					}
				}
				if (!empty($attribs[$row["CODE"]]["ALIAS"])
							&& trim($attribs[$row["CODE"]]["ALIAS"]) != "") {
					$sefCodes[$attribs[$row["CODE"]]["ALIAS"]] = $row["CODE"];
				}
				if (!empty($attribs[$row["CODE"]]["SECTIONS"])) {
					$attribs[$row["CODE"]]["SECTIONS"] = array_flip($attribs[$row["CODE"]]["SECTIONS"]);
				}
			}
		}
	}*/

	file_put_contents(
		$basePath . _FILE_ATTRIBS,
		"<?php\nreturn " . var_export(array($attribs, $sefCodes), true) . ";"
	);
}

function Config() {
	return include $_SERVER["DOCUMENT_ROOT"] . _FILE_ATTRIBS;
}

function Init(&$item) {
	if (empty($item["PROPERTIES"]["ATTRIBS"])) {
		return;
	}
	if (!empty($item["DISPLAY_PROPERTIES"]["ATTRIBS"])) {
		unset($item["DISPLAY_PROPERTIES"]["ATTRIBS"]);
	}
	$attribs = &$item["PROPERTIES"]["ATTRIBS"];
	$tmp = array();
	foreach ($attribs["~VALUE"] as $i => $v) {
		if (!empty($attribs["DESCRIPTION"][$i])) {
			$tmp[$attribs["DESCRIPTION"][$i]] = $v;
		}
	}
	// sort
	static $config = null;
	if (empty($config)) {
		list($config) = Config();
	}
	foreach ($config as $code => $v) {
		if (isset($tmp[$code])) {
			$item["PROPERTIES"][$code] = array(
				"CODE" => &$config[$code]["CODE"],
				"NAME" => &$config[$code]["NAME"],
				"HINT" => &$config[$code]["UNIT"],
				"VALUE" => $tmp[$code],
			);
			$item["PROPERTIES"][$code]["~VALUE"] = &$item["PROPERTIES"][$code]["VALUE"];
		}
	}
	unset($item["PROPERTIES"]["ATTRIBS"]);
	//unset($item["PROPERTIES"]["LINKS"]);
}

function BuildTree(&$elements, $parentId = 0) {
	$branch = array();
	foreach ($elements as &$element) {
		if ($element["PARENT_ID"] == $parentId) {
			$children = BuildTree($elements, $element["ID"]);
			if ($children) {
				$element["CHILDREN"] = $children;
			}
			$branch[$element["ID"]] = $element;
			unset($element);
		}
	}
	return $branch;
}

function PrintTree($elements, &$result, $level = 0) {
	foreach ($elements as $element) {
		$result[$element["ID"]] = str_repeat(" -", $level) . " " . $element["NAME"];
		PrintTree($element["CHILDREN"], $result, $level + 1);
	}
}

function AppendValues($data, $n, $v) {
	for ($i = 0; $i < $n; $i++) {
		$data[] = $v;
	}
	return $data;
}

function SectionsTreeList($currentIblockId) {
	$resSections = \CIBlockSection::GetTreeList(
		array("IBLOCK_ID" => $currentIblockId),
		array("ID", "NAME", "DEPTH_LEVEL")
	);
	$sections = array();
	while ($section = $resSections->GetNext()) {
	  $sections[$section["ID"]] = str_repeat(" . ", $section["DEPTH_LEVEL"] - 1) . $section["NAME"];
	}
	return $sections;
}
