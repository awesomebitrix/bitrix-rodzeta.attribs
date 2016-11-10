﻿
# Модуль Атрибуты элемента инфоблока

## Описание

Данный модуль представляет собой конструктор атрибутов, который позволяет реализовать характеристики элемента инфоблока для указанных разделов (например товаров) на базе одного стандартного свойства "Строка, Множественное значение", а так же использовать данные параметры в фильтре. Актуально для редакции "Первый сайт" с ее ограничением - два инфоблока по два свойства в каждом. Включает пользовательский тип поля "Rodzeta - Настраиваемые атрибуты" и "Rodzeta - Простые атрибуты" (код атрибута - значение).

## Описание установки и настройки решения

Выберите в настройках модуля (вкладка Настройки) инфоблок в котором будет хранится контент сайта, при сохранении настроек свойство для хранения атрибутов создастся автоматически. Создайте список необходимых атрибутов через интерфейс редактирования (вкладка Атрибуты). 

Для свойства инфоблока с пользовательским типом "Rodzeta - Настраиваемые атрибуты" можно использовать максимум 30 разных атрибутов (ограничение bitrix при выводе множественного свойства). Доступны поля типа Селектор галереи / HTML-редактор / Текстовая область и обычное поле ввода.

Для свойства инфоблока с пользовательским типом "Rodzeta - Простые атрибуты" число атрибутов не ограничено (вывода поля при редактировании в виде селектора галереи / HTML-редактора / текстовой области код должен оканчиватся на _GALLERY / _HTML / _TEXT соответсвенно, например - DESCR_HTML, FEATURES_TEXT, IMAGES_GALLERY).

Настройки свойств хранятся в виде файла с php-массивом, что удобно для версионирования и редактирования программистом. При редактировании файла /upload/.rodzeta.attribs.php через фтп или стандартный файловый менеджер bitrix - нажмите в настройке модуля кнопку "Применить настройки".

### Пример для инициализации значений атрибутов в result_modifier.php компонента "Элемент каталога"

    \Rodzeta\Attribs\Init($arResult);

### Пример использования в шаблоне компонента "Элемент каталога"

    <?php foreach ($arResult["PROPERTIES"] as $code => $v) { ?>
        <div>
            <span><?= $v["NAME"] ?>:</span>
            <?= $v["VALUE"] ?> <?= $v["HINT"] ?>
        </div>
    <?php } ?>

### Пример фильтрации по атрибутам для использования в компоненте "Элементы раздела"

    $ids = \Rodzeta\Attribs\Filter::getIds([
        "PRICE" => "10000", // равно
    ]);
    /*
    $ids = \Rodzeta\Attribs\Filter::getIds([
        "PRICE" => "10000;20000", // диапазон
        "ves" => 200, // используем sef-алиасы
    ]);
    $ids = \Rodzeta\Attribs\Filter::getIds($_GET); // из параметров
    */
    if (empty($ids)) {
        $ids = 0;
    }
    $GLOBALS["arrAttribsFilter"]["ID"] = $ids;

    ...

    <?$APPLICATION->IncludeComponent("bitrix:catalog.section", "furniture", 
            ...
            "FILTER_NAME" => "arrAttribsFilter",
            ...

## Описание техподдержки и контактных данных

Тех. поддержка и кастомизация оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.attribs/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.attribs/pulls

## Ссылка на демо-версию

http://villa-mia.ru/
