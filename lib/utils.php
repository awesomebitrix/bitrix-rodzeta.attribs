<?php
/***********************************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Attribs;

use Bitrix\Main\Config\Option;

final class Utils {

	const MAP_NAME = "/upload/cache.rodzeta.attribs.php";
	//const SRC_NAME = "/upload/rodzeta.attribs.csv";

	static function init(&$item) {
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
			list($config) = self::get();
		}
		foreach ($config as $code => $v) {
			if (isset($tmp[$code])) {
				$item["ATTRIBS"][$code] = array(
					"FIELD" => &$config[$code],
					"VALUE" => $tmp[$code],
				);
			}
		}
	}

	static function createCache() {
		$basePath = $_SERVER["DOCUMENT_ROOT"];

		/*
		$fcsv = fopen($basePath . self::SRC_NAME, "r");
		if ($fcsv === FALSE) {
			return;
		}

		$attribs = array();
		$headers = array();
		$i = 0;
		while (($row = fgetcsv($fcsv, 4000, "\t")) !== FALSE) {
			$i++;
			if ($i == 1) {
				$headers = array_map("trim", $row);
				continue;
			}
			$row = array_map("trim", $row);
			$attribs[$row[0]] = array_combine($headers, $row);
			$attribs[$row[0]]["SORT"] = (int)$attribs[$row[0]]["SORT"];
		}
		fclose($fcsv);

		// ordering by key SORT
		uasort($attribs, function ($a, $b) {
			if ($a["SORT"] == $b["SORT"]) {
				return 0;
			}
			return ($a["SORT"] < $b["SORT"]) ? -1 : 1;
		});

		// collect sef codes
		$sefCodes = array();
		foreach ($attribs as $code => $v) {
			if (trim($v["SEF_CODE"]) != "") {
				$sefCodes[$v["SEF_CODE"]] = $code;
			}
		}
		*/

		$attribs = array();
		$sefCodes = array();
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
		}

		file_put_contents(
			$basePath . self::MAP_NAME,
			"<?php\nreturn " . var_export(array($attribs, $sefCodes), true) . ";"
		);
	}

	static function get() {
		return include $_SERVER["DOCUMENT_ROOT"] . self::MAP_NAME;
	}

	static function buildTree(&$elements, $parentId = 0) {
		$branch = array();
		foreach ($elements as &$element) {
			if ($element["PARENT_ID"] == $parentId) {
				$children = self::buildTree($elements, $element["ID"]);
				if ($children) {
					$element["CHILDREN"] = $children;
				}
				$branch[$element["ID"]] = $element;
				unset($element);
			}
		}
		return $branch;
	}

	static function printTree($elements, &$result, $level = 0) {
		foreach ($elements as $element) {
			$result[$element["ID"]] = str_repeat(" -", $level) . " " . $element["NAME"];
			self::printTree($element["CHILDREN"], $result, $level + 1);
		}
	}

}
