<?php

namespace App\Http\Controllers;

use App\Models\PostType;

class PostTypeController extends Controller
{
    public function create($type)
    {

        // check for permission in ob_post_type_general_create($buffer)

        // make it global for accessing title and other data
        generateGlobalTitle(new PostType, [
            "type" => $type,
            "typeLabel" => checkPostType($type)['current_post_type_info']['label']
        ]);

        $info = checkPostType($type);
        return view("dashboard.post_type_create", [
            'post_type' => $info['current_post_type'],
            'post_type_data' => $info['current_post_type_info'],
            'action' => __FUNCTION__,
            'DB' => [],
            'the_ID' => false,
            'route_args' => [
                'type' => $type
            ]
        ]);
    }

    public function store($type)
    {
        // check for permission
        $permissionCreate = postTypePermission($type, "create");

        if (isPostTypeForbidden($permissionCreate)) {
            abort(403);
        }

        $originalInputs = cleanTheArray(request()->post(), false);

        // fix slug if empty
        setElementValueByOtherOne($originalInputs, 'slug', 'title');

        $inputs = $originalInputs;
        $type = (checkPostType($type))['current_post_type'];

        do_action("before_post_type_" . $type . "_" . __FUNCTION__ . "_check", $inputs);
        do_action("before_post_type_" . __FUNCTION__ . "_check", $inputs);

        $error = validationUserFormInputs($inputs, [
            "status" => getStatusPage()
        ], [new PostType(), ['title']]);

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_post_type_" . $type . "_" . __FUNCTION__ . "_check", $inputs);
        do_action("after_post_type_" . __FUNCTION__ . "_check", $inputs);

        // post
        $post = new PostType();
        $new_post = $post->create([
            "title" => encodeEmojiCharactersToHtml($inputs['title']),
            "slug" => $inputs['slug'],
            "type" => $type,
            "body" => encodeEmojiCharactersToHtml($inputs['body']),
            "body_raw" => encodeEmojiCharactersToHtml($inputs['body_raw']),
            "thumbnail_url" => @$inputs['thumbnail_url'],
            "status" => $inputs['status']
        ]);

        // taxonomy
        $taxonomies = getSeperatedTaxonomyId(@$originalInputs['taxonomy']);
        if ($taxonomies) {
            $new_post->taxonomies()->attach($taxonomies);
            update_taxonomy_updated_at($new_post);
        }

        // post meta keys allowed
        $content = getPostTypeResourceHTML($type, "create", [], ["type" => $type]);
        $allowedListMetaKeys = getAllowedMetaByHtml($content);

        // meta
        updatePostTypeMeta($new_post, $inputs ?? [], $allowedListMetaKeys);

        do_action("post_type_successfully_" . __FUNCTION__ . "d", $new_post, $inputs);
        do_action("post_type_" . $type . "_successfully_" . __FUNCTION__ . "d", $new_post, $inputs);

        return redirect(getTypeEditLink($new_post, $GLOBALS['current_page'], ["type", "id"]));
    }

    public function edit($type, $id)
    {

        $post = PostType::where("id", $id)->where("type", $type)->with(['taxonomies', 'meta'])->withCount(["views", "comments"])->withAvg("comments_rating", "rating")->get();
        $post = $post->first();

        abortByEntity($post);

        do_action("post.type_edit_action", $post);
        do_action("post.type_" . $type . "_edit_action", $post);


        // check for permission in ob_post_type_general_edit($buffer)

        // make it global for accessing title and other data
        generateGlobalTitle($post, [
            "type" => $type,
            "typeLabel" => checkPostType($type)['current_post_type_info']['label']
        ]);

        $taxonomies = [];
        $i = 0;
        foreach ($post->taxonomies as $tax) {
            if (!isset($taxonomies[$tax->type])) $i = 0;
            $taxonomies[$tax->type][$i] = $tax;
            $i++;
        }

        $post['taxonomy'] = $taxonomies;

        $info = checkPostType($type);

        return view("dashboard.post_type_create", [
            'post_type' => $info['current_post_type'],
            'post_type_data' => $info['current_post_type_info'],
            'action' => __FUNCTION__,
            'DB' => $post,
            'the_ID' => $id,
            'route_args' => [
                'type' => $type,
                "id" => $id
            ]
        ]);
    }

