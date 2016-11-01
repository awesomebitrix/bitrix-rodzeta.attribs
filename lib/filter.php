<?php
/***********************************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

namespace Rodzeta\Attribs;

use \Bitrix\Main\Application;
use \Bitrix\Main\Config\Option;

final class Filter {

	static function getExprEqual($name, $value, $useIn = false, $convertInt = false) {
		$helper = Application::getConnection()->getSqlHelper();
		if (!is_array($value)) {
			return $name . " = '" . $helper->forSql($value) . "'";
		}
		$result = array();
		if ($useIn) {
			foreach ($value as $v) {
				$result[] = $convertInt? (int)$v : ("'" . $helper->forSql($v) . "'");
			}
			return $name . " in (" . implode(",", $result) . ")";
		}
		foreach ($value as $v) {
			$result[] = $name . " = '" . $helper->forSql($v) . "'";
		}
		return "(" . implode(" or ", $result) . ")";
	}

	static function getExprFilter(&$query, $iblockId, $propertyId, $code, $value, $isNumeric = true) {
		$helper = Application::getConnection()->getSqlHelper();
		$alias = "`" . $helper->forSql("f_" . $code) . "`";
		$query["join"][] =  "left join `" .
				$helper->forSql("b_iblock_element_prop_m" . (int)$iblockId) . "` as " . $alias . "
			on p.ID = " . $alias . ".IBLOCK_ELEMENT_ID
				and " . $alias . ".DESCRIPTION = '" . $helper->forSql($code) . "'
				and " . $alias . ".IBLOCK_PROPERTY_ID = " . (int)$propertyId . "\n";

		if (!empty($query["debug"])) {
			$query["columns"][] = $alias . ".VALUE as `" . $helper->forSql("val_" . $code) . "`";
		}

		if ($isNumeric) {
			if (count($value) == 2) { // range
				$where = array();
				if ($value[0] !== null && $value[0] !== "") {
					$where[] = $alias . ".VALUE_NUM >= " . $helper->forSql((float)$value[0]);
				}
				if ($value[1] !== null && $value[1] !== "") {
					$where[] = $alias . ".VALUE_NUM <= " . $helper->forSql((float)$value[1]);
				}
				$query["where"][] = "(" . $alias . ".VALUE_NUM is null or (" . implode(" and ", $where) . "))";
				//$query["where"][] = "(" . implode(" and ", $where) . ")";
			} else { // single numeric value
				$query["where"][] = self::getExprEqual($alias . ".VALUE_NUM", (float)$value);
			}
		} else { // single value
			$query["where"][] = self::getExprEqual($alias . ".VALUE", filter_var($value, FILTER_SANITIZE_STRING));
		}
	}

	static function getSql($query) {
		return "select " .
				(!empty($query["columns"])?
					implode(",", $query["columns"]) : "*") . "\n" .
			"from " . $query["from"] . "\n" .

			(count($query["join"])?
				implode("\n  ", $query["join"]) : "") . "\n" .

			(count($query["where"])?
				("where " . implode("\n  and ", $query["where"])) : "") . "\n" .

			(count($query["group"])?
				("group by " . $query["group"]) : "") . "\n" .

			(count($query["having"])?
				("having " . implode("\n  and ", $query["having"])) : "") . "\n" .

			(count($query["order"])?
				("order by " . implode(",", $query["order"])) : "") . "\n" .

			(count($query["limit"])?
				("limit " . implode(",", $query["limit"])) : "") . "\n";
	}

	static function getIds($params, $elementFields = array(), $iblockId = null, $propertyId = null) {
		$conn = Application::getConnection();
		list($attribs, $aliases) = Config();
		$result = array();

		$query = array(
			//"debug" => true,
			"columns" => array("p.ID as id"),
			"from" => "b_iblock_element as p",
			"where" => array()
		);

		// add additional filter (by element ID and etc.)
		foreach ($elementFields as $field => $value) {
			$query["where"][] = self::getExprEqual($field, $value, true, true);
		}

		$hasFilterAttribs = false;
		foreach ($params as $k => $value) {
			$code = isset($aliases[$k])? $aliases[$k] : $k; // get code by sef alias
			if (empty($attribs[$code]["FILTER"])) {
				continue;
			}
			$hasFilterAttribs = true;
			// check and process range value
			$tmp = explode(";", $value);
			if (count($tmp) > 1) {
				$value = $tmp;
			}
			self::getExprFilter(
				$query,
				!empty($iblockId)? $iblockId : Option::get("rodzeta.attribs", "iblock_id", 2),
				!empty($propertyId)? $propertyId : Option::get("rodzeta.attribs", "property_id", 2),
				$code,
				$value,
				!empty($attribs[$code]["NUMERIC"])
			);
		}
		if (!$hasFilterAttribs) {
			return null; // don't use filter
		}

		$sql = self::getSql($query);
		//var_dump($sql);
		$res = $conn->query($sql);
		while ($row = $res->fetch()) {
			$result[] = $row["id"];
		}
		return $result;
	}

}