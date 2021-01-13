<?php

namespace curia;

// modules
function getModuleNames(bool $useCache = true): array
{
    if ($useCache) {
        $moduleNames = \curia\getCacheValue('modules') ?? [];
    } else {
        $moduleNames = [];

        $directory = new \DirectoryIterator(MYBB_ROOT . 'inc/plugins/curia/modules');

        foreach ($directory as $file) {
            if (!$file->isDot() && $file->isDir()) {
                $moduleNames[] = $file->getFilename();
            }
        }
    }

    return $moduleNames;
}

function loadModules(array $moduleNames): void
{
    foreach ($moduleNames as $moduleName) {
        require_once MYBB_ROOT . 'inc/plugins/curia/modules/' . $moduleName . '/module.php';
    }
}

/**
 * @param array|callable $settings
 */
function registerSettings($settings): void
{
    global $curiaRuntimeRegistry;

    if (is_callable($settings)) {
        $curiaRuntimeRegistry['settingCallables'][] = $settings;
    } else {
        $curiaRuntimeRegistry['settings'] = array_merge($curiaRuntimeRegistry['settings'] ?? [], $settings);
    }
}

function getRegisteredSettings(): array
{
    global $curiaRuntimeRegistry;

    $settings = $curiaRuntimeRegistry['settings'] ?? [];

    if (!empty($curiaRuntimeRegistry['settingCallables'])) {
        foreach ($curiaRuntimeRegistry['settingCallables'] as $callable) {
            $settings = array_merge($settings, $callable());
        }
    }

    return $settings;
}

function loadModuleLanguageFile(string $moduleName, string $section): void
{
    \mint\loadExternalLanguageFile('inc/plugins/curia/modules/' . $moduleName . '/languages', $section);
}

function runScheduler(): void
{
}
