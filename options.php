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

		\Rodzeta\Attribs\Utils::createConfig();

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

<form method="post" action="<?= sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID) ?> type="get">
	<?= bitrix_sessid_post() ?>

	<?php $tabControl->beginNextTab() ?>

	<tr>
		<td class="adm-detail-content-cell-l" width="50%">
			<label>Модуль пока не содержит никаких дополнительный настроек</label>
		</td>
		<td class="adm-detail-content-cell-r" width="50%">
		</td>
	</tr>


	<?php
	 $tabControl->buttons();
  ?>

  <input class="adm-btn-save" type="submit" name="save" value="Применить настройки">

</form>

<?php

$tabControl->end();
