<?php

defined('COT_CODE') or die('Wrong URL');


// Определение глобальных переменных для работы с базой данных, конфигурацией и структурой
global $db, $db_structure, $cfg, $cot_extrafields;
// Объявление глобальной переменной структуры
global $structure;

/**
 * Загружает структуру категорий из базы данных с учетом иерархии и дополнительных полей
 *
 * @return void
 */
function cot_load_structure_custom()
{
    // Доступ к глобальным переменным
    global $db, $db_structure, $cfg, $cot_extrafields, $structure;
    // Инициализация массива структуры
    $structure = [];
    // Инициализация массива подкатегорий
    $subcats = [];

    // Выбор SQL-запроса в зависимости от режима обновления
    if (defined('COT_UPGRADE')) {
        // Запрос для режима обновления, сортировка только по пути
        $sql = $db->query("SELECT * FROM $db_structure ORDER BY COALESCE(structure_path, '') ASC");
    } else {
        // Запрос для обычного режима, сортировка по области и пути
        $sql = $db->query("SELECT * FROM $db_structure ORDER BY structure_area ASC, COALESCE(structure_path, '') ASC");
    }

    // Инициализация массивов для путей, текстовых путей и шаблонов
    $path = [];
    $tpath = [];
    $tpls = [];

    // Обработка каждой записи из результата запроса
    foreach ($sql->fetchAll() as $row) {
        // Пропуск записей с пустым или нестроковым кодом или областью
        if (empty($row['structure_code']) || !is_string($row['structure_code']) || empty($row['structure_area']) || !is_string($row['structure_area'])) {
            continue;
        }

        // Присваивание кода категории
        $row['structure_code'] = $row['structure_code'];
        // Установка пути категории, если не указан — использование кода
        $row['structure_path'] = !empty($row['structure_path']) && is_string($row['structure_path']) ? $row['structure_path'] : $row['structure_code'];
        // Присваивание области категории
        $row['structure_area'] = $row['structure_area'];
        // Установка заголовка, если не указан — пустая строка
        $row['structure_title'] = !empty($row['structure_title']) && is_string($row['structure_title']) ? $row['structure_title'] : '';
        // Установка описания, если не указано — пустая строка
        $row['structure_desc'] = !empty($row['structure_desc']) && is_string($row['structure_desc']) ? $row['structure_desc'] : '';
        // Установка иконки, если не указана — пустая строка
        $row['structure_icon'] = !empty($row['structure_icon']) && is_string($row['structure_icon']) ? $row['structure_icon'] : '';
        // Приведение флага блокировки к целому числу
        $row['structure_locked'] = isset($row['structure_locked']) ? (int)$row['structure_locked'] : 0;
        // Приведение счетчика к целому числу
        $row['structure_count'] = isset($row['structure_count']) ? (int)$row['structure_count'] : 0;
        // Установка шаблона, если не указан — использование кода
        $row['structure_tpl'] = !empty($row['structure_tpl']) && is_string($row['structure_tpl']) ? $row['structure_tpl'] : $row['structure_code'];
        // Приведение идентификатора к целому числу
        $row['structure_id'] = isset($row['structure_id']) ? (int)$row['structure_id'] : 0;

        // Поиск последней точки в пути
        $last_dot = mb_strrpos($row['structure_path'], '.');

        // Обработка иерархического пути
        if ($last_dot !== false) {
            // Извлечение родительского пути
            $path1 = mb_substr($row['structure_path'], 0, $last_dot);
            // Формирование полного пути
            $path[$row['structure_path']] = !empty($path[$path1]) ? $path[$path1] . '.' . $row['structure_code'] : $row['structure_code'];
            // Определение разделителя для текстового пути
            $separator = (strip_tags($cfg['separator']) === $cfg['separator']) ? ' ' . $cfg['separator'] . ' ' : ' \ ';
            // Формирование текстового пути
            $tpath[$row['structure_path']] = !empty($tpath[$path1]) ? $tpath[$path1] . $separator . $row['structure_title'] : $row['structure_title'];
            // Определение родительской категории
            $parent_dot = mb_strrpos($path[$path1] ?? '', '.');
            $parent = ($parent_dot !== false) ? mb_substr($path[$path1], $parent_dot + 1) : ($path[$path1] ?? $row['structure_code']);
            // Добавление кода категории в массив подкатегорий
            $subcats[$row['structure_area']][$parent][] = $row['structure_code'];
        } else {
            // Установка пути для корневой категории
            $path[$row['structure_path']] = $row['structure_code'];
            // Установка текстового пути для корневой категории
            $tpath[$row['structure_path']] = $row['structure_title'];
            // Установка родительской категории
            $parent = $row['structure_code'];
        }

        // Обработка шаблона, если указано 'same_as_parent'
        if ($row['structure_tpl'] === 'same_as_parent') {
            // Использование шаблона родителя или кода категории
            $row['structure_tpl'] = $tpls[$parent] ?? $row['structure_code'];
        }

        // Сохранение шаблона для категории
        $tpls[$row['structure_code']] = $row['structure_tpl'];

        // Формирование структуры данных категории
        $structure[$row['structure_area']][$row['structure_code']] = [
            // Путь категории
            'path' => $path[$row['structure_path']],
            // Текстовый путь категории
            'tpath' => $tpath[$row['structure_path']],
            // Исходный путь категории
            'rpath' => $row['structure_path'],
            // Идентификатор категории
            'id' => $row['structure_id'],
            // Шаблон категории
            'tpl' => $row['structure_tpl'],
            // Заголовок категории
            'title' => $row['structure_title'],
            // Описание категории
            'desc' => $row['structure_desc'],
            // Иконка категории
            'icon' => $row['structure_icon'],
            // Флаг блокировки
            'locked' => $row['structure_locked'],
            // Счетчик элементов
            'count' => $row['structure_count'],
            // Подкатегории
            'subcats' => $subcats[$row['structure_area']][$row['structure_code']] ?? []
        ];

        // Обработка дополнительных полей, если они существуют
        if (!empty($cot_extrafields[$db_structure])) {
            // Перебор дополнительных полей
            foreach ($cot_extrafields[$db_structure] as $exfld) {
                // Формирование имени поля
                $fieldName = 'structure_' . $exfld['field_name'];
                // Добавление значения дополнительного поля в структуру
                $structure[$row['structure_area']][$row['structure_code']][$exfld['field_name']] = $row[$fieldName] ?? null;
            }
        }
    }

    // Финальная проверка и фильтрация структуры
    foreach ($structure as $area => &$area_structure) {
        // Проверка, что структура области является массивом
        if (!is_array($area_structure)) {
            // Инициализация пустого массива для невалидной области
            $area_structure = [];
            continue;
        }
        // Проверка каждой записи в области
        foreach ($area_structure as $i => &$x) {
            // Пропуск невалидных записей
            if (!is_array($x) || empty($x['path']) || !is_string($x['path'])) {
                unset($area_structure[$i]);
                continue;
            }
            // Установка подкатегорий
            $x['subcats'] = $subcats[$area][$i] ?? [];
            // Присваивание пути
            $x['path'] = $x['path'];
            // Установка текстового пути, если не указан — использование кода
            $x['tpath'] = !empty($x['tpath']) && is_string($x['tpath']) ? $x['tpath'] : $i;
            // Установка исходного пути, если не указан — использование кода
            $x['rpath'] = !empty($x['rpath']) && is_string($x['rpath']) ? $x['rpath'] : $i;
            // Установка заголовка, если не указан — пустая строка
            $x['title'] = !empty($x['title']) && is_string($x['title']) ? $x['title'] : '';
            // Установка описания, если не указано — пустая строка
            $x['desc'] = !empty($x['desc']) && is_string($x['desc']) ? $x['desc'] : '';
            // Установка иконки, если не указана — пустая строка
            $x['icon'] = !empty($x['icon']) && is_string($x['icon']) ? $x['icon'] : '';
            // Приведение счетчика к целому числу
            $x['count'] = isset($x['count']) ? (int)$x['count'] : 0;
            // Приведение флага блокировки к целому числу
            $x['locked'] = isset($x['locked']) ? (int)$x['locked'] : 0;
            // Установка шаблона, если не указан — использование кода
            $x['tpl'] = !empty($x['tpl']) && is_string($x['tpl']) ? $x['tpl'] : $i;
            // Приведение идентификатора к целому числу
            $x['id'] = isset($x['id']) ? (int)$x['id'] : 0;
        }
        // Освобождение ссылки на последнюю запись
        unset($x);
    }
    // Освобождение ссылки на последнюю область
    unset($area_structure);

    // Сохранение копии структуры перед выполнением плагинов
    $temp_structure = $structure;
    // Выполнение плагинов, подключенных к событию structure
    foreach (cot_getextplugins('structure') as $pl) {
        // Восстановление структуры перед выполнением плагина
        $structure = $temp_structure;
        // Подключение файла плагина
        include $pl;
        // Проверка структуры после выполнения плагина
        foreach ($structure as $area => &$area_structure) {
            // Проверка, что структура области является массивом
            if (!is_array($area_structure)) {
                // Инициализация пустого массива для невалидной области
                $area_structure = [];
                continue;
            }
            // Проверка каждой записи в области
            foreach ($area_structure as $i => &$x) {
                // Пропуск невалидных записей
                if (!is_array($x) || empty($x['path']) || !is_string($x['path'])) {
                    unset($area_structure[$i]);
                    continue;
                }
            }
            // Освобождение ссылки на последнюю запись
            unset($x);
        }
        // Освобождение ссылки на последнюю область
        unset($area_structure);
    }
    // Финальное восстановление структуры
    $structure = $temp_structure;
}

?>