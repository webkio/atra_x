<?php


use App\Models\File;
use App\Models\PostType;
use App\Models\Taxonomy;

# ==========> any http request cbk

function getHttpScheme()
{
    $scheme = ["http", "https"];
    return $scheme;
}

function anyHttp_forceHttps()
{
    if (!isset($_SERVER['REQUEST_SCHEME'])) return false;

    $scheme = getHttpScheme();
    if (strtolower($_SERVER['REQUEST_SCHEME']) == $scheme[0] && getAllType('site_ssl_force')) {
        $res = substr(CURRENT_URL, 7);
        $target = $scheme[1] . "://" . $res;
        $GLOBALS['force_redirect'] = "{$target}";
    }
}

# ==========> END any http request cbk

# =========> Controller (can be very useful for RestAPI)

function mergeControllerAndMethod($controllerName, $controllerMethod)
{
    $fullAddress = "{$controllerName}@{$controllerMethod}";
    return $fullAddress;
}

function getRouteByController($controllerName, $controllerMethod = "")
{

    $routeList = Illuminate\Support\Facades\Route::getRoutes()->getRoutes();
    $fullControllerAddress = mergeControllerAndMethod($controllerName, $controllerMethod);

    $targetRouteDetails = (object)[];

    foreach ($routeList as $route) {
        $currentControllerNameAndMethod = $route->action['controller'] ?? null;
        if (!$currentControllerNameAndMethod) continue;

        if (!$controllerMethod) {
            if (is_int(stripos($currentControllerNameAndMethod, $controllerName))) {
                $targetRouteDetails = $route;
                break;
            }
        } else {
            if ($fullControllerAddress == $currentControllerNameAndMethod) {
                $targetRouteDetails = $route;
                break;
            }
        }
    }

    if (!get_class_methods($targetRouteDetails)) return [];

    $routeAction = $targetRouteDetails->getAction();

    $routeName = $routeAction['as'];
    $controllerNameAndMethod = $routeAction['controller'];
    $controllerName = (explode("@", $controllerNameAndMethod))[0];
    $methodName = (explode("@", $controllerNameAndMethod))[1];

    return [
        "route" => $targetRouteDetails,
        "routeName" => $routeName,
        "controllerName" => $controllerName,
        "controllerMethodName" => $methodName,
    ];
}

function canCallMethodOfControllerCurrentUser($controllerName, $controllerMethod)
{
    $accessDetails = getRouteByController($controllerName, $controllerMethod);

    $preservedList = getListPreservedDashboard403();
    $preservedList = array_merge($preservedList, ["check.entity", "check.data.get.entity"]);

    $canAccess = false;

    if ($accessDetails && in_array($accessDetails['routeName'], $preservedList)) {
        $canAccess = true;
    } else if ($accessDetails) {
        $canAccess = getCurrentUserRolesDetailsCanAccess($accessDetails['routeName']);
    }

    return $canAccess;
}

# =========> END Controller

# =========> Langauge and Translate

function trnsAlignCls()
{
    return $GLOBALS['lang']['direction'] == "rtl" ? "text-right" : "";
}

function trnsAlignReverseCls()
{
    return $GLOBALS['lang']['direction'] == "rtl" ? "text-left" : "";
}

function trnsAlignBlockCls()
{
    return $GLOBALS['lang']['direction'] == "rtl" ? "d-block text-right" : "";
}

function addTranslateToJs($list)
{
    $i18nPath = getTranslateFilePath();
    $i18n_data = '{}';
    if (file_exists($i18nPath)) {
        $i18n_data = preg_replace(["/\r\n/", "/\s{2,}/"], ["", " "], file_get_contents($i18nPath));
    }

    $list[] = setVariableJavascript("i18n_system", $i18n_data, "text/javascript", "");

    return $list;
}

function getTranslateFilePath()
{
    return ROOT . "/lang/" . ("custom/{$GLOBALS['site_language']}.json");
}

function __local_cached($label)
{

    $list = $GLOBALS['cached_translate'];

    $result_label = $list[$label] ?? $label;

    return $result_label;
}

function __local($label)
{

    $result_label = $label;

    if (empty($GLOBALS['db_settings_set'])) return $result_label;

    $json_path = getTranslateFilePath();

    if (isset($GLOBALS['cached_translate'])) {
        return __local_cached($label);
    }

    if (!file_exists($json_path)) {
        $GLOBALS['cached_translate'] = [];
        return $result_label;
    }

    $GLOBALS['cached_translate'] = json_decode(file_get_contents($json_path), true);

    return __local_cached($label);
}

# =========> END Langauge and Translate

# =========> Dashboard

function isDashboard()
{
    return getPartOfUrl(0) == "dashboard";
}

# =========> END Dashboard

# =========> Dashboard Nav

function set_global_dashboard_nav()
{
    // menu element 
    $GLOBALS['dashboardNavs'] = [
        [
            "icon" => "bi bi-activity",
            "label" => __local("Overview"),
            "link" => "/dashboard/index%home_dashboard%",
            "children" => []
        ],
        // post type
        // taxonomy
        [
            "icon" => "bi bi-ui-checks-grid",
            "label" => __local("Form"),
            "link" => "%forms%",
            "children" => [
                [
                    "label" => __local("Add - Form Schema"),
                    "link" => "/dashboard/forms_schema/create",
                ],
                [
                    "label" => "%EDIT%" . __local("Edit - Form Schema"),
                    "link" => "/dashboard/forms_schema/edit/{id}",
                ],
                [
                    "label" => __local("List - Form Schema"),
                    "link" => "/dashboard/forms_schema/index",
                ],
                [
                    "label" => __local("List - Form"),
                    "link" => "/dashboard/forms/index",
                ],
            ]
        ],
        [
            "icon" => "bi bi-menu-app-fill",
            "label" => __local("Menu"),
            "link" => "",
            "children" => [
                [
                    "label" => __local("Add - Menu"),
                    "link" => "/dashboard/menu/create",
                ],
                [
                    "label" => "%EDIT%" . __local("Edit - Menu"),
                    "link" => "/dashboard/menu/edit/{id}",
                ],
                [
                    "label" => __local("List - Menu"),
                    "link" => "/dashboard/menu/index",
                ]
            ]
        ],
        [
            "icon" => "bi bi-eye-fill",
            "label" => __local("Views"),
            "link" => "/dashboard/view/index",
            "children" => []
        ],
        [
            "icon" => "bi bi-file-earmark-ruled",
            "label" => __local("Files"),
            "link" => "",
            "children" => [
                [
                    "label" => __local("Add - File"),
                    "link" => "/dashboard/file/create",
                ],
                [
                    "label" => __local("List - File"),
                    "link" => "/dashboard/file/index",
                ]
            ]
        ],
        // comments
        [
            "icon" => "bi bi-person-circle",
            "label" => __local("Users"),
            "link" => "",
            "children" => [
                [
                    "label" => __local("Add - User"),
                    "link" => "/dashboard/user/create",
                ],
                [
                    "label" => "%EDIT%" . __local("Edit - User"),
                    "link" => "/dashboard/user/edit/{id}",
                ],
                [
                    "label" => __local("List - User"),
                    "link" => "/dashboard/user/index",
                ]
            ]
        ],
        [
            "icon" => "bi bi-envelope-check",
            "label" => __local("Newsletters"),
            "link" => "",
            "children" => [
                [
                    "label" => "%EDIT%" . __local("Edit - Newsletter"),
                    "link" => "/dashboard/newsletter/edit/{id}",
                ],
                [
                    "label" => __local("List - Newsletter"),
                    "link" => "/dashboard/newsletter/index",
                ]
            ]
        ],

        [
            "icon" => "bi bi-signpost-split",
            "label" => __local("Redirect"),
            "link" => "",
            "children" => [
                [
                    "label" => __local("Add - Redirect"),
                    "link" => "/dashboard/redirect/create",
                ],
                [
                    "label" => "%EDIT%" . __local("Edit - Redirect"),
                    "link" => "/dashboard/redirect/edit/{id}",
                ],
                [
                    "label" => __local("List - Redirect"),
                    "link" => "/dashboard/redirect/index",
                ]
            ]
        ],

        [
            "icon" => "bi bi-clock-history",
            "label" => __local("History Actions"),
            "link" => "",
            "children" => [
                [
                    "label" => "%EDIT%" . __local("View - History Action"),
                    "link" => "/dashboard/history_action/edit/{id}",
                ],
                [
                    "label" => __local("List - History Actions"),
                    "link" => "/dashboard/history_action/index",
                ]
            ]
        ],
        [
            "icon" => "bi bi-gear-fill",
            "label" => __local("Settings"),
            "link" => "/dashboard/settings/edit",
            "children" => []
        ]
    ];
}

// stuffix label dashboard functions callback
function comments_stuffix_cbk()
{
    $count = getCommentCountAllTypePending();
    if ($count === 0) return "";

    return "<b data-seperator=\"true\" class=\"h6 ml-2 p-1 active-bg-darker text-white font-weight-bold rounded badge-one badge-counter\">{$count}</b>";
}

function forms_stuffix_cbk()
{
    $count = getFormsRows()->count();

    if ($count === 0) return "";

    return "<b data-seperator=\"true\" class=\"h6 ml-2 p-1 active-bg-darker text-white font-weight-bold rounded badge-one badge-counter\">{$count}</b>";
}

// END

function getDashboardMenuItems($attr)
{

    $items = [
        "icon" => $attr['icon'],
        "label" => $attr['label'],
        "link" => @$attr['link'],
        "children" => []
    ];
    return $items;
}

function getDashboardMenuChildTypeItems($prop)
{
    $items = [
        'add' => [
            "label" => __local("Add") . " - x-label",
            "link" => $prop['add']['link'] ?? null,
        ],
        'edit' => [
            "label" => __local("Edit") . "%EDIT% - x-label",
            "link" => $prop['edit']['link'] ?? null,
        ],
        'list' => [
            "label" => __local("List") . " - x-label",
            "link" => $prop['list']['link'] ?? null,
        ]
    ];
    return $items;
}

function showDashboardNavMenu($echo = true)
{

    do_action("before_show_dashboard_menu");

    $paddingUl = $GLOBALS['lang']['direction'] == "rtl" ? "p-0" : "";

    $menuStr = "<ul class=\"nav flex-column {$paddingUl}\" id=\"depth-1\">";
    $navs = $GLOBALS['dashboardNavs'];

    $i = 0;
    foreach ($navs as $nav) {
        $nav['children'] = @$nav['children'] ? $nav['children'] : [];
        $templateKey = "";
        $hasChild = 0 < count($nav['children']) ? true : false;
        $templateKey = $hasChild ?  "dashboardMenuMenu" : "dashboardMenu";
        $template = getHtmlTemplate($templateKey);

        $link = @$nav['link'];
        $link_no_query = $link ? (parse_url($link))['path'] : $link;

        $callback_specialCharcter = "";

        if ($link) {
            $specialCharcterPattern = "/\%\w+\%/m";
            preg_match($specialCharcterPattern, $link, $specialCharcterSignature);

            if (!empty($specialCharcterSignature)) {
                $link_no_query = preg_replace($specialCharcterPattern, "", $link);
                $link = preg_replace($specialCharcterPattern, "", $link);

                $specialCharcterSignature = $specialCharcterSignature[0];
                if ($specialCharcterSignature) {
                    $callback_specialCharcter = "{$specialCharcterSignature}_stuffix_cbk";
                    $callback_specialCharcter = str_replace("%", "", $callback_specialCharcter);
                }
            }
        }


        if (!canShowLinkToUser($link_no_query)) {
            continue;
        }

        $indexPlusNumber = $i + 1;
        $id = encodeChrByAnyPrintableChr($nav['label']);
        $indexPlusNumberChr = getStrByIndex($indexPlusNumber, "element-sidebar-");

        $stuffix = "";


        if (is_callable($callback_specialCharcter)) {
            $stuffix = $callback_specialCharcter();
            $nav['label'] .=  $stuffix;
        }

        $template = groupReplacer($template, ["x-icon-class", "x-label", "x-link", "x-id", "x-number", "x-chr-number"], [@$nav['icon'], @$nav['label'], $link, $id, $indexPlusNumber, $indexPlusNumberChr]);

        if ($hasChild) {
            $menuChildStr = "";
            foreach ($nav['children'] as $nv) {
                $templateChild = getHtmlTemplate('dashboardChildMenu');
                $link_child = @$nv['link'];
                $link_child_no_query = (parse_url($link_child))['path'];


                // make element like x-type/{id} -> x-type/1
                canShowLinkToUser($link_child);

                if (!canShowLinkToUser($link_child_no_query)) {
                    continue;
                }

                $templateChild = groupReplacer($templateChild, ["x-label", "x-link"], [@$nv['label'], $link_child]);

                $dBlock = "d-block";
                if (strpos($templateChild, "%EDIT%") !== false || strpos($templateChild, "%SHOW%") !== false) {
                    $templateChild = str_replace($dBlock, "{$dBlock} edit-active", $templateChild);
                }

                $menuChildStr .= $templateChild;
            }
            $template = groupReplacer($template, ['x-children'], [$menuChildStr]);
        }
        $menuStr .= $template;

        $i++;
    }

    $menuStr .= "</ul>";


    // remove UnauthorizedPostType List
    $menuStr = removeUnauthorizedType($menuStr, ["index", "create"], "post.type");

    // remove empty child
    $menuStr = removeEmptyChildElement($menuStr);

    // remove %EDIT%
    $menuStr = str_replace("%EDIT%", "", $menuStr);

    // remove %SHOW%
    $menuStr = str_replace("%SHOW%", "", $menuStr);

    // fix unknown character for persian and same language
    $menuStr = mb_convert_encoding($menuStr, "8bit");

    $cbkBeforeShow = "before_show_menu_dashboard";
    if (is_callable($cbkBeforeShow)) {
        $menuStr = $cbkBeforeShow($menuStr);
    }


    if ($echo) echo $menuStr;
    else return $menuStr;
}

function addElementDashboardNavMenuCompletely($type, $Item, $subItem, $pos = 1)
{
    foreach ($type as $postType) {
        $childElementTemp = $subItem;

        foreach ($childElementTemp as $k => $element) {
            if (is_null($element['link'])) continue;

            $childElementTemp[$k] = replaceChildMenuElementType($childElementTemp[$k], ['label' => $postType['label'], 'slug' => $postType['slug']]);
            array_push($Item['children'], $childElementTemp[$k]);
        }
    }

    addElementDashboardNavMenu($Item, $pos);
}

function addElementDashboardNavMenu($prop, int $pos = -1)
{

    $nameList = ['label', 'icon'];

    foreach ($nameList as $name) {
        if (!@$prop[$name]) return showErrorMessage($name);
    }

    if ($pos === -1)
        array_push($GLOBALS['dashboardNavs'], $prop);
    else if ($pos === 0)
        array_unshift($GLOBALS['dashboardNavs'], $prop);
    else if (0 < $pos) {
        $GLOBALS['dashboardNavs'] = addElementByPosToArray($GLOBALS['dashboardNavs'], $pos, $prop);
    } else {
        return $pos;
    }


    return $GLOBALS['dashboardNavs'];
}

function removeItemFromDashboardNavByLinkDepth1($links, $preservedUserRole = [])
{

    $links = is_array($links) ? $links : [$links];

    $user = getCurrentUser();
    $removeList = [];

    $i = 0;
    foreach ($GLOBALS['dashboardNavs'] as $item) {

        if (in_array($item['link'], $links) && !in_array(getTypeRole($user), $preservedUserRole)) {
            $removeList[] = $i;
        }

        $i++;
    }

    for ($i = 0; $i < count($removeList); $i++) {
        $itemToRemove = $removeList[$i];
        unset($GLOBALS['dashboardNavs'][$itemToRemove]);
    }

    return $removeList;
}

# =========> END Dashboard Nav 

# =====> query

function generateAliasQuery($query, $column, $alias)
{
    $columnsStr = join(",", $column);

    $query = str_replace("select *", "select " . $columnsStr, $query);
    $query = "({$query}) as {$alias}";
    return $query;
}

function removeQueryWhere($query, array|string $queryNames, &$removeColumnValues = [])
{

    if (is_string($queryNames)) $queryNames = [$queryNames];

    $query->getQuery()->wheres = collect($query->getQuery()->wheres)->reject(function ($item) use ($queryNames, &$removeColumnValues) {
        $isInArray = in_array($item['column'], $queryNames);

        if ($isInArray) {
            $removeColumnValues[$item['column']] = $item['value'];
        }

        return $isInArray;
    })->values()->all();

    // bindings need to set
    $query->getQuery()->bindings['where'] = array_map(fn ($item) => $item['value'], $query->getQuery()->wheres);

    return $query;
}

function getQueries($builder)
{
    $withSingleQuote = str_replace('?', "'?'", $builder->toSql());
    $withSingleQuote = vsprintf(str_replace('?', '%s', $withSingleQuote), $builder->getBindings());

    // make number without '
    preg_match_all("/\'\d{1,}\'/", $withSingleQuote, $matches);
    $matches = $matches[0] ?? [];
    if ($matches) {
        foreach ($matches as $match) {
            $noSingleQuote = str_replace('\'', '', $match);
            $withSingleQuote = str_replace($match, $noSingleQuote, $withSingleQuote);
        }
    }

    return $withSingleQuote;
}

function debugQuery($query)
{
    return [$query->toSql(), $query->getBindings()];
}

function filterQueryFunc($theModel, $operator, $key, $old_value, $new_value, &$sort = [])
{

    // (usually META) if column name format like this-> key:price:value:x query -> key="price" and value operator $var
    $keyWasArray = is_array($key);
    if ($keyWasArray) {
        $tmpKey = $key;
        $tmpKeyListName = array_keys($tmpKey);
        $theKey = $tmpKeyListName[0];
        $theKeyValue = $tmpKey[$theKey];
        $theVal = $tmpKeyListName[1];
        $key = $theVal;
        $theModel->where($theKey, $theKeyValue);
    }

    // is count type OR avg type Value will be like RANGE(1,10)
    if (isCountType($key) || isAvgType($key)) {
        $theModel->havingBetween($key, explode(",", $old_value));
    } // is single type ex =,LIKE
    else if ($operator['type'] == 'single') {
        // if it's number cast to int
        $value = convertToNumber($new_value);
        $theModel->where($key, $operator['operator'], $value);
    } // is array type ex BETWEEN , IN
    elseif ($operator['type'] == 'array') {

        $operatorName = $new_value;
        $value = explode(",", $old_value);

        // if it's number cast to int
        $value = convertToNumber($value);

        // if time get generate date from timestamp
        if (isTimeType($key)) {
            $key = backTimeType($key);
            foreach ($value as $k_item => $date_item) {
                $value[$k_item] = getDateByUnixTime(null, $date_item);
            }
        }

        $theModel->$operatorName($key, $value);
    }

    // run sort action to query
    if ($keyWasArray) {
        if (!empty($sort) && is_array($sort) && $sort['name'] === $theKeyValue) {
            $sortTypeCbk = getSortTypeFunc($sort['value']);
            $theVal = Illuminate\Support\Facades\DB::raw("CONVERT(`{$key}`,decimal)");
            $theModel->$sortTypeCbk($theVal);

            // add convert for main query sort
            $sort = Illuminate\Support\Facades\DB::raw("CONVERT(`{$theKeyValue}`,decimal)");
        }

        $queryOne = getQueries($theModel);
        $alias = generateAliasQuery($queryOne, [$key], $theKeyValue);

        return $alias;
    }
}


# ====> end query


# =========> type
function set_type_entity()
{
    // post type
    registerPostType([
        'label' => __local("Post"),
        'slug' => 'post',
        "status" => "public",
        'taxonomy' => ["category"],
        'comment' => ["comment"]
    ]);

    registerPostType([
        'label' => __local("Product"),
        'slug' => 'product',
        "status" => "public",
        'taxonomy' => ['product_cat'],
        'comment' => ["comment", "rating"]
    ]);

    registerPostType([
        'label' => __local("Popup Ads"),
        'slug' => 'popup_ads',
        "status" => "private",
        'taxonomy' => [],
        'comment' => []
    ]);

    registerPostType([
        'label' => __local("Term"),
        'slug' => 'term',
        "status" => "public",
        'taxonomy' => [],
        'comment' => []
    ]);

    // taxonomy
    registerTaxonomy([
        'label' => __local("Category"),
        'slug' => 'category',
        "status" => "public",
        "is_single" => false,
        "extra" => "",
        "show_in_rest" => true,
    ]);

    registerTaxonomy([
        'label' => __local("مقالات اتراوب"),
        'slug' => 'product_cat',
        "status" => "public",
        "is_single" => false,
        "extra" => "",
        "show_in_rest" => true,
    ]);

    // comment
    registerComment([
        "label" => __local("Comment"),
        "slug" => "comment"
    ]);

    registerComment([
        "label" => __local("Rating"),
        "slug" => "rating"
    ]);

    // register menu from 1 to 5
    for ($i = 1; $i <= 5; $i++) {
        registerMenu([
            'label' => __local("Menu") . " {$i}",
            'slug' => "menu-{$i}",
        ]);
    }
}

function getMessageJoined($type = "Newsletter")
{
    return __local("You Successfully Joined the {$type}");
}

function getMessageTypeCount($count, string $extra = "")
{
    return str_replace("x-count", $count, __local("you have x-count item/s to see" . $extra));
}

function getMessageRequestSuccessfullySend()
{
    return __local("your request successfully send !");
}

function getEmptyStringSign()
{
    return "####";
}

function emptyTheStringBySignEmpty(array $list, array $keys, $default = null)
{
    foreach ($keys as $key) {
        if (!in_array($key, array_keys($list))) continue;

        if ($list[$key] == getEmptyStringSign()) {
            $list[$key] = $default;
        }
    }

    return $list;
}

function hasTable($table_name = "options")
{
    return \Illuminate\Support\Facades\Schema::hasTable($table_name);
}

function getMessagePageExpiredTryAgain()
{
    return __local("page expired");
}

function getTypeThumbnailURL($item, $default = null, $relative = false)
{
    $str = "";
    $image_url = getTypeAttr($item, "thumbnail_url", $default);
    if ($image_url) {
        $image_url = pathToURL($image_url);

        if ($relative) {
            $image_url = str_replace(request()->root(), "", $image_url);
        }

        $str = $image_url;
    }

    return $str;
}

function onOfTheseOK($list, $keys)
{

    // it will break by first un null item
    // onOfTheseOK(["a" => "" , "b" => null , "c" => false , "d" => [] , "e" => "0" , "f" => "1"] , ["a","b","f"])
    // result "f"
    $theKey = "";

    foreach ($list as $key => $item) {
        if (!in_array($key, $keys)) continue;

        if ($item != "") {
            $theKey = $key;
            break;
        }
    }

    return $theKey;
}

function putInAnotherArrayIfUnEqualAllExport($list, $export)
{
    $isInAnotherArray = false;
    if ($list && $export != "*") {
        $list = [$list];
        $isInAnotherArray = true;
    }

    $list = getArrayElementForOption($list, $export);

    if ($isInAnotherArray) $list = $list[0];

    return $list;
}

function getDynamicViewType($default_name, $current_name)
{
    $view_name = view()->exists($current_name) ? $current_name : $default_name;
    return $view_name;
}

function getArrayElementForOption($list, $export)
{
    $data = [];

    foreach ($list as $key => $item) {
        if ($export != "*") {
            $data[$key] = $item[$export] ?? null;
        } else {
            $data[$key] = $item;
        }
    }

    return $data;
}

function getSetRestResponse()
{
    return [
        "message" => "",
        "status" => "fail",
        "data" => [],
    ];
}

function getTypeResourceHTML($view_name, $args)
{
    $content = view($view_name, $args)->render();

    return $content;
}

function getAllowedMetaByHtml($content)
{
    $keyList = [];
    loadHTMLParser();

    $metaPrefix = "meta:";
    $doc = str_get_html($content);

    if (is_bool($doc)) return $keyList;

    $metaList = $doc->find(".component-meta [name*={$metaPrefix}]");

    foreach ($metaList as $meta) {
        $name = $meta->getAttribute("name");
        $fixedName = str_replace($metaPrefix, "", $name);
        $keyList[] = $fixedName;
    }


    return $keyList;
}

function getPageCountByItem($total, $per_page)
{
    return intval(ceil($total / $per_page));
}

function typeActionButtonApply()
{
    $label = __local("Apply");
    return "<input data-id-form=\"#main-form\" data-callback=\"submitFormActionClick\" data-page-type=\"{$GLOBALS['current_page']}\" class=\"btn active-bg mt-3 w-100\" onclick=\"buttonFormAction(event)\" id=\"submit_action\" type=\"button\" value=\"{$label}\">";
}

function typeActionButtonDelete()
{
    $label = __local("Delete");
    return "<input data-id-form=\"#delete-form\" data-callback=\"deleteFormActionClick\" data-label=\"\" class=\"btn btn-danger w-100 mt-3\" onclick=\"buttonFormAction(event)\" id=\"delete_action\" type=\"button\" value=\"{$label}\">";
}

function getTypeDateAndTimeBySpace($date, $seperator = " ")
{
    $list = [];
    $tmpList = explode($seperator, $date);

    $list["date"] = $tmpList[0];
    $list["time"] = $tmpList[1];

    return $list;
}

function typeActionElementDateCreated($date_created)
{
    $dateData = getTypeDateAndTimeBySpace($date_created);
    $label = __local("Date Created");
    return "<div class=\"the-date date-created text-white bg-primary rounded text-center mt-2\"><span class=\"label\">{$label} : </span><span class=\"value the-date-localize\" dir=\"ltr\">{$dateData['date']}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"value the-time-localize\" dir=\"ltr\">{$dateData['time']}</span></div>";
}

function typeActionElementDateUpdated($date_updated)
{
    $dateData = getTypeDateAndTimeBySpace($date_updated);
    $label = __local("Date Updated");
    return "<div class=\"the-date date-updated text-white bg-primary rounded text-center mt-2\"><span class=\"label\">{$label} : </span><span class=\"value the-date-localize\" dir=\"ltr\">{$dateData['date']}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class=\"value the-time-localize\" dir=\"ltr\">{$dateData['time']}</span></div>";
}

function callbackListPermissions($key)
{
    $list = [
        "post.type" => "postTypePermission",
    ];

    return $list[$key] ?? "";
}

function callbackListForbidden($key)
{
    $list = [
        "post.type" => "isPostTypeForbidden",
    ];

    return $list[$key] ?? "";
}




function setElementValueByOtherOne(&$inputs, $currentKey, $borrowKey)
{
    if (!array_key_exists($currentKey, $inputs)) {
        $inputs[$currentKey] = $inputs[$borrowKey] ?? getUniqueIDByTimestamp("{$borrowKey}-");
    }
}

function getMessageWithOptionalTag($message, $tag = "b")
{
    $open_tag = "";
    $close_tag = "";

    if ($tag) {
        $open_tag = "<{$tag}>";
        $close_tag = "</{$tag}>";
    }

    return "{$open_tag}{$message}{$close_tag}";
}

function is_part_of_partial($requiredList, ...$partials)
{
    $fieldList = [];
    foreach ($partials as $part) {
        foreach ($part as $element) {
            if (in_array($element, $requiredList)) {
                $fieldList[] = $element;
            }
        }
    }

    return [
        'element' => $fieldList,
        'is' => !empty($fieldList)
    ];
}

function getMetaWrapperType($element, $type, $id, $class = "input-wrapper x-type component-meta mt-3 col-12")
{

    $class = $class ?? "input-wrapper x-type component-meta mt-3 col-12";
    $id = $id ?? getUniqueIDByTimestamp("meta");

    $searchList = ["x-type", "component-meta"];

    // make sure have ["x-type" , "component-meta"]
    foreach ($searchList as $item_s) {
        if (!is_int(stripos($class, $item_s))) {
            $class .= " {$item_s}";
        }
    }

    $class = str_replace("x-type", $type, $class);

    return "<div class=\"{$class}\" id=\"wrapper-{$id}\">" . $element . "</div>";
}

function groupTypeForInputs($typeList, $prefix = "type")
{

    $prefix =  $prefix ?? "type";

    $list = [
        $prefix => []
    ];

    $i = 0;
    foreach ($typeList as $item) {
        $label = getInsideCharacters(preg_matchOnly('/\[\w+\]/m', $item));
        if ($label) {
            $list[$prefix][$label] = ($i + 1);
        }
        $i++;
    }

    return $list;
}

function getBasePageEntity($page)
{
    return "{$page}Entity";
}

function parsePermissions($permissions)
{
    $theList = [];

    foreach ($permissions as $page => $item) {

        foreach ($item as $element) {

            $resolutions = explode(":", $element, 3);

            $action = null;
            $condition = null;
            $type = null;
            $component = null;
            $component_part = [];
            $details = [];
            $entity_levels = [];

            $action = $resolutions[0];

            $condition = $resolutions[1];
            $condition = explode("=", $condition, 2);

            // check for component
            if (1 < count($condition)) {
                $component = $condition[1];
                $component = explode(",", $component);
                if (count($component) == 1) {
                    $component = trim($component[0]);
                    $component = str_replace("^", ":", $component);
                } else if (1 < count($component)) {
                    foreach ($component as &$component_item) {
                        $component_item = trim($component_item);
                        $component_item = str_replace("^", ":", $component_item);
                    }
                }
            }
            $condition = $condition[0];

            // check for type
            $condition = explode("?", $condition, 2);
            if (count($condition) == 2) {
                $type = $condition[1];
            }
            $condition = $condition[0];

            // check for component_part
            if ($component) {
                if (is_countable($component)) {
                    foreach ($component as &$component_item) {
                        $component_item = explode("#", $component_item, 2);
                        $key = $component_item[0];
                        if (1 < count($component_item)) {
                            $component_part[$key] = $component_item[1];
                        }
                        $component_item = $key;
                    }
                } else {
                    $component = explode("#", $component, 2);
                    if (1 < count($component)) {
                        $component_part = $component[1];
                    }
                    $component = $component[0];
                }
            }

            $details = $resolutions[2] ?? "[]";
            $details = json_decode($details, true);


            $entity_level_cbk = getBasePageEntity($page);

            if (is_callable($entity_level_cbk)) {
                $entity_levels = call_user_func($entity_level_cbk, $condition);
            }

            $theList[$page][] = [
                "action" => $action,
                "condition" => $condition,
                "type" => $type,
                "component" => $component,
                "component_part" => $component_part,
                "details" => $details,
                "entity_levels" => $entity_levels,
            ];
        }
    }



    return $theList;
}

function getModelAndInputsByID($model_name)
{
    $id = request('id');
    $model = call_user_func(getFullNamespaceByModel($model_name, "findOrfail"), $id);
    $inputs = $model->getAttributes();
    return [
        "model" => $model,
        "inputs" => $inputs,
    ];
}

function fixModelToOriginal(&$model)
{
    $original = $model->getOriginal();
    $attributes = $model->getAttributes();

    $keys = array_keys($attributes);

    foreach ($keys as $key) {
        if (!isset($original[$key])) {
            unset($model->$key);
        }
    }
}

function getUniqueIDByTimestamp($prefix = "element-")
{
    $time = getRecentlyUnix();
    $integer = randomInteger(3696, 15360);
    return "{$prefix}{$integer}-{$time}";
}

function cryptStringOneWayStr(string $str)
{
    if (!$str && $str != "0") return "";

    return base_convert(crc32($str), 10, 36);
}

function getUniqueStr($prefix = "")
{
    $str = cryptStringOneWayStr($prefix . intval(floor(microtime(true) * 1000)));
    return $str;
}

function randomInteger(int $from = 1000, int $to = 10000)
{
    return random_int($from, $to);
}

function customEncryptSSL($data, $salt, $cipher_algo = "aes-256-cbc")
{
    $key = hash('sha256', $salt, true);
    $iv = random_bytes(16);

    $encrypted = @openssl_encrypt($data, $cipher_algo, $key, 0, $iv);

    return @base64_encode($iv . $encrypted);
}

function customDecryptSSL($data, $salt, $cipher_algo = "aes-256-cbc")
{
    $key = hash('sha256', $salt, true);

    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);

    return @openssl_decrypt($encrypted, $cipher_algo, $key, 0, $iv);
}

function redirectAfterAction($route_name)
{
    return request()->routeIs($route_name) ? "same" : "index";
}

function getDashboardEditForm()
{
    return $GLOBALS['current_page'] . ".edit.form";
}

function messageShortNotVerified($field)
{
    $fieldCapital  = ucwords($field);
    return __local("{$fieldCapital} not verified yet");
}

function messageNotVerifiedYet($field)
{
    $fieldCapital = ucwords($field);
    return __local("{$fieldCapital} not verified yet please verify that");
}

function typeRegexData($type)
{
    $list = [
        "username_user" => [
            "regex" => ["/[a-z]+/", ["/[0-9]*/", true], ["/[_\.]*/", true]],
            "description" => __local("username must be have at least one alphabetical character and 0-9 (optional) and ._ (optional)"),
            "sample" => "arya_09",
        ],
        "phone_user" => [
            "regex" => ['/^(09\d{9})$/'],
            "description" => __local("enter valid phone number"),
            "sample" => "09XXXXXXXXX",
        ],
        "password_user" => [
            "regex" => ["/[a-zA-Z]+/", "/[0-9]+/", ["/[!@#$%*^&_\-+=()]*/", true]],
            "description" => __local("password must be have at least one alphabetical character and 0-9 and special characters !@#$%*^&_\-+=() (optional)"),
            "sample" => "123ABcD@",
        ],
        "must_positive_int_general" => [
            "regex" => ["/^\d+$/"],
            "description" => __local("this input must be integer and more than -1 (x-field)"),
            "sample" => "8",
        ],
    ];

    return $list[$type] ?? [];
}

