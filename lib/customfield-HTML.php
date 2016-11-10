<?php

$widgetEditor = new CHTMLEditor();
$widgetEditor->Show([
	'name' => $strHTMLControlName["VALUE"],
	'id' => $strHTMLControlName["VALUE"],
	'inputName' => $strHTMLControlName["VALUE"],
	'content' => $value["VALUE"],
	'width' => '100%',
	'minBodyWidth' => !empty($field["COLS"])? $field["COLS"] : 500,
	'normalBodyWidth' => !empty($field["COLS"])? $field["COLS"] : 500,
	'height' => !empty($field["ROWS"])? $field["ROWS"] : 200,
	'bAllowPhp' => false,
	'limitPhpAccess' => false,
	'autoResize' => true,
	'autoResizeOffset' => 40,
	'useFileDialogs' => false,
	'saveOnBlur' => true,
	'showTaskbars' => false,
	'showNodeNavi' => false,
	'askBeforeUnloadPage' => true,
	'bbCode' => false,
	'siteId' => SITE_ID,
	'controlsMap' => [
		['id' => 'Bold', 'compact' => true, 'sort' => 80],
		['id' => 'Italic', 'compact' => true, 'sort' => 90],
		['id' => 'Underline', 'compact' => true, 'sort' => 100],
		['id' => 'Strikeout', 'compact' => true, 'sort' => 110],
		['id' => 'RemoveFormat', 'compact' => true, 'sort' => 120],
		['id' => 'Color', 'compact' => true, 'sort' => 130],
		['id' => 'FontSelector', 'compact' => false, 'sort' => 135],
		['id' => 'FontSize', 'compact' => false, 'sort' => 140],
		['separator' => true, 'compact' => false, 'sort' => 145],
		['id' => 'OrderedList', 'compact' => true, 'sort' => 150],
		['id' => 'UnorderedList', 'compact' => true, 'sort' => 160],
		['id' => 'AlignList', 'compact' => false, 'sort' => 190],
		['separator' => true, 'compact' => false, 'sort' => 200],
		['id' => 'InsertLink', 'compact' => true, 'sort' => 210],
		['id' => 'InsertImage', 'compact' => false, 'sort' => 220],
		['id' => 'InsertVideo', 'compact' => true, 'sort' => 230],
		['id' => 'InsertTable', 'compact' => false, 'sort' => 250],
		['separator' => true, 'compact' => false, 'sort' => 290],
		['id' => 'Fullscreen', 'compact' => false, 'sort' => 310],
		['id' => 'More', 'compact' => true, 'sort' => 400]
	],
]);
