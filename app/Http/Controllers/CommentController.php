<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\PostType;


class CommentController extends Controller
{
    public function store($type, $fromDashboard = [])
    {
        $inputs = cleanTheArray(request()->post(), false);
        $originalInputs = $inputs;

        $current_user = getCurrentUser();

        $post_type = PostType::findOrFail($inputs['post_type_id'] ?? "");

        // check for comments/rating closed
        if (!checkCommentShowInPost($post_type, $type)) {
            return triggerServerError(getUserMessageValidate(getMessageCommentsClosed(ucwords($type), null), []));
        }

        $status = "pending";

        // from dashboard ex (super admin,admin , editor , ...)
        if ($fromDashboard) {
            $inputs['parent_id'] = $fromDashboard['parent_id'];
        }

        $commentInfo = checkComment($type);
        $comment_label = ucwords($commentInfo['current_comment_info']['label']);

        $ip = request()->ip();
        $depth = 1;
        $origin_parent_id = 0;
        $isLoggedIn = auth()->check();
        $blockRequestBase = [];

        if ($isLoggedIn) {
            $inputs['email'] = getTypeEmail($current_user);
            $inputs['fullname'] =  getTypeFullname($current_user);
            $inputs['user_id'] = getTypeID($current_user);

            // check for auto confirm comment
            $canConfirmComment = getCurrentUserRolesDetailsCanAccess("comments.reply");
            if ($canConfirmComment) {
                $status = "confirmed";
            }

            $blockRequestBase = [
                "key" => "user_id",
                "value" => $inputs['user_id']
            ];
        } else {
            $blockRequestBase = [
                "key" => "ip",
                "value" => $ip
            ];
        }

        $userMustBeLogginComment = $GLOBALS['comment_must_be_login'];
        // make sure for rating user must be loggin
        if ($type == "rating") {
            $userMustBeLogginComment = true;

            $cbk = "userCanRateThisPostType";
            if (is_callable($cbk) && $current_user) {
                $resultCbk = $cbk($type, $inputs, $post_type);
                if (is_a($resultCbk, "\Illuminate\Http\RedirectResponse")) {
                    return $resultCbk;
                }
            }
        }

        if ($userMustBeLogginComment) {
            if (!$isLoggedIn) {
                return triggerServerError([
                    "message" => getMessageCommentsMustBeLoggin($comment_label, null),
                    "data" => [],
                ]);
            }

            $inputs = getUserDetailsByKeys(['email', 'fullname', 'id'], $inputs, [null, null, "user_id"]);
        }

        $inputs['parent_id'] = intval(@$inputs['parent_id']);
        $inputs['parent_id'] = $inputs['parent_id'] <= 0 || $type == "rating" ? 0 : $inputs['parent_id'];

        $inputs['rating'] = empty($inputs['rating']) || $type != "rating" ? null : $inputs['rating'];

        if (0 < $inputs['parent_id']) {
            $replyTo = Comment::findOrFail($inputs['parent_id']);
            $tmp_origin_parent_id = getTypeAttr($replyTo, "origin_parent_id");
            if ($tmp_origin_parent_id != "0") {
                $origin_parent_id = $tmp_origin_parent_id;
            } else if ($tmp_origin_parent_id == "0") {
                $origin_parent_id = $inputs['parent_id'];
            }

            $depth = getTypeAttr($replyTo, "depth") + 1;
            if (getAllType('comment_max_depth') < $depth) {
                return triggerServerError([
                    "message" => __local("Max Nested Comment is ") . getAllType('comment_max_depth') . " " . __local("Cannot Submit this") . " {$comment_label} !",
                    "data" => ["max_nested_comment"],
                ]);
            }
        }

        do_action("before_comment_" . __FUNCTION__ . "_check", $inputs, $originalInputs);
        do_action("before_comment_" . $type . "_" . __FUNCTION__ . "_check", $inputs, $originalInputs);

        // input validation AND error handling
        $error_res = commentInputValidation($inputs, $type);
        if (is_object($error_res)) {
            return $error_res;
        }

        $post_type_info = checkPostType(getTypee($post_type));

        if (!commentExistsInPostType($post_type_info, $type)) abort(404);

        do_action("after_comment_" . $type . "_" . __FUNCTION__ . "_check", $inputs, $originalInputs);
        do_action("after_comment_" . __FUNCTION__ . "_check", $inputs, $originalInputs);

        // !$canConfirmComment -> limitation for undesire user
        if (!isset($canConfirmComment) || !$canConfirmComment) {
            $pendingRes = blockAlreadyPendingRequest(new \App\Models\Comment, [
                "status" => "pending",
                "post_type_id" => $inputs['post_type_id'],
                "type" => $type,
                $blockRequestBase["key"] => $blockRequestBase["value"]
            ]);

            if (is_object($pendingRes)) {
                return $pendingRes;
            }
        }


        // comment
        $new_comment = Comment::create([
            "type" => $type,
            "title" => encodeEmojiCharactersToHtml(getTypeTitle($post_type)),
            "post_type_id" => $inputs['post_type_id'],
            "user_id" => $inputs['user_id'] ?? 0,
            "email" => $inputs['email'],
            "fullname" => encodeEmojiCharactersToHtml($inputs['fullname']),
            "content" => encodeEmojiCharactersToHtml($inputs['content']),
            "parent_id" => $inputs['parent_id'],
            "origin_parent_id" => $origin_parent_id,
            "depth" => $depth,
            "ip" => $ip,
            "status" => $status,
            "rating" => $inputs['rating']
        ]);

        do_action("comment_successfully_" . __FUNCTION__ . "d", $new_comment, $inputs, $originalInputs);
        do_action("comment_" . $type . "_successfully_" . __FUNCTION__ . "d", $new_comment, $inputs, $originalInputs);

        return triggerServerError([
            "message" => __local(str_replace("x-label", $comment_label, __local("Thank you! Your x-label has been successfully submitted"))),
            "data" => [],
            "state" => "success"
        ]);
    }

