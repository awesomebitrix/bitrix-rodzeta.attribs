<?php
/***********************************************************************************************
 * rodzeta.attribs - Infoblock element attributes
 * Copyright 2016 Semenov Roman
 * MIT License
 ************************************************************************************************/

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\String;
use Bitrix\Main\Loader;

if (!$USER->isAdmin()) {
	$APPLICATION->authForm("ACCESS DENIED");
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

$currentIblockId = Option::get("rodzeta.attribs", "iblock_id", 2);

Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl("tabControl", array(
  array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("RODZETA_ATTRIBS_MAIN_TAB_SET"),
		"TITLE" => Loc::getMessage("RODZETA_ATTRIBS_MAIN_TAB_TITLE_SET"),
  ),
));

?>

<?= BeginNote() ?>
<p>
	<b>Как работает</b>
	<ul>
		<li>загрузите или создайте файл <b><a href="<?= \Rodzeta\Attribs\Utils::SRC_NAME ?>">rodzeta.attribs.csv</a></b> в папке /upload/ с помощью
			<a target="_blank" href="/bitrix/admin/fileman_file_edit.php?path=<?=
					urlencode(\Rodzeta\Attribs\Utils::SRC_NAME) ?>">стандартного файлового менеджера</a>;
		<li>после изменений в файле rodzeta.attribs.csv - нажмите в настройке модуля кнопку "Применить настройки";
	</ul>
</p>
<?= EndNote() ?>

<?php

if ($request->isPost() && check_bitrix_sessid()) {
	if (!empty($save) || !empty($restore)) {
		Option::set("rodzeta.attribs", "iblock_id", (int)$request->getPost("iblock_id"));
		Option::set("rodzeta.attribs", "property_id", (int)$request->getPost("property_id"));

		\Rodzeta\Attribs\Utils::createCache();

		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_ATTRIBS_OPTIONS_SAVED"),
	    "TYPE" => "OK",
	  ));
	}	/*else if ($request->getPost("clear") != "") {


		CAdminMessage::showMessage(array(
	    "MESSAGE" => Loc::getMessage("RODZETA_ATTRIBS_OPTIONS_RESETED"),
	    "TYPE" => "OK",
	  ));
	} */
}

$tabControl->begin();

?>

<script>

function RodzetaSettingsAttribsUpdate() {
	var $selectIblock = document.getElementById("iblock_id");
	var $selectProperty = document.getElementById("rodzeta-attribs-property-id");
	var iblockId = $selectIblock.value;
	var selectedOption = $selectProperty.getAttribute("data-value");

	BX.ajax.loadJSON("/bitrix/admin/rodzeta.attribs/optionsproperties.php?iblock_id=" + iblockId, function (data) {
		var html = ["<option value=''>(выберите свойство)</option>"];
		for (var k in data) {
			var selected = selectedOption == k? "selected" : "";
			html.push("<option " + selected + " value='" + k + "'>[" + k + "] " + data[k] + "</option>");
		}
		$selectProperty.innerHTML = html.join("\n");
	});
};

</script>

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?>" type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr class="heading">
		<td colspan="2">Настройки для фильтра по атрибутам</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Инфоблок контента</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<?= GetIBlockDropDownListEx(
				$currentIblockId,
				"iblock_type_id",
				"iblock_id",
				array(
					"MIN_PERMISSION" => "R",
				),
				"",
				"RodzetaSettingsAttribsUpdate()"
			) ?>
		</td>
	</tr>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Свойство "Характеристики"</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
			<select name="property_id" id="rodzeta-attribs-property-id" data-value="<?= Option::get("rodzeta.attribs", "property_id", 2) ?>">
				<option value="">(выберите свойство)</option>
				<?php
				/*
				$currentOption = Option::get("rodzeta.attribs", "property_id", 2);
				foreach ($optionsProperties as $k => $v) {
					$selected = $currentOption == $k? "selected" : "";
				?>
					<option <?= $selected ?> value="<?= $k ?>">[<?= $k ?>] <?= $v ?></option>
				<?php } */ ?>
			</select>
		</td>
	</tr>

	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<script>

RodzetaSettingsAttribsUpdate();

</script>

<?php

$tabControl->end();
