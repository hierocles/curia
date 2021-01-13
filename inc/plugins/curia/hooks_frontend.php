<?php

namespace curia\Hooks;

function global_start(): void
{
    global $mybb, $lang;

    $lang->load('curia');

    if (strpos($mybb->get_input('action'), 'curia_') === 0) {
        // load curia templates
    }
}

function misc_start(): void
{
    global $mybb, $lang, $db, $plugins, $headerinclude, $header,
    $theme, $footer;


    /**
     * Planned pages:
     * USERS
     * - curia_view_election
     * - curia_build_ballot
     * - curia_submit_ballot
     * - curia_edit_ballot
     * ADMIN
     * - curia_create_election
     * - curia_edit_election
     * - curia_archive_election
     * - curia_delete_election
     * - curia_run_election
     */

    $pages = [
        'page_name' => [
            'parents' => [
                'parent_page_name'
            ],
            'permission' => 'function returns false to deny access',
            'controller' => 'function returning eval($page)',
        ]
    ];

    // Add a hook for potential future extensions' pages
    $plugins->run_hooks('curia_misc_pages', $pages);

    if (array_key_exists($mybb->get_input('action'), $pages)) {
        $pageName = $mybb->get_input('action');
        $page = $pages[$mybb->get_input('action')];

        // Check permissions
        if (isset($page['permission'])) {
            if ($page['permission']() === false) {
                \error_no_permission();
            }
        }

        // Add parent breadcrumbs
        if (!empty($page['parents'])) {
            foreach ($page['parents'] as $parent) {
                \add_breadcrumb($lang->{'curia_page_' . $parent}, 'misc.php?action=' . $parent);
            }
        }

        \add_breadcrumb($lang->{'curia_page_' . $pageName});

        $globals = compact([
            'mybb',
            'lang',
            'db',
            'plugins',
            'headerinclude',
            'header',
            'theme',
            'usercpnav',
            'footer',
        ]);

        $content = $page['controller']($globals);

        \output_page($content);
    }
}