function resetGlobal($name, $value = null)
{
    $GLOBALS[$name] = $value;
}

function getAllType($typeName, $export = null)
{
    $list = isset($GLOBALS[$typeName]) ? $GLOBALS[$typeName] : [];
    $exportList = [];

    if (is_array($list) && 1 < count($list) && !is_null($export)) {
        foreach ($list as $item) {
            if (isset($item[$export])) {

                $currentItem = $item[$export];
                $exportList[] = $currentItem;
            }
        }
    } else if (is_array($list) && 1 == count($list) && !is_null($export)) {
        $tmpList = last($list);
        $exportList[] = $tmpList[$export];
    }

    if ($exportList) {
        $list = $exportList;
    }

    return $list;
}

function getAllTypeByCondition($elements, $keys, $cbk, $preserveKey = false)
{
    $list = collect($elements);
    $theList = $cbk($list);
    $tmp_theList = [];

    if (!is_array($keys)) {
        $keys = [$keys];
    }

    foreach ($theList as $element) {
        $element = collect($element);
        $values = ($element->only($keys))->toArray();
        if (count($keys) == 1) {
            if (!$preserveKey)
                $tmp_theList[] = $values[$keys[0]];
            else
                $tmp_theList[$keys[0]] = $values[$keys[0]];
        } else if (1 < count($keys)) {
            $theKeysValue = [];
            foreach ($keys as $item) {
                if (!$preserveKey)
                    $theKeysValue[] = $values[$item];
                else
                    $theKeysValue[$item] = $values[$item];
            }

            $tmp_theList[] = $theKeysValue;
        }
    }

    return $tmp_theList;
}

function getTypeOptionWithReserved($item, $model_name, $options_cbk, $byColumn = "slug", $showWhat = "title", $showWhatFormat = "(x)")
{
    $str = "";

    if (!$byColumn) $byColumn = "slug";
    if (!$showWhat) $showWhat = "title";
    if (!$showWhatFormat) $showWhatFormat = "(x)";

    if (!function_exists($options_cbk)) dd("Function Not Found " . __FUNCTION__);

    $options = $options_cbk();

    $model = call_user_func(getFullNamespaceByModel($model_name, "all"));

    foreach ($options as $key_op => $val_op) {
        $reservedName = "";
        $selected = "";


        if (isset($item) && $item == $key_op) {
            $selected = "selected";
        }

        foreach ($model as $model_item) {
            if ($key_op == $model_item->$byColumn) {
                $reservedName = str_replace('x', $model_item->$showWhat, $showWhatFormat);
                break;
            }
        }

        $str .= "<option {$selected} value=\"{$key_op}\">{$val_op} {$reservedName}</option>";
    }

    return $str;
}

function getDefaultType($str, $default, $equal = "")
{
    return $str == $equal ? $default : $str;
}

function blockAlreadyPendingRequest($model, array $whereBy, $condition = null)
{
    $condition = $condition ?? "where";
    foreach ($whereBy as $key => $item) {
        $model = $model->$condition($key, $item);
    }

    $model = $model->get();

    $message = [];
    $back = null;
    if (!is_null($model->first())) {
        $message = getUserMessageValidate();
        $message['message'] = __local("You Have a Request in Pending Status try later");
        $message['data'] = [];
    }

    if (!empty($message['message'])) {
        $back = back()->withInput()->withErrors(
            [
                'jsonServerMessage' => json_encode($message)
            ]
        );
    }

    return $back;
}

function getActionTypeDataByInterface($name)
{
    $list = [
        "delete" => [
            "message" => __local("Successfully Deleted !")
        ],
        "deactive" => [
            "message" => __local("Successfully Deactivated !")
        ],
        "deactive_block" => [
            "message" => __local("Successfully Blocked !")
        ],
        "active" => [
            "message" => __local("Successfully Activated !")
        ],
        "confirmed" => [
            "message" => __local("Successfully Confirmed !")
        ],
        "confirm" => [
            "message" => __local("Successfully Confirmed !")
        ],
        "cancel" => [
            "message" => __local("Successfully Cancelled !")
        ],
        "updated" => [
            "message" => __local("Successfully Updated !")
        ],
    ];

    return $list[$name] ?? [];
}

function activeType($modelWithMethod, $check = [], $title = "title", $action = "active")
{
    return deleteType($modelWithMethod, $check, $title, $action, $action);
}

function deactiveType($modelWithMethod, $check = [], $title = "title", $action = "deactive")
{
    return deleteType($modelWithMethod, $check, $title, $action, $action);
}

function deactive_blockType($modelWithMethod, $check = [], $title = "title", $action = "deactive_block")
{
    return deleteType($modelWithMethod, $check, $title, $action, $action);
}

function deleteType($modelWithMethod, $check = [], $title = "title", $action = "delete", $interface = null)
{

    $check = $check ?? [];
    $title = $title ?? "title";
    $action = $action ?? "delete";
    $interface = $interface ?? "delete";

    $interfaceData = getActionTypeDataByInterface($interface);
    $stuffixMessage = $interfaceData ? $interfaceData['message'] : " - " . __local("Done !");

    $id = request('id');
    $redirectTo = request('redirect');

    $referer = request()->headers->get('referer',  "");
    $backLink = $referer;

    if ($check) {
        $res = call_user_func($check['callback'], ...$check['callback_args']);
        if (!$res || is_object($res)) return $res;
    }

    $model = call_user_func($modelWithMethod, $id);
    $class_name = class_basename($model);

    do_action("before_" . strtolower($class_name) . "_successfully_" . strtolower($action) . "d", $model);

    if (method_exists($model, $action)) {
        $model->$action();
    } else {
        $secondAction = "setStatus";
        $model->$secondAction($action);
    }



    $title = !empty($model->$title) ? $model->$title : "X";

    $message = getUserMessageValidate("{$title} {$stuffixMessage}");

    do_action(strtolower($class_name) . "_successfully_" . strtolower($action) . "d", $model);

    $strpos = strpos($referer, "edit/");
    if (is_int($strpos) && $redirectTo != "same") {
        $backLink = substr($referer, 0, $strpos);
        $backLink .= $redirectTo;
    }

    if ($message['message']) {
        return redirect($backLink)->withErrors([
            'jsonServerMessage' => json_encode($message)
        ]);
    }
}

function statusType($modelWithMethod, $action, $check = [], $title = "title")
{
    return deleteType($modelWithMethod, $check, $title, $action, $action);
}

function confirmType($modelWithMethod, $data, $redirect = true, $messageSuccess = [], $messageFail = [], $check = [])
{
    $id = request("id");

    $default_messageSuccess = getUserMessageValidate(getActionTypeDataByInterface("confirmed")["message"], ["status" => "success"]);
    $default_messageFail = getUserMessageValidate(__local("Cannot Update this Request"), ["status" => "error"]);

    $messageSuccess = $messageSuccess ?: $default_messageSuccess;
    $messageFail = $messageFail ?: $default_messageFail;

    if ($check) {
        $res = call_user_func($check['callback'], ...$check['callback_args']);
        if (!$res) return $res;
    }

    $model = call_user_func($modelWithMethod, $id);

    $res = $model->update($data);

    $message = [];

    if ($res) {
        $message = $messageSuccess;
    } else {
        $message = $messageFail;
    }

    $res = $model;
    if ($redirect) {
        $res = triggerServerError($message);
    }

    return $res;
}

function cancelType($modelWithMethod, $data, $redirect = true, $messageSuccess = [], $messageFail = [], $check = [])
{
    return confirmType($modelWithMethod, $data, $redirect, $messageSuccess, $messageFail, $check);
}

function getTypeLink($model, $extra = "", $justExtra = false)
{
    if (!$model)
        return "";

    $id = "/{$model->id}";

    $extraURL = "";

    if ($extra) {

        $theExtra = $model->$extra;

        $extraURL = "/{$theExtra}";
        if ($justExtra) $id = "";
    }

    return request()->root() . "/{$model->type}{$id}{$extraURL}";
}

function makeURLAbsoulte($url)
{
    return str_replace(ROOT_URL, "", $url);
}

function getTypeEditLinkHTMLTag($item, $text = "Edit", $class = "btn-primary", $route_args = ['type', 'id'])
{
    $str = "";
    $text = __local($text);
    $editLink = getTypeEditLink($item, $GLOBALS['current_page'], $route_args);
    $str = "<a href=\"{$editLink}\" class=\"d-block w-100 btn {$class}\">{$text}</a>";
    return $str;
}

function getTypeEditLink($model, $route_prefix, $route_args, $model_name = "", $id = 0)
{
    if (!$model && is_callable($model_name))
        $model = call_user_func($model_name, $id);
    else if (!$model && !is_callable($model_name)) {
        return "";
    }

    $new_route_args = [];

    foreach ($route_args as $arg) {
        $new_route_args[$arg] = $model[$arg];
    }

    return route("{$route_prefix}.edit.form", $new_route_args);
}

function getTypeIndexLink($route_prefix, $route_args = [])
{
    $route_stuffix = ".list";
    $link = route("{$route_prefix}{$route_stuffix}", $route_args);

    return $link;
}

function getTheRoute($prefix, $action, $args)
{
    $route_name = "{$prefix}.{$action}";
    $route = route($route_name, $args);
    return $route;
}

function getTheCurrentRouteWithQuery($queryList)
{
    $query = http_build_query($queryList);
    $url = request()->url() . "?{$query}";
    return $url;
}

function filterListHandler($modelQuery, $filterHtmlTemplate, $countList = [], $avgList = [])
{
    // get query also filter for (null , "",false)
    $filterData = array_filter(request()->query(), function ($element) {
        return $element != "";
    });

    // sort and order by defaults
    $sortGroup = [];
    $orderBy = "id";
    $orderByTemp = "";
    $sortType = "desc";

    // if passed any filter query data go for searching
    if ($filterData) {
        // get allowed field from form list
        $filterTemplate = getFilterTemplate($filterHtmlTemplate);

        // get sort from query
        $filterOrganize = $filterData['sort'] ?? false;
        $filterOrganize = explode(":", $filterOrganize);

        if (sizeof($filterOrganize) == 2) {
            $orderByTemp = $filterOrganize[0];
        }

        // get order by from query
        if (in_array($orderByTemp, array_keys($filterTemplate))) {
            if (isTimeType($orderByTemp)) $orderByTemp = backTimeType($orderByTemp);
            $orderBy = $orderByTemp;
            $tmpType = isValidSort($filterOrganize[1]);

            $sortType = $tmpType ? $tmpType : $sortType;
        }


        unset($filterData['sort']);
        // select $aliasList from x-table
        $aliasList = ["*"];

        // loop throw query (key | value)
        foreach ($filterData as $key => $value) {
            // if not allowed query ignore this iteration
            if (!in_array($key, array_keys($filterTemplate))) continue;

            // get data from key and return (operator , ...)
            $currentFilterTemplate = $filterTemplate[$key];
            // get sign operator ex (equal -> =)
            $operator = getSqlOperatorByName($filterTemplate[$key]['operator']);
            // get sign term operator ex (LIKE -> %term%)
            $val = getSqlSignByOperator($operator, $value);

            if (!$operator) {
                continue;
            }

            // if table has relation
            if ($relationFilterData = $currentFilterTemplate['relation'] ?? false) {
                /*
                $relationFilterData = [
                "tableKey" => "table_name"
                "column" => "column_name(usually x_id)"
                ]
                */

                $sortGroup = [
                    'name' => $orderBy,
                    'value' => $sortType,
                ];

                // if has relation by passed tableKey
                $modelQuery->whereRelation($relationFilterData['tableKey'], function ($query) use ($relationFilterData, $operator, $value, $val, &$sortGroup, &$aliasList) {
                    $alias = filterQueryFunc($query, $operator, $relationFilterData['column'], $value, $val, $sortGroup);
                    if ($alias) $aliasList[] = Illuminate\Support\Facades\DB::raw($alias);
                });

                if (!is_array($sortGroup)) {
                    $orderBy = $sortGroup;
                }
            } else {
                // if has no relation just regular query will be run
                filterQueryFunc($modelQuery, $operator, $key, $value, $val);
            }
        }
        // select action trigger will be (* or Else)
        $modelQuery->select($aliasList);
    }

    // if query has count get count of relation
    if ($countList) {
        // counts
        $modelQuery->withCount($countList);
    }

    // if query has average get avg of relation
    if ($avgList) {
        // average
        foreach ($avgList as $avg) {
            // [relation , column]
            $modelQuery->withAvg(...$avg);
        }
    }

    // sort and order by
    $sortTypeCbk = getSortTypeFunc($sortType);
    $modelQuery->$sortTypeCbk($orderBy);

    // if want to see list
    if (!request()->query("export"))
        $modelQuery = $modelQuery->paginate($GLOBALS['record_per_page']);
    else if (request()->query("export") == "query") {
        $modelQuery = $modelQuery;
    } else {
        // if want to get export list
        $modelQuery = $modelQuery->get();
    }

    $GLOBALS["entity_rows"] = $modelQuery;

    return $modelQuery;
}

function getMetaInTableBody($meta, $metaMap)
{

    $str = "";
    foreach ($metaMap as $map) {
        $currentMeta = $meta->where("key", $map['data-name'])->first();
        $tag = getTagTemplate("td");
        if (!$currentMeta) {
            $str .= getTagTemplateWithComplex($tag, [], "-");
            continue;
        }
        $str .=  getTagTemplateWithComplex($tag, ["data-seperator" => "true"], $currentMeta->value);
    }

    return $str;
}

function backTimeType($name, $prefix = "time_")
{
    return str_replace($prefix, "", $name);
}

function isTimeType($name, $prefix = "time_")
{
    return strpos($name, $prefix) !== false;
}

function isCountType($name, $stuffix = "_count")
{
    if (is_array($name)) return false;

    return strpos($name, $stuffix) !== false;
}

function isAvgType($name, $stuffix = "_avg_")
{
    if (is_array($name)) return false;

    return strpos($name, $stuffix) !== false;
}

function getSortTypeFunc($type)
{
    $list = [
        "asc" => "oldest",
        "desc" => "latest",
    ];

    return $list[$type] ?? false;
}

function isValidSort($type)
{
    $type = strtolower($type);
    $list = ["asc", "desc"];

    return in_array($type, $list) ? $type : false;
}

function getSqlSignByOperator($operator, $data)
{
    if (!is_array($data)) {
        $data = [$data];
    }

    $list = [
        "=" => [
            "sign" => 'x',
        ],
        "LIKE" => [
            "sign" => '%x%',
        ],
        "IN" => "whereIn",
        "BETWEEN" => "whereBetween"
    ];

    $sign = false;

    if (empty($list[$operator['operator']]) || !count($data)) return $sign;
    else {
        $curentSign = $list[$operator['operator']];

        $replaceTheValue = function ($search, $replace) use ($curentSign) {
            return str_replace($search, $replace, $curentSign['sign']);
        };

        if (isset($curentSign['sign'])) {
            $sign = $replaceTheValue("x", $data[0]);
        } else {
            $sign = $list[$operator['operator']];
        }
    }

    return $sign;
}

function getSqlOperatorByName($name)
{
    $list = [
        "equal" => [
            "operator" => "=",
            "type" => "single",
        ],
        "like" => [
            "operator" => "LIKE",
            "type" => "single",
        ],
        "or" => [
            "operator" => "IN",
            "type" => "array",
        ],
        "range" => [
            "operator" => "BETWEEN",
            "type" => "array",
        ]
    ];

    return $list[$name] ?? false;
}

function getFilterTemplate($html)
{
    $dom = new DomDocument();
    $dom->loadHTML($html);

    $th = $dom->getElementsByTagName("th");
    $attr = [];

    foreach ($th as $t) {
        $attrDataName = $t->getAttribute("data-name");
        $attr[$attrDataName] = [
            'operator' => $t->getAttribute("data-operator")
        ];

        $attrRelation = $t->getAttribute("data-relation");
        if (!empty($attrRelation)) {
            $attrRelation = explode(":", $attrRelation);
            if (count($attrRelation)) {
                $attr[$attrDataName]['relation']['tableKey'] = $attrRelation[0];
                if (2 < count($attrRelation)) {
                    $attr[$attrDataName]['relation']["forignID"] = $attrRelation[1];
                    $attr[$attrDataName]['relation']["column"] = [
                        "key" => $attrRelation[2],
                        "value" => "x"
                    ];
                } else {
                    $attr[$attrDataName]['relation']["column"] = $attrRelation[1];
                }
            }
        }
    }

    $attr = array_filter($attr, fn ($element) => !empty($element['operator']));

    return $attr;
}

function isDynamicTypePublic($type_info, $key)
{
    return $type_info[$key]['status'] === "public";
}

function checkDynamicType($type, $callback)
{
    $current_type_info = call_user_func($callback, $type);
    if ($current_type_info === false) {
        abort(404, "invalid type");
    }
    return [
        "current_type_info" => $current_type_info,
    ];
}

function replaceChildMenuElementType($element, $attr)
{
    $element['link'] = str_replace('x-type', $attr['slug'], $element['link']);
    $element['label'] = str_replace('x-label', $attr['label'], $element['label']);
    return $element;
}

function create_simple_html_template($html)
{
    $html_template = "<!DOCTYPE html><html><body>{$html}</body></html>";
    return $html_template;
}

function createHtmlElement($attrs)
{

    $result = "";
    if (!$attrs) return $result;

    loadHTMLParser();
    $_attrs = $attrs;

    $doc = str_get_html(create_simple_html_template(""));

    $element = $doc->createElement($_attrs['tagName']);
    unset($_attrs['tagName']);

    foreach ($_attrs as $attrKey => $attrValue) {
        if ($attrKey == "text") {
            $element->__set("innertext", $attrValue);
        } else {
            $element->setAttribute($attrKey, $attrValue);
            $settedAttribute = $element->getAttribute($attrKey);
        }
    }

    $result = $element->__get("outertext");

    return $result;
}

// getType*() 

function getTypeAttr($item, $attr, $defualt = null)
{
    return isset($item[$attr]) ? $item[$attr] : $defualt;
}

function getTypeExcerpt(string $str, int $word = 5, $html_encode = true, $wordChrRate = 15)
{
    $tmpStr = trim($str);
    $word = abs($word);
    if (!$tmpStr) return $str;

    $list = explode(" ", $tmpStr);

    if (count($list) < $word) {
        $word = count($list);
    }

    $tmpStr = array_slice($list, 0, $word);
    $tmpStr = join(" ", $tmpStr);

    if ($html_encode) {
        $tmpStr = htmlentities($tmpStr);
    }

    // any word has 6 limit character -> 6 * 2 = 12 character limit
    $tmpStr = mb_substr($tmpStr, 0, ($wordChrRate * $word));

    return $tmpStr;
}

function getTypeExcerptHtml($html, $word = 5, $wordChrRate = 30)
{
    loadHTMLParser();

    if (!$html) return $html;

    $doc = str_get_html($html);

    if (is_bool($doc)) $doc = create_simple_html_template($html);
    if (is_string($doc)) $doc = create_simple_html_template("<p>" . $html . "</p>");

    $content = $doc->__get('plaintext');

    $excerpt_content = getTypeExcerpt($content, $word, false, $wordChrRate);


    return $excerpt_content;
}

function getTypeExcerptLetter(string $str, int $letters = 8)
{
    $tmpStr = trim($str);
    $letters = abs($letters);

    if ($tmpStr == "") return $str;

    $str_letters = str_split($str, 1);

    if (count($str_letters) < $letters) {
        $letters = count($str_letters);
    }

    return mb_substr($tmpStr, 0, $letters);
}

function getTypeAddress($type, $default = null)
{
    $str = "";
    $str = getTypeAttr($type, "address", $default);

    return $str;
}

function getTypeTel($type, $default = null)
{
    $str = "";
    $str = getTypeAttr($type, "tel", $default);

    return $str;
}

function getTypeTitle($type, $default = null)
{
    $str = "";
    $str = getTypeAttr($type, "title", $default);

    return $str;
}

function getTypeAction($type)
{
    $str = "";
    $str = getTypeAttr($type, "action");

    return $str;
}

function getTypeClientID($type, $default = null)
{
    $str = "";
    $str = getTypeAttr($type, "client_id", $default);

    return $str;
}

function getTypeIP($type, $default = null)
{
    $str = "";
    $str = getTypeAttr($type, "ip", $default);

    return $str;
}

function getTypeEmail($type)
{
    $str = "";
    $str = getTypeAttr($type, "email");

    return $str;
}

function getTypePhone($type)
{
    $str = "";
    $str = getTypeAttr($type, "phone");

    return $str;
}

function getTypeName($type)
{
    $str = "";
    $str = getTypeAttr($type, "name");

    return $str;
}

function getTypeFullname($type, $default = null)
{
    $str = "";
    $str = getTypeAttr($type, "fullname", $default);

    return $str;
}

function getTypeKey($type)
{
    $str = "";
    $str = getTypeAttr($type, "key");

    return $str;
}

function getTypeValue($type)
{
    $str = "";
    $str = getTypeAttr($type, "value");

    return $str;
}

function getTypeLabel($type)
{
    $str = "";
    $str = getTypeAttr($type, "label");

    return $str;
}

function getTypeUsername($type)
{
    $str = "";
    $str = getTypeAttr($type, "username");

    return $str;
}

function getTypePassword($type)
{
    $str = "";
    $str = getTypeAttr($type, "password", "");

    return $str;
}

function getTypeSizes($type)
{
    $str = "";
    $str = getTypeAttr($type, "sizes");

    return $str;
}

function getTypeDimension($type)
{
    $str = "";
    $str = getTypeAttr($type, "Dimension");

    return $str;
}

function getTypeRole($type)
{
    $str = "";
    $str = getTypeAttr($type, "role", "");

    return $str;
}


function getTypeContent($type)
{
    $str = "";
    $str = getTypeAttr($type, "content");

    return $str;
}

function getTypeDescription($type)
{
    $str = "";
    $str = getTypeAttr($type, "description");

    return $str;
}

function getTypeBody($type)
{
    $str = "";
    $str = getTypeAttr($type, "body");

    $old_str = $str;

    if ($str) {
        // make sure regex detect all patterns
        $str = str_replace(["</p>", "</div>"], ["</p>\n", "</div>\n"], $str);
        // shortcut text
        $shortcutTextList = findShortcutTextInString($str);


        foreach ($shortcutTextList as $shortcutTextItem) {
            $shortcutTextDetails = extractShortcutText($shortcutTextItem);
            if (!$shortcutTextDetails) continue;

            $cbk = getShortcutTextCbkName($shortcutTextDetails['tagName']);

            $result = "";

            $isCalledCbkShortcutText = false;

            if (is_callable($cbk)) {
                $result = $cbk(...$shortcutTextDetails);
                $isCalledCbkShortcutText = true;
            }

            $str = $isCalledCbkShortcutText ? str_replace($shortcutTextItem, $result, $str) : $old_str;

            $old_str = $str;
        }

        // filter body
        if (is_object($type) || is_array($type)) {
            $class_type = is_object($type) ? class_basename($type) : $type['type'];
            $class_type = strtolower($class_type);

            $real_type = getTypee($type);

            if ($class_type && $real_type) {
                $cbk = $class_type . "_{$real_type}_body_action";
                if (is_callable($cbk)) {
                    $str = $cbk($type, $str);
                }
            }
        }
    }

    return $str;
}

function getTypeBodyRaw($type, $default = null)
{
    $str = "";
    $str = getTypeAttr($type, "body_raw", $default);

    return $str;
}

function getTypeSlug($type)
{
    $str = "";
    $str = getTypeAttr($type, "slug");

    return $str;
}

function getTypeSize($type)
{
    $str = "";
    $str = getTypeAttr($type, "size");

    return $str;
}

function getTypeURL($type)
{
    $str = "";
    $str = getTypeAttr($type, "url");

    return $str;
}

function getTypee($type)
{
    $str = "";
    $str = getTypeAttr($type, "type");

    return $str;
}

function getTypeGroupType($type)
{
    $str = "";
    $str = getTypeAttr($type, "group_type");

    return $str;
}

function getTypeVerified($type, $name, $stuffix = "_verified")
{
    $str = "";
    $str = getTypeAttr($type, $name .  $stuffix);

    return $str;
}

function getTypeID($type)
{
    $str = "";
    $str = getTypeAttr($type, "id");

    return $str;
}

function getTypeDate($type, $attr, $include_time = true, $include_date = true)
{

    $str = "";

    if (is_scalar($type) && $type) {
        $type = [$attr => $type];
    }

    $str = getTypeAttr($type, $attr);
    $seperator_position = $str ? strpos($str, " ") : "";

    if (!$include_time && $str) $str = substr($str, 0, $seperator_position);
    else if (!$include_date && $str) $str = substr($str, $seperator_position);

    if ($str) $str = trim($str);

    return $str;
}

function getTypeDateCreated($type, $include_time = true)
{
    $str = getTypeDate($type, "created_at", $include_time);
    return $str;
}

function getTypeDateUpdated($type, $include_time = true)
{
    $str = getTypeDate($type, "updated_at", $include_time);
    return $str;
}

function getTypeDateExpired($type)
{
    $str = "";
    $str = getTypeAttr($type, "expired_at");

    return $str;
}

function getTypeStatus($type)
{
    $str = "";
    $str .= getTypeAttr($type, "status");
    return $str;
}

function getTypeCounts($type, $prefix)
{
    $str = "";
    $str .= getTypeAttr($type, $prefix . "_count");
    return $str;
}

function getTypeAvg($type, $prefix, $stuffix, $round = 1)
{
    $str = "";
    $str .= getTypeAttr($type, $prefix . "_avg_" . $stuffix);

    if ($str) {
        $str = round($str, $round);
    }

    return $str;
}

function getTypeThumbnail($type, $noImageMessage = "No Image")
{
    $str = "";
    $thumbnail = getTypeAttr($type, "thumbnail_url");
    if (!empty($thumbnail)) {
        $str .= "<img width=\"75\" src=\"{$thumbnail}\">";
    } else $str .= __local($noImageMessage);

    return $str;
}

function canShowLinkToUserByRoute($callback, $parameters = [])
{

    // be careful when using this function maybe have conflict like this
    //"dashboard/comments/{type}/update/{id}",
    //"dashboard/comments/comment/update/confirm"

    $theRoute = request()->route();
    $theParameters = $parameters ? $parameters : $theRoute->parameters;
    $require_route = $callback($GLOBALS['current_page'], $theParameters);
    $the_route = getURLSchema($require_route, 'path');

    $canShowRoute = canShowLinkToUser($the_route);
    return $canShowRoute;
}

function getTypeDeleteLinkHTMLTag($classDeleteForm = "")
{
    // delete route
    $canShowDeleteRoute = canShowLinkToUserByRoute('getTypeDeleteLink');
    $label = __local("Delete");


    $tag = "";

    $targetSelector = $classDeleteForm . "#delete-form";

    if ($canShowDeleteRoute)
        $tag = "<input data-id-form=\"{$targetSelector}\" data-callback=\"deleteFormActionClick\" data-label=\"\" class=\"d-block w-100 mt-3 btn btn-danger float-right\" onclick=\"buttonFormAction(event)\" id=\"delete_action\" type=\"button\" value=\"{$label}\">";

    return $tag;
}

function getTypeCancellLinkHTMLTag($cls = "")
{
    $label = __local("Cancel");
    $tag = "<input data-id-form=\"#cancel-form\" data-callback=\"cancelFormActionClickFormActionClick\" class=\"d-block w-100 mt-3 btn btn-danger {$cls}\" onclick=\"buttonFormAction(event)\" id=\"cancell_action\" type=\"button\" value=\"{$label}\">";
    return $tag;
}

function getTypePreviewLinkHTMLTag($link, $marginClass = "mb-3")
{
    $label = __local("Preview");
    $tag = "<a href=\"{$link}\" class=\"d-block w-100 btn btn-info {$marginClass}\" target=\"_blank\">{$label}</a>";
    return $tag;
}

function getTypeSendAlertHTMLTag($type_id, $serverCbk = null, $localCbk = null, $args = [], $cls = "")
{

    $serverCbk = $serverCbk ?? "sendemailrest";
    $localCbk = $localCbk ?? "";
    $jsonArgs = json_encode($args);
    $label = __local('Delivery Moved');
    $tag = "<input data-alt-callback=\"{$localCbk}\" data-alt-action=\"{$serverCbk}\" data-alt-args='{$jsonArgs}' data-alt-type-id='{$type_id}' class=\"d-block w-100 mb-3 btn btn-warning {$cls}\" id=\"delivery_moved_action\" type=\"button\" value=\"{$label}\">";
    return $tag;
}

function getTypeDeleteLink($path, $args, $stuffix = ".destroy")
{
    return route($path . $stuffix, $args);
}

function getTypeActiveLink($path, $args, $stuffix = ".active")
{
    return route($path . $stuffix, $args);
}

function getTypeDeactiveLink($path, $args, $stuffix = ".deactive")
{
    return route($path . $stuffix, $args);
}

function getTypedeactive_blockLink($path, $args, $stuffix = ".deactive.block")
{
    return getTypeDeactiveLink($path, $args, $stuffix);
}

function getTypeConfirmLink($path, $args, $stuffix = ".confirm")
{
    return route($path . $stuffix, $args);
}

function getTypeCancelLink($path, $args, $stuffix = ".cancel")
{
    return route($path . $stuffix, $args);
}

function getTypeConfirmAndReplyLink($path, $args, $stuffix = ".reply")
{
    return route($path . $stuffix, $args);
}

function getTypeSetStatusLink($path, $args, $stuffix = ".status.type")
{
    return route($path . $stuffix, $args);
}

function getColorModeLink($args, $export = "URL")
{
    $result = "";
    $export = strtoupper($export);

    $route_name = "front_end.set_color_mode";

    $result = getRouteInfo($route_name, $args, $export);

    return $result;
}

function getTableHeadTypeList($thTagProp)
{
    $str = "";

    if (!is_countable($thTagProp)) return $str;

    foreach ($thTagProp as $th) {
        if (!empty($th['action'])) {
            $callback = $th['action'][0];
            $args = $th['action'][1];
            if ($args[0] === -999) {
                continue;
            }
            $str .= call_user_func($callback, ...$args);
        } else if (!empty($th['text'])) {
            $tag = getTagTemplateWithComplex(getTagTemplate("th"), $th['attr'], $th['text']);
            $str .= $tag;
        }
    }

    return $str;
}

function cloneType($modelName, $insert_data, $id, $idKey = "id")
{
    $inserted_id = 0;
    $callback = getFullNamespaceByModel($modelName, "where");
    $result = (call_user_func($callback, $idKey, $id))->first();

    if ($result) {
        $callback_insert =  getFullNamespaceByModel($modelName, "create");
        $new_clone = call_user_func($callback_insert, $insert_data);
        $inserted_id = getTypeID($new_clone);
    }

    return $inserted_id;
}

function addXArrayToQuery($key, $list, $query)
{
    $query = $query->whereIn($key, $list);

    return $query;
}

function addTypeToQuery(array $type, $model_name = null, $query = null)
{
    $model = null;
    if ($model_name) {
        $model = instanceModelClass($model_name);
    }

    $query = $model ? $model : $query;

    $query = addXArrayToQuery("type", $type, $query);

    return $query;
}

function getExtraNamingType($targetKey, $mainKey)
{
    $finalKey = $mainKey . "_" . $targetKey;

    return $finalKey;
}

function getExtraFromType($item, $targetKey = "*", $mainKey = "extra")
{

    $result = null;

    $_item = getTypeAttr($item, $mainKey);
    if (!$_item) return $result;

    if (is_string($_item)) $_item = json_decode($_item, true);

    if ($targetKey == "*") {
        $result = $_item;
    } else {
        $targetValue = getTypeAttr($_item, $targetKey);
        $result = $targetValue;
    }

    return $result;
}

function extractDataFromExtra($targetKey, $item, &$targetInput, $mainKey = "extra")
{

    $status = false;

    $targetValue = getExtraFromType($item, $targetKey, $mainKey);
    $finalKey = getExtraNamingType($targetKey, $mainKey);

    if ($targetValue != "") {
        $targetInput[$finalKey] = $targetValue;
        $status = true;
    }

    return $status;
}

function joinDataToExtra($targetInput, &$item, $mainKey = "extra", $exclude = [])
{

    $statusInt = 0;

    if (empty($item[$mainKey])) {
        $item[$mainKey] = [];
    }

    if (is_string($item[$mainKey])) {
        $item[$mainKey] = json_decode($item[$mainKey], true);
    }

    $_item = [$mainKey => $item[$mainKey]];


    $mainKeyWithPrefix = "{$mainKey}_";

    if (is_countable($targetInput)) {
        foreach ($targetInput as $targetInputOne_Key => $targetInputOne) {
            if (is_int(stripos($targetInputOne_Key, "$mainKeyWithPrefix")) && !in_array($targetInputOne_Key, $exclude)) {
                $finalKey = str_ireplace("$mainKeyWithPrefix", "", $targetInputOne_Key);
                $_item[$mainKey][$finalKey] = $targetInputOne;
                $statusInt++;
            }
        }
    }

    if ($statusInt) {
        $item[$mainKey] = json_encode($_item[$mainKey]);
    } else {
        $item[$mainKey] = null;
    }


    return $statusInt;
}

function getCustomMessageFirst($list, $current)
{
    $the_via = isset($list[$current]) ? $list[$current] : __local(ucwords($current));

    return $the_via;
}

function redirectFromToB($redirectStatus, $B_Link)
{
    if ($redirectStatus) {
        return redirect($B_Link);
    }

    return null;
}

function makeTheseItemsLowercase($items, $keys)
{

    if (is_array($keys)) {
        foreach ($keys as $key) {
            if (isset($items[$key])) {
                $items[$key] = strtolower($items[$key]);
            }
        }
    } else if ($keys === true) {
        foreach ($items as &$item) {
            $item = strtolower($item);
        }
    }

    return $items;
}

