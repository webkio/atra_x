<?php

function generateDataTableList($callback, $args)
{

    if (!function_exists($callback)) {
        return "No Function";
    }

    return call_user_func($callback, $args);
}

function post_type_list_general($args)
{
    $DB = $args['DB'];
    $taxonomy = $args['taxonomy'];
    $post_type_data = $args['post_type_data'];
    $metaMap = getMetaArgsList();
    $str = "";

    $groupAction = json_encode(getGroupActionPostType());

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-group-action-option='{$groupAction}' data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":2},{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadPostType(['taxonomy' => $taxonomy, 'meta' => $metaMap, 'post.type_data' => $post_type_data['slug']]);
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    foreach ($DB as $item) {
        $taxonomy_items = getHasManyTaxonomyItems($item->taxonomies);

        $post_meta = $item->meta;

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";
        $title = getTypeTitle($item);
        $excerptTitle = getTypeExcerpt($title, 6);
        $str .= "<td><p class=\"btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$title}\">" . $excerptTitle . "</p></td>";
        $str .= "<td>" . getTypeThumbnail($item) . "</td>";
        $str .= getPostTypeTaxonomyInTable($taxonomy, $taxonomy_items);
        $str .= getMetaInTableBody($post_meta, $metaMap);
        $badgeStatus = getTypeStatus($item) === "publish" ? "success" : "warning";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-" . $badgeStatus . "\">" . getStatusPage()[getTypeStatus($item)] . "</div></td>";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-primary\">" . getTypeCounts($item, "views") . "</div></td>";

        // show td order by registered comment for post type
        $allCommentsType = getAllType("comment");
        if ($allCommentsType) {
            foreach ($allCommentsType as $comment_type) {
                if (!commentExistsInPostType($post_type_data, $comment_type['slug'])) continue;
                $str .= comment_td_list_callback($comment_type['slug'], $item);
            }
        }

        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$post_type_data['label']}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function taxonomy_list_general($args)
{
    $DB = $args['DB'];
    $taxonomy_data = $args['taxonomy_data'];

    $str = "";

    $groupAction = json_encode(getGroupActionTaxonomy());

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-group-action-option='{$groupAction}' data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":2},{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadTaxonomy();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    foreach ($DB as $item) {

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";
        $str .= "<td>" . getTypeTitle($item) . "</td>";
        $str .= "<td>" . getTypeSlug($item) . "</td>";
        $str .= "<td>" . getTypeThumbnail($item) . "</td>";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-primary\">" . getTypeCounts($item, "post_types") . "</div></td>";
        $badgeStatus = getTypeStatus($item) === "publish" ? "success" : "warning";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-" . $badgeStatus . "\">" . getStatusPage()[getTypeStatus($item)] . "</div></td>";
        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$taxonomy_data['label']}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function form_schema_list_general($args)
{
    $DB = $args['DB'];

    $str = "";

    $groupAction = json_encode(getGroupActionFormSchema());

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-group-action-option='{$groupAction}' data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadFormSchema();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    $labelFormSchema = __local("Form Schema");

    foreach ($DB as $item) {

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";
        $str .= "<td>" . getTypeTitle($item) . "</td>";
        $str .= "<td>" . getTypee($item) . "</td>";

        $badgeStatus = getTypeAttr($item, 'is_login_required') ? "primary" : "warning";
        $is_login_required_key = getTypeAttr($item, 'is_login_required') ? "yes" : "no";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-" . $badgeStatus . "\">" . getAnswer()[$is_login_required_key] . "</div></td>";

        $badgeStatus = getTypeStatus($item) === "publish" ? "success" : "warning";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-" . $badgeStatus . "\">" . getStatusPage()[getTypeStatus($item)] . "</div></td>";

        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$labelFormSchema}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function form_list_general($args)
{
    $DB = $args['DB'];

    $str = "";

    $groupAction = json_encode(getGroupActionForm());

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-group-action-option='{$groupAction}' data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadForm();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    $labelFormSchema = __local("Form");

    foreach ($DB as $item) {

        $str .= "<tr>";

        $str .= "<td>" . getTypeID($item) . "</td>";

        $str .= "<td>" . getTypeAttr($item, "user_id") . " - " . getTypeExcerpt(getTypeFullname($item->user, ""), 2, true, 8) . "</td>";


        $str .= "<td>" . getTypeAttr($item, "form_schema_id") . " - " . getTypeExcerpt(getTypeTitle($item->formsSchema, ""), 2, true, 8) . "</td>";
        $str .= "<td>" . getTypeIP($item) . "</td>";

        $badgeStatus = getTypeStatus($item) === "confirm" ? "success" : "warning";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-" . $badgeStatus . "\">" . getFormStatus()[getTypeStatus($item)] . "</div></td>";


        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$labelFormSchema}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function menu_list_general($args)
{
    $DB = $args['DB'];
    $str = "";

    $labelMenu = __local("Menu");

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadMenu();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    foreach ($DB as $item) {

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";
        $str .= "<td>" . getTypeTitle($item) . "</td>";
        $str .= "<td>" . getTypeSlug($item) . "</td>";
        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$labelMenu}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function view_list_general($args)
{
    $DB = $args['DB'];
    $str = "";

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-2}]}'>";

    $thead = getTableHeadView();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";


    foreach ($DB as $item) {
        $user_id_int = getTypeAttr($item, "user_id");
        $user = $user_id_int ? $item->user : null;
        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";
        $title = getTypeAttr($item, "post_type_title");
        $excerptTitle = getTypeExcerpt($title, 5);
        $str .= "<td><p class=\"btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$title}\">" . $excerptTitle . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeAttr($item, "ip") . "</p></td>";
        $user_id = onConditionTag($user_id_int, 0 < getTypeAttr($item, "user_id"), "<p class=\"badge badge-large badge-pill badge-info\">x-tag</p>", "x-tag");
        $str .= "<td>" . $user_id . "</td>";

        $user_fullname = onConditionTag(getTypeAttr($item, "user_fullname"), ("UNKNOWN") != getTypeAttr($item, "user_fullname"), "<p class=\"badge badge-large badge-pill badge-success\">x-tag</p>", "x-tag");
        $str .= "<td>" . $user_fullname . "</td>";
        $user_email = onConditionTag(getTypeAttr($item, "user_email"), ("UNKNOWN") != getTypeAttr($item, "user_email"), "<p class=\"badge badge-large badge-pill badge-primary\">x-tag</p>", "x-tag");
        $str .= "<td>" . $user_email . "</td>";

        $user_profile_link = "-";
        $profileLabel = __local('Profile');
        if ($user)
            $user_profile_link = "<a href=\"" . getTypeEditLink($user, "user", ["id"]) . "\" class=\"badge badge-large badge-pill badge-warning\">{$profileLabel}</a>";

        $str .= "<td>" . $user_profile_link . "</td>";

        $str .= "<td>" . getTypeDateCreated($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function file_list_general($args)
{
    $DB = $args['DB'];
    $str = "";

    $labelFile = __local('File');

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-1},{\"orderable\":false,\"targets\":-3}]}'>";

    $thead = getTableHeadFile();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");
    $i = 1;
    foreach ($DB as $item) {

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";
        $url = pathToURL(getTypeURL($item));
        $preview = getGroupTypeElementMetaDataByExt(getTypeAttr($item, "group_type"), getTypeAttr($item, "format"), 'can_preview') === true ? $url : pathToURL("static/images/file.png");
        $str .= "<td><img src=\"{$preview}\" width=\"100\"></td>";
        $original_title = getTypeAttr($item, "original_title");
        $str .= "<td><p class=\"badge badge-large badge-pill badge-primary text-white btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$original_title}\">" . getTypeExcerptLetter($original_title) . "</p></td>";
        $current_title = getTypeAttr($item, "current_title");
        $str .= "<td><p class=\"badge badge-large badge-pill badge-primary text-white btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$current_title}\">" . getTypeExcerptLetter($current_title) . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeAttr($item, "format") . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeAttr($item, "group_type") . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeSize($item) . "</p></td>";

        $select = "";
        $dimension = getTypeAttr($item, "dimension");
        $dimension = json_decode($dimension, true);
        if (!is_null($dimension)) {

            $select .= "<select class=\"select2 select2-simple select2-fixed-width file-size-select\">";
            $select .= "<option value=\"{$url}\">size : Full</option>";

            $j = 0;
            $length = count($dimension);
            foreach ($dimension as $current_dimension) {
                if ($j == $length - 1) continue;

                $dimensionURL = getSubSizesImageByFilename($current_title, false, $item);
                $theURL = pathToURL($dimensionURL[$current_dimension[0]]);
                $templateDimension = generateCustomTemplateByArray("x-0px X x-1px", $current_dimension);
                $select .= "<option value=\"{$theURL}\">size : {$templateDimension}</option>";

                $j++;
            }

            $select .= "</select>";
        }
        $str .= "<td><div class=\"wrapper\">{$select}<input class=\"form-control clipboard-inp mt-2\" type=\"text\" id=\"url-link-{$i}\" readonly value=\"{$url}\"> <div class=\"text-center\"><button class=\"btn-clipboard btn badge badge-white ml-2\" data-clipboard-target=\"#url-link-{$i}\" type=\"button\"><i class=\"bi bi-clipboard-check h5\"></i></button></div></div></td>";

        $user_id = getTypeAttr($item, "user_id");
        $user_fullname = getTypeAttr($item, "user_fullname");
        $str .= "<td><p class=\"badge badge-large badge-pill badge-warning text-dark btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$user_fullname} ({$user_id})\">" . getTypeExcerpt($user_fullname, 1) . " ({$user_id})" . "</p></td>";

        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$labelFile}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
        $i++;
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function comment_list_general($args)
{
    $DB = $args['DB'];
    $comment_data = $args['comment_data'];

    $type = isset($DB[0]) ? getTypee($DB[0]) : "comment";

    $str = "";

    $groupAction = json_encode(getGroupActionComment());

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-group-action-option='{$groupAction}' data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadComment($type);
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    foreach ($DB as $item) {

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";

        $title = getTypeTitle($item);
        $post_type_id = getTypeAttr($item, "post_type_id");
        $str .= "<td><p class=\"btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$title}\">" . getTypeExcerpt($title, 2) . "({$post_type_id})" . "</td>";
        $str .= "<td id=\"fullname_th\"><p class=\"badge badge-large badge-pill badge-info\">" . getTypeExcerpt(getTypeAttr($item, "fullname"), 2) . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeAttr($item, "ip") . "</p></td>";

        $replyTo = getCommentReplyTo($item);
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeExcerpt(getTypeAttr($replyTo, "fullname", "-"), 2) . "</p></td>";

        $content = htmlspecialchars(getTypeContent($item));
        $str .= "<td><p class=\"btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$content}\">" . getTypeExcerpt($content, 2) . "</p></td>";

        $badgeStatus = getTypeStatus($item) === "confirmed" ? "success" : "warning";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-" . $badgeStatus . "\">" . __local(getStatusComment()[getTypeStatus($item)]) . "</div></td>";
        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$comment_data['label']}\" data-action-pid=\"" . getTypeAttr($item, "post_type_id") . "\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function comment_list_rating($args)
{
    $DB = $args['DB'];
    $comment_data = $args['comment_data'];

    $str = "";

    $groupAction = json_encode(getGroupActionComment());

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-group-action-option='{$groupAction}' data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":4},{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadComment("rating");
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    foreach ($DB as $item) {

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";

        $post_type_id = getTypeAttr($item, "post_type_id");
        $title = getTypeTitle($item);
        $str .= "<td><p class=\"btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$title}\">" . getTypeExcerpt($title, 2) . "({$post_type_id})" . "</td>";
        $str .= "<td id=\"fullname_th\"><p class=\"badge badge-large badge-pill badge-info\">" . getTypeExcerpt(getTypeAttr($item, "fullname"), 2) . "</p></td>";

        $rating = getTypeAttr($item, "rating");
        $str .= "<td><p class=\"badge badge-large badge-pill badge-warning\">" . intval($rating) . "</p></td>";

        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeAttr($item, "ip") . "</p></td>";

        $content = htmlspecialchars(getTypeContent($item));
        $str .= "<td><p class=\"btn btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$content}\">" . getTypeExcerpt($content, 2) . "</p></td>";

        $badgeStatus = getTypeStatus($item) === "confirmed" ? "success" : "warning";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-" . $badgeStatus . "\">" . __local(getStatusComment()[getTypeStatus($item)]) . "</div></td>";
        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$comment_data['label']}\" data-action-pid=\"" . getTypeAttr($item, "post_type_id") . "\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function newsletter_list_general($args)
{
    $DB = $args['DB'];
    $str = "";

    $labelNewsletter = __local("Newsletter");

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadNewsletter();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $listType = getNewsletterType();

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    foreach ($DB as $item) {
        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";

        $clientID = getTypeClientID($item);
        $excerpt_clientID = getTypeExcerpt($clientID, 5, true, 50);
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info cursor-pointer btn-clipboard\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"{$clientID}\">" . $excerpt_clientID . "</p></td>";

        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . $listType[getTypee($item)] ?? "" . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeIP($item) ?? "" . "</p></td>";

        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$labelNewsletter}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";


        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function user_list_general($args)
{
    $DB = $args['DB'];

    $labelUser = __local("User");

    $str = "";

    $groupAction = json_encode(getGroupActionUser());

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-group-action-option='{$groupAction}' data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":4},{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadUser();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    foreach ($DB as $item) {

        $current_role = getTypeAttr($item, "role");

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";

        $str .= "<td><p class=\"badge badge-large badge-pill badge-primary\">" . getTypeExcerpt(getTypeAttr($item, "fullname"), 2) . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypeEmail($item) . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . getTypePhone($item) . "</p></td>";
        $str .= "<td><p class=\"badge badge-large badge-pill badge-info\">" . (getUserRoles())[$current_role] . "</p></td>";

        $badgeStatus = getTypeStatus($item) === "active" ? "success" : "warning";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-" . $badgeStatus . "\">" . (getStatusUser())[getTypeStatus($item)] . "</div></td>";

        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$labelUser}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function redirect_list_general($args)
{
    $DB = $args['DB'];

    $str = "";

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadRedirect();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";
    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    $labelRedirect = __local("Redirect");

    foreach ($DB as $item) {

        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";

        $str .= "<td><div class=\"badge badge-large badge-pill badge-info\" dir=\"ltr\">" . getTypeAttr($item, "from") . "</div></td>";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-info\" dir=\"ltr\">" . getTypeAttr($item, "to") . "</div></td>";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-primary\">" . getHttpCodeRedirect()[getTypeAttr($item, "http_code")] . "</div></td>";

        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td>" . getTypeDateUpdated($item) . "</td>";
        $str .= "<td data-label=\"{$labelRedirect}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}

function history_action_list_general($args)
{
    $DB = $args['DB'];


    $str = "";

    $str = "<table id=\"datatable-list\" class=\"datatable table table-bordered\" data-options='{\"columnDefs\":[{\"orderable\":false,\"targets\":-1}]}'>";

    $thead = getTableHeadHistoryAction();
    $str .= "<thead><tr>{$thead}</tr></thead>";

    $str .= "<tbody>";

   
    $labelHistoryAction = __local("History Action");

    require_once getDashboardViewPath("component/form/table.action.form.blade.php");

    foreach ($DB as $item) {


        $str .= "<tr>";

        $str .= "<td data-seperator=\"true\">" . getTypeID($item) . "</td>";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-info\">" . getHistoryActionModelLabel($item) . "</div></td>";
        $str .= "<td data-seperator=\"true\">" . getTypeAttr($item, "model_id") . "</td>";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-primary\">" . getHistoryActionModelAction($item) . "</div></td>";
        $str .= "<td>" . getTypeDescription($item) . "</td>";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-warning\">" . getTypeAttr($item, "by") . "</div></td>";
        $str .= "<td><div class=\"badge badge-large badge-pill badge-warning\">" . getTypeAttr($item, "by_raw") . "</div></td>";
        $str .= "<td>" . getTypeDateCreated($item) . "</td>";
        $str .= "<td data-label=\"{$labelHistoryAction}\" data-action-id=\"" . getTypeID($item) . "\">" . tableActionForm($item) . "</td>";

        $str .= "</tr>";
    }

    $str .= "</tbody>";

    $str .= "</table>";

    return $str;
}
