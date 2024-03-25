<?php 

function set_custom_option_settings($options, $_inputs)
{

    $inputs = renameArrayInput(request()->post());

    $list = extractKeysFromArrayByWord(array_keys($inputs), "cs_");


    $checkedMultipleList = [];

    foreach ($list as $itemKey) {

        // single type
        if (!is_int(stripos($itemKey, ":"))) {
            $value = getTypeAttr($inputs, $itemKey);

            if ($value) {
                $value = encodeEmojiCharactersToHtml($value);
            }

            updateOptionByKey($itemKey, $value, GROUP_KEY_CUSTOM_1);
        } else {
            // multiple type
            $itemKeyWithoutIndex = removeIndexInStr($itemKey);

            if (in_array($itemKeyWithoutIndex, $checkedMultipleList)) continue;

            $checkedMultipleList[] = $itemKeyWithoutIndex;

            $sub_list = extractKeysFromArrayByWord(array_keys($inputs), $itemKeyWithoutIndex);

            $value = [];

            foreach ($sub_list as $itemKeySub) {
                $value[] = getTypeAttr($inputs, $itemKeySub);
            }


            $value_encoded = json_encode($value, true);
            updateOptionByKey($itemKeyWithoutIndex, $value_encoded, GROUP_KEY_CUSTOM_1);
        }
    }
}


add_action('settings_successfully_updated', 'set_custom_option_settings', 10, 2);


function load_custom_options($merge_elements_type = false)
{
    $options = getOptionBuiltIn([GROUP_KEY_CUSTOM_1]);

    $options_array = [];

    foreach ($options as $option) {
        $key = getTypeAttr($option, "key");
        $value = getTypeValue($option);

        $array_value = !empty($value) ? @json_decode($value, true) : null;

        $options_array[$key] = !is_null($array_value) ? $array_value : $value;
    }

    if ($merge_elements_type) {

        $seperator = "_element_";

        $list = extractKeysFromArrayByWord(array_keys($options_array), $seperator);

        $new_list = [];

        // just elements has `_element_` in key
        foreach ($list as $item) {
            $baseKeyList = explode($seperator, $item);
            if (count($baseKeyList) != 2) continue;

            $mainKey = $baseKeyList[0];
            $subKey = $baseKeyList[1];

            if (empty($new_list[$mainKey])) {
                $new_list[$mainKey] = [];
            }

            $new_list[$mainKey][$subKey] = $options_array[$item];
        }

        // add regular item like 'cs_goals_title'
        foreach (array_keys($options_array) as $key) {
            if (!is_int(stripos($key, $seperator))) $new_list[$key] = $options_array[$key];
        }

        $options_array = $new_list;
    }


    $GLOBALS['custom_options'] = $options_array;
}

add_action("settings_edit_action", function () {
    load_custom_options(false);
});
add_action("head_front", function () {
    load_custom_options(true);
});