function getViewFileBasedOnPriority(string $template_name, array $priority_list)
{

    $result = "";

    $last_priority = str_replace("@", "", $template_name);
    $priority_list[] = $last_priority;


    foreach ($priority_list as $index => $priority_item) {

        $isLastIterate = (count($priority_list) - 1) == $index;

        $priority_item = str_replace(["-"], ["_"], $priority_item);
        $priority_to_check = $isLastIterate ? $priority_item : str_replace("@", "_{$priority_item}", $template_name);

        if (view()->exists($priority_to_check)) {
            $result = $priority_to_check;
            break;
        }
    }

    return $result;
}

function canSetStateType($prefix)
{
    $canSetState = getCurrentUserRolesDetailsCanAccess("{$prefix}.status.type");

    return $canSetState;
}

function setStatusType($modelName, $prefix, $check = [], $title = "title")
{
    $action = request()->post("action", "");
    $id = request()->post("id");

    $manualError = false;

    $manualError = apply_filters("before_check_set_status_type_" . strtolower($modelName), $manualError, $modelName, $action, $id);
    $manualError = apply_filters("before_check_set_status_type_" . strtolower($modelName) . "_{$action}", $manualError, $modelName, $action, $id);

    if (!$action || !$id) {
        $message = __local("action or id not set");
        return triggerServerError(getUserMessageValidate($message, []));
    } else if (!is_bool($manualError)) {
        return $manualError;
    }

    // check for permission
    if (!canSetStateType($prefix)) {
        $message = getMessageUserNotHavePermissionToDoThisAction();
        return triggerServerError(getUserMessageValidate($message, []));
    }

    $manualError = apply_filters("after_check_set_status_type_" . strtolower($modelName), $manualError, $modelName, $action, $id);
    $manualError = apply_filters("after_check_set_status_type_" . strtolower($modelName) . "_{$action}", $manualError,  $modelName, $action, $id);


    if (!is_bool($manualError)) {
        return $manualError;
    }

    return statusType(getFullNamespaceByModel($modelName, "findOrFail"), $action, $check, $title);
}

function sanitizeDashType($type, $reverse = false)
{
    $searchTerm = "-";
    $replaceTerm = "_";

    if ($reverse) {
        $searchTerm = "_";
        $replaceTerm = "-";
    }

    $sanitized_type = str_replace($searchTerm, $replaceTerm, $type);

    return $sanitized_type;
}

# =========> END type

# =========> Overview

function getOverviewGridContent($title, $small, $icon, $colParentClass = "col-12 col-sm-12 col-md-4 col-lg-3", $colLeftClass = "col-3", $colRightClass = "col-9")
{
    $textAlign = trnsAlignCls();

    if (!is_int(strpos($colParentClass, "mb-"))) {
        $colParentClass .= " mb-3";
    }

    return "<div class=\"{$colParentClass}\" >
    <div class=\"the-wrapper default-bg-light-color h-100 mr-1 p-2 shadow rounded row\">
    <div class=\"the-label-overview {$colLeftClass}\">
        <i class=\"{$icon} active-text h1\"></i>
    </div>
    <div class=\"content-overview {$colRightClass}\">
        <h3 class=\"{$textAlign} default-color\">{$title}</h3>
        <label class=\"d-block {$textAlign}\" for=\"\">{$small}</label>
    </div>
        
    </div>
    </div>";
}

function getOverviewWidgetPostTypeCounter()
{
    $str = "";
    $str .= "<div class=\"row post-type mt-1 p-3 border border-info\">";

    $posts = getAllType('post_type');
    foreach ($posts as $post) {
        $slug = $post['slug'];
        $label = $post['label'];
        $post_type_count = App\Models\PostType::where("type", $slug)->count();
        $str .= getOverviewGridContent($post_type_count, __local("Post Type") . " : {$label}", "bi bi-stickies", "col-12 col-sm-12 col-md-4 col-lg-3");
    }

    $str .= "</div>";

    return $str;
}

function getOverviewWidgetTaxonomyCounter()
{
    $str = "";
    $str .= "<div class=\"row taxonomy mt-1 p-3 border border-info\">";

    $taxonomies = getAllType('taxonomy');
    foreach ($taxonomies as $taxonomy) {
        $slug = $taxonomy['slug'];
        $label = $taxonomy['label'];
        $taxonomy_type_count = App\Models\Taxonomy::where("type", $slug)->count();
        $str .= getOverviewGridContent($taxonomy_type_count,  __local("Taxonomy") . " : {$label}", "bi bi-diagram-3");
    }

    $str .= "</div>";

    return $str;
}

function getOverviewWidgetViewsCounter()
{
    $str = "";
    $str .= "<div class=\"row views mt-1 p-3 border border-info\">";

    $view_count = App\Models\View::count();
    $str .= getOverviewGridContent($view_count, __local("All") . " " . __local("Views"), "bi bi-eye-fill");

    $str .= "</div>";

    return $str;
}

function getOverviewWidgetFilesCounter()
{
    $str = "";
    $str .= "<div class=\"row files mt-1 p-3 border border-info\">";

    $file_count = App\Models\File::count();
    $str .= getOverviewGridContent($file_count, __local("All") . " " . __local("Files"), "bi bi-file-earmark-ruled");

    $str .= "</div>";

    return $str;
}

function getOverviewWidgetCommentsCounter()
{
    $str = "";
    $str .= "<div class=\"row comments mt-1 p-3 border border-info\">";

    // comment
    $comment_count = App\Models\Comment::where("type", "comment")->count();
    $str .= getOverviewGridContent($comment_count, __local("All") . " " . __local("Comments"), "bi bi-chat-quote");

    // rating
    $rating_count = App\Models\Comment::where("type", "rating")->count();
    $str .= getOverviewGridContent($rating_count, __local("All") . " " . __local("Ratings"), "bi bi-star-half");

    $str .= "</div>";

    return $str;
}

function getOverviewWidgetUsersCounter()
{
    $str = "";
    $str .= "<div class=\"row users mt-1 p-3 border border-info\">";

    $roles = getUserRoles();
    $roles_key = array_keys($roles);

    // all user
    $all_count = App\Models\User::count();
    $str .= getOverviewGridContent($all_count, __local("All") . " " . __local("Users"), "bi bi-person-circle");

    // by role user
    foreach ($roles_key as $item) {
        $type_count = App\Models\User::where("role", $item)->count();
        $user_label = $roles[$item];
        $str .= getOverviewGridContent($type_count, "{$user_label}", "bi bi-person-circle");
    }

    $str .= "</div>";

    return $str;
}

# =========> END Overview

# =========> Post type

function getPostTypePublishable()
{
    $publishable_type = getAllTypeByCondition(getAllType("post_type"), ["slug"], function ($list) {
        return $list->where("status", "public");
    });
    return $publishable_type;
}

function canDeleteThisPostType($item)
{
    $state = false;
    $permissionDelete = postTypePermission(getTypee($item), "delete");

    if (!isPostTypeForbidden($permissionDelete)) {
        $state = true;
    }

    return $state;
}

function getPostTypeMetaByX($key = null, $value = null, $post_type_id = 0)
{
    $post_type_meta = App\Models\PostTypeMeta::where("id", "!=", 0);

    if ($key) {
        $post_type_meta->where("key", $key);
    }

    if ($value) {
        $post_type_meta->where("value", $value);
    }

    if ($post_type_id) {
        $post_type_meta->where("post_type_id", $post_type_id);
    }

    $post_type_meta = $post_type_meta->get();

    return $post_type_meta;
}

function getPostTypeMetaByKey($item, $key, $state = "all")
{
    $data = [];
    if ($item) {
        $meta = $item->meta;
        $data = $key == "*" ? $meta : $meta->where("key", $key);
    }

    if ($state != "all" && $item) {
        $data = $data->$state();
    }

    return $data;
}

function getPostTypeMetaByKeyFirst($item, $key)
{
    $data = null;
    if ($item) {
        $data = getPostTypeMetaByKey($item, $key, "first");
    }

    return $data;
}

function getPostTypeMetaOLD($key, $default = null)
{
    return old("meta:" . $key, $default);
}

function isPostTypeForbidden($permissionAction)
{
    $state = false;
    if (!$permissionAction) {
        $state = true;
        return $state;
    }

    $OwnNull = @$permissionAction['own'][0] ? $permissionAction['own'][0] : "";
    if (!$permissionAction || strtolower($OwnNull) == "null")
        $state = true;

    return $state;
}

function isPostTypeAllAccess($permissionAction)
{
    $state = false;
    if (!$permissionAction) return $state;

    if (!empty($permissionAction['own'][0]) && strtolower($permissionAction['own'][0]) == "true") {
        $state = true;;
    }

    return $state;
}

function ob_post_type_general_create($buffer)
{

    $type = $GLOBALS['post.type.type'];

    // check for permission
    $permissionCreate = postTypePermission($type, "create");


    if (isPostTypeForbidden($permissionCreate)) {
        return getCustom403Content();
    }

    return $buffer;
}

function ob_post_type_general_edit($buffer)
{

    loadHTMLParser();
    $type = $GLOBALS['post.type.type'];

    // check for permission
    $permissionEdit = postTypePermission($type);

    if (isPostTypeForbidden($permissionEdit)) {
        return getCustom403Content();
    } else if (isPostTypeAllAccess($permissionEdit)) {
        return $buffer;
    }

    $component_keys = array_keys($permissionEdit);
    $componentsList = [];
    $unacceptedList = [];

    $doc = str_get_html($buffer);
    $accept_class = "component-accept";

    foreach ($component_keys as $item_key) {
        $inputNames = $permissionEdit[$item_key];

        $basename = getBaseComponentName($item_key);
        $components = $doc->find(".{$basename}");
        $componentsList[] = $components;

        if (count($components)) {
            foreach ($components as $component) {
                if (count($inputNames)) {
                    foreach ($inputNames as $inputName) {
                        $nameSelector = $inputName;
                        if ($basename == 'component-taxonomy') {
                            $nameSelector = "taxonomy[{$inputName}]";
                        }

                        // force use ^ to fix ex -> taxonomy[brand]
                        $selector = "[name^=\"{$nameSelector}\"]";
                        $input = $component->find($selector);

                        if (count($input)) {
                            if (!$component->hasClass($accept_class))
                                $component->addClass($accept_class);
                        } else {
                        }
                    }
                } else {
                    $unacceptedList[] = $component;
                }
            }
        }
    }

    // filter for just accept class (remove)
    foreach ($componentsList as $element) {
        foreach ($element as $component_item) {
            if (!$component_item->hasClass($accept_class)) {
                $component_item->remove();
            }
        }
    }

    // filter if name of component does not named
    foreach ($unacceptedList as $the_item) {
        $the_item->remove();
    }

    $html = $doc->save();

    return $html;
}

function ob_post_type_post_edit($buffer)
{
    return ob_post_type_general_edit($buffer);
}

function getMetaWrapperPostType($element, $id, $class = null,)
{
    return getMetaWrapperType($element, "PostType", $id, $class,);
}

function getPostTypeLink($item)
{
    return getTypeLink($item, "slug");
}

