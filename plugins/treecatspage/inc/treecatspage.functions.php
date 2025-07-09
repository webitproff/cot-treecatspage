<?php

/**
 * File treecatspage.functions.php for Tree Cats Page
 *
 * @package treecatspage
 * @copyright (c) Cotonti
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */


// Определение константы для проверки корректного доступа к файлу
defined('COT_CODE') or die('Неверный URL.');

// Подключение языкового файла для модуля страниц
require_once cot_langfile('page', 'module');
// Подключение основного файла модуля страниц
require_once cot_incfile('page', 'module');
// Подключение файла функций модуля страниц
require_once cot_incfile('page', 'module', 'functions');
// Подключение файла ресурсов модуля страниц
require_once cot_incfile('page', 'module', 'resources');
// Подключение файла дополнительных полей
require_once cot_incfile('extrafields');

// Импорт кода категории из GET-параметров
$c = cot_import('c', 'G', 'TXT'); // код категории

/**
 * Формирует иерархическую структуру дерева категорий страниц
 *
 * @param string $parent Код родительской категории, пустой для корневого уровня (404)
 * @param string|array $selected Код(ы) выбранной категории для подсветки (строка или массив)
 * @param int $level Текущий уровень в иерархии категорий
 * @param string $template Файл шаблона для использования (зарезервировано)
 * @return string|bool Отрендеренный HTML для дерева категорий или false, если нет дочерних элементов
 */
