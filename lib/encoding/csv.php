<?php

namespace Encoding\Csv;

if (!function_exists("\Encoding\Csv\Write")) {

function Write($fname, $data, $sep = "\t") {
	$f = fopen($fname, "w");
	if ($f === false) {
		return;
	}
	foreach ($data as $row) {
		$row = array_map("trim", $row);
		if (count(array_filter($row)) == 0) {
			continue;
		}
		fputcsv($f, $row, $sep);
	}
	fclose($f);
}

function Read($fname, $sep = "\t") {
	$result = array();
	$f = fopen($fname, "r");
	if ($f === false) {
		return $result;
	}
	while (($row = fgetcsv($f, 4000, $sep)) !== false) {
		$result[] = array_map("trim", $row);
	}
	fclose($f);
	return $result;
}

}
