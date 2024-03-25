<?php



function postTypeListAction($item, &$str)
{
    $label = __local("PostType");

    $publishable_type = getPostTypePublishable();

    $type = getTypee($item);

    // view link
    if (in_array($type, $publishable_type))
        $str .= getTypePreviewLinkHTMLTag(getPostTypeLink($item));

    // edit button
    $permissionEdit = postTypePermission(getTypee($item), "edit");
    if (!isPostTypeForbidden($permissionEdit)) {
        $str .= getTypeEditLinkHTMLTag($item);
    }

    // status button
    $canSetStatus = canSetStateType("post.type");
    if ($canSetStatus) {

        if (getTypeStatus($item) == "publish") {
            $str .= getPostTypeActionButtons("draft");
        } else if (getTypeStatus($item) == "draft") {
            $str .= getPostTypeActionButtons("publish");
        }
    }


    // delete button
    if (canDeleteThisPostType($item)) {
        $str .= getTypeDeleteLinkHTMLTag();
    }
}

function taxonomyListAction($item, &$str)
{

    $publishable_type = getTaxonomyPublishable();

    $type = getTypee($item);

    // view link
    if (in_array($type, $publishable_type))
        $str .= getTypePreviewLinkHTMLTag(getTaxonomyLink($item));

    // edit button
    $str .= getTypeEditLinkHTMLTag($item);


    // status button
    $canSetStatus = canSetStateType("taxonomy");
    if ($canSetStatus) {
        if (getTypeStatus($item) == "publish") {
            $str .= getTaxonomyActionButtons("draft");
        } else if (getTypeStatus($item) == "draft") {
            $str .= getTaxonomyActionButtons("publish");
        }
    }

    // delete
    $str .= getTypeDeleteLinkHTMLTag();
}

function formsSchemaListAction($item, &$str)
{

    $previewLink = getShowFormLink(getTypee($item));
    $str .= getTypePreviewLinkHTMLTag($previewLink);

    // edit button
    $str .= getTypeEditLinkHTMLTag($item);

    // status button
    $canSetStatus = canSetStateType("forms_schema");
    if ($canSetStatus) {
        if (getTypeStatus($item) == "publish") {
            $str .= getFormSchemaActionButtons("draft");
        } else if (getTypeStatus($item) == "draft") {
            $str .= getFormSchemaActionButtons("publish");
        }
    }

    // delete
    $str .= getTypeDeleteLinkHTMLTag();
}

function formsListAction($item, &$str)
{

    $formSchema = $item->formsSchema;
    if ($formSchema) {
        $str .= getTypePreviewLinkHTMLTag(getShowFormLink(getTypee($formSchema), getTypeID($item)), "mt-3 ");
    }

    // status button
    $canSetStatus = canSetStateType("forms");
    if ($canSetStatus) {
        if (getTypeStatus($item) == "confirm") {
            $str .= getFormActionButtons("pending");
        } else if (getTypeStatus($item) == "pending") {
            $str .= getFormActionButtons("confirm");
        }
    }


    // delete
    $str .= getTypeDeleteLinkHTMLTag();
}

function menuListAction($item, &$str)
{
    // edit button
    $str .= getTypeEditLinkHTMLTag($item);
    // delete
    $str .= getTypeDeleteLinkHTMLTag();
}

function fileListAction($item, &$str)
{
    // delete
    $str .= getTypeDeleteLinkHTMLTag();
}

function commentListAction($item, &$str)
{

    $rawLabel = "";

    if (getTypee($item) == "comment") {
        $rawLabel = "Comment";
    } else if (getTypee($item) == "rating") {
        $rawLabel = "Rating";
    }

    $comment_link = get_post_type_comment_link(getTypeAttr($item, 'post_type_id'), getTypeID($item), getTypee($item), getTypee($item) . "_page");

    // edit button
    $str .= getTypeEditLinkHTMLTag($item);

    $canSetStatus = canSetStateType("comments");

    if ($comment_link) {
        $str .= getTypePreviewLinkHTMLTag($comment_link, "mt-3");
    }

    // pending , confirm , confirm.and.reply
    if ($canSetStatus) {
        if (getTypeStatus($item) == "confirmed") {
            $str .= getCommentActionButtons("pending", $rawLabel);
        } else if (getTypeStatus($item) == "pending") {
            $str .= getCommentActionButtons("confirmed", $rawLabel);

            if (!request()->is('dashboard/comments/rating/index')) {
                // confirm and reply
                $commentAndReplyBtn = getCommentActionButtons("confirmed", $rawLabel, "data-toggle=\"modal\" data-target=\".reply_modal\"");
                $str .= changeButtonConfrimToConfrimAndReply($commentAndReplyBtn);
            }
        }
    }




    // delete
    $str .= getTypeDeleteLinkHTMLTag();
}

function userListAction($item, &$str)
{
    $deactive_blockLabel = __local('Deactive (Block)');
    $activeLabel = __local('Active');
    $labelUser = __local('User');

    // edit button
    $str .= getTypeEditLinkHTMLTag($item);

    $canSetStatus = canSetStateType("user");
    if ($canSetStatus) {
        if (getTypeStatus($item) == "active" && canUserAction($item, "status", "deactive_block")) {
            $str .= getUserActionButtons("deactive_block");
        } else if (getTypeStatus($item) == "deactive_block" && canUserAction($item, "status", "active")) {
            $str .= getUserActionButtons("active");
        }
    }



    // delete
    if (canUserAction($item, "delete"))
        $str .= getTypeDeleteLinkHTMLTag();
}

function newsletterListAction($item, &$str)
{
    // edit button
    $str .= getTypeEditLinkHTMLTag($item , "Edit", "btn-primary" , ['id']);

    // delete
    $str .= getTypeDeleteLinkHTMLTag();
}

function redirectListAction($item, &$str)
{
    // edit button
    $str .= getTypeEditLinkHTMLTag($item);
    // delete
    $str .= getTypeDeleteLinkHTMLTag();
}

function historyActionListAction($item, &$str)
{
    $view_link = getTypeEditLinkHTMLTag($item, "View", "btn-info");
    if (getTypeAction($item) == "update")
        $str .= $view_link;
}
