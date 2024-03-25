<?php

echo typeActionButtonApply();

if (isset($DB) && $DB) {

    $is_user_edit_route = isInRoute('dashboard/user/edit');
    $is_postType_edit_route = isInRoute('dashboard/post.type/');

    $user_delete_action = ($is_user_edit_route && !empty($can_delete));
    $postType_delete_action = ($is_postType_edit_route && canDeleteThisPostType($DB));

    if ($user_delete_action || $postType_delete_action || (!$is_user_edit_route && !$is_postType_edit_route)) {
        if (isRouteExists($GLOBALS['current_page'] . '.destroy')) {
            $delete_route = getURLSchema(getTypeDeleteLink($GLOBALS['current_page'], $route_args), 'path');
            $canShowDeleteRoute = canShowLinkToUser($delete_route);
            if ($canShowDeleteRoute) {
                echo typeActionButtonDelete();
            }
        }
    }

    $date_created = getTypeDateCreated($DB);
    if ($date_created) {
        echo typeActionElementDateCreated($date_created);
    }

    $date_updated = getTypeDateUpdated($DB);
    if ($date_updated) {
        echo typeActionElementDateUpdated($date_updated);
    }
}
