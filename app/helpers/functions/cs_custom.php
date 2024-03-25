<?php 

define('GROUP_KEY_CUSTOM_1', "custom_option");
function menuHeaderCategory($doc) {
    $result = "";
    $_result = "";

    $listDepth = [
        "depth-2" => '<div class="nav-item dropdown dropright">
        <a href="@dropdown-handler-href" class="nav-link dropdown-toggle" data-toggle="dropdown">@dropdown-handler-text <i
                class="fa fa-angle-right float-right mt-1"></i></a>
        <div class="dropdown-menu position-absolute rounded-0 border-0 m-0">@dropdown-items</div>
    </div>',
        "depth-2-sub-item" => '<a href="@item-href" class="dropdown-item">@item-text</a>'
    ];

    $allLiTags = $doc->find('li[data-tree^="li-2-"]');

    foreach ($allLiTags as $allLiTag) {
        $currentChildCounts = intval($allLiTag->getAttribute("data-tree-child"));
        if (1 == $currentChildCounts) {

            $aTag = $allLiTag->find("a", 0);
            $aTag->setAttribute("class", "nav-item nav-link manipulate-item");

            $_result .= $aTag->__get("outertext");
        } else if (1 < $currentChildCounts) {

            $allLiTag->setAttribute("class", "dropdown-main manipulate-item");

            // dropdown handler
            $aTag = $allLiTag->find("a", 0);
            $aTag->setAttribute("class", "dropdown-handler");

            // sub menu
            $ulChildMenuTag = $allLiTag->find(".child-menu", 0);
            $ulChildMenuTag->setAttribute("class", "dropdown-menu");
            $aChildMenuTags = $ulChildMenuTag->find("a");
            foreach ($aChildMenuTags as $aChildMenuTag) {
                $aChildMenuTag->setAttribute("class", "dropdown-item");
            }

            $_result .= $allLiTag->__get("outertext");
        }
    }

    $doc = str_get_html($_result);

    $depth2 = $doc->find(".manipulate-item");

    foreach ($depth2 as $depth2Item) {
        if ($depth2Item->hasClass("dropdown-main")) {
            $handlerDOM = $depth2Item->find(".dropdown-handler", 0);

            // wrapper
            $tempHtml = groupReplacer($listDepth['depth-2'], [
                "@dropdown-handler-href",
                "@dropdown-handler-text"
            ], [
                $handlerDOM->getAttribute("href"),
                $handlerDOM->text(),
            ]);

            // item
            $subItemsList = ""; 
            foreach ($depth2Item->find(".dropdown-item") as $dropdownItem) {
               $subItemsList .= $dropdownItem->__get("outertext");
            }

            $tempHtml = groupReplacer($tempHtml, [
                "@dropdown-items",
            ], [
                $subItemsList
            ]);

            $result .= $tempHtml;
        } else {
            $result .= $depth2Item->__get("outertext");
        }
    }


    return $result;
}

function menuHeaderDepth1AppendArrow($html, $iconClass = "fa fa-angle-right mr-1")
{

    $result = "";

    $doc = str_get_html($html);

    if (is_bool($doc)) {
        return $result;
    }

    $aTags = $doc->find("a");

    foreach ($aTags as $aTag) {
        $aTag->__set("innertext", "<i class=\"{$iconClass}\"></i> " .  $aTag->text());
    }

    $result = $doc->save();

    return $result;
}

function removeIndexInStr($key, $seperator = ":")
{
    $index = stripos($key, $seperator);
    $itemKeyWithoutIndex = is_int($index) ? substr($key, 0, $index) : $key;

    return $itemKeyWithoutIndex;
}