<?php

namespace curia\Hooks;

function global_start(): void
{
    global $mybb, $lang;

    $lang->load('curia');

    if (\THIS_SCRIPT == 'misc.php') {
        if (strpos($mybb->get_input('action'), 'curia_') === 0) {
            \curia\loadTemplates([
                'hub',
                'create_election',
                'create_election_message',
                'create_election_form'
            ]);
        }
    }
}

function misc_start(): void
{
    global $mybb, $lang, $db, $plugins, $headerinclude, $header,
    $theme, $footer;

    /**
     * Planned pages/actions:
     * ADMIN
     * - curia_create_election
     * -- &run=create
     * - curia_edit_election
     * -- &run=edit
     * -- &run=archive
     * -- &run=delete
     * -- &run=count
     * -- &run=start
     * USERS
     * - curia_hub
     * - curia_view_election
     * -- &election_id=(int)
     * - curia_build_ballot
     * -- $election_id=(int)
     * -- &run=build
     * - curia_edit_ballot
     * -- &ballot_id=(int)
     * -- &run=edit
     */

    $pages = [
        'curia_create_election' => [
            'parents' => [
                'curia_hub'
            ],
            'permissions' => function (): bool {
                return \is_member(\curia\getCsvSettingValues('admin_group'));
            },
            'controller' => function (array $globals) {
                extract($globals);
                if ($mybb->input['run'] == 'create') {
                    $message = 'create function';
                    eval('$create_election_content = "' . \curia\tpl('create_election_message') . '";');
                } else {
                    eval('$create_election_content = "' . \curia\tpl('create_election_form') . '";');
                }
                eval('$page = "' . \curia\tpl('create_election') . '";');
                return $page;
            }
        ],
        'curia_edit_election' => [
            'parents' => [
                'curia_hub'
            ],
            'permissions' => function (): bool {
                return \is_member(\curia\getCsvSettingValues('admin_group'));
            },
            'controller' => function (array $globals) {
                extract($globals);

                switch ($mybb->input['run']) {
                    case 'edit':
                        break;
                    case 'archive':
                        break;
                    case 'delete':
                        break;
                    case 'start':
                        break;
                    case 'count':
                        break;
                    default:
                        break;
                }

                eval('$page = "' . \curia\tpl('edit_election') . '";');
                return $page;
            }
        ],
        'curia_hub' => [
            'permissions' => function (): bool {
                return \is_member(\curia\getCsvSettingValues('can_view'));
            },
            'controller' => function (array $globals) {
                extract($globals);
                // grab all current elections
                eval('$page = "' . \curia\tpl('hub') . '";');
                return $page;
            }
        ],
        'curia_view_election' => [
            'parents' => [
                'curia_hub'
            ],
            'permissions' => function (): bool {
                return \is_member(\curia\getCsvSettingValues('can_view'));
            },
            'controller' => function (array $globals) {
                extract($globals);
                // Get election
                eval('$page = "' . \curia\tpl('view_election') . '";');
                return $page;
            }
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
                \add_breadcrumb($lang->{$parent}, 'misc.php?action=' . $parent);
            }
        }

        \add_breadcrumb($lang->{$pageName});

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
