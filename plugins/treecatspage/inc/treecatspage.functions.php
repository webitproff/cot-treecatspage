<?php

/**
 * File treecatspage.functions.php for Tree Cats Page
 * v.2.0.0 from 29 Jan 2026
 * @package treecatspage
 * @copyright (c) webitproff
 * @license BSD
 */


defined('COT_CODE') or die('Неверный URL.');

require_once cot_langfile('page', 'module');
require_once cot_incfile('page', 'module');
require_once cot_incfile('page', 'module', 'functions');
require_once cot_incfile('page', 'module', 'resources');
require_once cot_incfile('extrafields');

$c = cot_import('c', 'G', 'TXT'); // код категории

/**
 * Формирует иерархическую структуру дерева категорий страниц
 *
 * !!! ВАЖНО !!!
 * - SQL-запрос выполняется ОДИН РАЗ
 * - Рекурсивный подсчёт выполняется ОДИН РАЗ
 * - Подсчёт снизу вверх
 * - static-кэш на всё время работы скрипта
 */
function cot_build_structure_page_tree($parent = '', $selected = '', $level = 0, $template = '')
{
    global $structure, $cfg, $db, $sys, $cot_extrafields, $db_structure, $db_pages;
    global $i18n_notmain, $i18n_locale, $i18n_write, $i18n_admin, $i18n_read, $db_i18n_pages;

    /* ============================================================
     * STATIC-КЭШ. ВСЁ СЧИТАЕТСЯ ОДИН РАЗ НА ВЕСЬ СКРИПТ
     * ============================================================ */
    static $initialized = false;
    static $accumulatedCounts = [];
    static $total_count = 0;

    /* ============================================================
     * ИНИЦИАЛИЗАЦИЯ ПОДСЧЁТОВ (ОДИН РАЗ)
     * ============================================================ */
    if (!$initialized) {
        $initialized = true;

        // Инициализация счётчиков
        foreach ($structure['page'] as $cat => $data) {
            $accumulatedCounts[$cat] = 0;
        }

        // Один SQL-запрос: считаем опубликованные страницы по категориям
        if ($db->tableExists($db_pages)) {
            $sql = $db->query("
                SELECT page_cat, COUNT(*) AS cnt
                FROM $db_pages
                WHERE page_state = 0
                GROUP BY page_cat
            ");
            while ($row = $sql->fetch()) {
                if (isset($accumulatedCounts[$row['page_cat']])) {
                    $accumulatedCounts[$row['page_cat']] += (int)$row['cnt'];
                }
                $total_count += (int)$row['cnt'];
            }
        }

        /**
         * Рекурсивный accumulate — СНИЗУ ВВЕРХ
         * ВЫЗЫВАЕТСЯ ОДИН РАЗ
         */
        $accumulate = function ($cat) use (&$accumulate, &$accumulatedCounts, &$structure) {
            if (empty($structure['page'][$cat]['subcats'])) {
                return $accumulatedCounts[$cat];
            }

            foreach ($structure['page'][$cat]['subcats'] as $sub) {
                $accumulatedCounts[$cat] += $accumulate($sub);
            }
            return $accumulatedCounts[$cat];
        };

        // Запуск accumulate только для корневых категорий
        foreach ($structure['page'] as $cat => $data) {
            if (mb_substr_count($data['path'], '.') === 0) {
                $accumulate($cat);
            }
        }

        // Записываем итоговые значения обратно в структуру
        foreach ($accumulatedCounts as $cat => $cnt) {
            $structure['page'][$cat]['count'] = $cnt;
        }
    }

    /* ============================================================
     * ДАЛЬШЕ — ЧИСТЫЙ РЕНДЕР БЕЗ ЛИШНИХ ПРОВЕРОК И SQL
     * ============================================================ */

    $blacklist_cfg = $cfg['plugin']['treecatspage']['blacktreecatspage'] ?? '';
    $blacklist = array_map('trim', explode(',', $blacklist_cfg));

    $urlparams = [];

    /* === Hook === */
    foreach (cot_getextplugins('page.tree.first') as $pl) {
        include $pl;
    }
    /* ===== */

    if (empty($parent)) {
        $i18n_enabled = $i18n_read;
        $children = [];
        foreach (cot_structure_children('page', '') as $x) {
            if (
                mb_substr_count($structure['page'][$x]['path'], '.') === 0 &&
                !in_array($x, $blacklist)
            ) {
                $children[] = $x;
            }
        }
    } else {
        $i18n_enabled = $i18n_read && cot_i18n_enabled($parent);
        $children = array_filter(
            $structure['page'][$parent]['subcats'] ?? [],
            fn($cat) => !in_array($cat, $blacklist)
        );
    }

    if (!$children) {
        return false;
    }

	// присваиваем шаблону имя части и/или локации расширения
	$tpl_ExtCode          = 'treecatspage';   // код плагина
	$tpl_PartExt          = 'page';                   // область редактирования
	$tpl_PartExtSecond    = 'tree';                   // что
	$tpl_PartCostumTpl    = $template;                // подставляем свой шаблон, если нужно

	// Загружаем шаблон 
	$extTplFile = cot_tplfile(
			[
			$tpl_ExtCode, 
			$tpl_PartExt, 
			$tpl_PartExtSecond,
			$tpl_PartCostumTpl
			], 
			'plug', 
			true
		);
	$t1 = new XTemplate($extTplFile);
	
/* 
 *    создаем свой $extTplFile
 *    /themes/index36/plugins/treecatspage/treecatspage.page.tree.sidebar.tpl	
 * 
 *    подключаем его глобально
 *    <!-- IF {PHP|function_exists('cot_build_structure_page_tree')} AND {PHP|cot_auth('page', 'any', 'R')} -->
 *    {PHP|cot_build_structure_page_tree('', '', 0, 'sidebar')}
 *    <!-- ENDIF -->
 */

    /* === Hook === */
    foreach (cot_getextplugins('page.tree.main') as $pl) {
        include $pl;
    }
    /* ===== */

    $title = $parent && isset($structure['page'][$parent]) ? $structure['page'][$parent]['title'] : '';
    $desc  = $parent && isset($structure['page'][$parent]) ? $structure['page'][$parent]['desc']  : '';
    $count = $parent && isset($structure['page'][$parent]) ? $structure['page'][$parent]['count'] : 0;
    $icon  = $parent && isset($structure['page'][$parent]) ? $structure['page'][$parent]['icon']  : '';

    $t1->assign([
        'TITLE' => htmlspecialchars($title),
        'DESC' => $desc,
        'COUNT' => $count,
        'ICON' => $icon,
        'HREF' => cot_url('page', $urlparams + ['c' => $parent]),
        'LEVEL' => $level,
        'TOTAL_COUNT' => $total_count,
    ]);

    $jj = 0;
    $extp = cot_getextplugins('page.tree.loop');

    foreach ($children as $row) {
        $jj++;
        $urlparams['c'] = $row;

        $t1->assign([
            'ROW_ID' => $row,
            'ROW_TITLE' => htmlspecialchars($structure['page'][$row]['title']),
            'ROW_DESC' => $structure['page'][$row]['desc'],
            'ROW_COUNT' => $structure['page'][$row]['count'],
            'ROW_ICON' => $structure['page'][$row]['icon'],
            'ROW_HREF' => cot_url('page', $urlparams),
            'ROW_SELECTED' => ((is_array($selected) && in_array($row, $selected)) || $row == $selected) ? 1 : 0,
            'ROW_SUBCAT' => cot_build_structure_page_tree($row, $selected, $level + 1),
            'ROW_LEVEL' => $level,
            'ROW_ODDEVEN' => cot_build_oddeven($jj),
            'ROW_JJ' => $jj
        ]);

        foreach ($cot_extrafields[$db_structure] as $exfld) {
            $uname = strtoupper($exfld['field_name']);
            $t1->assign([
                'ROW_'.$uname.'_TITLE' =>
                    $L['structure_'.$exfld['field_name'].'_title'] ?? $exfld['field_description'],
                'ROW_'.$uname =>
                    cot_build_extrafields_data('structure', $exfld, $structure['page'][$row][$exfld['field_name']]),
                'ROW_'.$uname.'_VALUE' =>
                    $structure['page'][$row][$exfld['field_name']],
            ]);
        }

        if ($i18n_enabled && $i18n_notmain) {
            $x_i18n = cot_i18n_get_cat($row, $i18n_locale);
            if ($x_i18n) {
                if (!$cfg['plugin']['i18n']['omitmain'] || $i18n_locale != $cfg['defaultlang']) {
                    $urlparams['l'] = $i18n_locale;
                }
                $t1->assign([
                    'ROW_URL' => cot_url('page', $urlparams),
                    'ROW_TITLE' => $x_i18n['title'],
                    'ROW_DESC' => $x_i18n['desc'],
                ]);
            }
        }

        foreach ($extp as $pl) {
            include $pl;
        }

        $t1->parse('MAIN.CATS');
    }

    $t1->parse('MAIN');
    return $t1->text('MAIN');
}
?>