    public function update($type, $id)
    {

        // check for permission
        $permissionEdit = postTypePermission($type);

        if (isPostTypeForbidden($permissionEdit)) {
            abort(403);
        }

        $post_data = request()->post();
        $originalInputs = cleanTheArray(request()->post(), false, ['thumbnail_url']);

        // get post
        $post = PostType::findOrFail($id);
        $primary_fields = $permissionEdit['own'];

        if (!empty($originalInputs['slug']) && !(in_array("TRUE", $primary_fields->ToArray()) || in_array("slug", $primary_fields->ToArray()))) {
            $originalInputs['slug'] = null;
        }

        if (empty($originalInputs['slug'])) {
            $originalInputs['slug'] = getTypeSlug($post);
        }

        $inputs = $originalInputs;

        checkPostType($type);

        do_action("before_post_type_" . $type . "_" . __FUNCTION__ . "_check", $inputs, $post);
        do_action("before_post_type_" . __FUNCTION__ . "_check", $inputs, $post);

        // get post
        $post = PostType::findOrFail($id);

        // post meta keys allowed
        $content = getPostTypeResourceHTML($type, "edit", $post, ["type" => $type, "id" => $id]);
        $allowedListMetaKeys = getAllowedMetaByHtml($content);

        $partial_is_active = false;

        if ($permissionEdit && !isPostTypeAllAccess($permissionEdit)) {
            $partial_inputs = [
                "_token" => @$inputs['_token'],
                "_method" => @$inputs['_method'],
            ];

            $partial_is_active = true;

            $partials_own = $permissionEdit['own'];
            $partials_taxonomy = $permissionEdit['taxonomy'];
            $partials_meta = $permissionEdit['meta'];

            // get partial own post type fields
            if ($partials_own) {
                foreach ($partials_own as $partials_own_item) {
                    $own_input = isset($inputs[$partials_own_item]) ? $inputs[$partials_own_item] : null;

                    if (!is_null($own_input))
                        $partial_inputs[$partials_own_item] = $own_input;
                }
            }

            // get partial taxonomy post type fields
            if ($partials_taxonomy) {
                $partial_inputs['taxonomy'] = [];
                foreach ($partials_taxonomy as $partials_taxonomy_item) {
                    $input_taxonomy = isset($inputs['taxonomy'][$partials_taxonomy_item]) ? $inputs['taxonomy'][$partials_taxonomy_item] : null;

                    if (!is_null($input_taxonomy))
                        $partial_inputs['taxonomy'][$partials_taxonomy_item] = $input_taxonomy;
                }
            }

            // get partial meta post type fields
            if ($partials_meta) {
                foreach ($partials_meta as $partials_meta_item) {
                    $input_meta = isset($inputs[$partials_meta_item]) ? $inputs[$partials_meta_item] : null;

                    if (!is_null($input_meta))
                        $partial_inputs[$partials_meta_item] = $input_meta;
                }
            }

            foreach ($partial_inputs as $partial_key => $partial_item) {
                $originalInputs[$partial_key] = $partial_item;
            }

            $inputs = $originalInputs;
        }

        $error = validationUserFormInputs($inputs, [
            "status" => getStatusPage()
        ], [new PostType(), ['title'], $id]);


        // add Required Field data if it's partial content
        if ($partial_is_active && $error['data']) {

            $checkPartialOf = is_part_of_partial($error['data'], $partials_own, $partials_taxonomy, $partials_meta);
            if (!$checkPartialOf['is']) {
                foreach ($error['data'] as $require_partial_item) {
                    $originalInputs[$require_partial_item] = $post[$require_partial_item];
                }
                $inputs = $originalInputs;

                $error = validationUserFormInputs($inputs, [
                    "status" => getStatusPage()
                ], [new PostType(), ['title'], $id]);
            } else {
                $error['data'] = $checkPartialOf['element'];
            }
        }



        if ($error['message'] || $error['data']) {
            return triggerServerError($error);
        }

        do_action("after_post_type_" . $type . "_" . __FUNCTION__ . "_check", $inputs);
        do_action("after_post_type_" . __FUNCTION__ . "_check", $inputs);

        // thumbnail by condition
        $thubmail_url = null;
        if (($partial_is_active && is_int($partials_own->search('thumbnail_url')) || !$partial_is_active)) {
            $thubmail_url = array_key_exists('thumbnail_url', $originalInputs) ? $originalInputs['thumbnail_url'] : null;
        } else if ($partial_is_active && !is_int($partials_own->search('thumbnail_url'))) {
            $thubmail_url = $post['thumbnail_url'];
        }

        // post update
        $post->update([
            "title" => encodeEmojiCharactersToHtml($inputs['title']),
            "slug" => $inputs['slug'],
            "body" => encodeEmojiCharactersToHtml($inputs['body']),
            "body_raw" => encodeEmojiCharactersToHtml($inputs['body_raw']),
            "thumbnail_url" => $thubmail_url,
            "status" => $inputs['status']
        ]);

        // taxonomy
        if (!isset($partials_taxonomy) || !(isset($partials_taxonomy) && !count($partials_taxonomy))) {
            $taxonomies = getSeperatedTaxonomyId(@$originalInputs['taxonomy']);

            $post->taxonomies()->detach();
            if ($taxonomies) {
                $post->taxonomies()->attach($taxonomies);
            }
            update_taxonomy_updated_at($post);
        }

        // meta
        updatePostTypeMeta($post, $post_data ?? [], $allowedListMetaKeys);

        // fix updated_at
        $post->touch();

        do_action("post_type_successfully_" . __FUNCTION__ . "d", $post, $inputs);
        do_action("post_type_" . $type . "_successfully_" . __FUNCTION__ . "d", $post, $inputs);

        return redirect(getTypeEditLink($post, $GLOBALS['current_page'], ["type", "id"]));
    }

