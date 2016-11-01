<?php
/***********************************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Attribs;

final class Utils {

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