    public function edit($type, $id)
    {
        $comment = Comment::where("id", $id)->get();
        $comment = $comment->first();

        abortByEntity($comment);

        do_action("comment_edit_action", $comment);
        do_action("comment_" . $type . "_edit_action", $comment);

        // make it global for accessing title and other data
        generateGlobalTitle($comment, [
            "type" => $type,
            "typeLabel" => checkComment($type)['current_comment_info']['label']
        ]);

        $info = checkComment($type);
        return view("dashboard.comment_create", [
            'comment_type' => $info['current_comment'],
            'comment_data' => $info['current_comment_info'],
            'action' => __FUNCTION__,
            'DB' => $comment,
            'the_ID' => $id,
            'route_args' => [
                'type' => $type,
                "id" => $id
            ]
        ]);
    }

    public function update($type, $id)
    {
        $inputs = cleanTheArray(request()->post(), false);
        $originalInputs = $inputs;

        checkComment($type);

        $inputs['rating'] = empty($inputs['rating']) || $type != "rating" ? null : $inputs['rating'];

        do_action("before_comment_" . __FUNCTION__ . "_check", $inputs, $originalInputs);
        do_action("before_comment_" . $type . "_" . __FUNCTION__ . "_check", $inputs, $originalInputs);

        // input validation AND error handling
        $error_res = commentInputValidation($inputs, $type, ['status' => getStatusComment()], $id);
        if (is_object($error_res)) {
            return $error_res;
        }

        $post_type = PostType::findOrFail($inputs['post_type_id']);
        $post_type_info = checkPostType(getTypee($post_type));

        if (!commentExistsInPostType($post_type_info, $type)) abort(404);

        do_action("after_comment_" . $type . "_" . __FUNCTION__ . "_check", $inputs, $originalInputs);
        do_action("after_comment_" . __FUNCTION__ . "_check", $inputs, $originalInputs);

        $comment = Comment::findOrFail($id);

        // comment
        $comment->update([
            "post_type_id" => $inputs['post_type_id'],
            "email" => $inputs['email'],
            "fullname" => encodeEmojiCharactersToHtml($inputs['fullname']),
            "content" => encodeEmojiCharactersToHtml($inputs['content']),
            "status" => $inputs['status'],
            "rating" => $inputs['rating']
        ]);

        do_action("comment_successfully_" . __FUNCTION__ . "d", $comment, $inputs, $originalInputs);

        return redirect(getTypeEditLink($comment, "comments", ["type", "id"]));
    }

    public function index($type)
    {
        do_action("comment.list");
        do_action("comment.{$type}.list");

        $comment = Comment::where("type", $type);
        $info = checkComment($type);

        // make it global for accessing title and other data
        generateGlobalTitle(new Comment, [
            "type" => $type,
            "typeLabel" => checkComment($type)['current_comment_info']['label']
        ]);

        $filterHtml = getTableHeadComment($type);
        $comment = filterListHandler($comment, $filterHtml);

        return view("dashboard.comment_list", [
            'comment' => $info['current_comment'],
            'comment_data' => $info['current_comment_info'],
            'DB' => $comment,
            'route_args' => [
                'type' => $type,
            ]
        ]);
    }

    public function destroy($type)
    {
        return deleteType(getFullNamespaceByModel("Comment", "findOrfail"), [
            'callback' => "checkComment",
            'callback_args' => [$type],
        ], "fullname", "deletePlusChilds");
    }

    public function statusType($type)
    {
        // identifier for block infinite loop
        if (!isset($GLOBALS['comment_status_type'])) {
            $GLOBALS['comment_status_type'] = 0;
        }

        $GLOBALS['comment_status_type']++;

        $result = setStatusType("Comment", "comments", [
            'callback' => "checkComment",
            'callback_args' => [$type],
        ], "fullname");

        return $result;
    }
}