    public function index($type)
    {

        do_action("post.type.list");
        do_action("post.type.{$type}.list");
        $post_types = PostType::with(['taxonomies', 'meta'])->where("type", $type);
        $info = checkPostType($type);

        if (!canUserListPostType($type)) abort(403);

        // make it global for accessing title and other data
        generateGlobalTitle(new PostType, [
            "type" => $type,
            "typeLabel" => checkPostType($type)['current_post_type_info']['label']
        ]);

        $filterHtml = getTableHeadPostType(['taxonomy' => $info['current_post_type_info']['taxonomy'], 'meta' => getMetaArgsList(), "post.type_type" => $type]);
        $post_types = filterListHandler($post_types, $filterHtml, ["views", "comments"], [["comments_rating", "rating"]]);

        return view("dashboard.post_type_list", [
            'post_type' => $info['current_post_type'],
            'post_type_data' => $info['current_post_type_info'],
            'DB' => $post_types,
            'route_args' => [
                'type' => $type,
            ]
        ]);
    }

    public function destroy($type)
    {
        // check for delete permission
        $postType = PostType::findOrFail(request("id"));
        $permissionDelete = postTypePermission(getTypee($postType), "delete");
        if (isPostTypeForbidden($permissionDelete)) {
            return triggerServerError(getUserMessageValidate(getMessageDeleteValue(), []));
        }

        return deleteType(getFullNamespaceByModel("PostType", "findOrfail"), [
            'callback' => "checkPostType",
            'callback_args' => [$type],
        ]);
    }

    public function statusType($type)
    {
        $result = setStatusType("PostType", "post.type", [
            'callback' => "checkPostType",
            'callback_args' => [$type],
        ]);
        return $result;
    }
}
