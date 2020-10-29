<?php


if (!defined("IN_MYBB")) {
    die("Direct initialization of this file is not allowed.");
}

/**
 * Load Plugin Library
 */
if (!defined("PLUGINLIBRARY")) {
    define("PLUGINLIBRARY", MYBB_ROOT."inc/plugins/pluginlibrary.php");
}

global $PL;
$PL or require_once PLUGINLIBRARY;

/**
 * Undocumented function
 *
 * @return void
 */
function curia_info()
{
    return array(
        "name"            => "Curia",
        "description"    => "An election management plugin.",
        "website"        => "https://tspforums.xyz/curia.html",
        "author"        => "Dylan H.",
        "authorsite"    => "https://hierocles.github.com",
        "version"        => "0.0.0",
        "guid"             => "baf8a2fc-8d9d-411a-8393-52d0a0154950",
        "codename"        => "curia",
        "compatibility" => "18*"
    );
}

/**
 * Undocumented function
 *
 * @return void
 */
function curia_install()
{
    if (!file_exists(PLUGINLIBRARY)) {
        flash_message("PluginLibrary is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }

    $PL->settings(
        'curia',
        'Election Administration',
        'Define administrative settings for Curia',
        array(
            'admin_group' => array(
                'title' => 'Administrative Group',
                'description' => 'Usergroup(s) that can adminsister elections',
                'optionscode' => 'groupselect'
            ),
            'can_delete' => array(
                'title' => 'Allow delegation',
                'description' => 'Can administrators delete elections permanently?',
                'optionscode' => 'boolean'
            )
        )
    );

}

function curia_is_installed()
{

}

function curia_uninstall()
{
    $PL->settings_delete('curia');
}

function curia_activate()
{
    if (!file_exists(PLUGINLIBRARY)) {
        flash_message("PluginLibrary is missing.", "error");
        admin_redirect("index.php?module=config-plugins");
    }

}

function curia_deactivate()
{

}