function getTableHeadPostType($attr, $thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "title",
                    "data-operator" => "like"
                ],
                "text" => __local("Title"),
            ],
            [
                "attr" => [
                    "data-name" => "thumbnail_url",
                ],
                "text" => __local("Thumbnail"),
            ],
            [
                "attr" => [],
                "text" => "",
                "action" => ["getTaxonomyTableHeadLoop", [$attr['taxonomy'] ?? -999]]
            ],
            [
                "attr" => [],
                "text" => "",
                "action" => ["getPostTypeMetaTableHeadLoop", [$attr['meta'] ?? -999]]
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getStatusPageOption(), true),
                    "data-name" => "status",
                    "data-operator" => "or"
                ],
                "text" => __local("Status"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberRange",
                    "data-options" => '{"min":0,"max":500000000,"from":0,"to":499000000,"step":10,"prefix":"<span class=\"d-inline-block\"></span> "}',
                    "data-name" => "views_count",
                    "data-operator" => "range"
                ],
                "text" => __local("Views"),
            ],
            getCommentCommentAttr(),
            getCommentRatingAttr(),
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" =>  __local("Action"),
            ]
        ];
    }

    // remove th rating from type post
    $allCommentsType = getAllType("comment");
    $post_type_info = checkPostType($attr['post.type_type'] ?? getPartOfUrl(-2));
    if ($allCommentsType) {
        foreach ($allCommentsType as $comment_type) {
            // if exists don't delete it
            if (commentExistsInPostType($post_type_info, $comment_type['slug'])) continue;

            // if does not exists delete it
            $cbk = getCommentAttrMapCallback($comment_type['slug']);
            if (is_callable($cbk)) {
                $searchElement = $cbk();
                $index = array_search($searchElement, $thTagProp);
                if (is_int($index)) {
                    unset($thTagProp[$index]);
                }
            }
        }
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function commentExistsInPostType($info, $comment_type)
{
    $theInfo = !empty($info['current_post_type_info']) ? $info['current_post_type_info'] : $info;

    return (in_array($comment_type, $theInfo['comment']));
}

function checkPostType($current_post_type)
{
    $current_post_type_info = checkDynamicType($current_post_type, "searchInPostType");

    return [
        "current_post_type" => $current_post_type,
        "current_post_type_info" => $current_post_type_info['current_type_info'],
    ];
}

function searchInPostType($slug)
{
    foreach ($GLOBALS['post_type'] as $postType) {
        if ($postType['slug'] === $slug) {
            return $postType;
        }
    }

    return false;
}

function registerPostType($prop)
{
    return registerTypeDynamically($prop, ['label', 'slug', 'taxonomy', "comment"], $GLOBALS['post_type']);
}

function updatePostTypeMeta(object $modelQuery, array $inputs, array $allowedListKey)
{
    $result = 0;

    if (!$allowedListKey) return $result;

    $meta = [];
    $metaKeyPrefix = "meta:";

    foreach (array_keys($inputs) as $inp) {
        $tmp_inp = str_replace($metaKeyPrefix, "", $inp);

        if (is_int(strpos($inp, $metaKeyPrefix)) && in_array($tmp_inp, $allowedListKey)) {
            $meta[$tmp_inp] = $inputs[$inp];
        }
    }

    if (!$meta) return $result;


    $storedMeta = $modelQuery->meta;
    foreach ($meta as $meta_key => $meta_item) {
        if (is_array($meta_item)) continue;

        $exists = $storedMeta->where("key", $meta_key)->first();
        if ($exists) {
            $id = $exists->id;
            $modelQuery->meta()->where("id", $id)->update(['value' => $meta_item]);
        } else {
            $modelQuery->meta()->create(['key' => $meta_key, 'value' => $meta_item]);
        }

        $result++;
    }

    return $result;
}

function clonePostTypeRest()
{

    $status = false;
    $cloneTypes = array_keys(getClonePage());

    $currentUser = getCurrentUser();

    $queryPostTypeID = request()->post("post_type_id", 0);
    $queryCloneType = request()->post("clone_type", $cloneTypes[0]);
    $queryIncludeList = request()->post("clone_include_keys", "{}");

    if (!is_array($queryIncludeList) || !isSuperAdmin($currentUser)) {
        $queryIncludeList = @json_decode($queryIncludeList, true);
    }

    $isValidCloneType = in_array($queryCloneType, $cloneTypes);

    $post_type = App\Models\PostType::find($queryPostTypeID);


    if ($post_type && $isValidCloneType) {

        // check for permission
        $permissionEdit = postTypePermission(getTypee($post_type));
        if (isPostTypeForbidden($permissionEdit)) return $status;

        $status = true;

        $new_title = getTypeTitle($post_type) . " - " . time();

        // clone post type
        $inserted_id_post_type = cloneType("PostType", [
            "title" => $new_title,
            "slug" => getTypeSlug($post_type),
            "type" => getTypee($post_type),
            "body" => getTypeBody($post_type),
            "body_raw" => getTypeBodyRaw($post_type),
            "thumbnail_url" => getTypeAttr($post_type, "thumbnail_url"),
            "status" => getTypeStatus($post_type)
        ], $queryPostTypeID);


        // clone post type meta
        if (is_int(stripos($queryCloneType, "meta"))) {

            $metaList = $post_type->meta()->get();

            if ($metaList->count()) {
                foreach ($metaList as $meta) {

                    if ($queryIncludeList && !in_array(getTypeAttr($meta, "key"), $queryIncludeList)) {
                        continue;
                    }

                    $inserted_id_post_type_meta = cloneType("PostTypeMeta", [
                        "post_type_id" => $inserted_id_post_type,
                        "key" => getTypeAttr($meta, "key"),
                        "value" => getTypeValue($meta)
                    ], getTypeID($meta));
                }
            }
        }

        // clone post type taxonomy
        if (is_int(stripos($queryCloneType, "taxonomy"))) {
            $taxonomies = $post_type->taxonomies()->get();

            if ($taxonomies->count()) {
                foreach ($taxonomies as $taxonomy) {
                    $inserted_id_post_type_taxonomy = cloneType("PostTypesTaxonomy", [
                        "post_type_id" => $inserted_id_post_type,
                        "taxonomy_id" => getTypeID($taxonomy),
                    ], $queryPostTypeID, "post_type_id");
                }
            }
        }
    }

    return $status;
}

function getSinglePostTypeQuery($id, $type = null)
{
    $query = App\Models\PostType::where("id", $id);

    if ($type) {
        $query = addTypeToQuery([$type], null, $query);
    }

    return $query;
}

function getPostTypeActionButtons($key)
{

    $label = __local("PostType");

    $list = [
        "draft" => createHtmlActionInputSetStatus("draft", getStatusPage()['draft'], $label, "yellow", true),
        "publish" => createHtmlActionInputSetStatus("publish", getStatusPage()['publish'], $label, "green", true),
    ];

    $target = $list[$key] ?? "";

    return $target;
}


# =========> END Post type


# =========> Taxonomy

function update_taxonomy_updated_at($post_type)
{
    $taxonomies = $post_type->taxonomies()->get();
    foreach ($taxonomies as $item_taxonomies) {
        $item_taxonomies['updated_at'] = getDateByUnixTime();
        $item_taxonomies->save();
    }
}

function getTaxonomyThumbnailURL($item)
{
    return getTypeThumbnailURL($item);
}

function getTaxonomyPublishable()
{
    $publishable_type = getAllTypeByCondition(getAllType("taxonomy"), ["slug"], function ($list) {
        return $list->where("status", "public");
    });
    return $publishable_type;
}

function setTaxonomyRest()
{
    $response = getSetRestResponse();

    $queryTitle = request()->post("title");
    $querySlug = request()->post("slug");
    $queryType = request()->post("type");
    $queryStatus = request()->post("status");

    $query = Taxonomy::where("title", $queryTitle)->where("type", $queryType);
    $taxonomy = $query->get()->first();

    $failMessage = str_replace("x-title", $queryTitle, __local("Fail To Create New Taxonomy x-title Maybe Already Exists"));

    if (!getTypeTitle($taxonomy)) {
        $tmp_current_page = $GLOBALS['current_page'];
        $GLOBALS['current_page'] = "taxonomy";

        $TaxonomyController = new App\Http\Controllers\TaxonomyController;
        $TaxonomyController->store($queryType);

        $GLOBALS['current_page'] = $tmp_current_page;

        $new_taxonomy = $query->get()->first();

        if (getTypeTitle($new_taxonomy)) {
            $response['status'] = "success";
        } else {
            $error = getAllType('error_validation');
            // add prefix to element like title -> quick_title
            array_walk($error['data'], function (&$element) {
                $element = "quick_{$element}";
            });
            $response['message'] = str_replace("x-field", $queryTitle, $error['message']);
            $response['data'] = $error['data'];
        }
    } else {
        $response['message'] = $failMessage . " :::";
    }

    return $response;
}

function getTaxonomyRest()
{
    $response = [];

    $fieldSearchList = [
        "q" => [
            "field" => "title",
            "operator" => "LIKE",
            "value" => "%x-value%"
        ],
        "id" => [
            "field" => "id",
            "operator" => "=",
            "value" => "x-value"
        ],
    ];


    $queryQ = request()->post("q");
    $queryID = request()->post("id");
    $queryTaxonomy = request()->post("taxonomy");
    $queryExtraParams = request()->post("extra_params");
    $extra = request()->query("extra");

    $fieldSearch = [];
    $theQuery = null;

    if ($queryQ) {
        $theQuery = $queryQ;
        $fieldSearch = $fieldSearchList["q"];
    } else if ($queryID) {
        $theQuery = $queryID;
        $fieldSearch = $fieldSearchList["id"];
    }

    if (is_null($theQuery) || is_null($queryTaxonomy) || !$fieldSearch) return $response;

    $taxonomy_published_type = getAllTypeByCondition(getAllType("taxonomy"), ["slug"], function ($list) {

        $showInRest = $list->where("show_in_rest", true);

        $res = $list->where("status", "public")->merge($showInRest);

        return $res;
    });

    if (is_array($taxonomy_published_type)) {
        $taxonomy_published_type = array_unique($taxonomy_published_type);
    }

    // check for taxonomy published type
    if (!count($taxonomy_published_type) || !in_array($queryTaxonomy, $taxonomy_published_type)) return $response;

    $status = ["publish"];

    $taxonomies = Taxonomy::whereIn("status", $status)->where("type", $queryTaxonomy)->where($fieldSearch['field'], $fieldSearch['operator'], getStringTemplateByValue($fieldSearch['value'], $theQuery))->skip(0)->take(10);

    if ($queryExtraParams && !empty($queryExtraParams['cbk']) && is_callable($queryExtraParams['cbk'])) {
        $taxonomies = $queryExtraParams['cbk']($taxonomies, $queryExtraParams);
    }

    $taxonomies = $taxonomies->get();

    if (count($taxonomies)) {
        foreach ($taxonomies as $taxonomy) {
            $tmp_taxonomy = $taxonomy->getOriginal();


            $extraData = null;
            if ($extra == "link") {
                $extraData = getTaxonomyLink($taxonomy);
            }

            $theTaxonomy = adapterArrayKey(["title", "type"], ["name", "taxonomy"], $tmp_taxonomy);

            $responseItem = [
                "id" => getTypeID($theTaxonomy),
                "name" => getTypeName($theTaxonomy),
                "slug" => getTypeSlug($theTaxonomy),
                "link" => getTaxonomyLink($taxonomy),
                "taxonomy" => getTypeAttr($theTaxonomy, "taxonomy"),
            ];

            if (!empty($extra)) {
                $responseItem['extra_' . $extra] =  $extraData;
            }

            $response[] = $responseItem;
        }
    }

    return $response;
}

function getTaxonomyLink($item, $prefix = "t")
{
    $link = getTypeLink($item, "slug");
    $link = str_replace(ROOT_URL . "/", ROOT_URL . "/{$prefix}/", $link);
    return $link;
}

function getTableHeadTaxonomy($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "title",
                    "data-operator" => "like"
                ],
                "text" => __local("Title"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "slug",
                    "data-operator" => "like"
                ],
                "text" => __local("Slug"),
            ],
            [
                "attr" => [
                    "data-name" => "thumbnail_url",
                ],
                "text" => __local("Thumbnail"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberRange",
                    "data-options" => '{"min":0,"max":500000000,"from":0,"to":499000000,"step":10,"prefix":"<span class=\"d-inline-block\"></span> "}',
                    "data-name" => "post_types_count",
                    "data-operator" => "range"
                ],
                "text" => __local("P.T Attached"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getStatusPageOption(), true),
                    "data-name" => "status",
                    "data-operator" => "or"
                ],
                "text" => __local("Status"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function registerTaxonomy($prop)
{
    return registerTypeDynamically($prop, ['label', 'slug'], $GLOBALS['taxonomy']);
}

function checkTaxonomy($current_taxonomy)
{
    $current_taxonomu_info = checkDynamicType($current_taxonomy, "searchInTaxonomy");

    return [
        "current_taxonomy" => $current_taxonomy,
        "current_taxonomy_info" => $current_taxonomu_info['current_type_info'],
    ];
}

function searchInTaxonomy($slug)
{
    foreach ($GLOBALS['taxonomy'] as $taxonomy) {
        if ($taxonomy['slug'] === $slug) {
            return $taxonomy;
        }
    }

    return false;
}

function groupTaxonomyForInputs($taxonomy, $prefix = "taxonomy")
{
    return groupTypeForInputs($taxonomy, $prefix);
}

function getSeperatedTaxonomyId($taxList)
{
    $taxonomy = !empty($taxList) && is_array($taxList) ? $taxList : [];
    $taxonomies = [];
    foreach ($taxonomy as $tx) {
        if (!$tx) continue;
        $taxo = explode(",", $tx);
        foreach ($taxo as $val) {
            $taxonomies[] = $val;
        }
    }

    return $taxonomies;
}

function generateTaxonomySelect2WidgetAll($widgetName = "select2TaxonomyWidgetLink")
{
    $str = "";

    foreach (getAllType("taxonomy") as $taxonomy) {
        $str .= generateTaxonomySelect2Widget($taxonomy['slug'], $taxonomy['label'], "", $widgetName);
    }

    return $str;
}

function generateTaxonomySelect2Widget($slug, $label, $value = "", $widgetName = "select2TaxonomyWidget")
{
    if (is_array($value)) {
        $tmpValue = [];
        foreach ($value as $v) {
            $tmpValue[] = $v->id;
        }
        $value = join(",", $tmpValue);
    }

    $template = getHtmlTemplate($widgetName);

    $template = groupReplacer($template, ['x-taxonomy-slug', 'x-taxonomy-label', 'x-value'], [$slug, $label, $value]);
    return $template;
}

function getHasManyTaxonomyItems($items)
{
    $new_items = [];
    foreach ($items as $tx) {
        $new_items[$tx->type][] = $tx;
    }


    return $new_items;
}

function getTaxonomyTableHeadLoop($taxonomy)
{
    $str = "";

    if (is_countable($taxonomy)) {
        foreach ($taxonomy as $taxy) {
            $current_taxy = searchInTaxonomy($taxy);

            if (!is_array($current_taxy)) {
                continue;
            }

            $typeLabel = __local("Taxonomy");
            $str .= "<th data-filter data-unsort data-relation=\"taxonomies:taxonomy_id\" data-operator=\"or\" data-input=\"selectMultipleAjax\" data-name=\"taxonomy_{$current_taxy['slug']}\" data-extra='{\"taxonomy\": \"{$current_taxy['slug']}\"}'>{$typeLabel} {$current_taxy['label']}</th>";
        }
    }

    return $str;
}

// for using in front user and dashboard

function getPostTypeTaxonomyInTable($allowed_taxonomy, $taxonomy_items, $emptyStr = "-")
{

    if (!count($taxonomy_items)) return str_repeat("<td>{$emptyStr}</td>", count($allowed_taxonomy));

    $str = "";
    foreach ($allowed_taxonomy as $taxy) {
        $current_taxy = searchInTaxonomy($taxy);
        if (!is_array($current_taxy)) continue;

        $taxItems = $taxonomy_items[$current_taxy['slug']] ?? [];
        $str .= "<td>";
        if (count($taxItems)) {
            foreach ($taxItems as $taxonomy_item) {
                $link = getTypeEditLink($taxonomy_item, "taxonomy", ["type", "id"]);
                $str .= "<a class=\"badge badge-large badge-pill badge-info\" href=\"{$link}\">{$taxonomy_item['title']}</a>";
            }
        } else {
            $str .= $emptyStr;
        }
        $str .= "</td>";
    }

    return $str;
}

function getSingleTaxonomyQuery($id, $type = null)
{
    $query = App\Models\Taxonomy::where("id", $id);

    if ($type) {
        $query = addTypeToQuery([$type], null, $query);
    }

    return $query;
}

function getTaxonomyActionButtons($key)
{
    $label = __local("Taxonomy");

    $list = [
        "draft" => createHtmlActionInputSetStatus("draft", getStatusPage()['draft'], $label, "yellow", true),
        "publish" => createHtmlActionInputSetStatus("publish", getStatusPage()['publish'], $label, "green", true),
    ];

    $target = $list[$key] ?? "";

    return $target;
}

function getGroupActionTaxonomy()
{
    $groupActions = [
        "includes" => [
            [
                "actionName" => "Delete",
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Delete Selected Items ?"),
                "actionCbk" => "input#delete_action",
                "actionCbkHelper" => "deleteItemsList"
            ],


            [
                "actionName" => __local("Set Status") . " - " . __local("Draft"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Draft Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"draft\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],

            [
                "actionName" => __local("Set Status") . " - " . __local("Publish"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Publish Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"publish\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],


        ],
    ];

    return $groupActions;
}

# =========> END Taxonomy

# =========> Form

function getDefaultClonableItem()
{
    $result = ["type-input-form" => "text"];

    return $result;
}

function validationFormSchema(&$inputs)
{
    $error = getUserMessageValidate();

    $schema = @json_decode($inputs['schema'] ?? "", true);

    // validation schema
    if (!$schema || !is_array($schema)) {
        $error['message'] = __local("x-field is invalid");
        $error['data'] = ['schema'];
        return $error;
    }


    $listOfInputType = getFormInputTypes();

    $listOfIds = [];

    // validation schema `items`
    foreach ($schema as &$item) {

        // Server rules
        if (!empty($item['server-rules'])) {
            $resultArrayServerRules = @json_decode($item['server-rules'], true);
            if (!is_array($resultArrayServerRules)) {
                $error['message'] = __local("x-field is invalid");
                $error['data'] = ['server-rules'];
                return $error;
            }
        }

        // re-correct "html attributes"
        $item['extra-html-attributes'] = str_replace('\'', '"', $item['extra-html-attributes']);

        // `name` and `id-form`
        if (!$item['name'] || !$item['id-form']) {
            $error['data'] = [];

            if (!$item['name']) $error['data'][] = 'name';
            if (!$item['id-form']) $error['data'][] = 'id-form';

            $sysnon = areOrIsByItems($error["data"]);
            $error["message"] = getEmptyMessage($sysnon);

            return $error;
        }


        // form-type validation
        if (!in_array($item['type-input-form'], array_keys($listOfInputType))) {
            $error['message'] = __local("x-field is invalid");
            $error['data'] = ['type-input-form'];
            return $error;
        }


        // `radio` and `checkbox`
        if ($item['type-input-form'] == "radio" || $item['type-input-form'] == "checkbox") {
            // `PLACEHOLDER` is required
            if (empty($item['placeholder-form'])) {
                $error['message'] = str_replace(['x-1', 'x-2', 'x-3'], [__local("radio"), __local("checkbox"), __local("Placeholder")], __local("(x-1) and (x-2) should have x-3"));
                $error['data'] = [$item['type-input-form']];
                return $error;
            }

            $html_attributes_list = getFormInputAttributes($item);

            // attr `VALUE` is required
            if (empty($html_attributes_list['sanitize']['value'])) {
                $error['message'] = str_replace(['x-1', 'x-2', 'x-3', 'x-4'], [__local("radio"), __local("checkbox"), 'value', __local("Extra Html Attributes")], __local("(x-1) and (x-2) should have x-3 in x-4"));
                $error['data'] = [$item['type-input-form']];
                return $error;
            }
        }

        if (in_array($item['id-form'], $listOfIds)) {
            $error['message'] = str_replace('x-id', $item['id-form'], __local("the ID x-id is duplicated"));
            $error['data'] = [$item['id-form']];
            return $error;
        }

        $listOfIds[] = $item['id-form'];
    }

    $inputs['schema'] = json_encode($schema);

    return $error;
}

function getFormSchemaByType($type, $status = "publish")
{
    $formSchema = App\Models\FormsSchema::where("type", $type)->where("status", $status)->get()->first();

    return $formSchema;
}

function getFormByID($id)
{
    $form = App\Models\Form::where("id", $id)->get()->first();

    return $form;
}

function getFormsRows($status = "pending")
{
    $form = App\Models\Form::where("status", $status);

    return $form;
}

function generateFormHtmlByFormSchema($form_schema, $id)
{
    $form_content = "";
    $formInputTypes = getFormInputTypes("*");
    $the_schema = json_decode($form_schema['schema'], true);

    foreach ($the_schema as $item_schema) {
        $input_type = $item_schema['type-input-form'];
        $input_attributes = $formInputTypes[$input_type];
        $html_cbk = $input_attributes['cbk']['onShowHtml'];

        $DB = [];

        $current_user = getCurrentUser();

        $isAdminViewMode = false;
        if (isSuperAdmin($current_user) && $id) {
            $DB = getFormByID($id);
            if ($DB) {
                $DB = json_decode($DB['form'], true);
                $isAdminViewMode = true;
            }
        }

        $form_content .= $html_cbk($item_schema, ["bootstrap" => "4.6", "DB" => $DB]);

        if ($isAdminViewMode) {
            loadHTMLParser();
            $doc = str_get_html($form_content);
            $inputFiles = $doc->find("[type='file']");

            foreach ($inputFiles as $inputFile) {
                $valueFile = $inputFile->getAttribute("value");
                if ($valueFile) {
                    $inputFile->setAttribute("type", "button");
                    $inputFile->setAttribute("class", "btn btn-primary w-100");
                    $inputFile->setAttribute("value", __local("View"));
                    $inputFile->setAttribute("onclick", "window.open(\"{$valueFile}\")");
                }
            }

            $form_content = $doc->save();
        }
    }

    return $form_content;
}

function showFormHtml($type, $id, $form_schema, $title_template = null, $submitAttr = [])
{

    $html = "";
    $routeName = "front_end.send_form";

    if (!isRouteExists($routeName)) return $html;

    $submitAttr['class'] = $submitAttr['class'] ?? "btn pl-5 pr-5 btn-primary";
    $submitAttr['label'] = $submitAttr['label'] ?? __local('Submit');

    if ($form_schema['is_login_required'] && !getCurrentUser()) {
        $html = "<h3 class='text-center'>" . getMessageMustBeLogginToDoThisAction() . "</h3>";
        return $html;
    }

    $title_template = $title_template ?: "<h1 class=\"text-center mt-2 mb-3\">x-title</h1>";

    $form_content = generateFormHtmlByFormSchema($form_schema, $id);

    $route = route($routeName, ["type" => $type]);
    $csrf_field = csrf_field();

    $captcha = "";

    if ($form_schema['is_captcha_required'] && !$id) {
        $captchaRendered = view('dashboard.user_panel.forms.captcha_part_form')->render();

        loadHTMLParser();
        $doc = str_get_html($captchaRendered);
        $inputWrapperCaptcha = $doc->find(".input-wrapper", 0);
        $inputWrapperCaptcha->setAttribute("class", "input-wrapper col-12 col-sm-12 col-md-8 col-lg-6 m-auto text-center");

        $captchaRendered = $doc->save();

        $captcha = "<div class=\"wrapper-form-inputs captcha-wrapper row mt-4\">" . $captchaRendered . "</div>";
    }

    $submit = $id ? "" : "<div class=\"wrapper text-center mt-3\">
    <input type=\"submit\" class=\"{$submitAttr['class']}\" value=\"{$submitAttr['label']}\">
</div>";


    $title =  str_replace("x-title", __local("Form") . " " .  getTypeTitle($form_schema), $title_template);

    $html = "<form method=\"POST\" enctype=\"multipart/form-data\" id=\"form_schema\" action=\"{$route}\" dir=\"{$GLOBALS['lang']['direction']}\">
{$csrf_field}
{$title}
 <div class=\"wrapper-form-inputs row\">{$form_content}</div>
 {$captcha}
{$submit}
</form>";

    return $html;
}

function collectXAttributesFromFormInputs(&$extra_html_attributes, $target_key = "item_gr_", $whichField = -1)
{
    $exceptional_extra_attributes = [];

    if (is_int(stripos(json_encode(array_keys($extra_html_attributes)), $target_key))) {

        foreach (array_keys($extra_html_attributes) as $theKey) {
            if (is_int(stripos($theKey, $target_key))) {
                $exceptional_extra_attributes[$theKey] = $extra_html_attributes[$theKey];

                if ($whichField != -1) {
                    $itemArray = @json_decode($exceptional_extra_attributes[$theKey], true);
                    if (is_array($itemArray)) {
                        $exceptional_extra_attributes[$theKey] = $itemArray[$whichField] ?? null;
                    } else {
                        $exceptional_extra_attributes[$theKey] = null;
                    }
                }
            }
        }

        foreach ($exceptional_extra_attributes as $itemKey => $itemValue) {
            unset($extra_html_attributes[$itemKey]);
        }
    }



    return $exceptional_extra_attributes;
}

function getFormInputAttributes($schema)
{
    $extra_html_attributes = [];

    if ($schema['extra-html-attributes']) {
        loadHTMLParser();

        $sampleSelectorAttribute = 'eha-id';

        $schemaAttributes = $schema['extra-html-attributes'];


        // standardize for json attributes
        $schemaAttributes = str_replace(['="{', '}"'], ['=\'{', '}\''], $schemaAttributes);
        $schemaAttributes = str_replace(['="[', ']"'], ['=\'[', ']\''], $schemaAttributes);

        $doc = str_get_html("<div {$sampleSelectorAttribute}='sample' {$schemaAttributes}></div>");

        $dom = $doc->find("[{$sampleSelectorAttribute}='sample']", 0);

        if ($dom) {
            $extra_html_attributes = $dom->getAllAttributes();
        }

        if (!empty($extra_html_attributes[$sampleSelectorAttribute])) {
            unset($extra_html_attributes[$sampleSelectorAttribute]);
        }
    }

    foreach ($extra_html_attributes as &$item) {
        $item = trim($item);
    }

    $_extra_html_attributes = $extra_html_attributes;

    $extra_html_attributes = [];

    foreach ($_extra_html_attributes as $_itemKey => $_itemValue) {

        if (!is_null($_itemValue)) {
            // remove quote from beginning
            if (is_string($_itemValue) && $_itemValue[0] == '"' || $_itemValue[0] == '\'') {
                $_itemValue = substr($_itemValue, 1);
            }

            // remove quote from end
            if (is_string($_itemValue) && $_itemValue[strlen($_itemValue) - 1] == '"' || $_itemValue[strlen($_itemValue) - 1] == '\'') {
                $_itemValue = substr($_itemValue, 0, strlen($_itemValue) - 1);
            }
        }

        $extra_html_attributes[$_itemKey] = $_itemValue;
    }

    return [
        "raw" => $_extra_html_attributes,
        "sanitize" => $extra_html_attributes,
    ];
}

function onShowHtmlFormGeneral($schema, $options = [])
{
    $html = "";

    $html_attributes_list = getFormInputAttributes($schema);

    $DB = $options['DB'] ?? [];
    $row_schema = $options['row_schema'] ?? [];

    $form_type = getTypee($row_schema);
    $form_type_sanitized = sanitizeDashType($form_type);

    $DB = apply_filters("add_row_form_data_to_form_html", $DB);
    $DB = apply_filters("add_row_form_data_to_form_html_{$form_type_sanitized}", $DB);

    $inputTypeSanitize = sanitizeDashType($schema['id-form']);
    $DB = apply_filters("add_row_form_data_to_form_html_{$form_type_sanitized}_{$inputTypeSanitize}", $DB);


    $extra_html_attributes = $html_attributes_list['sanitize'];

    $convertHtmlAttributesArrayToStr = function ($list) {
        $html_attributes = "";
        foreach ($list as $itemKey => $itemValue) {
            $html_attributes .= " {$itemKey}='{$itemValue}'";
        }

        return $html_attributes;
    };

    $exceptional_types = [
        "select" => function ($schema, $extra_html_attributes, $old_value) use ($convertHtmlAttributesArrayToStr) {

            $tag_input = "";

            $options = "";

            if (!empty($extra_html_attributes['options'])) {

                $options_array = json_decode($extra_html_attributes['options'],  true);

                unset($extra_html_attributes['options']);

                foreach ($options_array as $itemKey => $itemValue) {

                    $selected = "";

                    if ($old_value && $old_value == $itemKey) {
                        $selected = "selected";
                    }

                    $options .= "<option {$selected} value='{$itemKey}'>{$itemValue}</option>";
                }
            }

            $html_attributes = $convertHtmlAttributesArrayToStr($extra_html_attributes);
            $tag_input = "<select class='{$schema['class']} form-input-one' data-label='{$schema['name']}' name='{$schema['id-form']}' title=\"{$schema['placeholder-form']}\" id='{$schema['id-form']}' {$html_attributes}>{$options}</select>";

            return $tag_input;
        },
        "textarea" => function ($schema, $extra_html_attributes, $old_value) use ($convertHtmlAttributesArrayToStr) {
            $tag_input = "";

            $html_content = "";

            if (!empty($extra_html_attributes['html_content'])) {
                $html_content = $extra_html_attributes['html_content'];
                unset($extra_html_attributes['html_content']);
            }

            // overwrite `old_value` if has value
            if ($old_value) {
                $html_content = $old_value;
            }

            $html_attributes = $convertHtmlAttributesArrayToStr($extra_html_attributes);
            $tag_input = "<textarea class='{$schema['class']} form-input-one' data-label='{$schema['name']}' name='{$schema['id-form']}' id='{$schema['id-form']}' placeholder='{$schema['placeholder-form']}' {$html_attributes}>{$html_content}</textarea>";

            return $tag_input;
        }
    ];

    $target_key = "item_gr_";
    $exceptional_extra_attributes = collectXAttributesFromFormInputs($extra_html_attributes, $target_key);

    $options['bootstrap'] = $options['bootstrap'] ?? "4.6";

    $label_class = $GLOBALS['lang']['direction'] == "rtl" ? "float-right" : "float-left";

    if (is_int(stripos($options['bootstrap'], "5."))) {
        $label_class = str_replace(["right", 'left'], ["end", "start"], $label_class);
    }

    if ($schema['type-input-form'] == 'checkbox' || $schema['type-input-form'] == 'radio') {
        $label_class .= " custom-control-label";
    }


    $label = "<label class=\"{$label_class}\" for='{$schema['id-form']}'>{$schema['name']}</label>";

    $tag_input = "";

    $defaultValue = null;

    // set attr `value` if set
    if (!empty($extra_html_attributes['value'])) {
        $defaultValue = $extra_html_attributes['value'];

        unset($extra_html_attributes['value']);
    }


    $data_old = [];
    $key_old = $schema['id-form'];

    if (!empty($DB[$key_old])) {
        $data_old = $DB;
    }


    $old_value = getValueFromOldOrDB($key_old, $data_old, $defaultValue);

    if (is_array($old_value)) {
        $old_value = $defaultValue;
    }

    // reset "radiobox"
    if (empty($DB[$key_old]) && $schema['type-input-form'] == "radio") {
        $old_value = $defaultValue;
    }

    if (in_array($schema['type-input-form'], array_keys($exceptional_types))) {
        $tag_input = $exceptional_types[$schema['type-input-form']]($schema, $extra_html_attributes, $old_value);
    } else {
        $html_attributes = $convertHtmlAttributesArrayToStr($extra_html_attributes);
        $tag_input = "<input value='{$old_value}' data-value='{$defaultValue}' class='{$schema['class']} form-input-one' data-label='{$schema['name']}' name='{$schema['id-form']}' id='{$schema['id-form']}' type='{$schema['type-input-form']}' placeholder='{$schema['placeholder-form']}' {$html_attributes}>";
    }


    $afterOpenWrapper = "";
    $beforeCloseWrapper = "";

    $labelAndInput = "{$label}{$tag_input}";

    if ($schema['type-input-form'] == 'checkbox' || $schema['type-input-form'] == 'radio') {
        $prefixWrapperCustom = $schema['type-input-form'] == "radio" ? $schema['type-input-form'] : 'switch';

        $descriptionThisType = "<div data-label=\"{$schema['placeholder-form']}\" data-group-id=\"{$schema['id-form']}\" class=\"description-custom-control font-weight-bold mb-2\">{$schema['placeholder-form']}</div>";

        $afterOpenWrapper = "{$descriptionThisType}<div class=\"custom-control custom-{$prefixWrapperCustom} d-inline-block ml-3 \">";
        $beforeCloseWrapper = "</div>";

        $labelAndInput = "{$tag_input}{$label}";

        if ($schema['type-input-form'] == 'checkbox') {
            loadHTMLParser();

            $doc = str_get_html($labelAndInput);
            $input = $doc->find(".form-input-one", 0);
            $name = $input->getAttribute('name');

            $input->setAttribute('name', $name . "[]");
            $labelAndInput = $doc->save();
        }
    }

    $final_input_items = "{$afterOpenWrapper}{$labelAndInput}{$beforeCloseWrapper}";

    if ($exceptional_extra_attributes) {

        $exceptional_extra_attributes_func_helper = [
            "radioBaseClonerFunc" => function ($final_input_items) use ($exceptional_extra_attributes) {

                $result = $final_input_items;

                loadHTMLParser();

                $doc_main = str_get_html($result);

                $input = $doc_main->find(".form-input-one", 0);
                $input->removeAttribute("data-label");

                $result = $doc_main->save();


                $counter = 0;

                foreach ($exceptional_extra_attributes as $itemKey => $itemValue) {
                    $attrValue = json_decode($itemValue, true);
                    if (!is_array($attrValue)) continue;

                    $value = @$attrValue[0];
                    $label = @$attrValue[1];


                    $doc = str_get_html($final_input_items);

                    $input = $doc->find(".form-input-one", 0);
                    $input->setAttribute('data-label', $label);
                    $input->setAttribute('value', $value);

                    $theID = $input->getAttribute('id') . "-" . ($counter + 1);
                    $input->setAttribute('id', $theID);

                    $labelDom = $doc->find(".custom-control-label", 0);
                    $labelDom->setAttribute('for', $theID);
                    $labelDom->__set('innertext', $label);

                    $descriptionThisType = $doc->find(".description-custom-control", 0);
                    if ($descriptionThisType) $descriptionThisType->remove();


                    $result .= $doc->save();

                    $counter++;
                }



                return $result;
            },

            "makeInputCheckedIfValueSet" => function ($DB, $html) {
                $result = $html;

                loadHTMLParser();

                $doc = str_get_html($html);

                $inputs = $doc->find(".form-input-one");
                $baseName = "";
                $array_value = [];

                foreach ($inputs as $index => $input) {
                    $originalValue = $input->getAttribute("data-value");

                    if ($index == 0) {
                        $baseName = str_replace(["[", "]"], "", $input->getAttribute("name"));
                        $rawValue = $DB[$baseName] ?? null;

                        if (!$rawValue) break;

                        $array_value = json_decode($rawValue, true);

                        if (!is_array($array_value)) $array_value = [$rawValue];

                        $input->setAttribute("value", $originalValue);
                    }

                    $input_value = $input->getAttribute("value");

                    if (in_array($input_value, $array_value)) {
                        $input->setAttribute("checked", "checked");
                    }
                }

                $result = $doc->save();

                return $result;
            }
        ];

        $exceptional_extra_attributes_func = [
            "radio" => function ($final_input_items) use ($exceptional_extra_attributes_func_helper, $DB) {
                $result = $exceptional_extra_attributes_func_helper['radioBaseClonerFunc']($final_input_items);

                if ($DB) {
                    $result = $exceptional_extra_attributes_func_helper['makeInputCheckedIfValueSet']($DB, $result);
                }

                return $result;
            },
            "checkbox" => function ($final_input_items) use ($exceptional_extra_attributes_func_helper, $DB) {
                $result = $exceptional_extra_attributes_func_helper['radioBaseClonerFunc']($final_input_items);

                if ($DB) {
                    $result = $exceptional_extra_attributes_func_helper['makeInputCheckedIfValueSet']($DB, $result);
                }

                return $result;
            },
        ];

        $cbk = $exceptional_extra_attributes_func[$schema['type-input-form']] ?? null;

        if (is_callable($cbk)) {
            $final_input_items = $cbk($final_input_items);
        }
    }

    $wrapper = "<div class='form-input-wrapper {$schema['type-input-form']}-wrapper {$schema['class-wrapper']}'>{$final_input_items}</div>";
    $html = $wrapper;

    return $html;
}

function getShowFormLink($type, $id = 0)
{
    $args = ["type" => $type];

    if ($id) {
        $args['id'] = $id;
    }

    $link = route("front_end.show_form", $args);

    return $link;
}

function getTableHeadFormSchema($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "title",
                    "data-operator" => "like"
                ],
                "text" => __local("Title"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "type",
                    "data-operator" => "like"
                ],
                "text" => __local("Type"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getAnswerOption(-1, true), true),
                    "data-name" => "is_login_required",
                    "data-operator" => "or"
                ],
                "text" => __local("is Login Required"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getStatusPageOption(), true),
                    "data-name" => "status",
                    "data-operator" => "or"
                ],
                "text" => __local("Status"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function getTableHeadForm($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "user_id",
                    "data-operator" => "equal"
                ],
                "text" => __local("User ID"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "form_schema_id",
                    "data-operator" => "equal"
                ],
                "text" => __local("Form Schema ID"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "ip",
                    "data-operator" => "like"
                ],
                "text" => __local("IP"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getStatusCommentOption(), true),
                    "data-name" => "status",
                    "data-operator" => "or"
                ],
                "text" => __local("Status"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function seperateRequiredAndUnRequiredElementsFormSchema($formSchema)
{

    $result = [];

    if (!$formSchema) return $result;

    $schema = json_decode($formSchema['schema'], true);

    if (!count($schema)) return $result;

    $result = [
        "required" => [],
        "optional" => [],
    ];

    foreach ($schema as $item) {
        if ($item['is_required']) {
            $result['required'][] = $item;
        } else {
            $result['optional'][] = $item;
        }
    }

    return $result;
}

function itemsToSaveFromFormSchema($formSchema)
{
    $result = [];

    if (!$formSchema) return $result;

    $schema = json_decode($formSchema['schema'], true);

    if (!count($schema)) return $result;

    foreach ($schema as $item) {
        $result[$item['id-form']] = [
            "type-input-form" => $item['type-input-form'],
            "is_required" => !empty($item['is_required']),
        ];
    }

    return $result;
}

function itemsToSaveFromFormSchemaToDBArrayList($array_list, $inputs)
{
    $result = [];

    if (!$array_list || !is_array($array_list)) return $result;

    foreach ($array_list as $itemKey => $itemValue) {
        $value = $inputs[$itemKey] ?? null;

        $result[$itemKey] = $value;
    }

    return $result;
}

function uploadFormFiles($formDBList)
{

    $result = $formDBList;

    if (!$formDBList || !is_array($formDBList)) return $result;

    $currentUser = getCurrentUser();
    $tempUserTriggered = false;

    if (!$currentUser) {
        // temporarily set User 1=Super_admin system to pass upload
        setCurrentUser(1);
        $tempUserTriggered = true;
    }

    foreach ($formDBList as $itemKey => &$itemValue) {
        if (!isUploadedFile($itemValue)) continue;

        $extension = getExtensionUploadedFile($itemValue);
        $size = $itemValue->getSize() + 1;

        $uploadedResult = null;

        $_uploadedResult = bridgeUploadFileForSimpleUser([$itemValue], $size, [$extension]);

        if ($_uploadedResult['is_valid']) {
            $uploadedResult = str_replace(ROOT_URL, "", $_uploadedResult['data']['url']);
        }

        $itemValue = $uploadedResult;
    }

    if ($tempUserTriggered) {
        emptyCurrentUser();
    }

    $result = $formDBList;

    return $result;
}

function exportFormAction($inputs)
{
    $import_code_raw = getTypeAttr($inputs, 'import_code');
    // re-create inputs by `import_code`
    if ($import_code_raw) {
        $inputs = [];
        $import_code = json_decode($import_code_raw, true);
        foreach ($import_code as $itemKey => $itemValue) {
            $itemKeySanitized = str_replace("#", "", $itemKey);
            $inputs[$itemKeySanitized] = is_array($itemValue) ? json_encode($itemValue) : $itemValue;
        }
    }

    return $inputs;
}

function getFormSchemaActionButtons($key)
{
    $label = __local("Form Schema");

    $list = [
        "draft" => createHtmlActionInputSetStatus("draft", getStatusPage()['draft'], $label, "yellow", true),
        "publish" => createHtmlActionInputSetStatus("publish", getStatusPage()['publish'], $label, "green", true),
    ];

    $target = $list[$key] ?? "";

    return $target;
}

function getGroupActionFormSchema()
{
    $groupActions = [
        "includes" => [
            [
                "actionName" => "Delete",
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Delete Selected Items ?"),
                "actionCbk" => "input#delete_action",
                "actionCbkHelper" => "deleteItemsList"
            ],


            [
                "actionName" => __local("Set Status") . " - " . __local("Draft"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Draft Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"draft\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],

            [
                "actionName" => __local("Set Status") . " - " . __local("Publish"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Publish Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"publish\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],


        ],
    ];

    return $groupActions;
}

function getFormActionButtons($key)
{

    $label = __local("Form");

    $list = [
        "pending" => createHtmlActionInputSetStatus("pending", getFormStatus()['pending'], $label, "yellow", true),
        "confirm" => createHtmlActionInputSetStatus("confirm", getFormStatus()['confirm'], $label, "green", true),
    ];

    $target = $list[$key] ?? "";

    return $target;
}

function getGroupActionForm()
{
    $groupActions = [
        "includes" => [
            [
                "actionName" => "Delete",
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Delete Selected Items ?"),
                "actionCbk" => "input#delete_action",
                "actionCbkHelper" => "deleteItemsList"
            ],


            [
                "actionName" => __local("Set Status") . " - " . __local("Pending"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Pending Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"pending\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],

            [
                "actionName" => __local("Set Status") . " - " . __local("Confirm"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Confirm Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"confirm\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],


        ],
    ];

    return $groupActions;
}

# =========> END Form

# =========> Validation
// validate by rules

function validateByAttrRegex($type, $value)
{
    $regex_data = typeRegexData($type);

    if (!$regex_data) dd("regex data not found");

    $tmp_value = $value;
    $regex = $regex_data['regex'];

    if (!$regex) dd("regex pattern not found");

    foreach ($regex as $reg) {
        $the_reg = $reg;
        $is_optional = false;

        if (is_array($reg)) {
            $the_reg = $reg[0];
            $is_optional = $reg[1];
        }

        $count = 0;
        $tmp_value = preg_replace($the_reg, "", $tmp_value, -1, $count);
        if ($count === 0 && !$is_optional) {
            $tmp_value = " ";
            break;
        }
    }



    return $tmp_value === "";
}

function validateByAttrEmail($type, $value)
{
    return is_string(filter_var($value, FILTER_VALIDATE_EMAIL));
}

function validateByAttrFileType($type, $value)
{
    $result = false;

    $listTypes = explode(",", $type);

    if (!$listTypes) die('invalid type set for callback ' . __FUNCTION__);

    foreach ($listTypes as &$item) {
        $item = trim($item);
    }

    $new_values = getUploadedFileTrackString($value);

    foreach ($new_values as $file) {
        if (!$file || !isUploadedFile($file)) return $result;

        $extension = getExtensionUploadedFile($file);

        if (!in_array($extension, $listTypes)) {
            return $result;
        }
    }

    if (is_array($new_values)) {
        $result = true;
    }


    return $result;
}

function validateByAttrFileSize($type, $value)
{
    $result = false;

    $new_values = getUploadedFileTrackString($value);

    foreach ($new_values as $file) {
        if (!$file || !isUploadedFile($file)) return $result;

        $size = $file->getSize();

        if ($type < $size) {
            return $result;
        }
    }

    if (is_array($new_values)) {
        $result = true;
    }


    return $result;
}

function validateByAttrIntRange($type, $value)
{
    $result = false;

    $value_raw = "[{$type}]";
    $type_array = @json_decode($value_raw, true);


    if (!$type_array || !is_array($type_array) || count($type_array) != 2) {
        die("invalid regex " . __FUNCTION__);
    }

    // check if really passed values are INTEGER
    if (is_int(stripos($value, ".")) || !is_numeric($value) || preg_match("/^-?\d+$/i", $value) !== 1) {
        return $result;
    }

    $result = mustBetweenNumber($value, $type_array[0], $type_array[1]);

    return $result;
}

function validateByAttrFloatRange($type, $value)
{
    $result = false;

    $value_raw = "[{$type}]";
    $type_array = @json_decode($value_raw, true);


    if (!$type_array || !is_array($type_array) || count($type_array) != 2) {
        die("invalid regex " . __FUNCTION__);
    }

    // check if really passed values are FLOAT
    if (!is_numeric($value) || preg_match("/^[-]?[0-9]*\.[0-9]+$/i", $value) !== 1) {
        return $result;
    }

    $result = mustBetweenNumber($value, $type_array[0], $type_array[1]);

    return $result;
}

function validateByAttrReservedValues($type, $value, $valueIsArrayble = true)
{
    $result = false;

    $the_value = @json_decode($value, true);

    $list = [];

    $rawList = $type;

    $list = @json_decode($rawList, true);

    if (!$list || !is_array($list)) {
        die("invalid reserved list set => {$rawList}");
    }

    $isInList = function ($item) use ($list) {
        $result = false;

        if (is_array($item)) {
            $result_depth_2 = true;
            foreach ($item as $sub_item) {
                if (!in_array($sub_item, $list)) {
                    $result_depth_2 = false;
                    break;
                }
            }
            $result = $result_depth_2;
        } else if (is_string($item)) {
            if (in_array($item, $list)) {
                $result = true;
            }
        }

        return $result;
    };

    if ($valueIsArrayble && is_array($the_value)) {
        $result = $isInList($the_value);
    } else if (is_string($value)) {
        $result = $isInList($value);
    }

    return $result;
}

function validateByAttrReservedValuesOnce($type, $value)
{
    $result = validateByAttrReservedValues($type, $value, false);

    return $result;
}

function validateByAttrNewsletterEmail($type, $value)
{
    $isEmail = validateByAttrEmail("email", $value);
    if (!$isEmail) return $isEmail;

    $valueList = explode("@", $value, 2);
    $valueDomain = $valueList[1];

    $list = $GLOBALS['newsletter_valid_domain'];
    $valildList = is_array($list) && $list ? $list : [];

    if ($valildList && !in_array($valueDomain, $valildList)) return false;

    return true;
}

function validateByAttrMax($max, $value)
{
    return (mb_strlen($value) <= $max) ? true : false;
}

function validateByAttrMin($min, $value)
{
    return ($min <= mb_strlen($value)) ? true : false;
}

function validateByAttrFalseMessage($callback, $rule, $title)
{
    $on_call_action_list = [
        "validateByAttrRegex"
    ];
    $list = [
        "validateByAttrMin" => __local("x-field must be greater than or equal x-rule character/s"),
        "validateByAttrMax" => __local("x-field must be lesser than or equal x-rule character/s"),
        "validateByAttrEmail" => __local("x-field is invalid"),
        "validateByAttrFileType" => "(x-field) " . __local("you can only upload these file types (x-rule)"),
        "validateByAttrFileSize" => "(x-field) " . __local("file is more than allowed upload or file is invalid") . " (" . __local("Maximum") . " x-rule " . __local("bytes") . ")",
        "validateByAttrIntRange" => __local("x-field must be integer and between x-rule"),
        "validateByAttrFloatRange" => __local("x-field must be float and between x-rule"),
        "validateByAttrReservedValues" => __local("x-field is not part of list"),
        "validateByAttrReservedValuesOnce" => __local("x-field is not part of list"),
        "validateByAttrNewsletterEmail" => __local("x-field is invalid must be on of these domains x-rule"),
    ];

    $on_call_search = array_search($callback, $on_call_action_list);
    if (is_int($on_call_search)) {
        $list[$callback] = (typeRegexData($rule))['description'];
    }

    $res = $list[$callback] ?? false;

    if (!$res) die("callback for {$callback} NOT FOUND");

    $searchList = ['x-field', 'x-rule'];
    $replaceList = [$title, $rule];


    if ($GLOBALS['ananymousFieldError'] === true) {
        array_shift($searchList);
        array_shift($replaceList);
    }


    $str = str_replace($searchList, $replaceList, $res);

    return $str;
}

function getValidateByAttrList($attrName)
{
    $list = [
        "min" => "validateByAttrMin",
        "max" => "validateByAttrMax",
        "email" => "validateByAttrEmail",
        "file_type" => "validateByAttrFileType",
        "file_size" => "validateByAttrFileSize",
        "int_range" => "validateByAttrIntRange",
        "float_range" => "validateByAttrFloatRange",
        "resereved_values" => "validateByAttrReservedValues",
        "resereved_values_once" => "validateByAttrReservedValuesOnce",
        "newsletter_email" => "validateByAttrNewsletterEmail",
        "regex" => "validateByAttrRegex",
    ];


    $res = $list[$attrName] ?? false;

    if (!$res)
        die("callback for {$attrName} NOT FOUND");

    return $res;
}

function validateByAttrName(string $attr)
{
    $tmpAttr = explode(":", $attr);
    $validBy = $tmpAttr[0];
    $validByValues = array_slice($tmpAttr, 1);

    $callback = getValidateByAttrList($validBy);
    if (!$callback) die("callback for {$validBy} NOT FOUND");

    return [
        "callback" => $callback,
        "res" => call_user_func($callback, ...$validByValues),
        "rule" => $tmpAttr[1]
    ];
}

function validateHandler(string $attr, $value, $for)
{

    if (isUploadedFile($value)) {
        $value = setUploadedFileTrackableForRequest($for);
    }

    $new_attr = "{$attr}:{$value}";
    $res = validateByAttrName($new_attr);

    if (!$res['res']) {
        $theMessage  = validateByAttrFalseMessage($res['callback'], $res['rule'], $for);
        return [
            'message' => $theMessage,
            'data' => $for
        ];
    }

    return true;
}

function validateInputsByRule(array $inputs, array $rules)
{
    if (!$inputs) die("inputs empty");
    else if (!$rules) die("rules empty");

    foreach ($rules as $ruleKey => $rule) {
        $input = @$inputs[$ruleKey];
        if (empty($input) && $input != "0") continue;

        foreach ($rule as $ruleElement) {

            $res = validateHandler($ruleElement, $input, $ruleKey);

            if ($res !== true) {
                return $res;
            }
        }
    }

    return true;
}

// END validate by rules

function restMessageEncode(array $list)
{
    return [
        'jsonServerMessage' => json_encode($list)
    ];
}

function triggerServerError($error, $to = null, $toRoute = null)
{
    $action = back();

    if ($to) {
        $action = redirect($to);
    } else if ($toRoute) {

        $route_name = "";
        $route_Args = [];

        if (is_array($toRoute)) {
            $route_name = $toRoute['name'];
            $route_Args = $toRoute['args'] ?? [];
            $route_query = $toRoute['query'] ?? [];

            if ($route_query) {
                $route_Args = array_merge($route_Args, $route_query);
            }
        } else {
            $route_name = $toRoute;
        }

        $action = redirect()->route($route_name, $route_Args);
    }

    return $action->withInput()->withErrors(restMessageEncode($error));
}

function getUserMessageValidate($message = null, $data = [])
{
    return [
        "message" => $message,
        "data" => $data,
    ];
}

function getUrlLikeSantizeInputList()
{
    return ["slug"];
}

function urlLikeSantize($arg, $list = [])
{
    $list = $list ?: getUrlLikeSantizeInputList();
    $value = $arg['value'];
    if (in_array($arg['key'], $list)) {
        $urlObject = new GenerateSeoFriendlyURL($value, 15, [
            "/[`_\"'\/\\\\]{1,}/i",
            "/[\s+*&^%$#@()=.,-]{1,}/i",
            "/[?!]{2,}/i",
        ]);
        $value = $urlObject->getUrl();
    }

    return $value;
}

function empty_white_space_input($input)
{
    if (empty($input)) return "";

    $input = preg_replace('/[\s]{1,}/i', "", $input);
    $input = trim($input);

    return $input;
}

function sanitize_input($input)
{
    if (empty($input) && $input != "0") return $input;

    if (is_array($input)) return $input;

    $input = preg_replace('/[\s]{2,}/i', " ", $input);
    $input = trim($input);

    return $input;
}

function getExistsMessage()
{
    return __local("x-field already Exists try another value");
}

function getUnAllowedMessage($XField = "x-field")
{
    $str = __local("invalid value for {$XField}");
    return $str;
}

function getEmptyMessage($sysnon)
{
    return __local("x-field $sysnon empty");
}

function getExistsDataTable($inputs, $existsData, $condition = null)
{
    $condition = $condition ?? "orWhere";

    $inputs = removeFilesFromListInputs($inputs);

    $duplicateExistsData = [];
    $existsModel = $existsData[0];
    foreach ($existsData[1] as $exists) {
        if (empty($inputs[$exists]) && @$inputs[$exists] != "0") continue;

        $inputs[$exists] = strtolower($inputs[$exists]);
        $existsModel = $existsModel->$condition($exists, $inputs[$exists]);
    }

    $checkDuplicate = function ($row) use ($existsData, $inputs, &$duplicateExistsData) {
        foreach ($existsData[1] as $element) {
            if (!empty($row[$element]) && (!is_null($row[$element]) && !is_null(@$inputs[$element])) && strtolower($row[$element]) == strtolower(@$inputs[$element]) && $row['id'] != @$existsData[2]) {
                if (!in_array($element, $duplicateExistsData))
                    $duplicateExistsData[] = $element;
            }
        }
    };


    $existsModel = $existsModel->skip(0)->take(2)->get();

    if (0 < count($existsModel)) {
        foreach ($existsModel as $row) {
            $checkDuplicate($row);
        }
    }


    return $duplicateExistsData;
}

function validationUserFormInputs(&$inputs, $allowedData = [], $existsData = [], $existsCondition = null)
{
    $require_input_list = getRequireInputJsonKeyMap();
    $inputs = renameArrayInput($inputs);

    $tmpInputs = [];
    $invalidAllowedData = [];
    $duplicateExistsData = [];


    foreach ($inputs as $k_input => $input) {

        // if FILE_OBJ PASS this phase
        if (isUploadedFile($input)) {
            $tmpInputs[] = $k_input;
            continue;
        }

        $inputs[$k_input] = urlLikeSantize([
            'key' => $k_input,
            'value' => $input
        ]);


        $inputs[$k_input] = sanitize_input($inputs[$k_input]);

        if (empty($inputs[$k_input]) && @$inputs[$k_input] != "0") continue;

        if (in_array($k_input, array_keys($require_input_list)) && $inputs[$k_input] != "") {
            $tmpInputs[] = $k_input;
        }

        // check for must be data like status 
        foreach ($allowedData as $key => $data) {
            if ($key === $k_input) {
                if (!in_array($inputs[$k_input], array_keys($data))) {
                    $invalidAllowedData[] = $k_input;
                }
            }
        }
    }



    // check for duplicate
    if (count($existsData)) {
        $duplicateExistsData = getExistsDataTable($inputs, $existsData, $existsCondition);
    }

    $res = array_diff(array_keys($require_input_list), $tmpInputs);
    $error = getUserMessageValidate();

    // validate inputs by rules
    $rules = $require_input_list;

    $validate_res = validateInputsByRule($inputs, $rules);

    if ($res) {
        // empty error
        $error["data"] = $res;
        $sysnon = areOrIsByItems($error["data"]);
        $error["message"] = getEmptyMessage($sysnon);
    } else if ($validate_res !== true) {
        // rule error
        $error["data"] = [$validate_res['data']];
        $error["message"] = $validate_res['message'];
    } else if ($invalidAllowedData) {
        // invalid value for some field ex: status
        $error["message"] = getUnAllowedMessage();
        $error["data"] = $invalidAllowedData;
    } else if ($duplicateExistsData) {
        // error for unique errors
        $error["message"] = getExistsMessage($duplicateExistsData);
        $error["data"] = $duplicateExistsData;
    }

    // if was ananymousFieldError false show completed error
    if ($error["data"]) {
        if ($GLOBALS['ananymousFieldError'] === false) {
            $error["message"] = str_replace('x-field', join(",", $error["data"]), $error['message']);
        }
    }

    $GLOBALS['error_validation'] = $error;

    return $error;
}

# =========> END Validation

# =========> inputs

function getValueFromOldOrDB($key, $db = [], $default = null)
{

    $result = old($key, "") != "" ? old($key) : @$db[$key];

    if (is_null($result) || (is_string($result) && trim($result) == "")) $result = $default;


    return $result;
}

# =========> END inputs

# =========> Post Type

function getPostTypeThumbnail($item, string $alt = "",  string $class = "rounded", string $id = "the-thumbnail", string $extra = "")
{
    $class = $class ?: "rounded";
    $id = $id ?: "the-thumbnail";
    $extra = trim($extra);
    if ($extra != "")
        $extra = " $extra";

    $str = "";
    $image_url = getTypeAttr($item, "thumbnail_url");
    if ($image_url) {
        $image_url = pathToURL($image_url);
        $str .= "<img src=\"{$image_url}\" class=\"{$class}\" id=\"{$id}\" alt=\"{$alt}\"{$extra}>";
    }

    return $str;
}

function getPostTypeThumbnailURL($item)
{
    return getTypeThumbnailURL($item);
}

function getPostTypeTaxonomy(object $post, array $types = [], array $status = ["publish"])
{
    $types = $types ?: getAllTypeByCondition(getAllType("taxonomy"), ["slug"], function ($list) {
        return $list->where("status", "public");
    });

    $theTaxonomyList = [];

    $i = 0;
    foreach ($post->taxonomies as $taxonomy) {
        $type = getTypee($taxonomy);
        if (in_array(getTypeStatus($taxonomy), $status) && in_array($type, $types)) {
            $theTaxonomyList[] = $taxonomy;
        }
        $i++;
    }

    return $theTaxonomyList;
}

function getPostTypeTaxonomyLinks(object $post, array $types = [], string $class = "bg-warning text-dark mt-2", string $id = "taxonomy-", array $status = ["publish"])
{

    $taxonomyList = getPostTypeTaxonomy($post, $types, $status);

    $class = $class ?: "bg-warning mt-2";
    $id = $id ?: "taxonomy-";

    $str = "";
    $i = 0;
    foreach ($taxonomyList as $taxonomy) {
        $theID = $id . ($i + 1);
        $link = getTaxonomyLink($taxonomy);
        $title = getTypeTitle($taxonomy);
        $str .= "<a href=\"{$link}\" class=\"{$class}\" id=\"{$theID}\">{$title}</a>\n";
        $i++;
    }

    return $str;
}

function getPostTypeAuthor(object $post, $action = "create", $export = "*")
{

    $action = $action ?? "create";

    $history_action = $post->history_action;

    $author = $history_action->where("action", $action)->first();

    if (!$author) return "";

    $tmp_author = $author->user;

    if (getTypeID($tmp_author)) {
        $author = $tmp_author;
    }

    if ($export != "*" && $author) {
        $author = getTypeAttr($author, $export, "");
    }

    return $author;
}

function getPostTypeListAuthor($itemList)
{

    $list = [];


    if (is_countable($itemList)) {
        $postIDList = [];
        foreach ($itemList as $item) {
            $postIDList[] = getTypeAttr($item, "model_id");
        }

        if (count($postIDList)) {
            $posts = App\Models\PostType::whereIn("id", $postIDList)->where("status",  "publish")->get();
            $list = $posts;
        }
    }

    return $list;
}

function getPostTypeResourceHTML($type, $action, $post, $route_args)
{
    $info = checkPostType($type);

    return getTypeResourceHTML("dashboard.post_type_create", [
        'post_type' => $info['current_post_type'],
        'post_type_data' => $info['current_post_type_info'],
        'action' => $action,
        'DB' => $post,
        'the_ID' => getTypeID($post),
        'route_args' => $route_args
    ]);
}

function getPostTypeTermData($term_value, $relation = "")
{
    $meta = App\Models\PostTypeMeta::where("key", "post_type_term_select")->where("value", $term_value)->with('postType')->get()->first();
    $model = $meta;

    if (!$model) return false;

    if ($relation == "postType") {
        $model = $meta->$relation;
    }

    return $model;
}

function postTypePermission($type, $action = "edit")
{
    $component_taxonomy = [];
    $component_meta = [];

    $post_permission = getCurrentUserPermission('post.type');

    if (!count($post_permission)) return [];

    $type_permission = $post_permission->where("action", $action)->where("type", $type);

    if (!count($type_permission)) return [];

    $component_own = collect($type_permission->first()['component']);
    if (count($type_permission)) {
        $component_taxonomy = searchTheCollection($type_permission, "component", "taxonomy[");
        $component_meta = searchTheCollection($type_permission, "component", "meta:");

        $taxonomy_group_input = groupTaxonomyForInputs($component_taxonomy);

        // diff
        $component_own = $component_own->diff($component_taxonomy);
        $component_own = $component_own->diff($component_meta);
        $component_own = $component_own->values();
    }

    return [
        "own" => $component_own,
        "taxonomy" => collect(array_keys($taxonomy_group_input['taxonomy'])),
        "meta" => $component_meta
    ];
}

function getMetaArgsList()
{

    $metaMap = $GLOBALS['meta_args_list'];

    return $metaMap[$GLOBALS['current_page']] ?? [];
}

function getPostTypeMetaTableHeadLoop($metaDataMap)
{
    $str = "";
    $headMap = [];

    if (is_countable($metaDataMap) && count($metaDataMap)) {
        foreach ($metaDataMap as $meta) {
            $currentMeta = $meta;
            $currentLabel = $currentMeta['label'];
            unset($currentMeta['label']);
            $currentMap = [
                'attr' => $currentMeta,
                'text' => $currentLabel
            ];

            array_push($headMap, $currentMap);
        }
        $str = getTableHeadPostType([], $headMap);
    }

    return $str;
}

function getGroupActionPostType()
{
    $groupActions = [
        "includes" => [
            [
                "actionName" => "Delete",
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Delete Selected Items ?"),
                "actionCbk" => "input#delete_action",
                "actionCbkHelper" => "deleteItemsList"
            ],


            [
                "actionName" => __local("Set Status") . " - " . __local("Draft"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Draft Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"draft\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],

            [
                "actionName" => __local("Set Status") . " - " . __local("Publish"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Publish Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"publish\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],


        ],
    ];

    return $groupActions;
}

# =========> END Post Type

# ========> Menu

function registerMenu($prop)
{
    return registerTypeDynamically($prop, ['label', 'slug'], $GLOBALS['menu']);
}

function getTableHeadMenu($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "title",
                    "data-operator" => "like"
                ],
                "text" => __local("Title"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "slug",
                    "data-operator" => "like"
                ],
                "text" => __local("Slug"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function menuErrorHandler(&$inputs, $error)
{
    if ($error['message']) {
        return triggerServerError($error);
    } else if (!$error['message']) {
        // for json validation
        $inputs['menu_items'] = json_decode($inputs['menu_items'] ?: [], true);
        $inputs['menu_items'] = $inputs['menu_items']['menuElements'] ?: [];

        $error = getUserMessageValidate();
        if ($inputs['menu_items']) {
            resetGlobal('str', "");
            resetGlobal('loopbreak', false);
            nestedLoopArray($inputs['menu_items'], 'menuIndexerDashboardValidation');
            $error = $GLOBALS['str'];
        } else {
            $error['message'] = __local("Menu Items are Empty");
            $error['data'] = [];
        }


        if (!empty($error['message'])) {
            return triggerServerError($error);
        }
    }
}

function getMenuMap($key)
{
    $list = [
        "menu_li_name" => [
            "label" => __local("Name")
        ],

        "menu_li_url" => [
            "label" => __local("Link"),
        ],

        "menu_li_css" => [
            "label" => __local("Css Class ( optional )"),
        ],
    ];

    return $list[$key] ?? [];
}

function generateTemplateMenuDashboard($element, $extraAttr = "")
{
    return "<label for=\"{$element['id']}\">{$element['label']}</label>
<input type=\"text\" value=\"{$element['value']}\" {$extraAttr} class=\"form-control input-event focus-disable-sortable text-center mb-2\" id=\"{$element['id']}\" data-label=\"{$element['label']}\">";
}

function menuIndexerDashboard($key, $element)
{

    if (is_array($element)) {
        $id = $element['id'] ?? '';

        $GLOBALS['str'] .= "<li id=\"{$id}\">";
        $attrList = ['menu_li_name', 'menu_li_url', 'menu_li_css'];

        $labelDelete = __local("Delete");

        foreach ($attrList as $i => $attrItem) {

            $theElement = getMenuMap($attrItem);
            $element_id = $attrItem;
            $element_label = $theElement['label'];

            $htmlAttrs = [
                'id' => $element_id,
                'label' => $element_label,
                'value' => @$element[$element_id]
            ];

            $extra_htmlAttrs = "";

            if ($element_id == 'menu_li_url')
                $extra_htmlAttrs = "dir=\"ltr\"";

            $GLOBALS['str'] .= generateTemplateMenuDashboard($htmlAttrs, $extra_htmlAttrs);

            if ($i + 1 == count($attrList)) {
                $GLOBALS['str'] .= "<input type=\"button\" class=\"btn btn-danger btn-delete focus-disable-sortable\" value=\"{$labelDelete}\">";
            }
        }
        $GLOBALS['str'] .= "<ul>";
        if (!empty($element['children']))
            nestedLoopArray($element['children'], 'menuIndexerDashboard');
        $GLOBALS['str'] .= "</ul></li>";
    }
}

function showMenuItems($menuItemsJson, $callback)
{
    $str = "";

    $menuItems = json_decode($menuItemsJson, true) ?? [];

    if (!empty($menuItems['menuElements']) || !empty($menuItems[0])) {
        if (!empty($menuItems['menuElements'])) $menuItems = $menuItems['menuElements'];

        resetGlobal('str', "");
        nestedLoopArray($menuItems, $callback);
        $str = getAllType('str');
    }

    return $str;
}

function menuIndexerFrontEnd($key, $element)
{

    if (is_array($element)) {
        $id = $element['id'] ?? '';
        $liID = $element['menu_li_css'] ? "li-{$element['menu_li_css']}" : $id;

        $GLOBALS['str'] .= "<li id=\"{$liID}\">";

        $aID = $element['menu_li_css'] ? "a-{$element['menu_li_css']}" : "";

        $GLOBALS['str'] .= "<a class=\"text-decoration-none {$element['menu_li_css']}\" id=\"{$aID}\" href=\"{$element['menu_li_url']}\">{$element['menu_li_name']}</a>";


        if (!empty($element['children'])) {
            $GLOBALS['str'] .= "<ul class=\"child-menu\">";
            nestedLoopArray($element['children'], 'menuIndexerFrontEnd');
            $GLOBALS['str'] .= "</ul>";
        }
        $GLOBALS['str'] .= "</li>";
    }
}

function addAttrWalk($str, $attr_walk)
{
    loadHTMLParser();

    $doc = str_get_html($str);

    $all_html = $doc->find("*");

    foreach ($all_html as $element) {
        $dataTree = $element->getAttribute('data-tree');
        if (!$dataTree) continue;

        $dataTreeList = explode("-", $dataTree);
        $targetName = $dataTreeList[0] . "-" . $dataTreeList[1];

        if (!in_array($targetName, array_keys($attr_walk))) continue;

        $targetAttrs = $attr_walk[$targetName];

        foreach ($targetAttrs as $targetElementKey => $targetElement) {

            if ($targetElementKey == "class")
                $element->addClass($targetElement);
            else
                $element->setAttribute($targetElementKey, $targetElement);
        }
    }

    return $doc->save();
}

function addTreeDomRelation($str)
{
    loadHTMLParser();

    $doc = str_get_html($str);

    $treeRelation = [
        // sample (when parent is root function will using this)
        // 'x' => [
        //     'depth' => 1,
        //     'number' => 0
        // ]
    ];

    $all_html = $doc->find("*");

    foreach ($all_html as $element) {
        $tagName = $element->nodeName();
        $parent = $element->parentNode();
        if ($parent->nodeName() == "root") {
            if (!isset($treeRelation[$tagName])) {
                $treeRelation[$tagName] = [
                    'depth' => 1,
                    'number' => 0
                ];
            }

            $treeRelation[$tagName]['number']++;

            $depth = $treeRelation[$tagName]['depth'];
            $number = $treeRelation[$tagName]['number'];
        } else {
            $treeData = $parent->getAttribute("data-tree");
            $treeData = explode("-", $treeData);

            $dataTreeChild = $parent->getAttribute("data-tree-child") ?: 0;
            $dataTreeChild++;
            $number = $dataTreeChild;
            $parent->setAttribute("data-tree-child", $dataTreeChild);

            $depth = $treeData[1] + 1;
        }


        $baseNameClass = "{$tagName}-{$depth}-{$number}";

        $element->setAttribute("data-tree", $baseNameClass);
        $element->addClass($baseNameClass);
    }

    return $doc->save();
}

function getMenuBySlug($menu_slug)
{
    $menus = App\Models\Menu::where("slug", $menu_slug)->get();
    return $menus;
}
function showMenuFrontEnd($menu_slug, $attr_walk = [])
{
    $str = "";
    $menu = getMenuBySlug($menu_slug)->first();

    if (!$menu) {
        return $str;
    }

    $menuStr = showMenuItems(getTypeAttr($menu, "menu_items"), "menuIndexerFrontEnd");
    $menuStr = "<ul data-attr-name=\"{$menu_slug}\">" . $menuStr . "</ul>";

    $menuStr = addTreeDomRelation($menuStr);
    if ($attr_walk)
        $menuStr = addAttrWalk($menuStr, $attr_walk);

    return $menuStr;
}

function showMenuFrontEndDepth1(string $menu_slug, array $attr_walk = [], string|callable|null $cbk = null)
{
    $result = "";
    $html = showMenuFrontEnd($menu_slug, $attr_walk);

    if (!$html) return $result;


    loadHTMLParser();

    $doc = str_get_html($html);

    if (is_callable($cbk)) {
        $result = $cbk($doc);
    } else {
        $aTags = $doc->find("a");

        foreach ($aTags as $aTag) {
            $result .= $aTag->__get("outertext");
        }
    }

    return $result;
}
function getMenuValidationElements()
{
    $list = [
        "menu_li_name",
        "menu_li_url",
    ];

    return $list;
}

function menuIndexerDashboardValidation($key, $element)
{

    if (is_array($element)) {

        $require_elements = getMenuValidationElements();
        $error = getUserMessageValidate();

        foreach ($require_elements as $require_element) {
            if (empty(trim($element[$require_element]))) {
                $error['data'][] = $require_element;
            }
        }

        if ($error['data']) {
            $sysnon = areOrIsByItems($error["data"]);
            $error['message'] = getEmptyMessage($sysnon);
            $GLOBALS['loopbreak'] = true;
            $GLOBALS['str'] = $error;
            return;
        }

        if (!empty($element['children']))
            nestedLoopArray($element['children'], 'menuIndexerDashboardValidation');
    }
}

# =======> END Menu


# ======> View

function getTableHeadView($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "post_type_title",
                    "data-operator" => "like"
                ],
                "text" => __local("Post Type Title"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "ip",
                    "data-operator" => "like"
                ],
                "text" => __local("IP"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "user_id",
                    "data-operator" => "equal"
                ],
                "text" => __local("User ID"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "user_fullname",
                    "data-operator" => "like"
                ],
                "text" => __local("User Full Name"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "user_email",
                    "data-operator" => "like"
                ],
                "text" => __local("User Email"),
            ],
            [
                "attr" => [
                    "data-input" => "",
                    "data-name" => "user_details",
                    "data-operator" => "like"
                ],
                "text" => __local("User Details"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Visited"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function addViewLog($post_type_id)
{

    $inserted_id = 0;
    $post_type = App\Models\PostType::where("id", $post_type_id)->where("status", "publish")->get()->first();

    if (is_null($post_type)) return -1;

    $ip = request()->ip();
    if (!$ip) return -2;

    $canInsert = false;

    $lastThisIP = App\Models\View::where("ip", $ip)->where("post_type_id", getTypeID($post_type))->latest()->skip(0)->take(1)->get()->first();

    $secondsHour = 3600;
    $hour = 24;
    $baseRelease = $hour * $secondsHour;

    $current_time = time();

    if (is_null($lastThisIP)) {
        $canInsert = true;
    } else {
        $visited_time = dateToUnixTime(getTypeDateCreated($lastThisIP));
        $releaseRecordTime = $visited_time + $baseRelease;
        if ($releaseRecordTime < $current_time) {
            $canInsert = true;
        }
    }

    $user = getCurrentUser();

    if ($canInsert) {
        $row = App\Models\View::create([
            'post_type_id' => getTypeID($post_type),
            'post_type_title' => getTypeTitle($post_type),
            'user_id' => $user ? getTypeID($user) : 0,
            'user_fullname' => $user ? getTypeFullname($user) : "UNKNOWN",
            'user_email' => $user ? getTypeEmail($user) : "UNKNOWN",
            'ip' => $ip
        ]);

        $inserted_id = getTypeID($row);
    }

    return $inserted_id;
}

function getViewLog($post_type_id)
{
    $views = App\Models\View::where("post_type_id", $post_type_id)->count();
    return $views;
}

# =====> END View


# =====> File

function getMessageFileNotFound($extra = "")
{
    return "File Not Found{$extra}";
}

function getFileRest()
{
    $response = [
        "element" => [],
        "pages" => 0,
        "current_page" => 0,
    ];

    $current_user = getCurrentUser();

    if (!getCurrentUserRolesDetailsCanAccess("file.list")) {
        abort(403);
    }


    $queryQ = request()->post("q");
    $queryGroupType = request()->post("group_type");
    $queryType = strtolower(request()->post("type", ""));


    if (is_null($queryGroupType)) return $response;

    $groupTypeList = getFileGroupTypes();
    $groupTypeList["all"] = "All";

    // check group type
    if (!in_array($queryGroupType, array_keys($groupTypeList))) return $response;

    $files = File::when($queryGroupType, function ($query, $queryGroupType) {
        if ($queryGroupType != "all")
            $query->where("group_type", $queryGroupType);
    })->when($queryQ, function ($query, $queryQ) {
        $query->where(function ($query) use ($queryQ) {
            $query->where("original_title", "LIKE", "%{$queryQ}%")->orWhere("current_title", "LIKE", "%{$queryQ}%");
        });
    })->when($queryType, function ($query, $queryType) {
        $query->where("format", $queryType);
    });


    if (!isSuperAdmin($current_user)) {
        $files->where("source", "dashboard");
    }

    $per_page = getAllType('record_per_page') < 10 ? 10 : getAllType('record_per_page');

    $response['count'] = $files->count();
    $response['pages'] = getPageCountByItem($response['count'], $per_page);
    $response['current_page'] = intval(request()->post("page", 1));

    $files = $files->latest();
    $files = $files->paginate($per_page);

    if (count($files)) {
        foreach ($files as $file) {
            $tmp_file = $file->getOriginal();
            $theFile = adapterArrayKey(["original_title", "current_title", "dimension", "url", "format"], ["name", "current_name", "sizes", "file_url", "type"], $tmp_file);

            $response["element"][] = [
                "id" => getTypeID($theFile),
                "name" => getTypeName($theFile),
                "current_name" => getTypeAttr($theFile, "current_name"),
                "sizes" => getTypeSizes($theFile),
                "file_url" => "/" . getTypeAttr($theFile, "file_url"),
                "type" => getTypee($theFile),
                "group_type" => getTypeGroupType($theFile),
            ];
        }
    }

    return $response;
}

function getTableHeadFile($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-name" => "preview",
                ],
                "text" => __local("Preview"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "original_title",
                    "data-operator" => "like"
                ],
                "text" => __local("Original Title"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "current_title",
                    "data-operator" => "like"
                ],
                "text" => __local("Current Title"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-unsort" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getFileTypesOption(), true),
                    "data-name" => "format",
                    "data-operator" => "or"
                ],
                "text" => __local("Type"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-unsort" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getFileGroupTypesOption(), true),
                    "data-name" => "group_type",
                    "data-operator" => "or"
                ],
                "text" => __local("Group Type"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberRange",
                    "data-options" => '{"min":0,"max":1000,"from":0,"to":998,"step":1,"prefix":"<span class=\"d-inline-block\">MB</span> "}',
                    "data-name" => "size",
                    "data-operator" => "range"
                ],
                "text" => __local("File Size(MB)"),
            ],
            [
                "attr" => [
                    "data-name" => "dimension",
                ],
                "text" => __local("Dimension"),
            ],
            [
                "attr" => [
                    "data-name" => "user_fullname",
                ],
                "text" => __local("Uploaded By"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Uploaded"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function searchInGroupType($type)
{

    $type = strtolower($type);
    $allTypes = getAllType('group_type');

    $theGroupKey = "misc";

    foreach ($allTypes as $theTypeKey => $theType) {
        foreach ($theType as $fileElementKey => $fileElement) {
            if ($type == strtolower($fileElementKey)) {
                $theGroupKey = $theTypeKey;
                break 2;
            }
        }
    }

    return $theGroupKey;
}

function getGroupTypeElementByExt(string $groupType, string $type)
{
    $groupType = strtolower($groupType);
    $type = strtoupper($type);

    $allGroupType = getAllType('group_type');

    $element = [];

    $currentGroupType = isset($allGroupType[$groupType]) ? $allGroupType[$groupType] : [];

    if (empty($currentGroupType)) return $element;

    $currentType = isset($currentGroupType[$type]) ? $currentGroupType[$type] : [];

    return $currentType;
}

function getGroupTypeElementMetaDataByExt(string $groupType, string $type, string $metaKey)
{
    $element = getGroupTypeElementByExt($groupType, $type);
    return isset($element[$metaKey]) ? $element[$metaKey] : null;
}

function getImageFileNameByDimensions($basename, $dimension)
{
    $fileParts = explode(".", $basename);
    $basenameWithoutExt = $fileParts[0];
    $theExt = $fileParts[1];

    $dynamicTemplate = generateCustomTemplateByArray("x-0x-x-1y", $dimension);
    $newBasename = $basenameWithoutExt . "-{$dynamicTemplate}." . $theExt;

    return $newBasename;
}

function genereateSubImageByCondition(string $filename, string $save_path, $sizes, array $condition_dimension, string $condition_type)
{

    $saved_file_list = [];

    $the_filename = base_path($filename);
    $file_info = @getimagesize($the_filename);

    $save_path = base_path($save_path) . SPE;

    if (!$file_info) return getMessageFileNotFound();

    list($file_info_width, $file_info_height) = $file_info;
    list($condition_width, $condition_height) = $condition_dimension;

    $file_info_type = $file_info['mime'];

    if (!((($file_info_width == $condition_width) && ($file_info_height == $condition_height)) && ($file_info_type == $condition_type))) {
        return __local("File not have required Condition");
    }

    $imagine = new \Imagine\Gd\Imagine();

    foreach ($sizes as $size) {
        list($width, $height) = $size;
        $image = $imagine->open($the_filename);
        $new_filename = "favicon-{$width}" . ".png";
        $image->resize(new \Imagine\Image\Box($width, $height))->save($save_path . $new_filename);
        $saved_file_list[$width] = $new_filename;
    }

    return $saved_file_list;
}

function generateImageSizesByUploadedFile($group_type, $file_extension, $url_location)
{

    do_action("before_image_sizes_check", $group_type, $file_extension, $url_location);

    $image_can_resize = getGroupTypeElementMetaDataByExt($group_type, $file_extension, 'can_resize');
    $dimension = [];

    if ($image_can_resize === true) {
        $fileSavedPath = getElementByExplodePart($url_location, 1, 25);
        if ($fileSavedPath) {
            $fileSavedPath = join(SPE, $fileSavedPath);
            $full_fileSavedPath = storage_path($fileSavedPath);
            $basename = basename($full_fileSavedPath);

            $resizeList = getAllType('resize_image_list');
            $sizes = @getimagesize($full_fileSavedPath);

            if (!$sizes) return $dimension;

            list($width, $height) = $sizes;

            foreach ($resizeList as $resizeElement) {
                if (!($resizeElement < $width)) continue;

                $newDimension = getResizeByDimension([$width, $height], $resizeElement);
                if (!$newDimension) continue;

                $newBasename = getImageFileNameByDimensions($basename, $newDimension);

                $newFilePathNameWithDeminsion = str_replace($basename, $newBasename, $full_fileSavedPath);
                $dimension[] = [$newDimension[0], $newDimension[1]];

                // imagine
                $imagine = new \Imagine\Gd\Imagine();
                $image = $imagine->open($full_fileSavedPath);
                $image->resize(new \Imagine\Image\Box($newDimension[0], $newDimension[1]))->save($newFilePathNameWithDeminsion);
                // END imagine

                do_action("after_image_size_generate", $newFilePathNameWithDeminsion);
            }
            $dimension[] = [$width, $height];
        }
    }

    return $dimension;
}

function getSubSizesImageByFilename($file_path, $absolute = true, $model = null)
{
    $basename = basename($file_path);

    $row = $model ? $model : App\Models\File::where("current_title", "LIKE", $basename)->skip(0)->take(1)->get()->first();

    $new_dimensions = [];

    if (!$row) return $new_dimensions;

    $dimensions = json_decode($row->dimension, true);
    $dimensions = $dimensions ?? [];

    foreach ($dimensions as $dimension) {
        $width = $dimension[0];
        $new_dimensions[$width] = getImageFileNameByDimensions($basename, $dimension);
        if (!$absolute) {
            $new_dimensions[$width] = str_replace($basename, $new_dimensions[$width], $row->url);
        }
    }

    return $new_dimensions;
}

function isUnAllowedFormat($format_name)
{
    $format_name = strtolower($format_name);
    $unallowedList = arrayToLowerCase(getAllType('unallowed_extension'));
    return in_array($format_name, $unallowedList) === false ? false : true;
}

function createDirectoryArchive($sliceFromIndex = false)
{
    $base_path = str_replace('\\', SPE, env('STORAGE_BASE'));

    $elements = [
        [
            "prefix" => storage_path($base_path),
            "directory" => SPE .  date("Y") . SPE
        ],
        [
            "prefix" => "X_BEFORE",
            "directory" => SPE .  date("m") . SPE
        ]
    ];

    $lastDataIndex = "";
    foreach ($elements as $key => $element) {
        if ($key === 0 && $element['prefix'] == "X_BEFORE") $element['prefix'] = "";

        $prefix = $element['prefix'] == "X_BEFORE" ? $lastDataIndex : $element['prefix'];
        $directory = $element['directory'];
        $theDirectoryPath = $prefix . $directory;
        if (!is_dir($theDirectoryPath)) {

            mkdir($theDirectoryPath);
            // forbidden indexing files
            file_put_contents($theDirectoryPath . "index.php", "");
        }

        $lastDataIndex = realpath($theDirectoryPath);
    }

    if (is_int($sliceFromIndex)) {
        $lastDataIndex = explode(SPE, $lastDataIndex);
        $lastDataIndex = array_slice($lastDataIndex, $sliceFromIndex);
        $lastDataIndex = join(SPE, $lastDataIndex);
    }

    return $lastDataIndex;
}

function getResizeByDimension(array $original_dimension, int $new_width)
{
    if (!$new_width) return false;

    $metaData = getAspectRatio($original_dimension[0], $original_dimension[1]);

    if (!$metaData) return false;

    if ($metaData['max'] === 0) {
        $new_height = $new_width / $metaData['aspect_ratio'];
    } else if ($metaData['max'] === 1) {
        $new_height = $new_width * $metaData['aspect_ratio'];
    }

    $new_height = intval($new_height);

    return [$new_width, $new_height];
}

function getAspectRatio($width, $height)
{
    if ($width === 0 || $height === 0) return false;

    $max = 0;

    if ($width < $height) $max = 1;

    $aspectRatio = $max === 0 ? $width / $height : $height / $width;

    return [
        'aspect_ratio' => $aspectRatio,
        'max' => $max
    ];
}

function bridgeUploadFileForSimpleUser($uploadedFiles, $max_file_size_byte = null, $allowed_format = [])
{
    $result = [
        "is_valid" => false,
        "error" => "",
        "data" => []
    ];

    $max_file_size_byte = $max_file_size_byte ?: 2500000;
    $allowed_format = $allowed_format ?: ["docx"];

    $files = $uploadedFiles;

    $current_user = getCurrentUser();

    if (empty($files) || !is_countable($files) || 1 < count($files)) {
        $result['error'] = str_replace("x-field", __local("File"), __local("x-field is invalid"));
        return $result;
    } else if (!$current_user) {
        $result['error'] = getMessageMustBeLogginToDoThisAction();
        return $result;
    }

    $file = $files[0];

    $file_originalName = $file->getClientOriginalName();
    $file_originalNameExploded = explode(".", $file_originalName);

    $extension = null;

    if (1 < count($file_originalNameExploded)) {
        $extension = array_reverse($file_originalNameExploded)[0] ?? null;
    }

    if ($max_file_size_byte < $file->getSize()) {
        $result['error'] = str_replace("x-size", $max_file_size_byte, __local("file is more than maximum size (x-size) bytes"));
        return $result;
    } else if (!in_array($extension, $allowed_format)) {
        $result['error'] = str_replace("x-format", join(" , ", $allowed_format), __local("file must be have these format/s (x-format)"));
        return $result;
    }

    request()->request->add(["source" => "authorize"]);

    $file_controller = new App\Http\Controllers\FileController();
    $response = $file_controller->store([$file]);


    if (empty($response['success'][0])) {
        $result['error'] = $response['errors'][0]['message'];
        return $result;
    }

    $result['is_valid'] = true;
    $result['data'] = $response['success'][0];


    return $result;
}

function isUploadedFile($input_file)
{
    return (is_a($input_file, 'Illuminate\Http\UploadedFile'));
}

function setUploadedFileTrackableForRequest(string $request_file_name)
{
    $prefix = "FILE____";

    $result =  $prefix . $request_file_name;

    return $result;
}

function getUploadedFileTrackableForRequest(string $encodedTrackableStr)
{
    $prefix = "FILE____";

    $result =  str_replace($prefix, "", $encodedTrackableStr);

    return $result;
}

function getUploadedFileTrackString(string $encodedTrackableStr)
{
    $new_value = getUploadedFileTrackableForRequest($encodedTrackableStr);
    $new_value = request()->file($new_value);

    $new_values = [$new_value];

    return $new_values;
}

function removeFilesFromListInputs($inputs)
{
    // re-correct for removing FILE_OBJECT
    $_inputs = $inputs;
    $inputs = [];

    foreach ($_inputs as $_itemKey => $_itemValue) {
        if (isUploadedFile($_itemValue)) {
            continue;
        }

        $inputs[$_itemKey] = $_itemValue;
    }

    return $inputs;
}

function getExtensionFileByFileName($file_originalName)
{
    $file_originalNameExploded = explode(".", $file_originalName);
    $file_originalNameExploded = cleanTheArray($file_originalNameExploded);

    $extension = end($file_originalNameExploded);

    $extension = strtolower($extension);

    return $extension;
}

function getExtensionUploadedFile($file)
{
    $result = null;
    if (!isUploadedFile($file)) return $result;

    $file_originalName = $file->getClientOriginalName();
    $extension = getExtensionFileByFileName($file_originalName);

    return $extension;
}



# =====> END File


# ======> Comment

function getCommentRest()
{
    $response = [
        "element" => [],
        "pages" => 0,
        "current_page" => 0,
    ];


    $queryPostID = request()->post("post_id", 0);
    $queryType = request()->post("type", "comment");

    if (!$queryPostID) return $response;

    $queryPostID = intval($queryPostID);

    $comments = App\Models\Comment::where("type", $queryType)->where("post_type_id", $queryPostID)->where("status", "confirmed")->get();

    $per_page = getAllType('comment_per_page');

    $response['count'] = $comments->count();
    $response['pages'] = getPageCountByItem($response['count'], $per_page);
    $response['current_page'] = intval(request()->query("page", 1));

    $response["element"] = get_post_type_comments(null, $queryPostID, null, $queryType, "page", true, null, $comments);

    return $response;
}

function getCommentCountAllTypePending()
{
    return App\Models\Comment::where("status", "pending")->count();
}

function getMessageCommentsClosed($comment_label, $tag = "b")
{
    return getMessageWithOptionalTag(__local("{$comment_label} Closed"), $tag);
}

function getMessageCommentsUnRegisterd($tag = "b")
{
    return getMessageWithOptionalTag(__local('This Comment Type Does not registered for this post type'), $tag);
}

function getMessageCommentsDisabled($comment_label, $tag = "b")
{
    return getMessageWithOptionalTag(__local("{$comment_label} Disabled By admin"), $tag);
}

function getMessageCommentsMustBeLoggin($comment_label, $tag = "b")
{

    return getMessageWithOptionalTag(__local("to Send {$comment_label} You Must Login First !"), $tag);
}

function comment_element_edit_callback($key, $item)
{
    $the_element = "";

    $callback_comment = function () use ($item) {
        return "<label>" . __local('Comments') . " : </label> " . "<span id=\"comments\" class=\"badge badge-info\">" . getTypeCounts($item, "comments") . "</span><br>";
    };

    $callback_rating = function () use ($item) {
        $labelOf = " " . __local("of") . " ";
        $labelUsers = __local("user/s");
        return "<label>" . __local('Ratings') . " : </label> " . "<span id=\"rating\" class=\"badge badge-info\">" . getDefaultType(getTypeAvg($item, "comments_rating", "rating"), "-") . $labelOf . count($item["comments_rating"]) . " {$labelUsers}</span>";
    };

    $list = [
        "comment" => $callback_comment,
        "rating" => $callback_rating
    ];

    $the_element = $list[$key] ?? "";

    return $the_element ? $the_element($item) : $the_element;
}

function comment_td_list_callback($key, $item)
{
    $the_td = "";

    $list = [
        "comment" => "<td><div class=\"badge badge-large badge-pill badge-primary\">" . getTypeCounts($item, "comments") . "</div></td>",
        "rating" => "<td><div class=\"badge badge-large badge-pill badge-primary\">" . getDefaultType(getTypeAvg($item, "comments_rating", "rating"), "-") . "</div></td>",
    ];

    $the_td = $list[$key] ?? "";

    return $the_td;
}

function getCommentAttrMapCallback($key)
{
    $list = [
        "comment" => 'getCommentCommentAttr',
        "rating" => 'getCommentRatingAttr',
    ];

    return $list[$key] ?? false;
}

function getCommentCommentAttr()
{
    return [
        "attr" => [
            "data-filter" => "",
            "data-input" => "numberRange",
            "data-options" => '{"min":0,"max":500000000,"from":0,"to":499000000,"step":10,"prefix":"<span class=\"d-inline-block\"></span> "}',
            "data-name" => "comments_count",
            "data-operator" => "range"
        ],
        "text" => __local("Comments"),
    ];
}

function getCommentRatingAttr()
{
    return [
        "attr" => [
            "data-filter" => "",
            "data-input" => "numberRange",
            "data-options" => '{"min":0,"max":5,"from":0,"to":5,"step":1,"prefix":"<span class=\"d-inline-block\"></span> "}',
            "data-name" => "comments_rating_avg_rating",
            "data-operator" => "range",
        ],
        "text" => __local("Rating of (5)"),
    ];
}

function getCommentReplyTo(App\Models\Comment $item)
{
    $replyTo = getTypeAttr($item, "parent_id") ?? 0;
    $replyTo = $replyTo != "0" ? App\Models\Comment::where("id", $replyTo)->get()->first() : null;

    return $replyTo;
}

function getTableHeadComment_comment($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "title",
                    "data-operator" => "like"
                ],
                "text" => __local("Post Type Title")
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "fullname",
                    "data-operator" => "like"
                ],
                "text" => __local("Fullname"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "ip",
                    "data-operator" => "like"
                ],
                "text" => __local("IP"),
            ],
            [
                "attr" => [
                    "data-name" => "reply_to",
                ],
                "text" => __local("Reply To"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "content",
                    "data-operator" => "like"
                ],
                "text" => __local("Content"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getStatusCommentOption(), true),
                    "data-name" => "status",
                    "data-operator" => "or"
                ],
                "text" => __local("Status")
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function getTableHeadComment_rating($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "title",
                    "data-operator" => "like"
                ],
                "text" => __local("Post Type Title"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "fullname",
                    "data-operator" => "like"
                ],
                "text" => __local("Fullname"),
            ],

            [
                "attr" => [],
                "text" => "",
                "action" => ["getCommentRatingTable", [[true] ?? -999]]
            ],

            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "ip",
                    "data-operator" => "like"
                ],
                "text" => __local("IP"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "content",
                    "data-operator" => "like"
                ],
                "text" => __local("Content"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getStatusCommentOption(), true),
                    "data-name" => "status",
                    "data-operator" => "or"
                ],
                "text" => __local("Status"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function getTableHeadComment($type = "comment", $thTagProp = [])
{

    $callback = "getTableHeadComment_{$type}";

    if (!is_callable($callback)) dd("Cannot Call This Function");

    return $callback($thTagProp);
}

function commentInputValidation(&$inputs, $type, $allowedData = [], $id = null)
{
    $exists = [];
    $existsCondition = $type === "rating" ? "where" : null;

    if ($type === "rating") {
        $inputs['type'] = 'rating';
        $inputs = getUserDetailsByKeys(['id'], $inputs, ["user_id"]);

        $exists = [new App\Models\Comment(), ["type", "post_type_id", "user_id"], $id];
    }

    $error = validationUserFormInputs($inputs, $allowedData, $exists, $existsCondition);

    $res = null;
    if ($error['message']) {
        // --> be careful <-- it will handle all exitst to this message
        if ($error['message'] == getExistsMessage($error['data'])) {
            $error['data'] = [];
            $error['message'] = __local("You Have Already Rate this Product");
        }
        $res = triggerServerError($error);
    } else {
        $error = getUserMessageValidate();
        if ($type == "rating" && (!is_numeric($inputs['rating']) || 0 < preg_match_all('/\.{1,}/i', $inputs['rating']) || $inputs['rating'] <= 0)) {
            $error['message'] = __local("x-field must be integer and more than 0");
            $error['data'] = ["rating"];
        } else if ($type == "rating" && !(mustBetweenNumber($inputs['rating'], 1, 5))) {
            $error['message'] = __local("x-field must be between 1 and 5");
            $error['data'] = ["rating"];
        }
        if ($error['message']) {
            $res = triggerServerError($error);
        }
    }

    $customValidationCbk = "custom_validation_{$type}";

    if (!$error['message'] && is_callable($customValidationCbk)) {
        $resultCustomValition = $customValidationCbk($inputs, $type, $allowedData, $id);
        if ($resultCustomValition !== true) {
            $error = $resultCustomValition;
            $res = triggerServerError($error);
        }
    }

    return $res;
}

function registerComment($prop)
{
    return registerTypeDynamically($prop, ['label', 'slug'], $GLOBALS['comment']);
}

function searchInComment($slug)
{
    foreach ($GLOBALS['comment'] as $comment) {
        if ($comment['slug'] === $slug) {
            return $comment;
        }
    }

    return false;
}

function checkComment($current_comment)
{
    $current_comment_info = checkDynamicType($current_comment, "searchInComment");

    return [
        "current_comment" => $current_comment,
        "current_comment_info" => $current_comment_info['current_type_info'],
    ];
}

function getCommentRatingTable()
{
    $str = "";
    $labelRating = __local('Rating of (5)');
    $str .= "<th data-filter data-operator=\"range\" data-input=\"numberRange\" data-name=\"rating\" data-options='{\"min\":0,\"max\":5,\"from\":0,\"to\":5,\"step\":1,\"prefix\":\"<span class=\\\"d-inline-block\\\"></span> \"}'>{$labelRating}</th>";
    return $str;
}

function checkCommentShowInPost($post, $type)
{
    $canUse = false;
    $meta = $post->meta;

    if (!count($meta)) {
        return true;
    } else if (!commentExistsInPostType(checkPostType(getTypee($post)), $type)) {
        return $canUse;
    }

    $post_type_comment_switch = $meta->where("key", "post_type_comment_switch")->first();

    if (!is_null($post_type_comment_switch)) {
        $the_values = getTypeAttr($post_type_comment_switch, "value");

        if ($the_values) {
            if (!is_int(strpos($the_values, $type))) {
                $canUse = true;
            }
        } else {
            $canUse = true;
        }
    } else {
        $canUse = true;
    }

    return $canUse;
}

function rating_template_list()
{
    $template = "<li class=\"shadow p-3 mb-3 border border-success comment-depth-x-depthx-parentx-childx-has-child comment-item comment-x-id\" id=\"comment-x-id\"> <b class=\"comment-fullname\" data-star-rating=\"x-rating\">x-fullname</b><p class=\"comment-content\">x-content</p> <time>x-full_date-created</time></li>";
    return $template;
}

function getCommentFormTemplate($type = "comment")
{
    $labels = [
        "email" => __local("Email"),
        "fullname" => __local("Fullname"),
        "post_type_id" => __local("Post Type ID"),
        "content_plc" => __local("what do you think ?"),
        "content" => __local("Comment Content"),
        "submit" => __local("Submit"),
        "parent_id" => __local("Reply To"),
        "rating" => __local("Rating")
    ];


    $loginStatus = getUserClassHtml();


    $user_info = "<input class=\"form-control text-start\" type=\"email\" name=\"email\" id=\"email\" placeholder=\"{$labels['email']}\" value=\"x-value-email\" data-label=\"{$labels['email']}\">
    <input class=\"form-control\" type=\"text\" name=\"fullname\" id=\"fullname\" placeholder=\"{$labels['fullname']}\" value=\"x-value-fullname\" data-label=\"{$labels['fullname']}\"><br>";

    $postID_info = "<input class=\"form-control\" type=\"hidden\" name=\"post_type_id\" id=\"post_type_id\" value=\"x-value-post_type_id\" data-label=\"{$labels['post_type_id']}\">";
    $comment_text = "<textarea class=\"form-control {$loginStatus}\" name=\"content\" data-group-id=\"content\" cols=\"30\" rows=\"10\" placeholder=\"{$labels['content_plc']}\" data-label=\"{$labels['content']}\">x-value-content</textarea><br>";
    $submit_type = "<input class=\"btn btn-warning text-dark fw-bold\" type=\"submit\" value=\"{$labels['submit']}\">";

    if (auth()->check()) {
        $user_info = "";
    }

    $saveUserInfo = !$user_info ? "" : "<label for=\"save_comment_info\">" . __local("Save my name, email in this browser for the next time I comment") . "</label><input type=\"checkbox\" name=\"save_comment_info\" value=\"yes\" id=\"save_comment_info\">";

    $classAndID = "class=\"form-type-comment\" id=\"form-{$type}\"";
    $cancelBtn = "<input type=\"button\" class=\"btn btn-danger\" id=\"cancel-comment-form\" value=\"لغو\">";

    $list = [
        "comment" => "<form action=\"x-route\" method=\"post\" {$classAndID}>
        x-csrf
        {$user_info}
        {$postID_info}
        <input class=\"form-control\" type=\"hidden\" name=\"parent_id\" id=\"parent_id\" value=\"x-value-parent_id\" data-label=\"{$labels['parent_id']}\">
        {$comment_text}
        {$saveUserInfo}
        {$submit_type}
        {$cancelBtn}
    </form>",
        "rating" => "<form action=\"x-route\" method=\"post\" {$classAndID}>
        x-csrf
        {$user_info}
        {$postID_info}
        <input class=\"form-control text-start\" type=\"number\" step=\"1\" id=\"rating\" name=\"rating\" placeholder=\"{$labels['rating']}\" value=\"x-value-rating\" data-label=\"{$labels['rating']}\"><br>
        {$comment_text}
        {$submit_type}
        {$cancelBtn}
    </form>",
    ];
    return $list[$type] ?? "";
}

function getCommentFormTemplateValue($post_type_id, $type = "comment")
{
    $user_cookie_info = getUserCommentInfoByCookie();

    $list = [
        "comment" => [
            "x-value-email" => old('email', @$user_cookie_info['email']),
            "x-value-fullname" => old('fullname', @$user_cookie_info['fullname']),
            "x-value-parent_id" => old('parent_id') ? old('parent_id') : -1,
            "x-value-post_type_id" => old('post_type_id') ? old('post_type_id') : $post_type_id,
            "x-value-content" => old('content'),
            "x-value-submit" => old('submit') ? old('submit') : "Submit",
        ],
        "rating" => [
            "x-value-email" => old('email'),
            "x-value-fullname" => old('fullname'),
            "x-value-rating" => old('rating'),
            "x-value-parent_id" => -1,
            "x-value-post_type_id" => $post_type_id,
            "x-value-content" => old('content'),
            "x-value-submit" => old('submit') ? old('submit') : "Submit",
        ],
    ];
    return $list[$type] ?? "";
}

function getCommentForm($model = null, $post_type_id = 0, $type = "comment")
{
    if (!$model) {
        $model = PostType::find($post_type_id);
        if (!$model) return false;
    } else if (!$post_type_id) {
        $post_type_id = getTypeID($model);
    }

    $post_type_info = checkPostType(getTypee($model));

    // check for comment close
    if (!checkCommentShowInPost($model, $type)) {
        return getMessageCommentsClosed(ucwords($type));
    }

    // check for user logged in for desire comment type
    if (($type == "rating" || getAllType('comment_must_be_login')) && !auth()->check()) {
        return getMessageCommentsMustBeLoggin(ucfirst($type));
    }

    // check for registered comment for post type
    if (!commentExistsInPostType($post_type_info, $type)) {
        return getMessageCommentsUnRegisterd();
    }

    $route = getCommentRouteForm($type);
    $csrf = csrf_field();

    $template = getCommentFormTemplate($type);
    $values = getCommentFormTemplateValue($post_type_id, $type);

    // in common fields
    $values['x-csrf'] = $csrf;
    $values['x-route'] = $route;

    $the_value_keys = array_keys($values);
    $the_value_values = array_values($values);

    $dynamicTemplate = str_replace($the_value_keys, $the_value_values, $template);

    return $dynamicTemplate;
}

function getCommentRouteForm($type = "comment")
{
    $route = getTheRoute("comments", "create", ["type" => $type]);
    return $route;
}

function comment_indexer($items, $originalItems, $template)
{

    $labels = [
        "parent_id" => __local("Reply to"),
        "reply" => __local("Reply"),
    ];

    $str = "";
    foreach ($items as $item) {

        $replyTo = $originalItems->where("id", getTypeAttr($item, "parent_id"))->first();
        $hasChild = is_null($originalItems->where("parent_id", getTypeID($item))->first()) ? "" : " has-child";

        $daysAgo = (date_diff(date_create(getTypeDateCreated($item, false)), date_create(getDateByUnixTime())))->format("%a");
        $daysAgo = $daysAgo ? $daysAgo . " " . __local("days ago") : "";

        $argsToParse = [
            "x-depth" => getTypeAttr($item, "depth"),
            "x-id" => getTypeID($item),
            "x-fullname" => htmlentities(getTypeAttr($item, "fullname")),
            "x-email" => getTypeEmail($item),
            "x-reply-to" => getTypeAttr($replyTo, "fullname") ? "<span class=\"comment-reply-to\"> {$labels['parent_id']} <strong>" . getTypeAttr($replyTo, "fullname") . "</strong></span>" : "",
            "x-parent" => getTypeAttr($item, "parent_id") === 0 ? " parent-comment" : "",
            "x-_parent-id" => getTypeAttr($item, "parent_id"),
            "x-avatar-url" => getUserGravatarByEmail(getTypeEmail($item), 100),
            "x-child" => getTypeAttr($item, "parent_id") != 0 ? " child-comment" : "",
            "x-has-child" => $hasChild,
            "x-content" => sanitizeXssScriptString(getTypeContent($item)),
            "x-rating" => getTypeAttr($item, 'rating'),
            "x-reply-btn" => $GLOBALS['comment_max_depth'] < getTypeAttr($item, 'depth') + 1 ? "" : "<button class=\"comment-reply-btn\">{$labels['reply']}</button>",
            "x-full_date-created" => getTypeDateCreated($item),
            "x-date-created" => getTypeDateCreated($item, false),
            "x-time-created" => getTypeDate($item, "created_at", true, false),
            "x-full_date-updated" => getTypeDateUpdated($item),
            "x-days-ago" => $daysAgo,
            "x-date-updated" => getTypeDateUpdated($item, false),
            "x-time-updated" => getTypeDate($item, "updated_at", true, false),
        ];

        $dynamicTemplate = str_replace(array_keys($argsToParse), array_values($argsToParse), $template);
        $str .= $dynamicTemplate;
        $id = getTypeID($item);
        $row = $originalItems->where("parent_id", $id);
        if ($row) {
            $str .= comment_indexer($row, $originalItems, $template);
        }
    }

    return $str;
}

function get_post_type_comments_query($post_type_id, $comment_type, $sort = "latest")
{
    $query = App\Models\Comment::where("post_type_id", $post_type_id)->where("type", $comment_type)->where("status", "confirmed")->$sort();
    return $query;
}

function get_post_type_comments_page($items, $post_type_id, $comment_page)
{
    $pages = get_comment_pages_count($items);
    $post_type_link = getPostTypeLink(null, $post_type_id);
    $str = "";
    if (1 < $pages) {
        $str .= "<div class=\"pagination pagination-{$comment_page}\">";

        for ($i = 0; $i < $pages; $i++) {
            $current_page_number = $i + 1;
            $active = "";
            if (@$_GET[$comment_page] == $current_page_number) {
                $active = " active-page";
            }
            $current_page = $post_type_link . "?{$comment_page}={$current_page_number}";
            $str .= "<a class=\"text-dark{$active}\" href={$current_page} data-page=\"{$current_page_number}\">{$current_page_number}</a>";
        }
        $str .= "</div>";
    }

    return $str;
}

/*
    @return comment link used in front end
*/
function get_post_type_comment_link($post_type_id, $comment_id, $comment_type = null, $comment_page = null)
{

    $link = null;

    $comment_type = $comment_type ?? "comment";
    $comment_page = $comment_page ?? "comment_page";

    $comments = get_post_type_comments_query($post_type_id, $comment_type);

    $single_comment = clone $comments;
    $single_comment = $single_comment->where("id", $comment_id)->get()->first();

    $comments = $comments->get();

    if (!$comments) return $link;

    if (!$single_comment) return $link;


    $i = 1;
    while ($link === null) {
        $_GET[$comment_page] = $i;

        $current_item = get_post_type_comments_per_page($comments, $comment_page);
        $pages = get_comment_pages_count($comments);

        $searchRow = $current_item->where("id", $comment_id)->first();

        if (!$searchRow) {
            $single_parent_id = getTypeAttr($single_comment, "origin_parent_id");
            $searchRow = $current_item->where("id", $single_parent_id)->first();
        }

        if ($searchRow) {
            $post = PostType::findOrFail($post_type_id);
            $link = getPostTypeLink($post);
            $link .= "?{$comment_page}={$i}&focus_element=comment-{$single_comment->id}";
            break;
        }

        if ($pages < $i) break;

        $i++;
    }



    return $link;
}

function get_comment_pages_count($current_item)
{
    return getPageCountByItem(count($current_item), $GLOBALS['comment_per_page']);
}

function get_comments_base_depth($comments)
{
    return $comments->where("depth", 1);
}

function get_post_type_comments_per_page($comments, $comment_page)
{
    $current_item = get_comments_base_depth($comments);

    $pages = get_comment_pages_count($current_item);
    if (1 < $pages) {
        $current_page = empty($_GET[$comment_page]) || !is_numeric($_GET[$comment_page]) || $pages < $_GET[$comment_page] ? 1 : $_GET[$comment_page];
        $skip = $current_page * $GLOBALS['comment_per_page'] - $GLOBALS['comment_per_page'];
        $take = $GLOBALS['comment_per_page'];

        $current_item = $current_item->skip($skip)->take($take);
    }

    return $current_item;
}

/*
    @return string|array comment list with pagination
*/
function get_post_type_comments($model = null, $post_type_id = 0, $template = null, $comment_type = null, $comment_page = null, $pagination = false, $comment_sort = null, $commentOveloaded = false)
{

    if (!$model) {
        $model = PostType::find($post_type_id);
        if (!$model) return false;
    } else if (!$post_type_id) {
        $post_type_id = getTypeID($model);
    }

    $comment_type = $comment_type ?? "comment";
    $comment_page = $comment_page ?? $comment_type . "_page";
    $comment_sort = $comment_sort ?? "latest";

    $cbk_template = "{$comment_type}_template_list";
    if ($template === null && is_callable($cbk_template)) {
        $template = $cbk_template();
    }

    $template = $template ?? "<li data-id=\"x-id\" data-parent=\"x-_parent-id\" class=\"bg-info mb-3 comment-depth-x-depthx-parentx-childx-has-child comment-item comment-x-id\" id=\"comment-x-id\"> <b class=\"comment-fullanme\">x-fullname</b>x-reply-to <p class=\"comment-content\">x-content</p> <time>x-full_date-created</time>x-reply-btn</li>";

    $post_type_info = checkPostType(getTypee($model));

    // check for disable comment from dashboard
    # if (!checkCommentShowInPost($post_type_id, $comment_type)) return [
    #     "error" => getMessageCommentsClosed(ucwords($comment_type))
    # ];

    // check for registered comment for post type
    if (!commentExistsInPostType($post_type_info, $comment_type)) {
        return [
            "error" => getMessageCommentsUnRegisterd()
        ];
    }


    $comments = $commentOveloaded ? $commentOveloaded : get_post_type_comments_query($post_type_id, $comment_type, $comment_sort)->get();
    $comments_base_depth = get_comments_base_depth($comments);
    if (!$comments) return "";

    $current_item = get_post_type_comments_per_page($comments, $comment_page);

    $comment_list = comment_indexer($current_item, $comments, $template);

    $pages = get_post_type_comments_page($comments_base_depth, $post_type_id, $comment_page);

    $finalData = $pagination ? ["comment_list" => $comment_list, "pagination" => $pages] : $comment_list;

    return $finalData;
}

function doesThisUserAlreadyCommentType($type, $post_type_id, $user_id = 0, $collect = "first")
{
    $result = collect([]);
    $user = $user_id ? getUserByID_Query($user_id)->get()->first() : getCurrentUser();

    if (!$user) return $collect == "first" ? $result->first() : $result;

    $comment_type = $user->comment_type()->where("type", $type)->where("post_type_id", $post_type_id)->get();

    if ($collect == "first") {
        $comment_type = $comment_type->first();
    }

    $result = $comment_type;

    return $result;
}

function doesThisUserAlreadyCommentThisPostType($post_type_id, $user_id = 0, $collect = "first")
{
    return doesThisUserAlreadyCommentType("comment", $post_type_id, $user_id, $collect);
}

function doesThisUserAlreadyRateThisPostType($post_type_id, $user_id = 0, $collect = "first")
{
    return doesThisUserAlreadyCommentType("rating", $post_type_id, $user_id, $collect);
}

function getUserCommentInfoByCookie()
{
    return isset($_COOKIE['user_comment_info']) ? json_decode($_COOKIE['user_comment_info'], true) : [];
}

function getCommentActionButtons($key, $labelType = "Comment", $extraAttr = "")
{

    $labelType = __local($labelType);

    $list = [
        "pending" => createHtmlActionInputSetStatus("pending", getStatusComment()['pending'], $labelType, "yellow", true, $extraAttr),
        "confirmed" => createHtmlActionInputSetStatus("confirmed", getStatusComment()['confirmed'], $labelType, "green", false, $extraAttr),
    ];

    $target = $list[$key] ?? "";

    return $target;
}

function changeButtonConfrimToConfrimAndReply($commentAndReplyBtn)
{
    $result = "";
    loadHTMLParser();
    $doc = str_get_html($commentAndReplyBtn);

    $btnAction = $doc->find("#set-status_action", 0);
    if ($btnAction) {
        // value
        $btnAction->__set("value", __local("Reply"));

        // class
        $btnAction->addClass("openModal");

        // action
        $btnAction->__set("data-id-form", "form.confirm_and_reply");


        // callback
        $btnAction->__set("data-callback-zero", "ReplyFormActionClick");
    }

    $result = $doc->save();

    return $result;
}

function action_after_check_set_status_type_comment_confirmed($manualError, $modelName, $action, $id)
{
    $result = $manualError;

    if (1 < ($GLOBALS['comment_status_type'] ?? 0)) return $result;

    $sub_action = request()->post("sub_action");

    if ($sub_action != "confirm_and_reply") return $result;

    $parent_id = request()->post("id");

    $comment = App\Models\Comment::find($id);

    if (!$comment) return $result;

    $tmp = [];
    $tmp['parent_id'] = $parent_id;

    $controllerComment = new App\Http\Controllers\CommentController;

    $type = getTypee($comment);
    $resultController = $controllerComment->store($type, $tmp);

    $error = getErrorMessage();

    if (!$error) {
        return $result;
    } else {
        $errorDynamic = json_decode($error, true);
        if (!empty($errorDynamic['state']) && $errorDynamic['state'] == "success") {

            $controllerComment->statusType($type);
        } else {
            $result = $resultController;
        }
    }



    return $result;
}

add_filter("after_check_set_status_type_comment_confirmed", "action_after_check_set_status_type_comment_confirmed", 10, 4);

function getGroupActionComment()
{
    $groupActions = [
        "includes" => [
            [
                "actionName" => "Delete",
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Delete Selected Items ?"),
                "actionCbk" => "input#delete_action",
                "actionCbkHelper" => "deleteItemsList"
            ],


            [
                "actionName" => __local("Set Status") . " - " . __local("Pending"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Pending Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"pending\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],

            [
                "actionName" => __local("Set Status") . " - " . __local("Confirm"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Confirm Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"confirmed\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],


        ],
    ];

    return $groupActions;
}

# =====> END Comment

# ======> Email

function sendEmailManually($pendingMail, $mailable)
{
    // config("mail.mailers.smtp.password") -> if password has # in it in then env file will be ignore it so can set it in config/mail.php
    // also this one called from PendingMail@send IF default mail not work
    $mailable = $mailable->build();
    $html = view($mailable->view, ["data" => $mailable->data])->render();
    $to = accessUnaccessableProperty($pendingMail, "to");

    $username = config("mail.mailers.smtp.username");


    if (filter_var($username, FILTER_VALIDATE_EMAIL) !== false) {
        $username_params = explode("@", $username, 2);
        $domain = $username_params[1];

        $address_params = explode("@", $mailable->from[0]['address'], 2);
        $name = $address_params[0];

        if ($username_params[1] != $address_params[1]) {
            $mailable->from[0]['address'] = "{$name}@{$domain}";
        }
    }

    // PHPMailer
    $mail = new PHPMailer\PHPMailer\PHPMailer(false);
    $mail->CharSet = 'UTF-8';

    try {
        // auth 
        $mail->isSMTP();
        $mail->Host = config("mail.mailers.smtp.host");
        $mail->SMTPAuth = true;

        $mail->Username = $username;
        $mail->Password = config("mail.mailers.smtp.password");
        $mail->SMTPSecure = config("mail.mailers.smtp.encryption");
        $mail->Port = intval(config("mail.mailers.smtp.port"));
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ),
        );

        // address
        $mail->setFrom($mailable->from[0]['address'], $mailable->from[0]['name']);
        $mail->addReplyTo($mailable->from[0]['address'], $mailable->from[0]['name']);
        $mail->addAddress($to);

        // content
        $mail->isHTML(true);
        $mail->Subject = $mailable->subject;
        $mail->Body = $html;

        $mail->send();
        return true;
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

function sendEmailRest()
{

    $response = [
        "is_valid" => false,
        "message" => "",
    ];

    $predefined_content = [
        "frontend.form.mail.order_delivery_moved"
    ];

    $queryEmail = request()->post("email");
    $querySubject = request()->post("subject");
    $queryContent = request()->post("content");
    $queryTypeID = request()->post("type_id");

    $user = getCurrentUser();
    $attachedUser = $user->vendor;


    // check for valid vendor
    if (!$attachedUser && !isSuperAdmin($user)) return $response;

    if (!getCurrentUserRolesDetailsCanAccess("order.list") || !isset($queryEmail) || !filter_var($queryEmail, FILTER_VALIDATE_EMAIL) || !isset($querySubject) || !isset($queryContent) || !in_array($queryContent, $predefined_content) ||  !isset($queryTypeID)) {
        return $response;
    }

    $content = view($queryContent)->render();
    $data = [
        "subject" => __local($querySubject),
        "content" => $content,
        "no-did-you" => true,
        "type" => "Order",
        "type_id" => $queryTypeID,
        "action" => last(explode(".", $queryContent))
    ];

    \Illuminate\Support\Facades\Mail::to($queryEmail)->send(new \App\Mail\EmailSys($data));

    $response['is_valid'] = true;
    $response['message'] = getMessageRequestSuccessfullySend();

    return $response;
}

function addEmailLog($data)
{

    if (!isset($data['type']))
        dd("type does not set for log");
    else if (!isset($data['type_id']))
        dd("type ID does not set for log");

    App\Models\EmailLog::create([
        "subject" => $data['subject'],
        "type" => $data['type'],
        "type_id" => $data['type_id'],
        "action" => ($data['action'] ?? null)
    ]);
}

function getEmailLog(array $attrs)
{
    $query = App\Models\EmailLog::where("id", "!=", 0);

    foreach ($attrs as $keyAttr => $attr) {
        $query->where($keyAttr, $attr);
    }

    $rows = $query->get();
    return $rows;
}

# ======> END Email

# ======> newsletter

function getNewsletterForm($type = "email")
{
    return view("dashboard.component.form.newsletter_form", [
        "type" => $type
    ]);
}

function getTableHeadNewsletter($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "client_id",
                    "data-operator" => "like"
                ],
                "text" => __local("Client ID"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-unsort" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getNewsletterTypeOption(), true),
                    "data-name" => "type",
                    "data-operator" => "or"
                ],
                "text" => __local("Type"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "ip",
                    "data-operator" => "like"
                ],
                "text" => __local("IP"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

# ======> END newsletter

# ======> User

function getUserMaxDescriptionLength()
{
    return 500;
}

function isUserRoleX($user, $role)
{
    return getTypeRole($user) == $role;
}

function isSuperAdmin($user)
{
    return isUserRoleX($user, "super_admin");
}

function isAdmin($user)
{
    return isUserRoleX($user, "admin");
}

function isSimpleUser($user)
{
    return isUserRoleX($user, "user");
}

function getUserClassHtml()
{
    return getCurrentUser() ? "logged-in" : "guest";
}

function getUserRoleClassHtml($prefix = "view-")
{
    $user = getCurrentUser();
    return $prefix . getTypeRole($user);
}

function getUserIDClassHtml($prefix = "user-")
{
    $user = getCurrentUser();
    return $prefix . getTypeID($user);
}

function getMessageMustBeLogginToDoThisAction()
{
    return __local("To Complete this aciton you must login first !");
}

function toDoThisActionMustBeLoggin()
{
    $user = getCurrentUser();

    if (!$user) return triggerServerError(getUserMessageValidate(getMessageMustBeLogginToDoThisAction(), []));

    return $user;
}

function getUserPageFrontEnd($user_id)
{
    $route_name = "front_end.author.list";

    if (!$user_id) return "/";

    return isRouteExists($route_name) ? route($route_name, [$user_id]) : "/";
}

function checkClientIDByStatus($inputs, $user, $old_status = null)
{
    $state = null;

    // force deactive when client id (email,phone) changed
    $keys = array_keys($inputs);
    $clientUpdateList = getClientIDFields(null);
    foreach ($keys as $key) {
        if (in_array($key, $clientUpdateList)) {
            $changed = checkClientIDChangedNitche($inputs[$key], $user[$key], $key);
            if ($changed) {
                $inputs['status'] = "deactive";
            }
        }
    }


    // check for client id (email,phone) if change *_verified => false and *_verified_at => null        
    // status == "active" is exclude of that
    if ($inputs['status'] == "active") {
        // fix *_verified and *_verified_at CHANGE on every status active update
        if ($inputs['status'] != $user['status'] || (isset($old_status) && $inputs['status'] != $old_status)) {
            $state = "verified";
            checkUpdateClientIDON($inputs, $user);
        }
    } else {
        $state = "need verifiy";
        checkUpdateClientIDOff($inputs, $user);
    }

    return $state;
}

function getCurrentUserBaseEntityVariable($id = -1)
{
    $permissions = getCurrentUserPermission("user");
    $the_list = [];
    $user = getCurrentUser();
    $user_role = getTypeRole($user);
    $item_user = $id === -1 ? null : App\Models\User::find($id);

    return [
        'permissions' => $permissions,
        'the_list' => $the_list,
        'user' => $user,
        'user_role' => $user_role,
        'item_user' => $item_user,
    ];
}

function getCurrentUserAccessLevelEntity_Create($id = -1)
{
    $var_list = getCurrentUserBaseEntityVariable($id);

    $actions = $var_list['permissions']->where("action", "create");

    foreach ($actions as $action) {
        $own_id = $action['details']['own_id'] ?? null;

        if (!is_null($own_id) && $own_id != getTypeID($var_list['user'])) {
            continue;
        }

        $var_list['the_list'] = array_merge($var_list['the_list'], $action['entity_levels']);
    }

    // fix when user watching own profile considering #role
    if (getTypeID($var_list['user']) == $id) {
        $label = (getUserRoles())[$var_list['user_role']];
        $var_list['the_list'] = [$var_list['user_role'] => $label];
    }

    return $var_list['the_list'];
}

function getCurrentUserStatusEntity_Create($id = -1, $export = "dynamic")
{
    $id = $id ?? -1;
    // when set to "simple" get all status non duplicate without watching entity level
    // when set to "dynamic" get all status non duplicate with watching entity level and should pass $id
    $export = $export ?? "dynamic";

    $var_list = getCurrentUserBaseEntityVariable($id);
    $user_status = getTypeStatus($var_list['user']);

    $actions = $var_list['permissions']->where("action", "index")->where("component", "status");
    $existsStatusList = getStatusUser();

    $item_user_role = getTypeRole($var_list['item_user']);

    foreach ($actions as $action) {
        $own_id = $action['details']['own_id'] ?? null;

        if (!is_null($own_id) && $own_id != getTypeID($var_list['user'])) {
            continue;
        }

        $status_value = $existsStatusList[$action['component_part']] ?? null;
        if (in_array($action['component_part'], array_keys($existsStatusList))) {
            $var_list['the_list'][$action["condition"]][] = ["key" => $action['component_part'], "value" => $status_value];
        }
    }

    $tmpList = [];
    if ($export == "simple") {
        $tmpList = mergeTheArrayKeyValue($var_list['the_list']);
        $var_list['the_list'] = $tmpList;
    } else if ($export == "dynamic" && $var_list['item_user']) {
        if (getUserOrderRole($var_list['user_role'], $item_user_role) === 0) {
            // same entity

            $same_entity_list = $var_list['the_list']['same_entity'] ?? [];

            if ($same_entity_list) {
                $tmpList = mergeTheArrayKeyValue($same_entity_list, false);
                $var_list['the_list'] = $tmpList;
            }
        } else if (getUserOrderRole($var_list['user_role'], $item_user_role) === 1) {
            // bottom entity
            $bottom_entity_list = $var_list['the_list']['bottom_entity'] ?? [];
            if ($bottom_entity_list) {
                $tmpList = mergeTheArrayKeyValue($bottom_entity_list, false);

                $var_list['the_list'] = $tmpList;
            }
        }
    }

    // fix when user watching own profile considering #status
    if (getTypeID($var_list['user']) == $id) {
        $label = $existsStatusList[$user_status];
        $var_list['the_list'] = [$user_status => $label];
    }

    // fix bottom_entity or same_entity
    if (count($var_list['the_list']) && (!empty($var_list['the_list']['bottom_entity']) || !empty($var_list['the_list']['same_entity']))) {
        $tmpList2 = array_values($var_list['the_list']);
        $var_list['the_list'] = end($tmpList2);
        $var_list['the_list'] = mergeTheArrayKeyValue($var_list['the_list'], false);
    }

    return $var_list['the_list'];
}

function canUserAction($user, $component, $component_part = null)
{

    $permissions = getCurrentUserPermission("user");
    $actions = $permissions->where("action", "index")->where("component", $component)->where("component_part", $component_part);

    $user_role = getTypeRole($user);
    $user_id = getTypeID($user);

    $current_user = getCurrentUser();
    $current_user_id = getTypeID($current_user);

    if ($current_user_id == $user_id) return false;

    foreach ($actions as $action) {
        if (in_array($user_role, array_keys($action['entity_levels']))) {
            if (!empty($action['details']['own_id']) && $current_user_id == $action['details']['own_id']) {
                return true;
            } else if (!empty($action['details']['own_id']) && $current_user_id != $action['details']['own_id']) {
                return false;
            } else if (empty($action['details']['own_id'])) {
                return true;
            }
        }
    }

    return false;
}

function canUserListPostType($type)
{
    $permissions = getCurrentUserPermission("post.type");
    $actions = $permissions->where("action", "index")->where("type", $type)->where("component", "NULL");
    return 0 === count($actions);
}

function getUserOrderRole($role_key1, $role_key2)
{
    $state = false;

    $roles = getUserRoles("id");

    $role_id1 = $roles[$role_key1] ?? null;
    $role_id2 = $roles[$role_key2] ?? null;

    if (is_null($role_id1) || is_null($role_id2)) return $state;

    if ($role_id1 < $role_id2) {
        $state = 1;
    } else if ($role_id1 > $role_id2) {
        $state = -1;
    } else if ($role_id1 == $role_id2) {
        $state = 0;
    }
    return $state;
}

function getCurrentUserPermission($page = null)
{
    $user = getCurrentUser();
    $role = getTypeRole($user);

    if (!$role)
        return [];

    $current_user_permissions = getUserRoles("permissions")[$role];
    $list = parsePermissions($current_user_permissions);

    $list = $page ? $list[$page] ?? [] : $list;

    return collect($list);
}

function userEntity($entity_condition)
{
    $entity_key = getTypeRole(getCurrentUser()) ?: "";

    $theList = [];
    $list = getUserRoles();
    $i = 0;
    foreach ($list as $key => $item) {

        if ($key == $entity_key) {
            if ($entity_condition === "bottom_entity") {
                $theList = array_slice($list, $i + 1, null, true);
                break;
            } else if ($entity_condition === "same_entity") {
                $theList = [$key => $item];
                break;
            }
        }
        $i++;
    }

    return $theList;
}

function canShowLinkToUser(&$link)
{
    if (!$link) return true;

    $role_details = getCurrentUserRolesDetails();

    $accesses = $role_details['can_access'];
    $accesses = array_values($accesses);
    foreach ($accesses as $link_access) {

        $current_access_link = getRouteURI($link_access);

        if (isURLPatternTrue($current_access_link, $link)) {
            return true;
        }
    }


    return false;
}

function isInDashboardRoute($route)
{
    $list = getAllDashboardRoutes();
    return in_array($route, $list);
}

function getRouteInfo(string $route_name, array $args, string $export = "URL")
{
    $result = "";
    $export = strtoupper($export);

    if ($export == "URL") {
        $result = route($route_name, $args);
    } else if ($export == "URL_SCHEMA") {
        $result = getRouteURI($route_name);
    } else if ($export == "URL_PARAMETERS") {
        $result = getRouteParameterNames($route_name);
    }

    return $result;
}

function getRouteURI($route_name, $absolute = true)
{
    $uri = app('router')->getRoutes()->getByName($route_name)->uri;

    if (!$absolute) {
        $uri = ROOT_URL . "/" . $uri;
    }

    return $uri;
}

function getRouteParameterNames($route_name)
{
    $result = "";
    $uri = app('router')->getRoutes()->getByName($route_name);

    if (is_object($uri)) {
        $result = $uri->parameterNames();
    }

    return $result;
}

function getRouteByUrl($url)
{

    $result = "";

    try {
        $instanceRouteByUrl = request()->create($url);
        $result = app('router')->getRoutes()->match($instanceRouteByUrl)->getName();
    } catch (\Exception $e) {
        $result = "";
    }

    return $result;
}

function getAllDashboardRoutes($middlewareNameGroup = "dashboard", $key = "i", $value = "name")
{
    $middlewareNameGroup = $middlewareNameGroup ?? "dashboard";
    $key = $key ?? "i";
    $value = $value ?? "name";


    $theList = [];
    $list = Illuminate\Support\Facades\Route::getRoutes();
    $i = 0;
    foreach ($list as $item) {
        $middlewares = $item->middleware();
        if (in_array($middlewareNameGroup, $middlewares)) {
            $attr = [
                "i" => $i,
                "name" => $item->getName(),
                "uri" => $item->uri(),
                "item" => $item
            ];
            if (!in_array($attr['name'], $theList)) {
                $theList[$attr[$key]] = $attr[$value];
                $i++;
            }
        }
    }

    return $theList;
}

function isUserAccess($role_details)
{
    return in_array(getCurrentRouteName(), $role_details['can_access']);
}

function getUserRoles($export = "label")
{
    $list = [];

    $secondaryList = getUserRolesDetails("*");

    foreach ($secondaryList as $roleKey => $roleProps) {
        $list[$roleKey] = [
            "label" => $roleProps['label'],
            "permissions" => $roleProps['permissions']
        ];
    }


    $list = doModificationOnRoles($list);

    // add id (notice: lesser id greater Permission)
    $i = 1;
    array_walk($list, function (&$item) use (&$i) {
        $item['id'] = $i;
        $i++;
    });

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getUserRolesDetails($role)
{
    $list = [
        "super_admin" => [
            "label" => __local("Super Admin"),
            "can_access" => "all",
            "permissions" => [
                "user" => [
                    "create:same_entity:{\"own_id\":1}",
                    "create:bottom_entity",

                    "index:bottom_entity=delete",
                    "index:bottom_entity=status#active",
                    "index:bottom_entity=status#deactive",
                    "index:bottom_entity=status#deactive_block",

                    "index:same_entity=delete:{\"own_id\":1}",
                    "index:same_entity=status#active:{\"own_id\":1}",
                    "index:same_entity=status#deactive:{\"own_id\":1}",
                    "index:same_entity=status#deactive_block:{\"own_id\":1}",
                ],
                "post.type" => [
                    "create:type_entity?post=TRUE",
                    "create:type_entity?product=TRUE",
                    "create:type_entity?popup_ads=TRUE",
                    "create:type_entity?term=TRUE",

                    "edit:type_entity?post=TRUE",
                    "edit:type_entity?product=TRUE",
                    "edit:type_entity?popup_ads=TRUE",
                    "edit:type_entity?term=TRUE",

                    "index:type_entity?post=TRUE",
                    "index:type_entity?product=TRUE",
                    "index:type_entity?popup_ads=TRUE",
                    "index:type_entity?term=TRUE",

                    "delete:type_entity?post=TRUE",
                    "delete:type_entity?product=TRUE",
                    "delete:type_entity?popup_ads=TRUE",
                    "delete:type_entity?term=TRUE",
                ],
            ],
        ],

        "admin" => [
            "label" => __local("Admin"),
            "can_access" => [

                // post types
                "post.type.create.form",
                "post.type.create",
                "post.type.edit.form",
                "post.type.edit",
                "post.type.list",
                "post.type.list.filter",
                "post.type.destroy",

                // taxonomy
                "taxonomy.create.form",
                "taxonomy.create",
                "taxonomy.edit.form",
                "taxonomy.edit",
                "taxonomy.list",
                "taxonomy.list.filter",

                // file
                "file.create.form",
                "file.create",
                "file.list",
                "file.list.filter",

                // comment
                "comments.create",
                "comments.edit.form",
                "comments.edit",
                "comments.list",
                "comments.list.filter",

                // user
                "user.create.form",
                "user.create",
                "user.edit.form",
                "user.edit",
                "user.list",
                "user.list.filter",
                "user.destroy",
                "user.deactive.block",
                "user.active",

            ],
            "permissions" => [
                "user" => [
                    "create:bottom_entity",

                    "index:bottom_entity=status#deactive",
                    "index:bottom_entity=status#deactive_block",
                ],
                "post.type" => [
                    // sample
                    //"edit:type_entity?post=title,status,taxonomy[brand],taxonomy[category],meta^post_type_comment_switch",
                    //"edit:type_entity?term=NULL",
                    "edit:type_entity?product=TRUE",
                    "edit:type_entity?post=NULL",
                    "edit:type_entity?popup_ads=NULL",
                    "edit:type_entity?term=NULL",

                    "index:type_entity?post=NULL",
                    "index:type_entity?product=TRUE",
                    "index:type_entity?popup_ads=NULL",
                    "index:type_entity?term=NULL",
                ],
            ],

        ],

        "user" => [
            "label" => __local("User"),
            "can_access" => [
                "user.edit.form",
                "user.edit",
            ],
            "permissions" => [
                "user" => [],
                "post.type" => [
                    "edit:type_entity?product=NULL",
                    "edit:type_entity?post=NULL",
                    "edit:type_entity?popup_ads=NULL",
                    "edit:type_entity?term=NULL",

                    "index:type_entity?post=NULL",
                    "index:type_entity?product=NULL",
                    "index:type_entity?popup_ads=NULL",
                    "index:type_entity?term=NULL",
                ],
            ],
        ]
    ];

    if ($role != "*") {
        $action = $list[$role] ?? [];

        if (isset($action['can_access']) && $action['can_access'] == "all") {
            $action['can_access'] = getAllDashboardRoutes();
        }
    } else {
        $action = $list;
    }


    return $action ?? [];
}

function getCurrentUserRolesDetails()
{
    $user_role = getTypeRole(getCurrentUser());
    $current_user_role_details = getUserRolesDetails($user_role);
    return $current_user_role_details;
}

function getCurrentUserRolesDetailsCanAccess($access_name)
{
    $role_details = getCurrentUserRolesDetails();
    if (!$role_details) return false;

    return in_array($access_name, $role_details['can_access']);
}

function setCurrentUser($user_id)
{
    $user = App\Models\User::find($user_id);

    $result = null;

    if ($user) {
        $result = $user;
        $GLOBALS['user'] = $user;
    }


    return $result;
}

function emptyCurrentUser()
{
    $GLOBALS['user'] = null;
}

function getCurrentUser()
{
    $user = $GLOBALS['user'] ?? null;

    return $user;
}

function getCurrentUserAttr($attr, $default = "")
{
    $user = getCurrentUser();
    return getTypeAttr($user, $attr, $default);
}

function checkForCurrentAddressByRouteList($list, &$route = "")
{
    foreach ($list as $theRoute) {
        if (request()->routeIs($theRoute)) {
            $route = $theRoute;
            return true;
        }
    }

    return false;
}


function getListPreservedDashboard403()
{
    $list = [
        "newsletter.create",
        "user.signup.form",
        "user.create",
        "user.signin.form",
        "user.signin",
        "user.reset.password.form",
        "user.reset.password",
        "user.verify.client.form",
        "user.logout",
        "comments.create",
    ];

    return $list;
}

function checkCurrentAddressFor403()
{
    $preservedList = getListPreservedDashboard403();
    $isPreserved = checkForCurrentAddressByRouteList($preservedList);

    if (!$isPreserved) {
        abortByEntity($GLOBALS['user'], 403);
    }
}

function getListRedirectWhenLoggedIn()
{
    $list = [
        "user.signup.form",
        "user.signin.form",
        "user.signin",
        "user.reset.password.form",
        "user.reset.password",
        "user.verify.client.form",
    ];

    return $list;
}

function checkCurrentAddressForLoggedIn()
{
    $list = getListRedirectWhenLoggedIn();
    $route = "";
    $isOnPage = checkForCurrentAddressByRouteList($list, $route);

    if ($route) {
        $callback_route = str_replace(".", "_", $route);
        $callback_route .= "_redirect_check";
        if (is_callable($callback_route)) {
            $res = $callback_route($route);
            if (is_bool($res)) {
                $isOnPage = $res;
            }
        }
    }

    if ($isOnPage) {
        return redirect(getTypeEditLink(getCurrentUser(), "user", ["id"]));
    }
}

function isInPreservedDashobard403($route)
{
    return in_array($route, getListPreservedDashboard403());
}

function userActionsMiddleware()
{

    $sign_in_route_name = ["user", "signin.form"];
    $sign_in_route = getTheRoute($sign_in_route_name[0], $sign_in_route_name[1], []);

    do_action("dashboard_middle_ware_started");

    if ($GLOBALS['user']) {

        $current_user_role_details = getCurrentUserRolesDetails();

        $route_name = getCurrentRouteName();

        if (isInDashboardRoute($route_name)) {
            if (!isUserAccess($current_user_role_details) && !isInPreservedDashobard403($route_name)) {
                abort(403);
            }
        }

        $redirect_res = checkCurrentAddressForLoggedIn();
        if (is_object($redirect_res)) {
            return $redirect_res;
        }
    } else if (!$GLOBALS['user']) {

        checkCurrentAddressFor403();
    }

    // check for deactive or deactive_block
    if ($GLOBALS['user']) {
        $res = checkUserBlocked($GLOBALS['user'], null, $sign_in_route);
        if (is_object($res)) return $res;
    }
}

function getFieldOnChangeStatusOff()
{
    $type = getTypeValue(getOptionByKey("account_type_registeriation"));
    return $type;
}

function clientUpdateStatusOff($name, &$user, $status_off = "deactive")
{
    $must_be_verify_to_status_on = getFieldOnChangeStatusOff();

    if ($name != $must_be_verify_to_status_on) return false;

    $user['status'] = $status_off;

    return true;
}

function checkClientIDChangedNitche($first, $second, $key)
{
    if ($first != $second) {
        return $key;
    }

    return false;
}

function checkClientIDChanged($inputs, $user)
{
    $keys = array_keys($inputs);
    $clientUpdateList = getClientIDFields(null);

    foreach ($keys as $key) {
        if (in_array($key, $clientUpdateList)) {
            // $fields = getClientIDFields($key);
            $res = checkClientIDChangedNitche($inputs[$key], $user[$key], $key);
            if ($res) {
                return $res;
            }
        }
    }

    return false;
}

function checkUpdateClientIDON($inputs, $user)
{
    return checkUpdateClientIDOff($inputs, $user,  "active");
}

function checkUpdateClientIDOff($inputs, $user, $interface = null)
{

    $forceOff = $forceOff ?? false;
    $interface = $interface ?? null;

    $forceOff = getTypeStatus($user) == "deactive_block";

    $keys = array_keys($inputs);
    $clientUpdateList = getClientIDFields(null);

    foreach ($keys as $key) {
        if (in_array($key, $clientUpdateList)) {
            $changed = checkClientIDChangedNitche($inputs[$key], $user[$key], $key);
            if ($changed || $forceOff || $interface == "active") {
                $fields = getClientIDFields($key);
                $value = -1;
                foreach ($fields as $field) {

                    if (isset($field['can_be_null']) && $field['can_be_null']) {
                        $value = null;

                        if ($interface == "active" && $field['value'] == 'CURRENT_TIME_STAMP') {
                            $value = getOTPToUserFieldRawDynamic($field['value']);
                        }
                    } else if (is_bool($field['value'])) {
                        $value = !($field['value']);
                        if ($interface == "active") {
                            $value = !$value;
                        }
                    }
                    $user[$field['name']] = $value;
                }

                $user[$key] = $inputs[$key];
            }
        }
    }

    $user['status'] = $inputs['status'];
    $user->save();

    return $user;
}

function checkUserDeletePermission($user)
{
    $res = true;
    $res = canUserAction($user, "delete");
    if (!$res) {
        $res = triggerServerError(getUserMessageValidate(getMessageDeleteValue(), []));
        return $res;
    }

    return $res;
}

function checkUserStatusPermission($status)
{
    $res = true;
    $status_s = getCurrentUserStatusEntity_Create(request('id'), "dynamic");
    if (!array_key_exists($status, $status_s)) {
        $res = triggerServerError(getUserMessageValidate(getMessageInvalidValue(), []));
        return $res;
    }

    return $res;
}

function checkUserBlocked($user, $assign = "redirect", $redirectLocation = "/")
{
    $assign = $assign ?? "redirect";
    $redirectLocation = $redirectLocation ?? "/";

    if (getTypeStatus($user) == "deactive" || getTypeStatus($user) == "deactive_block") {

        $message = "";

        if (getTypeStatus($user) == "deactive") {
            $message = getUserDeactiveMessage($user);
        } else if (getTypeStatus($user) == "deactive_block") {
            $message = getUserBlockedMessage();
        }

        if (auth()->check()) {
            auth()->logout();
        }

        if ($assign == "redirect") {
            $theAssign = redirect($redirectLocation)->withErrors(restMessageEncode(getUserMessageValidate($message, [])));
        } else if ($assign == "back") {
            $theAssign = triggerServerError(getUserMessageValidate($message, []));
        }

        return $theAssign;
    }

    return false;
}

function getUserDeactiveMessage($user)
{
    $reason = "";
    $primaryField = getFieldOnChangeStatusOff();
    if (!getTypeVerified($user, $primaryField)) {
        $reason = messageShortNotVerified($primaryField);
    }
    return __local("Your Account is deactive : ") .  $reason;
}

function getUserBlockedMessage()
{
    return __local("for some reason your account Blocked by Admin");
}

function getMessageUserNotHavePermissionToAddUser()
{
    return __local("you don't have require permission to add new user");
}

function getNoticePasswordChange()
{
    return __local("if you want to update password enter your new password else don't edit it");
}

function getMessageUserNotHavePermissionToDoThisAction()
{
    return __local("you don't have require permission to to this Action");
}

function getMessageInvalidValue()
{
    return __local("invalid value for this action");
}

function getMessageDeleteValue()
{
    return __local("cannot delete this !");
}

function getRelatedUserLink($anchor)
{
    $list = [
        "Sign-up" => getTheRoute("user", 'signup.form', []),
        "Sign-in" => getTheRoute("user", 'signin.form', []),
        "Reset-Password" => getTheRoute("user", 'reset.password.form', ["via" => "email"]),
    ];

    return $list[$anchor] ?? "";
}

function getRelatedUserAnchors($anchors, $template = null)
{

    $template = $template ?? "<a class=\"text-decoration-none\" href=\"x-href\">x-anchor-text</a>";
    $str = "";

    if (!is_array($anchors)) {
        $anchors = [$anchors];
    }

    foreach ($anchors as $anchor) {
        if ($anchor == 'Reset-Password' && getAllType("account_type_registeriation") != "email") {
            continue;
        }

        $current_link = getRelatedUserLink($anchor);
        $str .= str_replace(["x-href", "x-anchor-text"], [$current_link, __local($anchor)], $template) . "<br>\n";
    }

    return $str;
}

function onConditionUserSignInLogout($template = null)
{
    if (auth()->check()) {
        include getDashboardViewPath("component/form/logout.form.blade.php");
    } else {
        echo getRelatedUserAnchors(["Sign-up", "Sign-in"], $template);
    }
}

function onConditionUserSignInLogoutList($template = null)
{
    if (auth()->check()) {
        include getDashboardViewPath("component/form/logout.form.blade.php");
        $labelEditProfile = __local("Edit Profile");
        $labelLogout = __local("Logout");
        $user = getCurrentUser();
        $user_fullname = getTypeFullname($user);
        $profileLink = getTypeEditLink($user, "user", ["id"]);
        echo "<a href=\"javascript:void(0)\" id=\"user-fullname-menu\" class=\"text-decoration-none\">{$user_fullname}<i class=\"fa fa-caret-down p-1\"></i></a>";
        echo "<div class=\"list-group position-absolute d-none user-list-menu s-shadow border br-primary\">
        <a href=\"{$profileLink}\" class=\"list-group-item list-group-item-action\">{$labelEditProfile}</a>
        <a href=\"javascript:void(0)\" id=\"logout-action\" data-target=\"#logout-form\" data-submit=\"true\" class=\"list-group-item list-group-item-action text-danger\">{$labelLogout}</a>
      </div>";
    } else {
        echo getRelatedUserAnchors(["Sign-in"], $template);
    }
}

function getCurrentUserProfileEdit()
{
    return getTypeEditLink(getCurrentUser(), "user", ["id"]);
}

function getUserClientIDList()
{
    $base = [
        "username",
    ];

    $account_type_registeriation = getAllType("account_type_registeriation");

    if ($account_type_registeriation == "email") {
        $base[] = "email";
    } else if ($account_type_registeriation == "phone") {
        $base[] = "phone";
    }

    return $base;
}

function getMinimumPassowrdCharacter()
{
    return strlen(typeRegexData('password_user')['sample']);
}

function getUserGravatarByEmail($email, int $size = 256, $rate = "g")
{

    if (is_null($email)) $email = "default";

    $gravatar = "";
    if ($email == "") return $gravatar;
    $tmp_email = md5(trim($email));
    $gravatar = "https://secure.gravatar.com/avatar/{$tmp_email}?s={$size}&d=mm&r={$rate}";
    return $gravatar;
}

function getUserDetailsByKeys($keys, $inputs = [], $inputKeys = [])
{
    $current_user = getCurrentUser();

    foreach ($keys as $i => $key) {
        $inputKey = isset($inputKeys[$i]) && !is_null($inputKeys[$i]) ? $inputKeys[$i] : $key;
        $inputs[$inputKey] = $current_user[$key];
    }

    return $inputs;
}

function getTableHeadUser($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "fullname",
                    "data-operator" => "like"
                ],
                "text" => __local("Fullname"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "email",
                    "data-operator" => "like"
                ],
                "text" => __local("Email"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "phone",
                    "data-operator" => "like"
                ],
                "text" => __local("Phone"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getUserRolesOption(), true),
                    "data-name" => "role",
                    "data-operator" => "or"
                ],
                "text" => __local("Role"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getStatusUserOption(), true),
                    "data-name" => "status",
                    "data-operator" => "or"
                ],
                "text" => __local("Status"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function getUserByX_Query(string $key, $value, $extraQueryCbk = null)
{

    $query = App\Models\User::where($key, $value);
    if (is_callable($extraQueryCbk)) {
        $query = $extraQueryCbk($query);
    }

    return $query;
}

function getUserByID_Query(int $id, $extraQueryCbk = null)
{

    $query = getUserByX_Query("id", $id, $extraQueryCbk);

    return $query;
}

function getUserByUsername_Query(string $username, $extraQueryCbk = null)
{

    $query = getUserByX_Query("username", $username, $extraQueryCbk);

    return $query;
}

function getDefaultThemeUser($key)
{
    $list = [
        "theme_color" => "#01AF37",
        "theme_color_hover" => "rgba(190, 250, 209, 0.8)",
    ];

    return $list[$key] ?? "";
}

function getRequiredFieldsCallbackTemplates($cbk)
{
    return "getRequiredFields_{$cbk}";
}

function getRequiredFields_phone()
{
    $userRequireDefaultFields = $GLOBALS['require_inputs']['user']['elements'];

    $itemsToRemove = ["password"];

    foreach ($itemsToRemove as $removeOne) {
        unset($userRequireDefaultFields[$removeOne]);
    }

    $list = [
        "elements" => [
            "phone" => ["regex:phone_user", "min:11", "max:11"],
        ]
    ];

    $list['elements'] =  array_merge($userRequireDefaultFields, $list['elements']);

    return $list;
}


function addRequireInputBasedOnLoginType($account_type_registeriation, $action, &$inputs, $user = null)
{
    if ($account_type_registeriation != "email") {
        $cbk_require_fields = getRequiredFieldsCallbackTemplates($account_type_registeriation);
        $require_list = $cbk_require_fields();
        removeRequireInputToJsonKeyMap("user", true);
        addRequireFieldToUserPageFromScratch($require_list['elements']);

        if ($account_type_registeriation == "phone") {
            $inputs['email'] = $action == "store" ? @$inputs['username'] . "_system" . "@" . $_SERVER['SERVER_NAME'] : getTypeAttr($user, "email");
            $inputs['password'] = getUniqueStr($account_type_registeriation);
        }
    } else {
        $inputs['phone'] = getUniqueStr("001_");
    }
}

function getUserActionButtons($key)
{

    $label = __local("User");

    $list = [
        "deactive_block" => createHtmlActionInputSetStatus("deactive_block", getStatusUser()['deactive_block'], $label, "yellow", true),
        "active" => createHtmlActionInputSetStatus("active", getStatusUser()['active'], $label, "green", true),
    ];

    $target = $list[$key] ?? "";

    return $target;
}

function getGroupActionUser()
{
    $groupActions = [
        "includes" => [
            [
                "actionName" => "Delete",
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Delete Selected Items ?"),
                "actionCbk" => "input#delete_action",
                "actionCbkHelper" => "deleteItemsList"
            ],


            [
                "actionName" => __local("Set Status") . " - " . __local("Deactive (Block)"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Deactive (Block) Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"deactive_block\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],

            [
                "actionName" => __local("Set Status") . " - " . __local("Active"),
                "actionType" => "entity",
                "actionAsk" => __local("Do You Want To Active Selected Items ?"),
                "actionCbk" => "input[data-id-form=\"#status-form\"][data-action=\"active\"]",
                "actionCbkHelper" => "setStatusItemsList"
            ],


        ],
    ];

    return $groupActions;
}

# ======> end User

# ======> Redirect

function getTableHeadRedirect($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "from",
                    "data-operator" => "like"
                ],
                "text" => __local("From Path"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "to",
                    "data-operator" => "like"
                ],
                "text" => __local("To Path"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getHttpCodeRedirectOption(), true),
                    "data-name" => "http_code",
                    "data-operator" => "or"
                ],
                "text" => __local("Status"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Created"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_updated_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Updated"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function getRedirectByFromPath($from_path)
{
    $rows = App\Models\Redirect::where("from", $from_path)->get();

    return $rows;
}

function redirectWithStatusCode($http_code, $location)
{
    http_response_code($http_code);
    header("Location: {$location}");
    exit;
}

function makeStarTemplateForRedirect($path_without_query_and_slash)
{
    $path_without_query_and_slash_parts = explode("/", $path_without_query_and_slash);

    if (1 < count($path_without_query_and_slash_parts)) {
        $path_without_query_and_slash_parts = array_slice($path_without_query_and_slash_parts, 0, count($path_without_query_and_slash_parts) - 1);
    }


    $createTemplate = function ($path_without_query_and_slash_parts) {
        $tempalte = "";
        foreach ($path_without_query_and_slash_parts as $itemKey => $itemValue) {
            $tempalte .= "{$itemValue}/";
        }
        $tempalte .= "*";

        return $tempalte;
    };

    $counter = 0;
    $templates = [];

    foreach ($path_without_query_and_slash_parts as $item) {
        $path_part_to_action = array_slice($path_without_query_and_slash_parts, 0, count($path_without_query_and_slash_parts) - $counter);

        $templates[] = $createTemplate($path_part_to_action);
        $counter++;
    }

    $rootTemplate = "/*";
    if (!in_array($rootTemplate, $templates)) {
        $templates[] = $rootTemplate;
    }

    return $templates;
}

function redirectionAction()
{

    if (!hasTable("redirects")) return;

    $http_code_response = http_response_code();
    $path_without_query_and_slash = request()->path();

    // detect redirect
    if (300 <= $http_code_response && $http_code_response < 400) {
        return;
    }

    $finalRedirect = null;

    // only redirect in front side
    if ($GLOBALS['isDashboard']) return;


    $redirect = getRedirectByFromPath($path_without_query_and_slash)->first();

    if (!$redirect) {
        // priority #SECOND and MORE
        $templates = makeStarTemplateForRedirect($path_without_query_and_slash);

        if ($templates && is_array($templates)) {
            foreach ($templates as $template) {
                $redirect = getRedirectByFromPath($template)->first();
                if ($redirect) {
                    $finalRedirect = $redirect;
                    break;
                }
            }
        }
    } else {
        // priority #FIRST
        $finalRedirect = $redirect;
    }

    if ($finalRedirect) {
        $targetLink = $finalRedirect['to'] == "/" ? $finalRedirect['to'] : "/{$finalRedirect['to']}";
        redirectWithStatusCode($finalRedirect['http_code'], $targetLink);
    }
}

# ======> end Redirect


# ======> History Action

function getTableHeadHistoryAction($thTagProp = [])
{

    if (!$thTagProp) {
        $thTagProp = [
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "id",
                    "data-operator" => "equal"
                ],
                "text" => __local("id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getHistoryActionModelsNameOption(-1, getHistoryActionModelsName("label")), true),
                    "data-name" => "model_type",
                    "data-operator" => "or"
                ],
                "text" => __local("Page Name"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "model_id",
                    "data-operator" => "equal"
                ],
                "text" => __local("Page id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "selectMultiple",
                    "data-values" => json_encode(getHistoryActionTheActionOption(), true),
                    "data-name" => "action",
                    "data-operator" => "or"
                ],
                "text" => __local("Action"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "description",
                    "data-operator" => "like"
                ],
                "text" => __local("Description"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "numberSeperator",
                    "data-name" => "by",
                    "data-operator" => "equal"
                ],
                "text" => __local("User id"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "text",
                    "data-name" => "by_raw",
                    "data-operator" => "like"
                ],
                "text" => __local("User Fullname"),
            ],
            [
                "attr" => [
                    "data-filter" => "",
                    "data-input" => "dateRange",
                    "data-options" => '{"timePicker": {"enabled": false}}',
                    "data-name" => "time_created_at",
                    "data-operator" => "range"
                ],
                "text" => __local("Date Action"),
            ],
            [
                "attr" => [],
                "text" => __local("Action"),
            ]
        ];
    }

    $str = getTableHeadTypeList($thTagProp);

    return $str;
}

function getHistoryActionModelLabel($item)
{
    $historyActionModels = getHistoryActionModelsName("*");
    $label = $historyActionModels[getTypeAttr($item, "model_type")]['label'];

    return $label;
}

function getHistoryActionModelAction($item)
{
    $historyActionActions = getHistoryActionTheAction("*");
    $label = $historyActionActions[getTypeAction($item)]['label'];

    return $label;
}

# =====> END History Action

# ======> Settings

function getCloneSettingsEmptyMessage($label)
{
    return "if you don't want {$label} leave element one empty and remove all other also can <b>click</b> on empty {$label}";
}

function getRequireSocialLinkMap()
{
    return [
        "title",
        "url",
        "icon",
    ];
}

function checkSocialLinks(string $json)
{

    $theArray = json_decode($json, true) ?? [];
    if (!$theArray) return triggerServerError(getUserMessageValidate(getMessageInvalidValue(), ["social_links"]));

    $requireFields = getRequireSocialLinkMap();
    $invalidFields = [];
    foreach ($theArray as $element) {
        foreach ($requireFields as $keymap_element) {
            if (!in_array($keymap_element, array_keys($element)) || $element[$keymap_element]["value"] == "") {
                $invalidFields[] = $keymap_element;
            }
            if (count($invalidFields) == count($requireFields)) break 2;
        }
    }

    // if has one element with full empty don't show error
    if (count($theArray) == 1 && count($invalidFields) == count($requireFields)) {
        $invalidFields = [];
    }

    if ($invalidFields) {
        return triggerServerError(getUserMessageValidate(getEmptyMessage(areOrIsByItems($invalidFields)) . " (Social Media)", $invalidFields));
    }

    return true;
}

function getSocialLinkByTitle($title)
{
    $data = getAllType("social_links");
    $data = collectionMapDepth2($data, function ($item) {
        return strtolower($item);
    });

    $element = $data->pluck("title")->where("value", strtolower($title))->first();
    $item = $data->where("title", $element)->first();

    if (!$item) return false;

    $theList = [];
    foreach ($item as $element_key => $element_value) {
        $theList[$element_key] = $element_value['value'];
    }

    return $theList;
}

function getSocialLinks()
{
    $theList = [];

    $data = getAllType("social_links");
    if (!count($data)) return $theList;

    foreach ($data as $item) {
        $tmpList = [];
        foreach ($item as $element_key => $element_value) {
            $tmpList[$element_key] = $element_value['value'];
        }
        $theList[] = $tmpList;
    }

    return $theList;
}

function getSocialLinksHTML($wrapperClass, $type = "img", $itemClass = "social-cls", $itemId = "social-x", $isBlanked = true, $icon_width = 20)
{

    $itemClass = $itemClass ?: "social-cls";
    $itemId = $itemId ?: "social-x";

    $str = "";

    $list = getSocialLinks();

    if (!$list || !$list[0]['title']) return $str;

    $blank = $isBlanked ? " target=\"_blank\"" : "";

    if (count($list)) {
        $str .= "<div class=\"social-media-wrapper $wrapperClass\">\n";
        foreach ($list as $index => $item) {
            $currentID = str_replace("x", ($index + 1), $itemId);
            $the_picture = $type == "img" ? "<img width=\"{$icon_width}\" src=\"{$item['icon']}\">" : file_get_contents(ROOT . $item['icon']);

            if ($type == "svg") {
                $the_picture = str_replace("<svg", "<svg width=\"{$icon_width}\" height=\"{$icon_width}\"", $the_picture);
            }

            $str .= "<a id=\"{$currentID}\" class=\"{$itemClass}\" title=\"{$item['title']}\" href=\"{$item['url']}\"{$blank}>{$the_picture}</a>";
        }
        $str .= "\n</div>";
    }

    return $str;
}

function getWebsiteLogo()
{
    return ROOT_URL . "/{$GLOBALS['favicon_url']}";
}

function getGroupKeyByKey($key, $default = "built_in_option")
{
    $default = $default ?: "built_in_option";
    $list = [
        /*
        # sample
        "currency" => "shop_option",
       */];

    $res = $list[$key] ?? $default;

    return $res;
}

function updateOptionByKey($key, $value, $default_group_key = null)
{
    $isUpdated = false;

    if (is_null($key)) return $isUpdated;

    $model = new App\Models\Option;

    $query = $model->where("key", $key);

    $option = $query->get()->first();

    // update
    if ($option) {
        $isUpdated = $option->update([
            "value" => $value
        ]);
    } // create
    else {
        $model->create([
            "key" => $key,
            "value" => $value,
            "group_key" => getGroupKeyByKey($key, $default_group_key),
        ]);

        $isUpdated = 2;
    }

    return $isUpdated;
}

function getOptionByKey($key = null, $group_key = null, $export = "toArray", $whichField = "*")
{
    $options = [];

    if (is_null($key) && is_null($group_key)) return $options;

    $listFields = [];

    if (isset($key)) {
        $listFields["key"] = $key;
    }

    if (isset($group_key)) {
        $listFields["group_key"] = $group_key;
    }

    if (!$listFields) return $options;

    $query = App\Models\Option::where("id", "!=", 0);

    foreach ($listFields as $item_key => $item) {
        $query->where($item_key, $item);
    }

    $options = $query->get();

    if (count($options) == 1) {
        $options = $options->first();
    }

    if ($export != "")
        $options = $options->$export();

    if ($whichField != "*") {
        $options = getTypeAttr($options, $whichField);
    }

    return $options;
}

function getOptionBuiltIn($option_group_keys = ["built_in_option" , GROUP_KEY_CUSTOM_1])
{
    $optionsRes = collect([]);

    foreach ($option_group_keys as $option_group_key) {
        $items = getOptionByKey(null, $option_group_key, "");

        if (!is_countable($items) || count($items) == 1) {
            $items = [$items];
        }

        $optionsRes = $optionsRes->merge($items);
    }

    return $optionsRes;
}

function getOptionBuiltInValueByKey($options, $key, $priority_old = false)
{
    $value = "";

    if ($priority_old && old($key, "")) {
        $value = old($key);
    } else if (!$priority_old || !old($key, "")) {
        $value = $options->where("key", $key)->first()['value'] ?? "";
    }

    return $value;
}

function checkFileExistsByMessage($inputs, $input_name)
{
    $fullpath_file = base_path($inputs[$input_name]);
    $isFileExists = file_exists($fullpath_file);

    if (!$isFileExists) {
        return triggerServerError(getUserMessageValidate(getMessageFileNotFound(" on this Server (x-field)"), [$input_name]));
    }

    return true;
}

function getFaviconSizes()
{
    return [
        [512, 512],
        [384, 384],
        [192, 192],
        [180, 180],
        [152, 152],
        [144, 144],
        [128, 128],
        [96, 96],
        [48, 48],
        [32, 32],
        [16, 16],
    ];
}

function getFaviconPath($absolute_path = false)
{
    $cbk = $absolute_path ? "strval" : "base_path";

    return [
        "favicon_url_default" => $cbk('public/static/images/favicon/default'),
        "favicon_url_current" => $cbk('public/static/images/favicon')
    ];
}

function getFaviconLink($size = 192)
{
    return request()->root() . "/" . getFaviconBySize($size, true);
}

function getFaviconBySize(int $width, $absolute_path = false, $prefix = "favicon", $type = "png")
{
    $path = getFaviconPath($absolute_path)["favicon_url_current"];
    // to fix file_exists
    $path_full = getFaviconPath(false)["favicon_url_current"];


    $filename = $path_full . SPE . "{$prefix}-{$width}.{$type}";
    $file_path = file_exists($filename) ? $path . "/{$prefix}-{$width}.{$type}" : "";

    return $file_path;
}

function getSiteManifestTemplate($data)
{
    $theData = $data;
    $theData['name'] = trim(getTypeExcerptLetter($theData['name'], 25));
    $theData['description'] = trim(getTypeExcerptLetter($theData['description'], 50));

    return [
        "name" => "{$theData['name']} - {$theData['description']}",
        "short_name" => "{$theData['name']}",
        "description" => "{$theData['description']}",
        "dir" => "{$theData['dir']}",
        "lang" => "{$theData['lang']}",
        "id" => "{$theData['id']}",
        "start_url" => "{$theData['start_url']}",
        "scope" => "{$theData['scope']}",
        "orientation" => "{$theData['orientation']}",
        "icons" => [
            [
                "src" => $theData['favicon_512'],
                "sizes" => "512x512",
                "type" => "image/png"
            ],

            [
                "src" => $theData['favicon_384'],
                "sizes" => "384x384",
                "type" => "image/png"
            ],

            [
                "src" => $theData['favicon_192'],
                "sizes" => "192x192",
                "type" => "image/png"
            ],

            [
                "src" => $theData['favicon_180'],
                "sizes" => "180x180",
                "type" => "image/png"
            ],

            [
                "src" => $theData['favicon_152'],
                "sizes" => "152x152",
                "type" => "image/png"
            ],

            [
                "src" => $theData['favicon_144'],
                "sizes" => "144x144",
                "type" => "image/png"
            ],

            [
                "src" => $theData['favicon_128'],
                "sizes" => "128x128",
                "type" => "image/png"
            ],

            [
                "src" => $theData['favicon_96'],
                "sizes" => "96x96",
                "type" => "image/png"
            ],

            [
                "src" => $theData['favicon_48'],
                "sizes" => "48x48",
                "type" => "image/png"
            ],
        ],
        "theme_color" => $theData['theme_color'],
        "background_color" => $theData['background_color'],
        "display" => "standalone"
    ];
}

function getSiteManifestTemplateURL($type = null)
{
    $url = getFaviconBySize(32, true);
    $basename = basename($url);
    $url = str_replace($basename, "site.webmanifest", $url);

    if ($type == "path") {
        $url = base_path($url);
    }

    return $url;
}

function addSiteManifestFile()
{
    $favicon_url_current = getFaviconPath()['favicon_url_current'];
    $manifest_content = json_encode(getSiteManifestTemplate([
        "name" => getTypeValue(getOptionByKey('site_name', null, "")),
        "description" => getTypeValue(getOptionByKey('site_description_raw', null, "")),
        "dir" => $GLOBALS['lang']['direction'] ?? "rtl",
        "lang" => $GLOBALS['site_language'],
        "id" => "rpd_core_1",
        "start_url" => defined("ROOT_URL") ? ROOT_URL : "/",
        "scope" => defined("ROOT_URL") ? ROOT_URL : "/",
        "orientation" => "portrait-primary",
        "favicon_512" => "/" . getFaviconBySize(512, true),
        "favicon_384" => "/" . getFaviconBySize(384, true),
        "favicon_192" => "/" . getFaviconBySize(192, true),
        "favicon_180" => "/" . getFaviconBySize(180, true),
        "favicon_152" => "/" . getFaviconBySize(152, true),
        "favicon_144" => "/" . getFaviconBySize(144, true),
        "favicon_128" => "/" . getFaviconBySize(128, true),
        "favicon_96" => "/" . getFaviconBySize(96, true),
        "favicon_48" => "/" . getFaviconBySize(48, true),
        "theme_color" => getTypeValue(getOptionByKey('theme_color', null, "")),
        "background_color" => "#ffffff",
    ]), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents($favicon_url_current . SPE . 'site.webmanifest', $manifest_content);
    return $manifest_content;
}

# ======> END Settings

# =====> OTP

function sendOTPToClient($user, $fromPage = "sign_up", $type = "verify_email", $via = "email", $data = null)
{
    $fromPage = $fromPage ?? "sign_up";
    $type = $type ?? "verify_email";
    $via = $via ?? "email";
    $data = $data ?? null;

    $user_id = getTypeID($user);

    $client_id = getTypeAttr($user, getOTPClientIDByType($type));

    if (!$client_id) {
        dd('client_id is empty');
    }

    $blockRes = blockOTPSentByExpireDate($via, $client_id);
    if ($blockRes) {
        return $blockRes;
    }

    $user_hash_id = cryptStringOneWayStr($user_id);
    $client_code = randomInteger(10001, 100001);
    $client_code_hex = dechex($client_code);

    $_type = $type;
    if ($fromPage == "sign_in") {
        $_type = "verify_phone_sign_in";
        $data = "JUST_LOGIN";

        if (request()->post("remember_me")) {
            $data .= "_REMEMBER";
        }
    }

    $otp = \App\Models\OTP::create([
        "type" => $_type,
        "via" => $via,
        "user_id" => $user_id,
        "user_hash_id" => $user_hash_id,
        "client_id" => $client_id,
        "client_code" => $client_code,
        "data" => $data,
        "expired_at" => getDateByUnixTime(null, addMinuteToSecondFromNowUnix(5)),
    ]);

    $parameters = ["user_hash_id" => $user_hash_id, "id" => getTypeID($otp)];
    $_parameters = $parameters;
    $parameters['action'] = "sign_in";

    $redirect = getTheRoute("user", "verify.client.form", $parameters);
    $_redirect = getTheRoute("user", "verify.client.form", $_parameters);

    // change link based on `account_type_registeriation`
    $the_redirect = $via == "email" ? $_redirect : $redirect;

    $userExtraData = $user;
    $userExtraData['client_code'] = $client_code;
    $userExtraData['type'] = $otp['type'];
    $userExtraData['verify_link'] = "{$the_redirect}/{$client_code_hex}";
    $userExtraData['new_password'] = $data;

    $content = getMailContentByName($fromPage, $userExtraData);

    $action = getOTPActionForVia($via);

    $action($client_id, $content, "User", $user_id);

    return $redirect;
}

function getCustomMessageFirstOTP($current_label)
{
    $custom_messages = [
        "phone" => __local("sms")
    ];

    $reuslt = getCustomMessageFirst($custom_messages, $current_label);

    return $reuslt;
}

function getOTPMessageForm($via, $otp_message, $expire)
{

    $the_via = getCustomMessageFirstOTP($via);

    return str_replace(["x-via", "x-otp-message", "x-seconds", "x-max-attempt"], [$the_via, $otp_message, "<time class=\"timer\" data-action=\"lesser\" data-on-end=\"reload\">{$expire}</time>", $GLOBALS['user_max_attempt']], __local('we sent you an x-via with activate code to x-otp-message it will expire at x-seconds seconds (after x-max-attempt times wrong code it will expire)'));
}

function getOTPByFields($model, $inputs, $cbk)
{
    foreach ($inputs as $the_item_key => $the_item_value) {
        $model = $model->where($the_item_key, $the_item_value);
    }

    $model = $model->get();
    $model = $model->first();

    if ($model) {
        $cbk($model);
    }
}

function getOTPToUserFieldRawDynamic($raw_name)
{
    $list = [
        "CURRENT_TIME_STAMP" => getDateByUnixTime(),
    ];

    $result = $list[$raw_name] ?? "";

    return $result;
}

function getOTPClientIDByType($type)
{
    $list = [
        "verify_email" => "email",
        "verify_phone" => "phone",

        "reset_password" => getAllType("account_type_registeriation"),
    ];

    $client_id = $list[$type] ?? "";

    return $client_id;
}

function OTPSendEmail($client_id, $content, $type, $type_id)
{
    $content['type'] = $type;
    $content['type_id'] = $type_id;
    Illuminate\Support\Facades\Mail::to($client_id)->send(new \App\Mail\EmailSys($content));
}

function OTPSendSMS($client_id, $content, $type, $type_id)
{
    $content['type'] = $type;
    $content['type_id'] = $type_id;

    // -------> GET RAW CONTENT <--------
    loadHTMLParser();
    $doc = str_get_html(create_simple_html_template($content['content']));
    $client_code = ($doc->find(".client_code")[0])->text();
    $links = $doc->find("a");
    if (!is_countable($links)) $links = [];

    foreach ($links as $link) {
        $link->__set("innertext", $link->getAttribute("href"));
    }

    $doc = str_get_html($doc->save());
    $raw_content = ($doc->find("body")[0])->text();

    // excerpt it
    $raw_content = substr($raw_content, 0, strpos($raw_content, $client_code) + strlen($client_code));
    // remove (double or more) newline
    $raw_content = preg_replace("/\n{2,}/i", "\n", $raw_content);
    // add site name
    $raw_content .= "\n" . getAllType("site_name");
    // -------> END GET RAW CONTENT <--------

    // if sms provider is differnet can add function to sms.php and change it here
    melipayamak_send_sms($client_id, $raw_content);

    addEmailLog($content);
}

function getOTPActionForViaList($justKeys = false)
{
    $list = [
        "email" => "OTPSendEmail",
        "phone" => "OTPSendSMS"
    ];

    return $justKeys ? array_keys($list) : $list;
}

function getOTPActionForVia($name)
{
    $list = getOTPActionForViaList();

    $action = $list[$name] ?? "";

    return $action;
}

function getClientIDFields($name = null)
{
    $list = [
        "email" => [
            [
                "name" => "email_verified",
                "value" => true,
            ],
            [
                "name" => "email_verified_at",
                "value" => "CURRENT_TIME_STAMP",
                "raw" => true,
                "can_be_null" => true
            ],
        ],

        "phone" => [
            [
                "name" => "phone_verified",
                "value" => true,
            ],
            [
                "name" => "phone_verified_at",
                "value" => "CURRENT_TIME_STAMP",
                "raw" => true,
                "can_be_null" => true
            ],
        ]
    ];

    $res = !$name ? array_keys($list) : $list[$name] ?? [];

    return $res;
}

function getClientIDLabels($key)
{
    $list = [
        "email" => "Email",
        "phone" => "Phone Number",
    ];

    return $list[$key] ?? "";
}

function getOTPToUserField($name)
{
    $account_type_registeriation = getAllType("account_type_registeriation");

    $list = [
        "verify_email" => [
            "elements" => getClientIDFields('email'),
            "key" => "email_verified"
        ],

        "verify_phone" => [
            "elements" => getClientIDFields('phone'),
            "key" => "phone_verified"
        ],

        "reset_password" => [
            "elements" => getClientIDFields($account_type_registeriation),
            "key" => $account_type_registeriation . "_verified"
        ],

    ];

    $list['verify_phone_sign_in'] = $list['verify_phone'];

    $field = $list[$name] ?? [];

    return $field;
}

function blockOTPSentByExpireDate($via, $user_email)
{
    $query = \App\Models\OTP::where("via", $via)->where("client_id", $user_email)->where("expired_at", ">", getDateByUnixTime())->latest("expired_at")->skip(0)->take(1);
    $row = $query->get();
    $row = $row->first();

    if ($row) {
        $expired_at = getTypeDateExpired($row);
        $expired_at_unix = dateToUnixTime($expired_at);
        $diff_unix = "<span class=\"\">" . diffUnixTwoDate($expired_at_unix) . "</span>";
        $label = getCustomMessageFirstOTP($via);;
        $html_content = str_replace(["x-label", "x-diff-unix"], [$label, $diff_unix], __local("We Already Sent You x-label You can Send Another Request at x-diff-unix Second/s"));
        return triggerServerError(getUserMessageValidate($html_content, []));
    }

    return false;
}

# =====> END OTP


# =====> Mail

function getMailFrom($prefix = "noreply@")
{
    return $prefix . getRootURL();
}

function getMailSubject($subject)
{
    return "{$GLOBALS['site_name']} : " . $subject;
}

function parseMailContent(string $content, $item)
{

    $list = [
        "x-fullname" => getTypeExcerpt(getTypeFullname($item), 1, true, 8),
        "x-new_password" => getTypeAttr($item, "new_password"),
        "x-client_code" => getTypeAttr($item, "client_code"),
        "x-verify_link" => getTypeAttr($item, "verify_link"),
    ];

    $dynamicContent = str_replace(array_keys($list), array_values($list), $content);

    return $dynamicContent;
}

function generateMailContent($part_name, $item)
{
    $account_type_registeriation = getAllType('account_type_registeriation');
    $content = view("mail.parts.{$part_name}_by_{$account_type_registeriation}")->render();
    return parseMailContent($content, $item);
}

function subject_from_sign_up()
{
    $account_type_registeriation = getAllType('account_type_registeriation');
    $label = __local("verify your {$account_type_registeriation}");

    return $label;
}

function subject_from_sign_in()
{
    $label = __local("enter code number to sign in");

    return $label;
}

function generateMailContent_sign_up($item)
{
    return generateMailContent("sign_up", $item);
}

function generateMailContent_sign_in($item)
{
    return generateMailContent("sign_in", $item);
}

function generateMailContent_reset_password($item)
{
    return generateMailContent("reset_password", $item);
}

function getMailContentByName($name, $item)
{
    $list = [
        "sign_up" => [
            "subject" => call_user_func("subject_from_sign_up"),
            "content" => ["generateMailContent_sign_up", [$item]]
        ],
        "sign_in" => [
            "subject" => call_user_func("subject_from_sign_in"),
            "content" => ["generateMailContent_sign_in", [$item]]
        ],
        "reset/password" => [
            "subject" => __local("reset your password"),
            "content" => ["generateMailContent_reset_password", [$item]]
        ],
    ];

    $action = $list[$name] ?? [];

    if ($action) {
        $action['content'] = call_user_func($action['content'][0], ...$action['content'][1]);
    }

    return $action;
}

# =====> END Mail


# ========> Security

function generateCaptcha()
{
    session_start();

    $builder = new Gregwar\Captcha\CaptchaBuilder();
    $builder->build();
    $_SESSION['antibotc'] = $builder->getPhrase();

    return $builder->inline();
}

function validateCaptcha()
{
    session_start();
    $captcha_code = request()->post('captcha-code');
    $state = false;

    if (!empty($captcha_code)) {
        if (!empty($_SESSION['antibotc']) && Gregwar\Captcha\PhraseBuilder::comparePhrases($_SESSION['antibotc'], $captcha_code)) {
            $state = true;
        }
    }

    return $state;
}

function isCaptchaDisabled($page, $part = null)
{

    $isDisabled = true;

    $current_page = $GLOBALS['captcha_disable'][$page] ?? null;

    if (is_null($current_page)) return $isDisabled;
    else {
        if ($part) {
            return $current_page[$part] ?? true;
        }
    }

    return $current_page;
}

function getMessageCaptchaInvalid()
{
    return __local("Invalid Captcha Code");
}


# ========> END Security


# ========> Search

function searchTheTerm($term)
{

    if ($term === "" || $term === null) return [];

    $postType_published_type = getPostTypePublishable();

    $term = "%{$term}%";
    $postTypeSearch = PostType::where(function ($query) use ($postType_published_type) {
        $query->where("status", "publish")->whereIn("type", $postType_published_type);
    });

    $postTypeSearch = $postTypeSearch->where(function ($query) use ($term) {
        $query->where("title", "LIKE", $term)->orWhere("body_raw", "LIKE", $term);
    });

    $postTypeSearch = $postTypeSearch->latest()->paginate(getAllType("type_per_page"));

    if (count($postTypeSearch)) {
        return $postTypeSearch;
    } else {
        return [];
    }
}

function getSearchNameRoute()
{
    return "front_end.search.list";
}

function getSearchBase()
{
    $uri = getRouteURI(getSearchNameRoute(), false);
    return str_replace("{term?}", "", $uri);
}

function getSearchTerm()
{
    $uri = getRouteURI(getSearchNameRoute(), true);

    $isSamePattern = getDiffBrackets($uri, request()->path());
    $term = $isSamePattern ? getPartOfUrl(-1) : "";

    if ($term) {
        $term = urldecode($term);
        $term = htmlentities($term);
    }

    return $term;
}

function searchForm()
{
    require getFrontViewPath("form/search_form.blade.php");
}

# ========> END Search


# ======> Cookie

function setCookie_Policy()
{

    $status = true;
    $expire = 0;

    $queryAction = request()->post("action");

    // allowed actions
    $allowed_actions = array_keys(getCookieActions());

    if (!$queryAction || !in_array($queryAction, $allowed_actions)) return false;


    if ($queryAction == "agree") {
        // remain 30 days
        $expire = time() + 86400 * 30;
    }

    setcookie("cookie_policy", $queryAction, $expire, "/", "", false, true);


    return $status;
}

function getPersistDataType()
{

    $persistDataType = @$_COOKIE['cookie_policy'];
    $isDisabledFromAdmin = isCookieDisabledFromAdmin();

    $type = null;

    if ($isDisabledFromAdmin || $persistDataType == "agree") {
        $type = "cookie";
    } else if ($persistDataType == "disagree" || !$persistDataType) {
        $type = "session";
    }

    return $type;
}

function isCookieDisabledFromAdmin($cookie_content = null)
{

    $cookie_content = $cookie_content ?: getOptionByKey("cookie_policy", null, null, "value");

    $result = $cookie_content == "<p>" . getEmptyStringSign() . "</p>";

    return $result;
}


# ======> END Cookie

# ======> SEO

function editRobots()
{
    $path_sample = base_path("public/robots-sample.txt");
    $path = base_path("public/robots.txt");
    $content = file_get_contents($path_sample);
    $content = str_replace(["ROOT_URL", ROOT_URL], ROOT_URL, $content);
    file_put_contents($path, $content);
    return true;
}

function fixStringAmpersand($str)
{
    $result = str_replace("&", "", $str);

    return $result;
}

function generateSitemap(array $types, int $per_page = 2)
{
    $sitemap = new \Melbahja\Seo\Sitemap(ROOT_URL);

    $sitemap_path = base_path("public/sitemap/");
    $sitemap_path = baseChangeSlashDirectory($sitemap_path);

    removeFiles($sitemap_path);

    $sitemap->setSavePath($sitemap_path);

    foreach ($types as $type) {
        foreach (getAllType($type['name']) as $typeItem) {
            if ($typeItem['status'] != "public") continue;

            $typeObject = new $type['class'];
            $itemList = $typeObject->where("status", "publish")->where("type", $typeItem['slug'])->get();

            $totalItem = count($itemList);
            $itemList = $itemList->chunk($per_page);

            $pagesItem = getPageCountByItem($totalItem, $per_page);
            for ($i = 0; $i < $pagesItem; $i++) {
                $currentNumber = $i + 1;
                $basename = "{$type['prefix']}-{$typeItem['slug']}-{$currentNumber}.xml";


                $current_itemList = $itemList[$i];

                $sitemap->links(['name' => $basename, 'images' => true], function ($map) use ($current_itemList, $type) {
                    foreach ($current_itemList as $item) {

                        $link = fixStringAmpersand(makeURLAbsoulte($type['callback_link']($item)));

                        $map->loc($link)
                            ->freq('daily')
                            ->lastMod(getTypeDateUpdated($item));

                        $thumbnail = $type['callback_thumbnail']($item);
                        if ($thumbnail) {
                            $map->image($thumbnail, ['caption' => fixStringAmpersand(getTypeTitle($item))]);
                        }
                    }
                });
            }
        }
    }


    $sitemap->save();
    editRobots();
}

function generateSitemapTypes()
{
    return generateSitemap([
        [
            "name" => "post_type",
            "class" => "App\Models\PostType",
            "prefix" => "post-type",
            "callback_link" => "getPostTypeLink",
            "callback_thumbnail" => "getPostTypeThumbnailURL",
        ],
        [
            "name" => "taxonomy",
            "class" => "App\Models\Taxonomy",
            "prefix" => "taxonomy",
            "callback_link" => "getTaxonomyLink",
            "callback_thumbnail" => "getTaxonomyThumbnailURL",
        ]
    ], 1000);
}

function getSeoMaxDescriptionLength()
{
    return 200;
}

function getSeoRobotDefault()
{
    return "index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1";
}

function getSeoMetaTags()
{
    if ($GLOBALS['isDashboard']) return false;

    $item = $GLOBALS['DB'] ?? [];

    // block is not set global DB
    if (!$item) return false;

    if (is_object($item))
        $class_name = get_class($item);
    else {
        $class_name = $item['fake_class'];
    }

    $class_name_lower = strtolower($class_name);

    $seoTypeListPublic = getSeoPublicTypeMeta("*");
    $currentElementSeo = $seoTypeListPublic[$class_name] ?? null;
    $isPublicTypeSeo = isset($currentElementSeo);

    // block if not public seo type
    if (!$isPublicTypeSeo) return false;

    $metatags = new Melbahja\Seo\MetaTags();

    $title = getCurrentTitlePage();
    $description = isset($item['body_raw']) ? mb_substr($item['body_raw'], 0, getSeoMaxDescriptionLength()) : "";
    $current_url = pathToURL(request()->path());
    $current_image = !empty($item['thumbnail_url']) ? pathToURL($item['thumbnail_url']) : "";


    $unix_created = isset($item['created_at']) ? dateToUnixTime($item['created_at']) : "";
    $unix_updated = isset($item['updated_at']) ? dateToUnixTime($item['updated_at']) : "";

    if ($unix_created)
        $atom_created = date(getFormatDateAtom(), $unix_created);
    if ($unix_updated)
        $atom_updated = date(getFormatDateAtom(), $unix_updated);

    $ogType = ($class_name_lower != "home") ? "article" : "website";


    $robot_index = $currentElementSeo['robot_index'];

    if ($robot_index == "*") $robot_index = getSeoRobotDefault();

    $metatags
        ->robots($robot_index)
        ->url($current_url)
        ->og("title", $title)
        ->og("type", $ogType)
        ->og("locale", $GLOBALS['site_language'])
        ->og("site_name", $GLOBALS['site_name']);



    if (!is_int(strpos($currentElementSeo['robot_index'], "noindex"))) {
        $metatags->canonical($current_url);
    }


    if ($description) {
        $metatags->description($description);
    }


    if ($current_image) {
        $metatags->image($current_image);
    }

    if ($currentElementSeo['published_by']) {
        // be careful $item must registered for HistoryAction
        $fullname = getPostTypeAuthor($item, "create", "fullname");
        $metatags->twitter("label1", "by");
        $metatags->twitter("data1", $fullname);
    }

    if ($currentElementSeo['date_publish'] && isset($atom_created))
        $metatags->meta("article:published_time", $atom_created);

    if ($currentElementSeo['date_modify'] && isset($atom_updated))
        $metatags->meta("article:modified_time", $atom_updated);


    $tags = explode("\n", $metatags->__toString());
    if ($tags[0] == "") {
        unset($tags[0]);
    }

    $tags = join("\n", $tags);


    return $tags;
}

function getSeoSchema()
{
    if ($GLOBALS['isDashboard']) return false;

    $item = $GLOBALS['DB'] ?? [];

    // block is not set global DB
    if (!$item) return false;

    if (is_object($item))
        $class_name = get_class($item);
    else {
        $class_name = $item['fake_class'];
    }

    $class_name_lower = strtolower($class_name);

    $seoTypeListPublic = getSeoPublicTypeSchema("*");
    $currentElementSeo = $seoTypeListPublic[$class_name] ?? null;
    $isPublicTypeSeo = isset($currentElementSeo);

    // block if not public seo type
    if (!$isPublicTypeSeo) return false;

    if (!is_callable($currentElementSeo['callback'])) return false;

    return setVariableJavascript(null, $currentElementSeo['callback']($item), "application/ld+json", "");
}

function getCurrentTitlePageDashboard($item, $current_title_args, $seperator_main)
{
    $seperator_prefix = "|";
    $prefix = __local(ucfirst(getPartOfUrl(0)));

    $strList = [$prefix, " ", $seperator_prefix, " ", $current_title_args['label']];

    if ($current_title_args['x-type']) {
        $typeLabel = getPartOfUrl(-2);

        if ($item['extraData']) {
            $typeLabel = $item['extraData']['typeLabel'];
        }

        $strList[] = [" ", $typeLabel];
    }


    $strList[] = [" ", $seperator_main, " "];

    $action = getPartOfUrl(-1);

    if (!isset(getTitlePageAction()[$action])) {
        $action = getPartOfUrl(-2);
    }

    if (!isset(getTitlePageAction()[$action])) {
        $action = getPartOfUrl(-3);
    }

    if (!isset(getTitlePageAction()[$action])) {
        $action = getPartOfUrl(-4);
    }

    $actionLabel = getTitlePageAction()[$action] ?? "";
    $actionLabel = __local($actionLabel);


    $strList[] = $actionLabel;

    if ($action == "edit") {
        $the_title = getTypeFullname($item, false) ? getTypeFullname($item, false) : getTypeTitle($item, false);

        $the_title = getTypeExcerpt($the_title, 4, false);

        $strList[] = [" ", $the_title];
    }

    return $strList;
}

function getCurrentTitlePageFrontEnd($item, $seperator_main)
{

    $the_title = getTypeTitle($item, false);
    if (!$the_title && $item['title_seo']) {
        $the_title = $item['title_seo'];
    }

    if (is_object($item) && class_basename(get_class($item)) == "Taxonomy") {
        $info = checkTaxonomy(getTypee($item));
        $the_title = "{$info['current_taxonomy_info']['label']} {$the_title}";
    }

    $strList[] = [$the_title, " ", $seperator_main, " ", getAllType("site_name")];

    return $strList;
}

function getCurrentTitlePage()
{
    $strList = [];
    $seperator_main = "-";

    if (!isset($GLOBALS["DB"])) return "";

    $item = $GLOBALS["DB"];

    if ($item['fake_class']) {
        $class_name = $item['fake_class'];
    } else {
        $class_name = get_class($item);
    }

    $class_name_base = class_basename($class_name);

    $titlePages = getTitlePage("*");

    $current_title_args = $titlePages[$class_name_base];

    if (!empty($current_title_args['template'])) {
        $item['title'] = str_replace("x-title", $item['title'] ?? "", $current_title_args['label']);
    }


    if ($GLOBALS['isDashboard']) {
        // in dashboard
        $strList = getCurrentTitlePageDashboard($item, $current_title_args, $seperator_main);
    } else if (!$GLOBALS['isDashboard']) {
        // in front end
        $strList = getCurrentTitlePageFrontEnd($item, $seperator_main);
    }

    $strList = simpleArrayMerger($strList);
    $str = join("", $strList);
    $str = trim(getTypeExcerptLetter($str, 70));

    if (!empty($GLOBALS['title_overwrite']))
        $str = $GLOBALS['title_overwrite'];

    return $str;
}

function getSchemaSeoPostType($item)
{
    $user = getPostTypeAuthor($item, "create");

    $list = [
        "title" => getCurrentTitlePage(),
        "site_description_ideal" => getTypeExcerptLetter(getTypeBodyRaw($item, ""), getSeoMaxDescriptionLength()),

        "url" => getPostTypeLink($item),
        "post_title" => getTypeTitle($item),
        "post_date_published" => date(getFormatDateAtom(), dateToUnixTime(getTypeDateCreated($item))),
        "post_date_modified" => date(getFormatDateAtom(), dateToUnixTime(getTypeDateUpdated($item))),
    ];

    if (getPostTypeThumbnailURL($item)) {
        $image_url = getPostTypeThumbnailURL($item);
        $image_info = @getimagesize($image_url);

        if ($image_info) {
            $list['thumb_url'] = $image_url;
            list($list['thumb_width'], $list['thumb_height']) = $image_info;
        }
    }

    if ($user) {
        $list['author_url'] = getUserPageFrontEnd(getTypeID($user));
        $list['author_name'] = getTypeFullname($user);
        $list['author_thumb_url'] = getUserGravatarByEmail(getTypeEmail($user));
    }

    $list = SchemaSeoCreator::getThingPostSingle($list);

    return $list;
}

function getSchemaSeoTaxonomy($item)
{

    return SchemaSeoCreator::getThingTaxonomy([
        "title" => getCurrentTitlePage(),
        "site_description_ideal" => getTypeExcerptLetter(getTypeBodyRaw($item, ""), getSeoMaxDescriptionLength()),
        "url" => getTaxonomyLink($item),
        "taxonomy_name" => getTypeTitle($item, " "),
    ]);
}

function getSchemaSeoUser($item)
{

    $list = [];

    if ($item) {
        $list['author_url'] = getUserPageFrontEnd(getTypeID($item));
        $list['author_name'] = getTypeFullname($item);
        $list['author_thumb_url'] = getUserGravatarByEmail(getTypeEmail($item));
    }

    return SchemaSeoCreator::getThingAuthor($list);
}

function getSchemaSeoHome($item)
{
    return SchemaSeoCreator::getThingOrganization([
        "title" => getCurrentTitlePage(),
        "site_description_ideal" => getTypeExcerptLetter($GLOBALS['site_description_raw'], getSeoMaxDescriptionLength()),
    ]);
}

function getSchemaSeoSearch()
{
    $search_term = getSearchTerm();
    return SchemaSeoCreator::getThingSearchResult([

        "title" => getCurrentTitlePage(),
        "search_url" => ROOT_URL .  "/search/" . urlencode($search_term),
        "search_title" => __local("you searched for ") . $search_term,

    ]);
}

# ======> END SEO
