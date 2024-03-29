<?php // phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

require MYBB_ROOT . 'inc/plugins/curia/common.php';
require MYBB_ROOT . 'inc/plugins/curia/core.php';
require MYBB_ROOT . 'inc/plugins/curia/hooks_frontend.php';

spl_autoload_register(function ($path) {
    $prefix = 'curia\\';
    $baseDir = MYBB_ROOT . 'inc/plugins/curia/';

    if (strpos($path, $prefix) === 0) {
        $className = str_replace('\\', '/', substr($path, strlen($prefix)));
        $file = $baseDir . $className . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
});

define('curia\DEVELOPMENT_MODE', 0);

\curia\loadModules(
    \curia\getModuleNames()
);

\curia\addHooksNamespace('curia\Hooks');

/**
 * Load plugin metadata
 *
 * @return array
 */
function curia_info(): array
{
    global $lang;
    $lang->load('curia');

    return [
        "name"            => "Curia",
        "description"    => $lang->curia_admin_description,
        "website"        => "https://tspforums.xyz/curia.html",
        "author"        => "Dylan H.",
        "authorsite"    => "https://hierocles.github.com",
        "version"        => "0.0.0",
        "guid"             => "baf8a2fc-8d9d-411a-8393-52d0a0154950",
        "codename"        => "curia",
        "compatibility" => "18*"
    ];
}

/**
 * Create settings and initialize database tables
 *
 * @return void
 */
function curia_install(): void
{
    global $db, $cache;

    \curia\loadPluginLibrary();

    \curia\createTables([
        \curia\DbRepository\Elections::class,
        \curia\DbRepository\Ballots::class
    ]);

    require_once MYBB_ROOT . 'inc/functions_task.php';

    $new_task = [
        'title' => 'Curia: Election scheduler',
        'description' => 'Runs every hour to start/stop scheduled elections',
        'file' => 'curia_scheduler',
        'minute' => '0',
        'hour' => '*',
        'day' => '*',
        'month' => '*',
        'weekday' => '*',
        'enabled' => '1',
        'logging' => '1'
    ];

    $new_task['nextrun'] = fetch_next_run($new_task);

    $db->insert_query('tasks', $new_task);
    $cache->update_tasks();
}

/**
 * Delete settings, cache, tables, and tasks
 *
 * @return void
 */
function curia_uninstall()
{
    global $db, $cache, $PL;

    \curia\loadPluginLibrary();

    // settings
    $PL->settings_delete('curia');

    // datacache
    $cache->delete('curia');

    // database tables
    \curia\dropTables([
        \curia\DbRepository\Elections::class,
        \curia\DbRepository\Ballots::class
    ], true, true);

    // tasks
    $db->delete_query('tasks', "file='curia_scheduler'");
    $cache->update_tasks();
}

/**
 * Create settings, load templates, update cache
 *
 * @return void
 */
function curia_activate()
{
    global $lang, $PL;

    $lang->load('curia');

    \curia\loadPluginLibrary();

    $settings = [
        'admin_group' => [
            'title' => $lang->curia_admin_grouptitle,
            'description' => $lang->curia_admin_groupdesc,
            'optionscode' => 'groupselect'
        ],
        'can_delete' => [
            'title' => $lang->curia_admin_candeletetitle,
            'description' => $lang->curia_admin_candeletedesc
        ],
        'can_vote' => [
            'title' => $lang->curia_admin_canvote,
            'description' => $lang->curia_admin_canvotedesc,
            'optionscode' => 'groupselect'
        ],
        'canv_view' => [
            'title' => $lang->curia_admin_canview,
            'description' => $lang->curia_admin_canviewdesc,
            'optionscode' => 'groupselect'
        ]
    ];
    $PL->settings(
        'curia',
        'Curia',
        $lang->curia_admin_settingsdesc,
        $settings
    );

    $PL->templates(
        'curia',
        'Curia',
        \curia\getFilesContentInDirectory(MYBB_ROOT . 'inc/plugins/curia/templates', '.tpl')
    );

    \curia\updateCache([
        'version' => curia_info()['version'],
        'modules' => $moduleNames
    ]);
}

/**
 * Delete templates
 *
 * @return void
 */
function curia_deactivate()
{
    global $PL;

    \curia\loadPluginLibrary();

    $PL->templates_delete('curia', true);
}

/**
 * Return true if settings group exists
 *
 * @return bool
 */
function curia_is_installed()
{
    global $db;

    // manual check to avoid caching issues
    $query = $db->simple_select('settinggroups', 'gid', "name='curia'");

    return (bool)$db->num_rows($query);
}
