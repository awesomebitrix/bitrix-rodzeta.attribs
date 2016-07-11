<?php
/***********************************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Attribs;

final class Utils {

	const MAP_NAME = "/upload/cache.rodzeta.attribs.php";
	const SRC_NAME = "/upload/rodzeta.attribs.csv";

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
			list($config) = self::getConfig();
		}
		foreach ($config as $code => $v) {
			if (isset($tmp[$code])) {
				$item["ATTRIBS"][$code] = $tmp[$code];
			}
		}
	}

	static function createCache() {
		$basePath = $_SERVER["DOCUMENT_ROOT"];

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

		file_put_contents(
			$basePath . self::MAP_NAME,
			"<?php\nreturn " . var_export(array($attribs, $sefCodes), true) . ";"
		);
	}

	static function getConfig() {
		return include $_SERVER["DOCUMENT_ROOT"] . self::MAP_NAME;
	}

}
