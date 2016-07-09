
# Модуль Атрибуты элемента инфоблока

## Описание

Данный модуль содержит набор функций, который позволяют реализовать характеристики элемента (например товаров) на базе одного стандартного свойства "Строка, Множественное значение", а так же использовать данные параметры в фильтре.

## Особенности

- имеет смысл для редакции "Первый сайт" с ее ограничением - два инфоблока по два свойства в каждом;
- коды характеристик указываются в описании значения поля;
- хранение настроек характеристик в php массиве, который может кешироватся opcache и не создает лишних нагрузок на БД;
- несложное администрирование и хранение настроек в csv файле.

## Как работает

- загрузите или создайте файл rodzeta.attribs.csv в папке /upload/ с помощью стандартного файлового менеджера Bitrix или по FTP;
- формат файла: в примере ниже указаны основные параметры характеристики, но возможно любое кол-во таких параметров (доступ к значению будет осуществлятся по коду параметра который указывается в первой строке csv файла);
- после изменений в файле rodzeta.attribs.csv - нажмите в настройке модуля кнопку "Применить настройки".

### Пример для инициализации значений атрибутов в result_modifier.php компонента "Элемент каталога"

    \Rodzeta\Attribs\Utils::init($arResult);
    list($arResult["ATTRIBS_CONFIG"]) = \Rodzeta\Attribs\Utils::getConfig();

### Пример использования в шаблоне компонента "Элемент каталога"

    <?php foreach ($arResult["ATTRIBS"] as $code => $value) { ?>
        <div>
            <span><?= $arResult["ATTRIBS_CONFIG"][$code]["NAME"] ?>:</span>
            <?= $value ?> <?= $arResult["ATTRIBS_CONFIG"][$code]["UNIT"] ?>
        </div>
    <?php } ?>

### Пример содержимого файла rodzeta.attribs.csv

    CODE NAME    UNIT    SEF_CODE    SORT
    PRICE   Цена    руб.    tzena   1000
    WEIGHT  Вес кг  ves 200
    WIDTH   Ширина  м   shirina 300
    HEIGHT  Высота  м   visota  400
    FEATURES    Особенности товара          100

### Пример фильтрации по атрибутам для использования в компоненте "Элементы раздела"

    $ids = \Rodzeta\Attribs\Filter::getIds(array(
        "PRICE" => "10000", // равно
    ));
    /*
    $ids = \Rodzeta\Attribs\Filter::getIds(array(
        "PRICE" => "10000;20000", // диапазон
        "ves" => 200, // используем sef-алиасы
    ));
    $ids = \Rodzeta\Attribs\Filter::getIds($_GET); // из параметров
    */
    if (empty($ids)) {
        $ids = 0;
    }
    $GLOBALS["arrAttribsFilter"]["ID"] = $ids;

    ...

    <?$APPLICATION->IncludeComponent("bitrix:catalog.section", "furniture", 
        array(
            ...
            "FILTER_NAME" => "arrAttribsFilter",
            ...

## Демо сайт

http://villa-mia.ru/

## Тех. поддержка и кастомизация

Оказывается на платной основе, e-mail: rivetweb@yandex.ru

Багрепорты и предложения на https://github.com/rivetweb/bitrix-rodzeta.attribs/issues

Пул реквесты на https://github.com/rivetweb/bitrix-rodzeta.attribs/pulls
