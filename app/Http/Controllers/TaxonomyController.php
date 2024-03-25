<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;

class TaxonomyController extends Controller
{
    public function create($type)
    {

        // make it global for accessing title and other data
        generateGlobalTitle(new Taxonomy, [
            "type" => $type,
            "typeLabel" => checkTaxonomy($type)['current_taxonomy_info']['label']
        ]);

        $info = checkTaxonomy($type);
        return view("dashboard.taxonomy_create", [
            'taxonomy_type' => $info['current_taxonomy'],
            'taxonomy_data' => $info['current_taxonomy_info'],
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
        $inputs = cleanTheArray(request()->post(), false);

        // fix slug if empty
        setElementValueByOtherOne($inputs, 'slug', 'title');

        $type = (checkTaxonomy($type))['current_taxonomy'];

        do_action("before_taxonomy_" . $type . "_" . __FUNCTION__ . "_check", $inputs);
        do_action("before_taxonomy_" . __FUNCTION__ . "_check", $inputs);

        $error = validationUserFormInputs($inputs, [
            "status" => getStatusPage()
        ], [new Taxonomy(), ["title"]]);

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_taxonomy_" . $type . "_" . __FUNCTION__ . "_check", $inputs);
        do_action("after_taxonomy_" . __FUNCTION__ . "_check", $inputs);

        // taxonomy
        $taxonomy = new Taxonomy();
        $new_taxonomy = $taxonomy->create([
            "title" => encodeEmojiCharactersToHtml($inputs['title']),
            "slug" => $inputs['slug'],
            "type" => $type,
            "body" => encodeEmojiCharactersToHtml(@$inputs['body']),
            "body_raw" => encodeEmojiCharactersToHtml(@$inputs['body_raw']),
            "thumbnail_url" => @$inputs['thumbnail_url'],
            "status" => $inputs['status']
        ]);

        do_action("taxonomy_successfully_" . __FUNCTION__ . "d", $new_taxonomy, $inputs);
        do_action("taxonomy_" . $type . "_successfully_" . __FUNCTION__ . "d", $new_taxonomy, $inputs);

        return redirect(getTypeEditLink($new_taxonomy, "taxonomy", ["type", "id"]));
    }

    public function edit($type, $id)
    {
        $taxonomy = Taxonomy::where("id", $id)->where("type", $type)->get();
        $taxonomy = $taxonomy->first();

        abortByEntity($taxonomy);

        do_action("taxonomy_edit_action", $taxonomy);
        do_action("taxonomy_" . $type . "_edit_action", $taxonomy);

        // make it global for accessing title and other data
        generateGlobalTitle($taxonomy, [
            "type" => $type,
            "typeLabel" => checkTaxonomy($type)['current_taxonomy_info']['label']
        ]);

        $info = checkTaxonomy($type);
        return view("dashboard.taxonomy_create", [
            'taxonomy_type' => $info['current_taxonomy'],
            'taxonomy_data' => $info['current_taxonomy_info'],
            'action' => __FUNCTION__,
            'DB' => $taxonomy,
            'the_ID' => $id,
            'route_args' => [
                'type' => $type,
                "id" => $id
            ]
        ]);
    }

    public function update($type, $id)
    {
        $originalInputs = cleanTheArray(request()->post(), false);
        $inputs = $originalInputs;
        $type = (checkTaxonomy($type))['current_taxonomy'];

        // fix slug if empty
        setElementValueByOtherOne($inputs, 'slug', 'title');

        do_action("before_taxonomy_" . $type . "_" . __FUNCTION__ . "_check", $inputs);
        do_action("before_taxonomy_" . __FUNCTION__ . "_check", $inputs);

        $error = validationUserFormInputs($inputs, [
            "status" => getStatusPage()
        ], [new Taxonomy(), ["title"], $id]);

        if ($error['message']) {
            return triggerServerError($error);
        }

        do_action("after_taxonomy_" . $type . "_" . __FUNCTION__ . "_check", $inputs);
        do_action("after_taxonomy_" . __FUNCTION__ . "_check", $inputs);

        // taxonomy
        $taxonomy = Taxonomy::findOrFail($id);
        $taxonomy->update([
            "title" => encodeEmojiCharactersToHtml($inputs['title']),
            "slug" => $inputs['slug'],
            "type" => $type,
            "body" => encodeEmojiCharactersToHtml(@$inputs['body']),
            "body_raw" => encodeEmojiCharactersToHtml(@$inputs['body_raw']),
            "thumbnail_url" => @$inputs['thumbnail_url'],
            "status" => $inputs['status']
        ]);

        do_action("taxonomy_successfully_" . __FUNCTION__ . "d", $taxonomy, $inputs);
        do_action("taxonomy_" . $type . "_successfully_" . __FUNCTION__ . "d", $taxonomy, $inputs);

        return redirect(getTypeEditLink($taxonomy, "taxonomy", ["type", "id"]));
    }

    public function index($type)
    {

        do_action("taxonomy.list");
        do_action("taxonomy.{$type}.list");
        $taxonomy = Taxonomy::where("type", $type);
        $info = checkTaxonomy($type);

        $filterHtml = getTableHeadTaxonomy();
        $taxonomy = filterListHandler($taxonomy, $filterHtml, ["post_types"]);

        // make it global for accessing title and other data
        generateGlobalTitle(new Taxonomy, [
            "type" => $type,
            "typeLabel" => checkTaxonomy($type)['current_taxonomy_info']['label']
        ]);

        return view("dashboard.taxonomy_list", [
            'taxonomy' => $info['current_taxonomy'],
            'taxonomy_data' => $info['current_taxonomy_info'],
            'DB' => $taxonomy,
            'route_args' => [
                'type' => $type,
            ]
        ]);
    }

    public function destroy($type)
    {
        return deleteType(getFullNamespaceByModel("Taxonomy", "findOrfail"), [
            'callback' => "checkTaxonomy",
            'callback_args' => [$type],
        ]);
    }

    public function statusType($type)
    {
        $result = setStatusType("Taxonomy", "post.type", [
            'callback' => "checkTaxonomy",
            'callback_args' => [$type],
        ]);
        return $result;
    }
}
