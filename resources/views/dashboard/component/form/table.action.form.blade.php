<?php


function tableActionForm($item , $cbk = null)
{
    $postType_index_path = 'dashboard/post.type/';
    $taxonomy_index_path = 'dashboard/taxonomy/';
    $form_schema_index_path = 'dashboard/forms_schema/index';
    $form_index_path = 'dashboard/forms/index';
    $menu_index_path = 'dashboard/menu/index';
    $file_index_path = 'dashboard/file/index';
    $comment_index_path = 'dashboard/comments/comment/index';
    $rating_index_path = 'dashboard/comments/rating/index';
    $newsletter_index_path = 'dashboard/newsletter/index';
    $user_index_path = 'dashboard/user/index';
    $redirect_index_path = 'dashboard/redirect/index';
    $history_action_index_path = 'dashboard/history_action/index';

    /*
    differrnce between canShowLinkToUserByRoute and canUserAction
    canShowLinkToUserByRoute : just look at can_access 
    canUserAction or other *Action : just look at permission
    */

    # for dynamic type use isInRoute
    

    $str = "";

    $str .= "<form action=\"\">";

    if($cbk){
        $cbk($item , $str);
    }
    
    // post type
    if(isInRoute($postType_index_path)){
        postTypeListAction($item , $str);
    }

    // taxonomy
    if(isInRoute($taxonomy_index_path)){
        taxonomyListAction($item , $str);
    }

    // form schema
    if(request()->is($form_schema_index_path)){
        formsSchemaListAction($item , $str);
    }

    // form
    if(request()->is($form_index_path)){
        formsListAction($item , $str);
    }


    // menu
    if(request()->is($menu_index_path)){
        menuListAction($item , $str);
    }


    // file
    if(request()->is($file_index_path)){
        fileListAction($item , $str);
    }

    // comment
    if (request()->is($comment_index_path) || request()->is($rating_index_path)) {
        commentListAction($item , $str);
    }

    // user
    if (request()->is($user_index_path)) {
        userListAction($item , $str);
    }

    // newsletter
    if (request()->is($newsletter_index_path)) {
        newsletterListAction($item , $str);
    }

    // redirect
    if (request()->is($redirect_index_path)) {
        redirectListAction($item , $str);
    }

    // history action
    if (request()->is($history_action_index_path)) {
        historyActionListAction($item , $str);
    }

    $str .=  "</form>";

    return $str;
}
