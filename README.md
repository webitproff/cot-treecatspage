# Tree Cats Page Plugin for Cotonti Siena 0.9.26

**Version:** 1.0  
**Compatibility:** Cotonti Siena 0.9.26+  
**PHP:** 8.1 – 8.3

Developed specifically for the free starter [marketplace build](https://github.com/webitproff/cot_2waydeal_build), a demonstration of its functionality can be seen [here](https://abuyfile.com/).

<img src="https://raw.githubusercontent.com/webitproff/cot-treecatspage/refs/heads/main/treecatspage_35.jpg" alt="Tree Cats Page Plugin for Cotonti Siena CMF" title="Tree Cats Page Plugin for Cotonti Siena 0.9.26" />

<img src="https://raw.githubusercontent.com/webitproff/cot-treecatspage/refs/heads/main/treecatspage_35%20(2).jpg" alt="Tree Cats Page Plugin for Cotonti Siena CMF" title="Tree Cats Page Plugin for Cotonti Siena 0.9.26" />


## Overview

The treecatspage plugin enables the invocation and display of the page module's category structure as a tree from any location on the site.

The plugin allows integrating the page category structure into any template of your site's theme on CMF Cotonti Siena v.0.9.26. The category tree template `treecatspage.page.tree.tpl` is built using Bootstrap 5.3. This template can be directly embedded into your templates or invoked via a link placed in the site's header or footer, depending on your preference, but more on that later.

## Plugin Features and Capabilities

- Creates a category tree from the "page" module.
- Supports excluded categories through the plugin configuration ("blacktreecatspage").
- Displays the following data for each category:
  - Title
  - Description
  - Icon
  - Page count
  - Nested subcategories
- Added support for i18n (multilingual category titles, structure internationalization): localized titles and descriptions (if enabled).
- Output based on a template using `treecatspage.page.tree.tpl`.
- Uses `cot_url()` for clean category links.
- Applies extra fields from the structure, if defined.

## Extension Installation

For those who know everything and are in a hurry, a site backup is mandatory. Upload the files step-by-step according to the instructions below. Installing the plugin is actually very simple; just take your time, follow the steps in order, and everything will work out.

1. Download the plugin source code and necessary files from the [Github repository](https://github.com/webitproff/cot-treecatspage).

2. Unpack the archive. In the "plugins" folder, there is a "treecatspage" folder — upload this folder to the "plugins" directory of your Cotonti site.

3. The "Tree Cats Page" plugin, in its default form, is designed for quick installation and integration with the ["2waydeal" theme](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes/2waydeal). Although developed for a freelance build, the "2waydeal" theme is compatible with any Cotonti Siena 0.9.26 site, using the latest source code as of 09.07.2025. This information is important because, before uploading the "themes" folder to your site's root, you need to know the name of your theme folder. If your theme is not "2waydeal" but, for example, the ancient "nemesis" skin from the late Cretaceous period, don’t worry — it will still work, as long as Bootstrap 5.3 is included.

3.1. If you already have the "2waydeal" theme installed, safely upload the "themes" folder from the archive to your site’s root.

3.1.2. Open the file `/themes/2waydeal/header.tpl` 
and after the line 
`{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/header/infoLeftOffcanvas.tpl"}` 
add the following line:  
`{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/header/treecatspageLeftOffcanvas.tpl"}`  
Save and close the file; it’s no longer needed.

3.1.3. Open the file `/themes/2waydeal/inc/header/sidebarMenuSections.tpl` and anywhere, or in place of the following code:

```
<!-- IF {PHP|cot_module_active('page')} -->
<hr class="my-2">
<li class="nav-item">
<a class="nav-link d-flex align-items-center" data-bs-toggle="collapse" href="#collapse-page" role="button" aria-expanded="false">
  <i class="fa-regular fa-newspaper me-2"></i>
  <span>{PHP.L.2wd_Publications}</span>
  <i class="fas fa-angle-down ms-auto"></i>
</a>
<div class="collapse" id="collapse-page">
  <ul class="nav flex-column ps-2">
	<!-- IF {PHP.structure.page.news} -->
	<!-- IF {PHP.structure.page.news.path} -->
	<li class="nav-item">
	  <a href="{PHP|cot_url('page','c=news')}" class="nav-link" title="{PHP.L.2wd_cat_title_news}">
		<span class="me-2">
		  <i class="fa-solid fa-pen-nib"></i>
		</span>{PHP.L.2wd_cat_title_news} </a>
	</li>
	<!-- ENDIF -->
	<!-- ENDIF -->
	<!-- IF {PHP.structure.page.articles} -->
	<!-- IF {PHP.structure.page.articles.path} -->
	<li class="nav-item">
	  <a href="{PHP|cot_url('page','c=articles')}" class="nav-link" title="{PHP.L.2wd_cat_title_articles}">
		<span class="me-2">
		  <i class="fa-solid fa-pen-nib"></i>
		</span>{PHP.L.2wd_cat_title_articles} </a>
	</li>
	<!-- ENDIF -->
	<!-- ENDIF -->
	<!-- IF {PHP.structure.page.usersblog} -->
	<!-- IF {PHP.structure.page.usersblog.path} -->
	<li class="nav-item">
	  <a href="{PHP|cot_url('page','c=usersblog')}" class="nav-link" title="{PHP.L.2wd_cat_title_usersblog}">
		<span class="me-2">
		  <i class="fa-solid fa-pen-nib"></i>
		</span>{PHP.L.2wd_cat_title_usersblog} </a>
	</li>
	<!-- ENDIF -->
	<!-- ENDIF -->
	<!-- IF {PHP.structure.page.events} -->
	<!-- IF {PHP.structure.page.events.path} -->
	<li class="nav-item">
	  <a href="{PHP|cot_url('page','c=events')}" class="nav-link" title="{PHP.L.2wd_cat_title_events}">
		<span class="me-2">
		  <i class="fa-solid fa-pen-nib"></i>
		</span>{PHP.L.2wd_cat_title_events} </a>
	</li>
	<!-- ENDIF -->
	<!-- ENDIF -->
  </ul>
</div>
</li>
<!-- ENDIF -->
```

insert the following code:

```
<!-- IF {PHP|cot_module_active('page')} -->
<hr class="my-2">
<li class="nav-item">
<a class="nav-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#treecatspageLeftOffcanvas" aria-controls="treecatspageLeftOffcanvas">
  <span class="me-2">
	<i class="fa-regular fa-newspaper me-2"></i>
  </span>{PHP.L.2wd_Publications}</a>
</li>
<hr class="my-2">
<!-- ENDIF -->
```

That’s it; you’ve edited just two files. Integration is complete, proceed to step 4.

3.2. If you are using the "nemesis" theme or any other theme:  
3.2.1. Open the "themes" folder in the unpacked archive and rename the "2waydeal" folder to match the name of your site’s theme.  
3.2.2. Upload the "themes" folder from the archive to your site’s root.  
3.2.3. Open your theme’s header template, e.g., `/themes/nemesis/header.tpl`, and anywhere after the `<body>` tag but before the `<!-- END: HEADER -->` block, add the following line:  
`{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/header/treecatspageLeftOffcanvas.tpl"}`

In the same `header.tpl`, for example, before or instead of:

```
<!-- IF {PHP|cot_module_active('rss')} -->
<li>
	<a href="{PHP|cot_url('rss')}" title="{PHP.L.RSS_Feeds}">
		RSS
		<span>Subscribe me</span>
	</a>
</li>
<!-- ENDIF -->
```

insert the following code:

```
<!-- IF {PHP|cot_module_active('page')} -->
<li>
<a type="button" data-bs-toggle="offcanvas" data-bs-target="#treecatspageLeftOffcanvas" aria-controls="treecatspageLeftOffcanvas">
  <span class="me-2">
	<i class="fa-regular fa-newspaper me-2"></i>
  </span>{PHP.L.Pages}</a>
</li>
<!-- ENDIF -->
```

Save and close the file.

3.2.4. Ensure Bootstrap 5.3 is included. Open `/themes/nemesis/nemesis.rc.php` and verify that the following lines are present and not commented out:

```
Resources::addFile('lib/bootstrap/css/bootstrap.min.css');
if (Cot::$cfg['headrc_consolidate']) {
    Resources::addFile('lib/bootstrap/js/bootstrap.bundle.min.js');
} else {
    Resources::linkFileFooter('lib/bootstrap/js/bootstrap.bundle.min.js');
}
```

4. To ensure the menu with subcategories works correctly and opens child categories when clicking on a parent category, check if you have a `functions.custom.php` file in the `system` folder.  
4.1. If the file does not exist, upload `functions.custom.php` to the `system` folder and proceed to step 5.  
4.2. If the file exists, open it and check if it contains the `cot_load_structure_custom()` function.  
4.3. You can keep it as is if it’s present or update it.

5. Ensure that `functions.custom.php` is enabled.  
5.2. Open the configuration file `/datas/config.php` and around line 89, verify that the following setting is present:  

`$cfg['customfuncs'] = true;`

If it’s set to `$cfg['customfuncs'] = false;`, change it to `true`. Save and close the file. Code-related work is complete.

6. Plugin Installation in the Admin Panel:  
6.1. In the site’s admin panel, go to "Extensions," scroll almost to the bottom, and find the "Tree Cats Page" plugin (Administration panel -> Extensions -> Tree Cats Page). Click the "Install" button.  
6.2. Click the "Configuration" button (Administration panel -> Extensions -> Tree Cats Page -> Configuration) and enter the category codes, separated by commas, to create a blacklist of page module structure elements you don’t want to display on the site. You can view your category codes by navigating to: "Administration panel -> Extensions -> Pages -> Structure."

7. Installation is complete.  
Go to the frontend and click your "Pages" or "Articles and Blogs" links, depending on your theme.

If you need to adjust the category list elements, they are located in the template:  

`plugins/treecatspage/tpl/treecatspage.page.tree.tpl`

For help or questions, write in Russian or English [on the forum](https://abuyfile.com/ru/forums/cotonti/custom/plugs)

09 July 2025, [webitproff](https://github.com/webitproff)

---

# Плагин Tree Cats Page для Cotonti Siena 0.9.26

**Версия:** 1.0  
**Совместимость:** Cotonti Siena 0.9.26+  
**PHP:** 8.1 – 8.3

Разрабатывалось специально для бесплатной стартовой сборки маркетплейса, а демонстрацию работы можно увидеть здесь.

## Обзор

Плагин treecatspage обеспечивает вызов и отображение структуры категорий модуля page в виде дерева, с любого места на сайте.

Плагин позволяет интегрировать структуру категорий страниц в любой шаблон темы вашего сайта на CMF Cotonti Siena v.0.9.26. Шаблон дерева категорий `treecatspage.page.tree.tpl` сверстан на Bootstrap 5.3. Этот шаблон можно напрямую встраивать в свои шаблоны или вызывать по ссылке, разместив ее в шапку или подвал сайта, — тут кому как удобно, но об этом позже.

## Функции и возможности плагина

- Создает дерево категорий из модуля "page".
- Поддержка исключенных категорий с помощью конфигурации плагина ("blacktreecatspage").
- Отображает следующие данные для каждой категории:
  - Название
  - Описание
  - Значок
  - Количество страниц
  - Вложенные подкатегории
- Добавлена поддержка i18n (мультиязычность названий категорий, интернационализация структуры): локализованные заголовки и описания (если они включены).
- Вывод на основе шаблона с использованием `treecatspage.page.tree.tpl`.
- Используется `cot_url()` для чистых ссылок на категории.
- Применяет дополнительные поля (экстраполя) из структуры, если они определены.

## Установка расширения

Для тех, кто всё знает и торопится — бекап сайта обязателен. Файлы закачиваем поэтапно, в соответствии с каждым пунктом инструкции ниже. Установка плагина на самом деле очень проста, просто не торопитесь, идем шаг за шагом, по порядку, и всё получится.

1. Скачиваем исходный код плагина и файлы, необходимые ему для корректной работы, с [репозитория Github](https://github.com/webitproff/cot-treecatspage).

2. Распакуйте архив, в папке "plugins" находится папка "treecatspage" — её (папку "treecatspage") смело закачиваем в папку с плагинами "plugins" вашего сайта на Cotonti.

3. Плагин "Tree Cats Page" в исходном варианте рассчитан на быструю установку и интеграцию в тему ["2waydeal"](https://github.com/webitproff/cot_2waydeal_build/tree/master/public_html/themes/2waydeal), которая хоть и разрабатывалась для сборки фриланса, совместима с любыми сайтами на Cotonti Siena 0.9.26, актуальной версией исходного кода по состоянию на 09.07.2025 г. Эта информация нужна для того, чтобы, прежде чем закачивать папку "themes" в корень вашего сайта, вы знали имя папки вашей темы. Если у вас тема не "2waydeal", а, например, древний скин "nemesis" времен конца мелового периода, ничего страшного — работать будет, главное, чтобы был подключен Bootstrap 5.3.

3.1. Если у вас уже установлена тема "2waydeal", спокойно закачиваем папку "themes" из архива в папку вашего сайта.

3.1.2. Открываем файл `/themes/2waydeal/header.tpl` и после строки `{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/header/infoLeftOffcanvas.tpl"}` добавляем строку:  
`{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/header/treecatspageLeftOffcanvas.tpl"}`  
Сохраняемся, файл закрываем. Он больше не нужен.

3.1.3. Открываем файл `/themes/2waydeal/inc/header/sidebarMenuSections.tpl` и в любом месте, или вместо кода:

```
<!-- IF {PHP|cot_module_active('page')} -->
<hr class="my-2">
<li class="nav-item">
<a class="nav-link d-flex align-items-center" data-bs-toggle="collapse" href="#collapse-page" role="button" aria-expanded="false">
  <i class="fa-regular fa-newspaper me-2"></i>
  <span>{PHP.L.2wd_Publications}</span>
  <i class="fas fa-angle-down ms-auto"></i>
</a>
<div class="collapse" id="collapse-page">
  <ul class="nav flex-column ps-2">
	<!-- IF {PHP.structure.page.news} -->
	<!-- IF {PHP.structure.page.news.path} -->
	<li class="nav-item">
	  <a href="{PHP|cot_url('page','c=news')}" class="nav-link" title="{PHP.L.2wd_cat_title_news}">
		<span class="me-2">
		  <i class="fa-solid fa-pen-nib"></i>
		</span>{PHP.L.2wd_cat_title_news} </a>
	</li>
	<!-- ENDIF -->
	<!-- ENDIF -->
	<!-- IF {PHP.structure.page.articles} -->
	<!-- IF {PHP.structure.page.articles.path} -->
	<li class="nav-item">
	  <a href="{PHP|cot_url('page','c=articles')}" class="nav-link" title="{PHP.L.2wd_cat_title_articles}">
		<span class="me-2">
		  <i class="fa-solid fa-pen-nib"></i>
		</span>{PHP.L.2wd_cat_title_articles} </a>
	</li>
	<!-- ENDIF -->
	<!-- ENDIF -->
	<!-- IF {PHP.structure.page.usersblog} -->
	<!-- IF {PHP.structure.page.usersblog.path} -->
	<li class="nav-item">
	  <a href="{PHP|cot_url('page','c=usersblog')}" class="nav-link" title="{PHP.L.2wd_cat_title_usersblog}">
		<span class="me-2">
		  <i class="fa-solid fa-pen-nib"></i>
		</span>{PHP.L.2wd_cat_title_usersblog} </a>
	</li>
	<!-- ENDIF -->
	<!-- ENDIF -->
	<!-- IF {PHP.structure.page.events} -->
	<!-- IF {PHP.structure.page.events.path} -->
	<li class="nav-item">
	  <a href="{PHP|cot_url('page','c=events')}" class="nav-link" title="{PHP.L.2wd_cat_title_events}">
		<span class="me-2">
		  <i class="fa-solid fa-pen-nib"></i>
		</span>{PHP.L.2wd_cat_title_events} </a>
	</li>
	<!-- ENDIF -->
	<!-- ENDIF -->
  </ul>
</div>
</li>
<!-- ENDIF -->
```

вставляем код:

```
<!-- IF {PHP|cot_module_active('page')} -->
<hr class="my-2">
<li class="nav-item">
<a class="nav-link" type="button" data-bs-toggle="offcanvas" data-bs-target="#treecatspageLeftOffcanvas" aria-controls="treecatspageLeftOffcanvas">
  <span class="me-2">
	<i class="fa-regular fa-newspaper me-2"></i>
  </span>{PHP.L.2wd_Publications}</a>
</li>
<hr class="my-2">
<!-- ENDIF -->
```

Всё, отредактировали всего два файла. Интеграция завершена, переходите к пункту №4.

3.2. Если у вас установлена тема "nemesis" или любая другая:  
3.2.1. Открываем в папке распакованного архива папку "themes" и папку "2waydeal" переименовываем в актуальное название вашей темы на сайте.  
3.2.2. Теперь спокойно закачиваем папку "themes" из архива в папку вашего сайта.  
3.2.3. Открываем шаблон шапки сайта, например `/themes/nemesis/header.tpl`, и в любом месте после тега `<body>`, но до закрытия блока `<!-- END: HEADER -->`, добавляем строку:  
`{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/inc/header/treecatspageLeftOffcanvas.tpl"}`

Здесь же в `header.tpl`, например, перед или вместо:

```
<!-- IF {PHP|cot_module_active('rss')} -->
<li>
	<a href="{PHP|cot_url('rss')}" title="{PHP.L.RSS_Feeds}">
		RSS
		<span>Subscribe me</span>
	</a>
</li>
<!-- ENDIF -->
```

вставляем код:

```
<!-- IF {PHP|cot_module_active('page')} -->
<li>
<a type="button" data-bs-toggle="offcanvas" data-bs-target="#treecatspageLeftOffcanvas" aria-controls="treecatspageLeftOffcanvas">
  <span class="me-2">
	<i class="fa-regular fa-newspaper me-2"></i>
  </span>{PHP.L.Pages}</a>
</li>
<!-- ENDIF -->
```

Сохранили и забыли.

3.2.4. Нужно убедиться, что у вас подключен Bootstrap 5.3. Открываем `/themes/nemesis/nemesis.rc.php` и проверяем, что строки присутствуют и они не закомментированы:

```
Resources::addFile('lib/bootstrap/css/bootstrap.min.css');
if (Cot::$cfg['headrc_consolidate']) {
    Resources::addFile('lib/bootstrap/js/bootstrap.bundle.min.js');
} else {
    Resources::linkFileFooter('lib/bootstrap/js/bootstrap.bundle.min.js');
}
```

4. Для того, чтобы меню с подкатегориями работало корректно и открывало дочерние категории при клике на родительскую, вам нужно проверить на сайте, есть ли у вас свой файл `functions.custom.php` в папке `system`.  
4.1. Если файла нет, закачиваем файл `functions.custom.php` в папку `system` и переходим к пункту №5.  
4.2. Если файл присутствует, открываем и проверяем, есть ли в нем функция `cot_load_structure_custom()`.  
4.3. Здесь ее можно оставить как есть, если она присутствует, или обновить.

5. Теперь нужно точно знать, что наш `functions.custom.php` подключен.  
5.2. Открываем файл конфигурации "кота" `/datas/config.php` и примерно в строке 89 проверяем запись, которая должна иметь вид:  

`$cfg['customfuncs'] = true;`

Если у вас `$cfg['customfuncs'] = false;`, переводим в значение `true`. Сохранились и забыли. Работа с кодом завершена.

6. Установка плагина в админке:  
6.1. В панели управления сайтом открываем ссылку "Расширения", прокручиваем страницу почти в самый низ и находим плагин "Tree Cats Page" (Administration panel -> Extensions -> Tree Cats Page). Жмем кнопку "Install".  
6.2. Теперь жмем кнопку "Configuration" (Administration panel -> Extensions -> Tree Cats Page -> Configuration) и вводим коды категорий через запятую, для формирования черного списка элементов структуры модуля "Page", которые не хотим видеть на сайте. Посмотреть коды ваших категорий можно, пройдя путь: "Administration panel -> Extensions -> Pages -> Structure."

7. Установка завершена.  
Идем на фронтенд, кликаем свои ссылки "Pages" или "Статьи и блоги", в зависимости от вашей темы.

Если кто пожелает подправить элементы списка категорий, они лежат в шаблоне:  

`plugins/treecatspage/tpl/treecatspage.page.tree.tpl`

Если нужна помощь или есть вопросы, пишите на русском или английском на [форуме](https://abuyfile.com/ru/forums/cotonti/custom/plugs)

09 Июль 2025 г., [webitproff](https://github.com/webitproff)