function cot_build_structure_page_tree($parent = '', $selected = '', $level = 0, $template = '')
{
    // Доступ к глобальным переменным для конфигурации, базы данных и системных данных
    global $structure, $cfg, $db, $sys, $cot_extrafields, $db_structure, $db_pages;
    // Доступ к глобальным переменным для поддержки интернационализации
    global $i18n_notmain, $i18n_locale, $i18n_write, $i18n_admin, $i18n_read, $db_i18n_pages;

    // Получение конфигурации черного списка категорий для исключения
    $blacklist_cfg = $cfg['plugin']['treecatspage']['blacktreecatspage'] ?? '';
    // Преобразование конфигурации черного списка в массив
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    // Инициализация массива параметров URL
    $urlparams = [];

    // Выполнение плагинов, подключенных к событию page.tree.first
	/* === Hook === */
	foreach (cot_getextplugins('page.tree.first') as $pl)
	{
		include $pl; // Подключение файла плагина
	}
	/* ===== */

    // Проверка, является ли родительская категория пустой для обработки корневых категорий
    if (empty($parent))
    {
        // Установка флага поддержки i18n для корневого уровня
        $i18n_enabled = $i18n_read;
        // Инициализация массива дочерних категорий
        $children = [];
        // Получение всех дочерних категорий для модуля страниц
        $allcat = cot_structure_children('page', '');
        // Перебор всех категорий
        foreach ($allcat as $x)
        {
            // Проверка, что категория находится на корневом уровне и не в черном списке
            if (
                mb_substr_count($structure['page'][$x]['path'], ".") == 0 &&
                !in_array($x, $blacklist)
            ) {
                // Добавление подходящей категории в массив дочерних
                $children[] = $x;
            }
        }
    }
    // Обработка категорий не корневого уровня
    else
    {
        // Проверка, включена ли поддержка i18n для родительской категории
        $i18n_enabled = $i18n_read && cot_i18n_enabled($parent);
        // Фильтрация подкатегорий, исключая те, что в черном списке
        $children = array_filter($structure['page'][$parent]['subcats'] ?? [], function($cat) use ($blacklist) {
            // Возвращает true для категорий, не входящих в черный список
            return !in_array($cat, $blacklist);
        });
    }

    // Загрузка файла шаблона для дерева категорий
    $mskin = cot_tplfile('treecatspage.page.tree', 'plug');
    // Инициализация объекта XTemplate с шаблоном
    $t1 = new XTemplate($mskin);

    // Выполнение плагинов, подключенных к событию page.tree.main
	/* === Hook === */
	foreach (cot_getextplugins('page.tree.main') as $pl)
	{
		include $pl; // Подключение файла плагина
	}
	/* ===== */


    // Проверка, есть ли дочерние категории
    if (count($children) == 0)
    {
        // Возврат false, если дочерних категорий нет
        return false;
    }

    // Инициализация общего количества страниц
    $total_count = 0;
    // Проверка существования таблицы страниц в базе данных
    if ($db->tableExists($db_pages)) {
        // Запрос общего количества опубликованных страниц
        $result = $db->query("SELECT COUNT(*) AS total FROM $db_pages WHERE page_state = 0")->fetch();
        // Установка общего количества из результата запроса
        $total_count = $result['total'] ?? 0;
    }

    // Инициализация переменной заголовка
    $title = '';
    // Инициализация переменной описания
    $desc = '';
    // Инициализация переменной счетчика
    $count = 0;
    // Инициализация переменной иконки
    $icon = '';
    // Проверка, существует ли родительская категория и установлена ли она в структуре
    if (!empty($parent) && isset($structure['page'][$parent])) {
        // Установка заголовка из родительской категории
        $title = $structure['page'][$parent]['title'];
        // Установка описания из родительской категории
        $desc  = $structure['page'][$parent]['desc'];
        // Установка счетчика из родительской категории
        $count = $structure['page'][$parent]['count'];
        // Установка иконки из родительской категории
        $icon  = $structure['page'][$parent]['icon'];
    }

    // Назначение переменных шаблона
    $t1->assign([
        // Назначение экранированного заголовка
        "TITLE" => htmlspecialchars($title),
        // Назначение описания
        "DESC" => $desc,
        // Назначение счетчика
        "COUNT" => $count,
        // Назначение иконки
        "ICON" => $icon,
        // Генерация URL для родительской категории
        "HREF" => cot_url("page", $urlparams + ['c' => $parent]),
        // Назначение текущего уровня
        "LEVEL" => $level,
        // Назначение общего количества страниц
        "TOTAL_COUNT" => $total_count,
    ]);

    // Инициализация счетчика цикла
    $jj = 0;
	
    // Получение плагинов для события page.tree.loop
	/* === Hook - Part1 : Set === */
	$extp = cot_getextplugins('page.tree.loop');
	/* ===== */
	
    // Перебор дочерних категорий
    foreach ($children as $row)
    {
        // Пропуск категорий, находящихся в черном списке
        if (in_array($row, $blacklist)) {
            // Переход к следующей итерации
            continue;
        }

        // Инкремент счетчика цикла
        $jj++;
        // Установка кода категории в параметры URL
        $urlparams['c'] = $row;
        // Фильтрация подкатегорий текущей категории
        $subcats = !empty($structure['page'][$row]['subcats']) ? array_filter($structure['page'][$row]['subcats'], function($cat) use ($blacklist) {
            // Возвращает true для подкатегорий, не входящих в черный список
            return !in_array($cat, $blacklist);
        }) : [];

        // Назначение переменных шаблона для текущей категории
        $t1->assign([
            // Назначение идентификатора категории
            "ROW_ID" => $row,
            // Назначение экранированного заголовка категории
            "ROW_TITLE" => htmlspecialchars($structure['page'][$row]['title']),
            // Назначение описания категории
            "ROW_DESC" => $structure['page'][$row]['desc'],
            // Назначение счетчика категории
            "ROW_COUNT" => $structure['page'][$row]['count'],
            // Назначение иконки категории
            "ROW_ICON" => $structure['page'][$row]['icon'],
            // Генерация URL для категории
            "ROW_HREF" => cot_url("page", $urlparams),
            // Проверка, выбрана ли категория
            "ROW_SELECTED" => ((is_array($selected) && in_array($row, $selected)) || (!is_array($selected) && $row == $selected)) ? 1 : 0,
            // Рекурсивное построение дерева подкатегорий
            "ROW_SUBCAT" => !empty($subcats) ? cot_build_structure_page_tree($row, $selected, $level + 1) : '',
            // Назначение текущего уровня
            "ROW_LEVEL" => $level,
            // Генерация класса для чётности/нечётности строки
            "ROW_ODDEVEN" => cot_build_oddeven($jj),
            // Назначение счетчика цикла
            "ROW_JJ" => $jj
        ]);

        // Обработка дополнительных полей для категории
        foreach ($cot_extrafields[$db_structure] as $exfld)
        {
            // Преобразование имени поля в верхний регистр
            $uname = strtoupper($exfld['field_name']);
            // Назначение переменных дополнительных полей
            $t1->assign([
                // Назначение заголовка дополнительного поля
                'ROW_'.$uname.'_TITLE' => isset($L['structure_'.$exfld['field_name'].'_title']) ?  $L['structure_'.$exfld['field_name'].'_title'] : $exfld['field_description'],
                // Формирование данных дополнительного поля
                'ROW_'.$uname => cot_build_extrafields_data('structure', $exfld, $structure['page'][$row][$exfld['field_name']]),
                // Назначение сырого значения дополнительного поля
                'ROW_'.$uname.'_VALUE' => $structure['page'][$row][$exfld['field_name']],
            ]);
        }

        // Проверка, включена ли поддержка i18n и не используется основной язык
        if ($i18n_enabled && $i18n_notmain){
            // Получение данных i18n для категории
            $x_i18n = cot_i18n_get_cat($row, $i18n_locale);
            // Проверка существования данных i18n
            if ($x_i18n){
                // Добавление параметра языка, если не используется язык по умолчанию
                if(!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang']){
                    // Установка параметра языка в URL
                    $urlparams['l'] = $i18n_locale;
                }
                // Назначение переменных шаблона для i18n
                $t1->assign([
                    // Назначение URL для i18n
                    'ROW_URL' => cot_url('page', $urlparams),
                    // Назначение заголовка для i18n
                    'ROW_TITLE' => $x_i18n['title'],
                    // Назначение описания для i18n
                    'ROW_DESC' => $x_i18n['desc'],
                ]);
            }
        }

        /* === Hook - Part2 : Include === */// Выполнение плагинов для события цикла
        foreach ($extp as $pl)
        {
            // Подключение файла плагина
            include $pl;
        }
		/* ===== */
		
        // Парсинг блока CATS в шаблоне
        $t1->parse("MAIN.CATS");
    }

    // Проверка, были ли обработаны категории
    if ($jj == 0)
    {
        // Возврат false, если не было обработано ни одной категории
        return false;
    }

    // Парсинг главного блока MAIN в шаблоне
    $t1->parse("MAIN");
    // Возвращение текста отрендеренного шаблона
    return $t1->text("MAIN");
}
?>