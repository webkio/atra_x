<?php

function loadHTMLParser()
{
    if (!function_exists('file_get_html')) {
        require_once "lib/simple_html_dom.php";
    }
}

function loadJDF()
{
    if (!function_exists('jmktime')) require_once "lib/jdf.php";
}

function logAndDie($data)
{
    logger($data);
    die();
}

function headRequest($url)
{
    $client = new \GuzzleHttp\Client();
    $response = $client->head($url);
    $headers = $response->getHeaders();

    return $headers;
}

function getModelBasePath($class_name)
{
    $path = "App\\Models\\{$class_name}";

    return $path;
}

function instanceModelClass($class_name)
{
    $class_str = getModelBasePath($class_name);
    $model = new $class_str;
    return $model;
}

function addExtraAttr($originalList, $attrList)
{
    foreach ($originalList as $key => $item) {
        if (!isset($attrList[$key])) {
            continue;
        };

        $element = $attrList[$key];
        foreach ($element as $sub_key => $sub_element) {
            $originalList[$key][$sub_key] = $sub_element;
        }
    }

    return $originalList;
}

function getLinkVoid()
{
    return "javascript:void(0)";
}

function addKeyByDefinedKey(&$item)
{
    foreach ($item as $key => &$item) {
        $item['key'] = $key;
    }
}

function concatDateAndTime($date, $time, $concator = " - ")
{
    $date = __local($date);
    return "{$date}{$concator}{$time}";
}

function addFragmentCommand($url, $commandKey, $commandValue)
{
    $template = "cfrag_x-command";

    $parsed_url = parse_url($url);
    $queryMark = "?";

    if (isset($parsed_url['query'])) {
        $queryMark = "&";
    }

    // sample => $url=https://rapdicode.ir , $queryMark=?|& , $dynamic cfrag_showContactUs=1 -> https://rapdicode.ir?cfrag_showContactUs=1

    $command = $url . $queryMark . str_replace("x-command", $commandKey, $template) . "={$commandValue}";
    return $command;
}

function dateToTimestamp($date, $format = "Y-m-d H:i:s")
{
    loadJDF();

    $parsed_date = date_parse_from_format($format, $date);
    $timestamp = jmktime($parsed_date['hour'], $parsed_date['minute'], $parsed_date['second'], $parsed_date['month'], $parsed_date['day'], $parsed_date['year']);

    return $timestamp;
}

function sumTimes($times)
{

    if (!$times) return null;

    if (!is_array($times)) $times = [$times];

    $basePrefixTime = "00";
    $totalSeconds = 0;

    $convertToSeconds = function ($h, $m, $s) {
        $seconds = $h * 3600 + $m * 60 + $s;
        return $seconds;
    };

    $convertToTime = function ($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(floor($seconds / 60) % 60);
        $seconds = floor($seconds % 60);

        if ($hours < 10) $hours = "0{$hours}";
        if ($minutes < 10) $minutes = "0{$minutes}";
        if ($seconds < 10) $seconds = "0{$seconds}";

        return "$hours:$minutes:$seconds";
    };

    foreach ($times as $time) {

        if (!$time) continue;

        $timeList = explode(":", $time);

        // polyfill hours:seconds
        if (!count($timeList)) continue;
        else if (3 < count($timeList)) $timeList = array_slice($timeList, 0, 3);
        else if (count($timeList) < 3) {
            $polyfill = array_fill(0, 3 - count($timeList), $basePrefixTime);
            $timeList = array_merge($polyfill, $timeList);
        }

        // cast integer
        $timeList = array_map(function ($element) {
            return intval($element);
        }, $timeList);

        $seconds = $convertToSeconds($timeList[0], $timeList[1], $timeList[2]);

        $totalSeconds += $seconds;
    }

    $result = $convertToTime($totalSeconds);

    return $result;
}

function removePartOfTime($time, $index = [], $seperator = ":")
{
    $seperator = $seperator ?: ":";

    $_time = $time;

    if (!$index) return $_time;

    $_timeList = explode($seperator, $_time);

    for ($i = 0; $i < count($index); $i++) {
        $currentIndex = $index[$i] ?? false;

        if (!$currentIndex) continue;

        unset($_timeList[$currentIndex]);
    }

    if ($_timeList)
        $_time = join($seperator, $_timeList);

    return $_time;
}

function isTimeInRange($time, $a_time, $b_time, $includeEqual = true)
{
    $time = @date_create($time);
    $a_time = @date_create($a_time);
    $b_time = @date_create($b_time);

    if (!$time || !$a_time || !$b_time) return false;

    $time = $time->format("H:i:s");
    $a_time = $a_time->format("H:i:s");
    $b_time = $b_time->format("H:i:s");

    return $includeEqual ? ($a_time <= $time && $time <= $b_time) : ($a_time < $time && $time < $b_time);
}

function getClosestRangeTime($time, $list)
{
    $result = null;

    $unixTime = strtotime($time);

    if ($unixTime === false) return $result;

    $closestDiff = null;

    foreach ($list as $index => $item) {

        $startTime = strtotime($item["start"]);
        $endTime = strtotime($item["end"]);

        $next_item = $list[$index + 1] ?? false;

        if ($next_item) {
            $next_startTime = strtotime($next_item['start']);
            $next_endTime = strtotime($next_item['end']);
        }

        $currentTime = $unixTime;

        // Check if the target time is within the range
        if ($currentTime >= $startTime && $currentTime <= $endTime) {
            // The target time is within the current range, so return it
            $result = $item["start"];

            if (!empty($next_item) && $currentTime >= $next_startTime && $currentTime <= $next_endTime) {

                $result = $next_item["start"];
            }

            break;
        }

        // Calculate the difference between the target time and the range start/end times
        $startDiff = abs($currentTime - $startTime);
        $endDiff = abs($currentTime - $endTime);

        // Check if the current range has a closer start time or end time to the target time
        if ($closestDiff === null || $startDiff < $closestDiff || $endDiff < $closestDiff) {
            $result = $item["start"];
            $closestDiff = min($startDiff, $endDiff);
        }
    }

    return $result;
}

function getFormatDateAtom()
{
    return "Y-m-d\TH:i:sP";
}

function getMessageExpireCounter($ultimatum, $text = null)
{
    $text = $text ?: "Page will expire after x-seconds second/s";

    $str = __local($text);
    $str = str_replace("x-seconds", "<b class=\"timer text-danger\" data-action=\"lesser\" data-on-end=\"reload\">{$ultimatum}</b>", $str);
    return $str;
}

function getFullNamespaceByModel($model_name, $method = "")
{
    $tmpMethod = $method ? "::{$method}" : "";
    $template = "App\\Models\\{$model_name}{$tmpMethod}";
    return $template;
}

function timestampToFaDate($timestamp, $format = "Y-m-d H:i:s", $digits = "en", $none = '', $timezone = 'Asia/Tehran')
{
    loadJDF();

    return jdate($format, $timestamp, $none, $timezone, $digits);
}

function getPersianDate($date, $export = "DATE_TIME", $digits = "fa")
{
    $_result = "";
    $result = "";
    $seperator_date_time = " ";

    $theDate = is_string($date) ? date_create($date) : $date;

    $_result = timestampToFaDate($theDate->format("U"), "Y/m/d H:i:s", $digits);
    $_result = explode($seperator_date_time, $_result);

    if (count($_result) != 2) return $result;

    if ($export == "DATE_TIME") {
        $result = $_result[0] . $seperator_date_time . $_result[1];
    } else if ($export == "DATE") {
        $result = $_result[0];
    } else if ($export == "TIME") {
        $result = $_result[1];
    }

    return $result;
}

function dateToUnixTime($date)
{
    if (is_numeric($date)) {
        $date = getDateByUnixTime(null, $date);
    }

    if (empty($date)) return "";

    $date = date_create($date);
    return intval(date_format($date, "U"));
}

function mainIntervalDate($date, $format, $action = "add")
{
    $result = null;

    if (!$date) return $result;

    $interval = new DateInterval($format);

    $date->$action($interval);

    $result = $date;

    return $result;
}

function addIntervalDay($date, int $days)
{
    $result = mainIntervalDate($date, "P{$days}D", "add");
    return $result;
}

function subIntervalDay($date, int $days)
{
    $result = mainIntervalDate($date, "P{$days}D", "sub");
    return $result;
}

function addIntervalTime($date, array $timeItems)
{
    $hours = $timeItems[0] ?? 0;
    $minutes = $timeItems[1] ?? 0;
    $seconds = $timeItems[2] ?? 0;

    $result = mainIntervalDate($date, "PT{$hours}H{$minutes}M{$seconds}S", "add");
    return $result;
}

function subIntervalTime($date, array $timeItems)
{
    $hours = $timeItems[0] ?? 0;
    $minutes = $timeItems[1] ?? 0;
    $seconds = $timeItems[2] ?? 0;

    $result = mainIntervalDate($date, "PT{$hours}H{$minutes}M{$seconds}S", "sub");
    return $result;
}

function rot13_64_encode($str)
{
    $encoded = base64_encode(str_rot13($str));
    return $encoded;
}

function rot13_64_decode($str)
{
    $decoded = str_rot13(base64_decode($str));

    return $decoded;
}

function hasChanceToAction($item, $currentTimestamp = null, $compare = null, $columnCbk = null, $extraTime = null, $operation = null)
{
    $currentTimestamp = $currentTimestamp ?? time();
    $compare = $compare ?? "less";
    $columnCbk = $columnCbk ?? "getTypeDateCreated";
    $extraTime = $extraTime ?? 0;
    $operation = $operation ?? "+";

    $theRowTime = $columnCbk($item);

    $targetTimestamp = $operation == "+" ? (dateToUnixTime($theRowTime) + $extraTime) : (dateToUnixTime($theRowTime) - $extraTime);

    if ($compare == "less") {
        $result = $currentTimestamp <= $targetTimestamp;
    } else if ($compare == "great") {
        $result = $targetTimestamp <= $currentTimestamp;
    }

    return $result;
}

function getRecentlyUnix($from = 3600, $to = 7200)
{
    return time() - randomInteger($from, $to);
}

function diffUnixTwoDate(int $to, $from = null)
{
    $from = $from ?? time();
    return $to - $from;
}

function getDateByUnixTime($format = "Y-m-d H:i:s", $unix = null)
{
    $format = $format ?? $GLOBALS['dateFormat'];
    $unix = $unix ?? time();

    return date($format, $unix);
}

function getUnixTimeByDate($date)
{
    if (!is_a($date, "DateTime")) {
        $date = @date_create($date);
    }

    if (!$date) return false;

    return $date->format("U");
}

function getMicroTime($includeTimestamp = true)
{
    $time = microtime(true);
    $result = $includeTimestamp ? $time : explode(".", $time)[1];
    return $result;
}

function getMiliTime()
{
    $microtime = getMicroTime();

    $seconds = floor($microtime);
    $microseconds = ($microtime - $seconds) * 1000000;

    $milliseconds = round(($seconds * 1000) + ($microseconds / 1000));

    return $milliseconds;
}

function addMinuteToSecondFromNowUnix($Minutes = 0)
{
    $the_seconds = $Minutes * 60;
    $the_unix = time() + $the_seconds;
    return $the_unix;
}

function getFirstAndLastYearRange(DateTime $date = null)
{

    $date = $date ?? new DateTime();

    $dateYear = $date->format("Y");

    $maxDateLastMonth = date_create("{$dateYear}-12-01")->format("t");

    $from = date_create("{$dateYear}-01-01");
    $to = date_create("{$dateYear}-12-{$maxDateLastMonth} 23:59:59");

    return [
        "from" => $from->format($GLOBALS['dateFormat']),
        "to" => $to->format($GLOBALS['dateFormat']),
    ];
}

function generateYearList($from, $to)
{
    $list = [];

    for (; $from <= $to; $from++) {
        $list[$from] = $from;
    }

    return $list;
}

function getFirstAndLastMonthRange(DateTime $date = null)
{

    $date = $date ?? new DateTime();

    $maxDateMonth = $date->format("t");

    $dateYear = $date->format("Y");
    $dateMonth = $date->format("n");

    $from = date_create("{$dateYear}-{$dateMonth}-01");
    $to = date_create("{$dateYear}-{$dateMonth}-{$maxDateMonth} 23:59:59");

    return [
        "from" => $from->format($GLOBALS['dateFormat']),
        "to" => $to->format($GLOBALS['dateFormat']),
    ];
}

function getFirstAndLastDayRange(DateTime $date = null)
{

    $date = $date ?? new DateTime();

    $dateYear = $date->format("Y");
    $dateMonth = $date->format("n");
    $dateDate = $date->format("j");

    $todayDate = "{$dateYear}-{$dateMonth}-{$dateDate}";
    $from = date_create($todayDate);
    $to = date_create("{$todayDate} 23:59:59");

    return [
        "from" => $from->format($GLOBALS['dateFormat']),
        "to" => $to->format($GLOBALS['dateFormat']),
    ];
}

function getDiffDaysFirstAndLast($plusFuture = true)
{
    $justDateFormat = "Y-m-d";

    $day = date("l");
    $dateToday = date($justDateFormat);

    $days = getDaysofWeekBase(-1);
    $result = [];

    $firstDayDiff = -2;
    $lastDayDiff = -1;

    $maxIndex = 6;

    $res = array_search($day, $days);
    if (!is_int($res)) return $result;

    $firstDayDiff = $res;


    if ($res < $maxIndex) {
        $lastDayDiff = $maxIndex - $res;
    } else if ($res === $maxIndex) {
        $lastDayDiff = 0;
    }

    // to fix range mysql
    if ($plusFuture) {
        $lastDayDiff++;
    }

    // date past
    $interval = new DateInterval("P{$firstDayDiff}D");
    $date = new DateTime($dateToday);
    $date->sub($interval);
    $datePast = $date->format($justDateFormat);


    // date future
    $interval = new DateInterval("P{$lastDayDiff}D");
    $date = new DateTime($dateToday);
    $date->add($interval);
    $dateFuture = $date->format($justDateFormat);

    return [
        "current" => $dateToday,
        "past" => $datePast,
        "future" => $dateFuture,
    ];
}

function getDaysofWeekBase($index = 0)
{
    $list = [
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday",
    ];

    return $list[$index] ?? $list;
}

function getDaysofWeekBaseLabel($label)
{

    $list = getDaysofWeekBase(-1);
    $result = array_search(strtolower($label), array_map('strtolower', $list));

    return is_bool($result) ? null : $result;
}

function round2Precision($number, $precision = 2, $seperator = ".")
{
    if ($precision <= 0) $precision = 1;

    $str_number = strval(floatval($number));

    if (!is_int(stripos($str_number, $seperator))) {
        $str_number .= "{$seperator}." . "0";
    }

    $str_number_list = explode($seperator, $str_number);
    if (count($str_number_list) <= 1) return 3;

    $leftSide = $str_number_list[0];
    $rightSide = $str_number_list[1];

    $count_rightSide = strlen($rightSide);

    if ($count_rightSide < $precision) {
        $remain = $precision - $count_rightSide;
        $rightSide .= str_repeat("0", $remain);
    }

    $rightSide = substr($rightSide, 0, $precision);

    $result = $leftSide . $seperator . $rightSide;

    return $result;
}

function numToPercent($piece, $all, $precision = 2)
{
    return round2Precision($piece / $all * 100, $precision);
}

function percentToNum($percent, $all, $precision = 2)
{
    return round2Precision($all / 100 * $percent, $precision);
}

function convertByteToMB($byte, string $stuffix = "", int $round = 5)
{
    if (!$byte) return 0;
    return round($byte / 1000000, $round) . $stuffix;
}

function arrayToLowerCase($array, $justKeys = false)
{
    $theArray = $array;

    if ($justKeys) $theArray = array_keys($array);

    $theArray = array_map('strtolower', $theArray);

    return $theArray;
}

function getBaseComponentName($str)
{
    return "component-{$str}";
}

function onConditionTag($value, $condition, $trueState, $falseState, $ananymousFormat = "x-tag")
{
    $str = "";
    $state = "";
    if ($condition) {
        $state = $trueState;
    } else {
        $state = $falseState;
    }
    $str .= str_replace($ananymousFormat, __local($value), $state);
    return $str;
}

function getNoImageSrc()
{
    $result = '/static/images/no.image.png';

    return $result;
}

function file_upload_max_size()
{
    static $max_size = -1;

    if ($max_size < 0) {
        $post_max_size = parse_size_file(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        $upload_max = parse_size_file(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

function parse_size_file($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

function getMaxUploadSizeMB()
{
    return convertByteToMB(file_upload_max_size(), "<span class=\"mx-2\">MB</span>", 2);
}

function pathToURL($path)
{
    $tmpPath = $path == "/" ? "" : $path;
    $tmpPath = slashFixerPath($tmpPath);

    $url_location = request()->root() . "/" . $tmpPath;
    $url_location = str_replace("\\", "/", $url_location);
    return $url_location;
}

function nestedLoopArray($objectList, $callback)
{
    if (is_array($objectList)) {
        $elements = $objectList;
        foreach ($elements as $key_element => $element) {
            if ($GLOBALS['loopbreak']) return;
            $callback($key_element, $element);
        }
    }
}

function isInRouteGroupOr($routes)
{
    foreach ($routes as $route) {
        if (isInRoute($route)) {
            return true;
        }
    }

    return false;
}

function getCurrentRouteName($forceChangeDotToUnderline = false)
{
    $route = request()->route()->getName();
    if ($forceChangeDotToUnderline) {
        $route = str_replace(".", "_", $route);
    }

    return $route;
}

function isInRoute(string $route, string $prefix = "dashboard")
{

    $result = false;

    if ($prefix && getPartOfUrl() != $prefix) {
        return $result;
    }

    $result = (strpos(request()->path(), $route) !== false);

    return $result;
}

function isRouteExists($route_name)
{
    $result = \Illuminate\Support\Facades\Route::has($route_name);

    return $result;
}

function toggleBetweenToItems($current_value, $typeA, $typeB)
{
    $result = "";

    if ($current_value == $typeA) $result = $typeB;
    else if ($current_value == $typeB) $result = $typeA;

    return $result;
}

function sortTheArray($mArray, $callback = null, $sortType = 'asc')
{

    $callback = $callback ?: function ($a, $b) {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    };

    usort($mArray, $callback);
    if ($sortType == 'desc')
        $mArray = array_reverse($mArray, true);

    return $mArray;
}

function prefixTheList($list, $prefix = "PRE_", $seperator = ",")
{
    $str = "";
    foreach ($list as $item) {
        $str .= $prefix . $item . $seperator;
    }

    if (strrev($str)[0] == $seperator) {
        $str = substr($str, 0, strlen($str) - 1);
    }

    return $str;
}

function uaSortAction($mArray, $sortType = 'asc')
{
    $srt = function ($a, $b) {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    };

    uasort($mArray, $srt);

    if ($sortType == 'desc')
        $mArray = array_reverse($mArray, true);

    return $mArray;
}

function showErrorMessage($str, $meessage = "Invalid ", $sign = "#")
{
    return $meessage . $sign . $str;
}

function setVariableJavascript($var_name, string $var_value, string $type = "text/javascript", string $prefix = "\n", string $stuffix = "\n")
{
    $content = "";
    if ($type == "text/javascript") {
        $content = "const {$var_name} = {$var_value}";
    } else if ($type == "application/ld+json") {
        $content = $var_value;
    }

    return "{$prefix}<script type=\"{$type}\">{$content}</script>{$stuffix}";
}

function redirectByJs($url)
{
    return setVariableJavascript("redirectByCms", "location.assign('{$url}')");
}

function setErrorMessage($message, $data = [])
{
    $session = session();

    $errors = new Illuminate\Support\ViewErrorBag;
    $session->flash(
        'errors',
        $errors->put("default", new Illuminate\Support\MessageBag(["jsonServerMessage" => json_encode(["message" => $message, "data" => $data])]))
    );
}

function getErrorMessage()
{
    $sessionError = session('errors');
    if (!$sessionError) return "";

    return $sessionError->getBag('default')->first();
}

function removeErrorMessage()
{
    session()->forget('errors');
}

function getErrorMessageJavascript()
{
    $str = "";
    $error = getErrorMessage();
    if ($error) {
        $str .= setVariableJavascript("jsonServerMessage", $error);
    }

    return $str;
}

function checkArrayElement($elements, $callback)
{
    foreach ($elements as $element) {
        $res = call_user_func($callback, $element);
        if (!$res) return $res;
    }

    return $res;
}

function loopthrowString($mStr, $callback, array $args = [])
{
    if (empty($mStr)) return false;
    $mArr = explode(",", $mStr);

    foreach ($mArr as $mArrChild) {
        array_unshift($args, $mArrChild);
        echo call_user_func($callback, ...$args);
        array_shift($args);
    }
}

function checkEnqueue($type)
{

    $callback = $type == "css" ? 'enqueueCssByRoute' : 'enqueueJsByRoute';

    if (isInRoute('index.php')) {
        //
    } else if (isInRouteGroupOr(['/create', '/edit'])) {
        loopthrowString("select2", $callback);
    } else if (isInRoute('/index')) {
        loopthrowString("select2,dataTables,ion.rangeSlider,element.list", $callback);
    } else if (isInRouteGroupOr(['/user/sign_up', '/user/sign_in', '/user/verify', '/user/reset'])) {
        loopthrowString("user.panel", $callback);
    }

    if (isInRoute('file/create')) {
        loopthrowString("file.add", $callback);
    } else if (isInRoute('/menu')) {
        loopthrowString("select2,dadj,cute.alert,menu", $callback);
    } else if (isInRoute('/settings/edit')) {
        loopthrowString("bootstrap-colorpicker,jquery.cloner", $callback);
    } else if (isInRoute('/user/edit')) {
        loopthrowString("bootstrap-colorpicker", $callback);
    } else if (isInRouteGroupOr(['/forms_schema/create', '/forms_schema/edit'])) {
        loopthrowString("jquery.cloner", $callback);
    }
}

function includeGroupCss($cssList)
{
    $tag = "";
    foreach ($cssList as $css) {
        $tag .= enqueue_style($css[0], $css[1]);
    }

    return $tag;
}

function includeGroupJs($jsList)
{
    $tag = "";
    foreach ($jsList as $js) {
        $tag .= enqueue_script($js[0], $js[1]);
    }

    return $tag;
}

function abortByUnPublicType($state, $status_code = 403)
{
    if (!$state) {
        abort($status_code);
    }
}

function abortByEntity($model, $status_code = 404)
{
    if (!$model) abort($status_code);
}

function abortByOwnID($model, $id, $status_code = 403)
{
    if (getTypeID($model) != $id) {
        abort($status_code);
    }
}

function abortByRole($user, $id)
{
    $user_role = getTypeRole($user);
    $levels = getCurrentUserAccessLevelEntity_Create();

    if (!in_array($user_role, array_keys($levels)) && getTypeID(getCurrentUser()) != $id) {

        abort(403);
    }
}

function isExpired($expired_at)
{
    $dates = [
        "now" => dateToUnixTime(getDateByUnixTime()),
        "expire" => dateToUnixTime($expired_at),
    ];

    if ($dates['expire'] < $dates['now']) {
        return true;
    }

    return false;
}

function abortByExpire($expired_at, $isExpired = null)
{

    if (isExpired($expired_at) || $isExpired) {
        abort(403, "Expired");
    }
}

function abortBySeen($isSeen, $message = "Used")
{

    if ($isSeen) {
        abort(403, $message);
    }
}

function fixPublicLaravel()
{
    if (getPartOfUrl(0) == "public") {
        $root_url = ROOT_URL;
        $url = request()->fullUrl();
        $url = str_replace($root_url . "/public", $root_url, $url);
        return redirect($url);
    }
}

function pathToAction()
{
    $pathUrl = request()->path();
    $needle = "dashboard";
    if (strpos($pathUrl, $needle) === false) return false;

    $pathList = explode($needle, $pathUrl);
    $pathList = end($pathList);
    $pathList = cleanTheArray(explode("/", $pathList));
    $path = join("_", $pathList);

    do_action($path);
}

function arrayToStrLine($mArr, $page, $glue = "\n")
{

    foreach ($mArr as $index => $cArr) {
        $cArr = "<!-- {$page} -->\n" . $cArr;
        $mArr[$index] = $cArr;
    }

    $mArr[] = "";
    return join($glue, $mArr);
}

function enqueueCssByRoute($page)
{

    $list = [
        'dataTables' => [
            ['dataTables.bootstrap4', 'static/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'],
            ['dataTables.buttons', 'static/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css']
        ],
        'datepicker.jalali' => [
            ['datepicker.jalali', 'static/libs/pwt.datepicker/css/persian-datepicker.min.css'],
            ['datepicker.jalali.theme', 'static/libs/pwt.datepicker/css/persian-datepicker-custom.min.css'],
        ],
        'datepicker.zebra' => [
            ['datepicker.zebra', 'static/libs/zebra_datepicker/css/bootstrap/zebra_datepicker.min.css'],
        ],
        'bootstrap-colorpicker' => [
            ['bootstrap-colorpicker', 'static/libs/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css'],
        ],
        'select2' => [
            ['select2', 'static/libs/select2/css/select2.min.css']
        ],
        'google.lato' => [
            ['google.lato', 'static/libs/google-fonts/Lato/css/google-lato.css']
        ],
        'local.font' => [
            ['local.font', 'static/fonts/peydaweb/font.css']
        ],
        'bootstrap.icons' => [
            ['bootstrap.icons', 'static/libs/bootstrap-icons/bootstrap-icons.css']
        ],
        'bootstrap.min' => [
            ['bootstrap.min', 'static/libs/bootstrap/css/bootstrap.min.css']
        ],
        'bootstrap-5.min' => [
            ['bootstrap.min', 'static/libs/bootstrap-5/css/bootstrap.min.css']
        ],
        'jquery.ios.picker' => [
            ['jquery.ios.picker', 'static/libs/jquery.ios.picker/css/jquery.ios.picker.min.css'],
        ],
        'simplebar' => [
            ['simplebar', 'static/libs/simplebar/css/simplebar.css']
        ],
        'sweet.alert' => [
            ['sweetalert2', 'static/libs/sweetalert2/sweetalert2.min.css']
        ],
        'cute.alert' => [
            ['cute.alert', 'static/libs/cute.alert/css/cute-alert.min.css']
        ],
        'dadj' => [
            ['dadj', 'static/libs/dadj/css/draganddrop.css']
        ],
        'ion.rangeSlider' => [
            ['ion.rangeSlider', 'static/libs/ion-rangeslider/css/ion.rangeSlider.min.css']
        ],
        'tags.input' => [
            ['tags.input', 'static/libs/tags.input/css/bootstrap-tagsinput.css']
        ],
        'light.box' => [
            ['light.box', 'static/libs/magnific-popup/magnific-popup.css']
        ]

    ];


    return !empty($list[$page]) ? includeGroupCss($list[$page]) : '';
}

function enqueueJsByRoute($page, $in_part = 'in_footer')
{
    $list = [
        'dataTables' => [
            'in_header' => [],
            'in_footer' => [
                ['datatablesJquery', 'static/libs/datatables.net/js/jquery.dataTables.min.js'],
                ['datatablesBootstrap4', 'static/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js']
            ]
        ],
        'tinymce' => [
            'in_header' => [],
            'in_footer' => [
                ['tinymceSrc', 'static/libs/tinymce/tinymce.min.js']
            ]
        ],
        'jquery' => [
            'in_header' => [],
            'in_footer' => [
                ['jquery', 'static/libs/jquery/jquery.min.js'],
            ]
        ],
        'bootstrap.bundle.min' => [
            'in_header' => [],
            'in_footer' => [
                ['bootstrap.bundle.min', 'static/libs/bootstrap/js/bootstrap.bundle.min.js'],
            ]
        ],
        'bootstrap-5.bundle.min' => [
            'in_header' => [],
            'in_footer' => [
                ['bootstrap-5.bundle.min', 'static/libs/bootstrap-5/js/bootstrap.bundle.min.js'],
            ]
        ],
        'jquery.ios.picker' => [
            'in_header' => [],
            'in_footer' => [
                ['jquery.ios.picker', 'static/libs/jquery.ios.picker/js/jquery.ios.picker.min.js'],
            ],
        ],
        'simplebar' => [
            'in_header' => [],
            'in_footer' => [
                ['simplebar', 'static/libs/simplebar/js/simplebar.min.js'],
            ]
        ],
        'select2' => [
            'in_header' => [],
            'in_footer' => [
                ['select2', 'static/libs/select2/js/select2.min.js'],
            ]
        ],
        'jquery.cloner' => [
            'in_header' => [],
            'in_footer' => [
                ['jquery.cloner', 'static/libs/jquery.cloner/jquery.cloner.min.js'],
            ]
        ],
        'apexcharts' => [
            'in_header' => [],
            'in_footer' => [
                ['apexcharts', 'static/libs/apexcharts/apexcharts.min.js']
            ]
        ],
        'datepicker.jalali' => [
            'in_header' => [],
            'in_footer' => [
                ['datepicker.jalali.date', 'static/libs/pwt.datepicker/js/persian-date.min.js'],
                ['datepicker.jalali', 'static/libs/pwt.datepicker/js/persian-datepicker.min.js'],
            ]
        ],
        'datepicker.zebra' => [
            'in_header' => [],
            'in_footer' => [
                ['datepicker.zebra', 'static/libs/zebra_datepicker/js/zebra_datepicker.min.js'],
            ]
        ],
        'bootstrap-colorpicker' => [
            'in_header' => [],
            'in_footer' => [
                ['bootstrap-colorpicker', 'static/libs/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js'],
            ]
        ],
        'sweet.alert' => [
            'in_header' => [],
            'in_footer' => [
                ['sweetalert2', 'static/libs/sweetalert2/sweetalert2.min.js']
            ]
        ],
        'cute.alert' => [
            'in_header' => [],
            'in_footer' => [
                ['cute.alert', 'static/libs/cute.alert/js/cute-alert.min.js']
            ]
        ],
        'dadj' => [
            'in_header' => [],
            'in_footer' => [
                ['dadj', 'static/libs/dadj/js/draganddrop.js']
            ]
        ],
        'ion.rangeSlider' => [
            'in_header' => [],
            'in_footer' => [
                ['ion.rangeSlider', 'static/libs/ion-rangeslider/js/ion.rangeSlider.min.js']
            ]
        ],
        'js.cookie' => [
            'in_header' => [],
            'in_footer' => [
                ['js.cookie', 'static/libs/js.cookie/js/js.cookie.min.js']
            ]
        ],
        'js.big' => [
            'in_header' => [],
            'in_footer' => [
                ['js.big', 'static/libs/js.big/js/big.min.js']
            ]
        ],
        'tags.input' => [
            'in_header' => [],
            'in_footer' => [
                ['tags.input', 'static/libs/tags.input/js/bootstrap-tagsinput.min.js']
            ]
        ],
        'light.box' => [
            'in_header' => [],
            'in_footer' => [
                ['light.box', 'static/libs/magnific-popup/jquery.magnific-popup.min.js']
            ]
        ],
        'clipboard' => [
            'in_header' => [],
            'in_footer' => [
                ['clipboard.min', 'static/libs/clipboard/js/clipboard.min.js']
            ]
        ],
        'file.add' => [
            'in_header' => [],
            'in_footer' => [
                ['file.add', 'static/js/custom/pages/file.add.js']
            ]
        ],
        'file.manager' => [
            'in_header' => [],
            'in_footer' => [
                ['file.manager', 'static/js/custom/pages/file.manager.js']
            ]
        ],
        'menu' => [
            'in_header' => [],
            'in_footer' => [
                ['menu.add', 'static/js/custom/pages/menu.js']
            ]
        ],
        'user.panel' => [
            'in_header' => [],
            'in_footer' => [
                ['user.panel', 'static/js/custom/pages/user.panel.js']
            ]
        ],
        'element.list' => [
            'in_header' => [],
            'in_footer' => [
                ['element.list.min', 'static/js/custom/pages/element.list.js']
            ]
        ],
        'product.add' => [
            'in_header' => [],
            'in_footer' => [
                ['product.add.min', 'static/js/custom/pages/product.add.js']
            ]
        ],
        'common.core' => [
            'in_header' => [],
            'in_footer' => [
                ['common.core', 'static/js/common.core.js']
            ]
        ]
    ];


    return !empty($list[$page][$in_part]) ? includeGroupJs($list[$page][$in_part]) : '';
}

function addElementByPosToArray(array $arr, int $index, mixed $element)
{
    $i = 0;
    $finallArray = [];
    foreach ($arr as $mElement) {

        if ($i === $index) {
            array_push($finallArray, $element);
        }
        array_push($finallArray, $mElement);

        $i++;
    }

    return $finallArray;
}

function groupReplacer(string $str, array $search_list, array $replace_list)
{
    $finall = str_replace($search_list, $replace_list, $str);
    return $finall;
}

function sanitizeBySign($text, $sign = "#")
{
    $index = stripos($text, $sign);
    $result = $text;
    if (is_int($index)) {
        $result = substr($result, 0, $index - 1);
    }

    return $result;
}

function registerTypeDynamically($prop, $keys, &$type)
{
    $nameList = $keys ? $keys : ['label', 'slug'];

    foreach ($nameList as $name) {
        if (!isset($prop[$name])) return showErrorMessage($name);
    }

    array_push($type, $prop);
    return true;
}

function getHtmlTemplate($name)
{

    $aParentAlign = $GLOBALS['lang']['direction'] == "rtl" ? trnsAlignCls() . " ml-2 mr-4" : "ml-4 mr-2";
    $aChildAlign = trnsAlignCls();
    $paddingChildUl = $GLOBALS['lang']['direction'] == "rtl" ? "p-0 pr-3" : "pl-3";

    $alignAndBlock = trnsAlignBlockCls();

    $taxonomyTypeLabel = __local("Taxonomy");
    $chooseLabel = __local("Choose");
    $labelAddToMenu = __local('Add To Menu');

    $templates = [
        'dashboardMenu' => "<li id=\"x-id\" data-element-number=\"x-number\" data-element-chr=\"x-chr-number\" class=\"nav-item mt-2 rounded position-relative\" data-active-class=\"active-bg\"> <i class=\"x-icon-class position-absolute h5 side-icon-prefix active-text\"></i><a class=\"text-decoration-none p-2 d-block font-weight-bold active-text {$aParentAlign}\" href=\"x-link\">x-label</a> </li>",
        'dashboardMenuMenu' => "<li id=\"x-id\" data-element-number=\"x-number\" data-element-chr=\"x-chr-number\" class=\"nav-item mt-2 rounded has-child position-relative bi bi-caret-down-fill\" data-active-class=\"active-bg\"><i class=\"x-icon-class position-absolute h5 side-icon-prefix active-text\"></i><a class=\"text-decoration-none p-2 ml-4 mr-2 d-block font-weight-bold active-text {$aParentAlign}\">x-label</a> <ul class=\"nav flex-column position-absolute w-100 {$paddingChildUl}\" id=\"depth-2\">x-children</ul></li>",
        "dashboardChildMenu" => "<li class=\"nav-item mt-2\" data-active-class=\"active\"><a class=\"text-decoration-none p-2 d-block font-weight-bold active-text {$aChildAlign}\" href=\"x-link\">x-label</a></li>",
        "select2TaxonomyWidget" => "<div class=\"input-wrapper taxonomy component-taxonomy x-taxonomy-slug wrapper-ajax quick-create active mt-3 col-6\"> <label for=\"taxonomy-x-taxonomy-slug\" class=\"control-label {$alignAndBlock}\">{$taxonomyTypeLabel} x-taxonomy-label <button type=\"button\" data-field=\"type:x-taxonomy-slug,status:publish\" class=\"quick-add btn btn-primary\">+</button></label> <select data-empty-content=\"true\" class=\"form-control select2 select2ajax select2-multiple\" id=\"taxonomy-x-taxonomy-slug\" data-taxonomy=\"x-taxonomy-slug\" data-group-id=\"taxonomy:x-taxonomy-slug\" data-label=\"x-taxonomy-label\" data-ajx=\"x-value\" multiple></select><input type=\"text\" id=\"taxonomy-content-x-taxonomy-slug\" name=\"taxonomy[x-taxonomy-slug]\" value=\"\" class=\"d-none the-value select2-content-list\"> </div>",
        "select2TaxonomyWidgetPrivate" => "<div class=\"input-wrapper private taxonomy component-taxonomy x-taxonomy-slug wrapper-ajax quick-create active mt-3 col-6\"> <label for=\"taxonomy-x-taxonomy-slug\" class=\"control-label {$alignAndBlock}\">{$taxonomyTypeLabel} x-taxonomy-label <button type=\"button\" data-field=\"type:x-taxonomy-slug,status:publish\" class=\"quick-add btn btn-primary\">+</button></label> <select data-empty-content=\"true\" class=\"form-control select2 select2ajax select2-multiple\" id=\"taxonomy-x-taxonomy-slug\" data-taxonomy=\"x-taxonomy-slug\" data-group-id=\"taxonomy:x-taxonomy-slug\" data-label=\"x-taxonomy-label\" data-ajx=\"x-value\" multiple></select><input type=\"text\" id=\"taxonomy-content-x-taxonomy-slug\" name=\"taxonomy[x-taxonomy-slug]\" value=\"\" class=\"d-none the-value select2-content-list\"> </div>",
        "select2TaxonomyWidgetMenu" => "<div class=\"input-wrapper taxonomy x-taxonomy-slug mb-4 n-border p-3\"> <label class=\"control-label {$alignAndBlock}\">{$chooseLabel} x-taxonomy-label</label> <select class=\"form-control select2 select2ajax select2-multiple\" data-taxonomy=\"x-taxonomy-slug\" data-extra=\"link\"></select> <input type=\"hidden\" class=\"push-data\"> <div class=\"text-right mt-3\"> <input type=\"button\" value=\"{$labelAddToMenu}\" class=\"btn btn-info add-to-menu\" data-target=\".x-taxonomy-slug .push-data\"> </div> </div>"
    ];

    // add some change for `select2TaxonomyWidgetSingle` from `select2TaxonomyWidget`
    $templates['select2TaxonomyWidgetSingle'] = str_replace(["select2-multiple", "multiple"], "", $templates['select2TaxonomyWidget']);


    return @$templates[$name];
}

function generateDataToTemplate(string $template, array $data)
{
    $result = $template;
    foreach ($data as $itemKey => $itemValue) {
        $result = str_ireplace($itemKey, $itemValue, $result);
    }

    return $result;
}

function encodeChrByAnyPrintableChr($str, $prefix = "element-")
{
    return $prefix . strtolower(md5(base64_encode($str)));
}

function getStrByIndex($index, $prefix = "element-")
{
    return $prefix . $index;
}

function dotToUnderline($str)
{
    return str_replace(".", "_", $str);
}

function slashFixerPath($pathURL)
{

    $str_reverse = function ($str) {
        $rev = strrev($str);
        if ((str_split($rev))[0] == "/") {
            $rev = substr($rev, 1);
        }

        return strrev($rev);
    };

    // fix / beginning
    $tmp_pathURL = $str_reverse(strrev($pathURL));
    $tmp_pathURL = strrev($tmp_pathURL);

    // fix / ending
    $tmp_pathURL = $str_reverse($tmp_pathURL);

    return $tmp_pathURL;
}

function getDiffBrackets($pattern, $url)
{
    $pattern = slashFixerPath($pattern);
    $url = slashFixerPath($url);

    $list_pattern = explode("/", $pattern);
    $list_url = explode("/", $url);

    if (count($list_pattern) != count($list_url)) {
        return false;
    }


    $dynamicURL = "";
    $url_sanitized = "";
    foreach ($list_pattern as $i => $item_pattern) {
        $requested_url = $list_url[$i];
        $url_sanitized .= "{$requested_url}/";
        if ($item_pattern == $requested_url || 0 < preg_match('/{+\w+\?{0,1}}+/', $item_pattern)) {
            $dynamicURL = $url_sanitized;
        } else {
            break;
        }
    }

    return $dynamicURL == $url_sanitized;
}

function isURLPatternTrue($pattern, &$url)
{

    $current_path = request()->path();

    $isCurrentUrl = getDiffBrackets($url, $current_path);

    if ($isCurrentUrl) {
        $url = $current_path;
        if ($url[0] != "/") $url = "/{$url}";
    }

    return getDiffBrackets($pattern, $url);
}

function removeEmptyChildElement($html)
{

    $dom = new DomDocument();
    @$dom->loadHTML($html);

    $parentUl = $dom->getElementsByTagName("ul")[0];

    $liList = $dom->getElementsByTagName("li");
    $liToRemoveList = [];
    foreach ($liList as $li) {
        $ulChild = $li->getElementsByTagName("ul");
        if (count($ulChild)) {
            $liChild = $ulChild[0]->getElementsByTagName("li");
            if (count($liChild) === 0) {
                $liToRemoveList[] = $li;
            }
        }
    }

    foreach ($liToRemoveList as $element) {
        $parentUl->removeChild($element);
    }

    $output = $dom->saveHTML($parentUl);

    return $output;
}

function getShortcutTextCbkName($callbackRaw)
{

    $result = "";

    if (!$callbackRaw) return $callbackRaw;

    $result = $callbackRaw . "_shortcut_text_action";

    return $result;
}

function extractShortcutText($input)
{
    $result = [];

    $startTag = "";
    $endTag = "";

    $openTagOpenSign = "[";
    $openTagCloseSign = "]";
    $closeTagOpenSign = "[/";

    $offest = 1;

    $indexStartTag = mb_stripos($input, $openTagCloseSign);

    if (!is_int($indexStartTag)) return $result;

    $indexEndTag = mb_stripos($input, $closeTagOpenSign);

    if (!is_int($indexEndTag)) return $result;

    $startTag = mb_substr($input, 0, $indexStartTag + $offest);
    $endTag = mb_substr($input, $indexEndTag);

    if (is_int(mb_stripos($startTag, $endTag))) return $result;

    $tagName = str_replace([$closeTagOpenSign, $openTagCloseSign], "", $endTag);

    if (trim($tagName) != $tagName) return $result;

    if (!$tagName) return $result;

    $searchTagStartWithNoSpace = mb_stripos($input, "{$openTagOpenSign}{$tagName}{$openTagCloseSign}");
    $pattern = "/\\" . "{$openTagOpenSign}" . "{$tagName}.{1,}\\" . "{$openTagCloseSign}/";


    $searchTagStartWithSpace = @preg_match($pattern, $input, $matches);

    if (!is_int($searchTagStartWithNoSpace) && $searchTagStartWithSpace != 1) {
        return $result;
    }

    $openTagText = str_replace(["[tagName ", $openTagCloseSign], "", $startTag);
    $pattern = '/\b(\w+)=__QOUTE__([^__QOUTE__]+)__QOUTE__/';

    // with single quotes
    $pattern_single = str_replace("__QOUTE__", "'", $pattern);
    preg_match_all($pattern_single, $openTagText, $matches_singleQuote);

    $attributesRaw = array_merge($matches_singleQuote[0]);

    $attributes = [];

    foreach ($attributesRaw as $attributeRawItem) {
        $attributeRawItemPart = explode("=", $attributeRawItem, 2);

        if (count($attributeRawItemPart) != 2) continue;

        $value_offest_action_1 = mb_substr($attributeRawItemPart[1], 1);

        $attributes[$attributeRawItemPart[0]] = mb_substr($value_offest_action_1, 0, mb_strlen($value_offest_action_1) - 1);
    }

    $text = str_replace("{$closeTagOpenSign}{$tagName}{$openTagCloseSign}", "", mb_substr($input, $indexStartTag + $offest));

    $result = [
        "tagName" => "$tagName",
        "text" => $text,
        "attributes" => $attributes
    ];

    return $result;
}

function findShortcutTextInString($text)
{

    $pattern = '/\[\w+\s*.*\].*\[\/\w+\]/';

    preg_match_all($pattern, $text, $matches);

    return $matches[0];
}

function searchURLByParts(array $search_list, string $subject)
{
    $state = true;
    foreach ($search_list as $search) {
        if (!is_int(strpos($subject, $search))) {
            $state = false;
        }
    }
    return $state;
}

function removeUnauthorizedType($html, $actions, $type = "post.type")
{
    loadHTMLParser();
    $doc = str_get_html($html);

    $a_list = $doc->find("a");
    $liToRemove = [];

    $callbackPermission = callbackListPermissions($type);
    $callbackForbidden = callbackListForbidden($type);

    foreach ($actions as $action) {
        foreach ($a_list as $a) {
            $link = $a->getAttribute("href");
            if (searchURLByParts(["dashboard/{$type}", "/{$action}"], $link)) {
                $entity_type = getPartOfUrl(-2, $link);
                $permission = $callbackPermission($entity_type, $action);
                if ($permission) {
                    if ($callbackForbidden($permission)) {
                        $liToRemove[] = $a->parent();
                    }
                }
            }
        }
    }

    foreach ($liToRemove as $li) {
        $li->remove();
    }

    return $doc->save();
}

function isNullDefaultValue($default, $val = null)
{
    return $val === null ? $default : $val;
}

function renameArrayInput($inputs)
{
    $finalInputs = [];
    foreach ($inputs as $key => $input) {
        if (is_array($input)) {
            foreach ($input as $key_item => $item) {
                $finalInputs[$key . ":" . $key_item] = $item;
            }
        } else {
            $finalInputs[$key] = $input;
        }
    }

    return $finalInputs;
}

function loadTheLinkOrScriptUrl($url)
{
    $result = $url;

    if (!(is_int(stripos($url, "http://")) || is_int(stripos($url, "https://")))) {
        $result = "/" . $result;
    }

    return $result;
}

function enqueue_style($id, $href, $dependency = "", $version = null, $rel = "stylesheet", $type = "text/css")
{
    $version = isNullDefaultValue(env("APP_VERSION"), $version);
    $rel = isNullDefaultValue('stylesheet', $rel);
    $type = isNullDefaultValue('text/css', $type);

    $href = loadTheLinkOrScriptUrl($href);

    $GLOBALS['triggeredStyles'][] = $id;

    if (empty($dependency)) {

        $styleTag = "\n<link href=\"{$href}?version={$version}\" id=\"{$id}\" rel=\"{$rel}\" type=\"{$type}\">";

        if (!empty($GLOBALS['attachedStyles'][$id])) {
            foreach ($GLOBALS['attachedStyles'][$id] as $key => $mVal) {
                $styleTag .= "{$mVal}";
            }
            $GLOBALS['attachedStyles'][$id] = [];
        }

        return $styleTag;
    } else {
        $args = func_get_args();
        $args[2] = "";
        $val = call_user_func(__FUNCTION__, ...$args);

        if (!isset($GLOBALS['attachedStyles'][$dependency]))
            $GLOBALS['attachedStyles'][$dependency] = [];

        array_push($GLOBALS['attachedStyles'][$dependency], $val);
        return "";
    }
}

function enqueue_script($id, $href, $dependency = "", $version = null)
{
    $version = isNullDefaultValue(env("APP_VERSION"), $version);

    $href = loadTheLinkOrScriptUrl($href);

    $GLOBALS['triggeredScripts'][] = $id;

    if (empty($dependency)) {

        $scriptTag = "\n<script id=\"{$id}\" src=\"{$href}?version={$version}\"></script>";

        if (!empty($GLOBALS['attachedScripts'][$id])) {
            foreach ($GLOBALS['attachedScripts'][$id] as $key => $mVal) {
                $scriptTag .= "{$mVal}";
            }
            $GLOBALS['attachedScripts'][$id] = [];
        }

        return $scriptTag;
    } else {
        $args = func_get_args();
        $args[2] = "";
        $val = call_user_func(__FUNCTION__, ...$args);

        if (!isset($GLOBALS['attachedScripts'][$dependency]))
            $GLOBALS['attachedScripts'][$dependency] = [];

        array_push($GLOBALS['attachedScripts'][$dependency], $val);
        return "";
    }
}

function addRequireInputToJsonKeyMap($page, $element)
{
    $added = false;
    if (is_array($element)) {
        foreach ($element as $key => $item) {
            $current_map = getRequireInputJsonKeyMapByPage($page);
            if (!isset($current_map)) continue;

            $GLOBALS['require_inputs'][$page]['elements'][$key] = $item;
            $added = true;
        }
    }

    return $added;
}

function removeRequireInputToJsonKeyMap($page, $element)
{
    $status = false;

    $current_map = getRequireInputJsonKeyMapByPage($page);
    if (!$current_map) return $status;

    if (is_array($element)) {
        foreach ($element as $itemKey) {
            unset($GLOBALS['require_inputs'][$page]['elements'][$itemKey]);
            $status = true;
        }
    } else if ($element === true) {
        $GLOBALS['require_inputs'][$page]['elements'] = [];
        $status = true;
    }

    return true;
}

function getRequireInputJsonKeyMapByPage($page)
{
    return $GLOBALS['require_inputs'][$page]['elements'] ?? [];
}

function getRequireInputJsonKeyMap()
{
    return getRequireInputJsonKeyMapByPage($GLOBALS['current_page']);
}

function changeCurrentPageTemporary($new_page, $state = "set")
{
    $GLOBALS['_current_page'] = $state == "set" ? $GLOBALS['current_page'] : $GLOBALS['_current_page'];
    $GLOBALS['current_page'] = $state == "set" ? $new_page : $GLOBALS['_current_page'];
}

function addKeyToArray($list)
{
    $tmp_list = [];
    foreach ($list as $item_key => $item_value) {
        $tmp_list[$item_value] = $item_value;
    }

    return $tmp_list;
}

function sanitizeXssScriptString($str)
{
    $replace_list = [
        "<script>" => htmlentities('<script>'),
        "</script>" => htmlentities('</script>'),
    ];

    return str_replace(array_keys($replace_list), array_values($replace_list), $str);
}

function simpleArrayMerger($list)
{
    $theList = $list;
    if (count($list)) {
        foreach ($theList as &$str_item) {
            if (!is_array($str_item))
                $str_item = [$str_item];
        }
        $theList = array_merge(...$theList);
    }

    return $theList;
}

function mergeTheArrayKeyValue($list, $merged = true)
{
    $tmpList = [];

    $tmpListTheList = array_values($list);
    if ($merged) {
        $tmpListTheList = array_merge(...$tmpListTheList);
    }

    array_walk($tmpListTheList, function ($item) use (&$tmpList) {
        $tmpList[$item['key']] = $item['value'];
    });

    return $tmpList;
}

function makeITGloabal($key, $value)
{
    $GLOBALS[$key] = $value;

    do_action("make_{$key}_global", $value);

    return $GLOBALS[$key];
}

function generateGlobalTitle($model, $extraData = [])
{
    $theGlobalItem = $model;
    $theGlobalItem['extraData'] = $extraData;
    makeITGloabal("DB", $theGlobalItem);
    return $theGlobalItem;
}

function escapeString($str, $quote = "\"", $replaceQuote = "\\\"")
{
    return str_replace($quote, $replaceQuote, $str);
}

function encodeUrlEncodePlusBase64($str, $reverse = false)
{
    return !$reverse ? base64_encode(urlencode($str)) : urldecode(base64_decode($str));
}

function isBinary($str)
{
    return (!preg_match('//u', $str));
}

function addZeroPrecisionIfInteger($number, $precision = 2)
{
    $number = strval($number);
    if (!preg_match("/\D/i", $number)) {
        $number .= "." . str_repeat("0", $precision);
    }

    return $number;
}

function getValueArrayByPossibleKey($list, $primaryKey, $stuffixKey = null, $stuffixPos = "after")
{

    if (!is_array($stuffixKey)) {
        $stuffixKey = [$stuffixKey];
    }

    foreach ($stuffixKey as $stfKey) {
        $stfKeyFinall = $stuffixPos == "after" ? $primaryKey . $stfKey :  $stfKey . $primaryKey;
        if (isset($list[$stfKeyFinall])) {
            return $list[$stfKeyFinall];
        }
    }
}

function getArrayElementByKeys(array $keys, array $list, $cbk = null)
{
    $theList = [];
    foreach ($keys as $key) {
        if (!isset($list[$key])) continue;
        $element = $list[$key];

        if ($cbk && is_callable($cbk)) {
            $element =  $cbk($element);
        }

        $theList[$key] = $element;
    }

    if (!count($keys)) {
        $theList = $list;
    }

    return $theList;
}

function getArrayElementByKeysGrouply($keys, $list, $index = null, $roundSingleElement = false)
{
    $result = [];
    $i = 0;
    foreach ($list as $item) {
        $key = is_null($index) ? $i : $item[$index] ?? $i;
        $current_result = getArrayElementByKeys($keys, $item);

        if (count($current_result) == 1 && $roundSingleElement) {
            $current_result = last($current_result);
        }

        $result[$key] = $current_result;

        $i++;
    }

    return $result;
}

function adapterArrayKey(array $keys_old, array $keys_new, array $list)
{
    foreach ($keys_old as $i => $key_old) {
        $new_key = $keys_new[$i];
        $list[$new_key] = $list[$key_old];
        unset($list[$key_old]);
    }
    return $list;
}

function adapterForTagOption($list, $two_keys = ["id", "title"])
{
    $result = [];
    foreach ($list as $item) {
        $id = getTypeAttr($item, $two_keys[0]);
        $label = getTypeAttr($item, $two_keys[1]);
        $result[$id] = $label;
    }

    return $result;
}

function extractAttributes($items, $attrsKey, $toKeys = [], $needLoop = true)
{
    $list = [];

    $extractByKeys = function ($item, $attrsKey) use ($toKeys) {
        $resultItem = [];
        $index = 0;
        foreach ($attrsKey as $key) {
            $theKey = $toKeys ? $toKeys[$index] : $key;
            $resultItem[$theKey] = $item[$key] ?? null;
            $index++;
        }
        return $resultItem;
    };

    if ($needLoop) {
        foreach ($items as $item) {
            $list[] = $extractByKeys($item, $attrsKey);
        }
    } else {
        $list = $extractByKeys($items, $attrsKey);
    }

    return $list;
}

function extractKeysFromArrayByWord($keys, $word)
{
    $result = [];
    foreach ($keys as $key) {
        if (is_int(stripos($key, $word))) {
            $result[] = $key;
        }
    }

    return $result;
}

function extractImgTagsFromHtml($html, $export = "HTML", $selector = "img")
{
    loadHTMLParser();

    $imgTags = [];

    $doc = str_get_html($html);

    if (!$doc) return $imgTags;

    $imgTagsDOM = $doc->find($selector);
    if ($imgTagsDOM) {
        foreach ($imgTagsDOM as $index => $imgTagItem) {

            if ($export == "HTML") {
                $imgTags[] = $imgTagItem->outertext();
            } else if ($export == "SRC") {
                $imgTags[] = $imgTagItem->getAttribute("src");
            }
        }
    }

    return $imgTags;
}

function persianToEnglishDigits($str, $reverse = false)
{
    $persianDigits = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $englishDigits = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    $result = !$reverse ? str_replace($persianDigits, $englishDigits, $str) : str_replace($englishDigits, $persianDigits, $str);
    return $result;
}

function baseJsonParse(string $json)
{
    if (trim($json) == "") return "";

    $parsedJson = json_decode($json, true) ?? [];

    return $parsedJson;
}

function showJsonPretty(string $json)
{

    $parsedJson = baseJsonParse($json);
    if (!$parsedJson) return $parsedJson;

    return var_export($parsedJson, true);
}

function highlightJson(string $json, array $fields, $color)
{
    $parsedJson = baseJsonParse($json);
    if (!$parsedJson) return $parsedJson;

    $str = "";

    foreach ($parsedJson as $item_key => $item_value) {
        if ($item_value)
            $item_value = htmlentities($item_value);
        if (in_array($item_key, $fields)) {
            $theCurrentStr = "<div style=\"background-color:{$color}\">[{$item_key}] => [{$item_value}]</div>\n";
        } else {
            $theCurrentStr = "[{$item_key}] => [{$item_value}]<br>\n";
        }

        $str .= $theCurrentStr;
    }

    return $str;
}

function customReturnAction($response, $cbk)
{
    return $cbk($response);
}

function justGetTheX($data, $method)
{
    $finall_data = collect([]);

    if (count($data)) {
        foreach ($data as $element) {
            $finall_data->push($element->$method());
        }
    }

    return $finall_data;
}

function justGetTheAttributes($data)
{
    return justGetTheX($data, "getAttributes");
}

function justGetTheOriginal($data)
{
    return justGetTheX($data, "getOriginal");
}

function cleanTheArray($list, $removeKey = true, $exceptions = [])
{

    if (!is_array($list)) {
        $list = [$list];
    }

    $tmpList = $list;

    $list = array_filter($list, function ($element) {
        return !empty($element) || $element != "";
    });

    if ($exceptions) {
        foreach ($exceptions as $excecpt) {
            if (!array_key_exists($excecpt, $tmpList)) continue;

            $list[$excecpt] = $tmpList[$excecpt];
        }
    }

    if ($removeKey)
        $list = array_values($list);
    return $list;
}

function makeJsonEncodeIfWasArray($list)
{
    $result = [];

    if (!is_array($list)) {
        return $result;
    }

    $result = $list;

    foreach ($result as $itemKey => &$itemValue) {
        if (!is_array($itemValue)) continue;

        $itemValue = json_encode($itemValue);
    }

    return $result;
}

function collectionMapDepth2($theCollect, $callback)
{
    $data = $theCollect->map(function ($item) use ($callback) {
        foreach ($item as &$elements) {
            foreach ($elements as &$element) {
                $element = $callback($element);
            }
        }
        return $item;
    });

    return $data;
}

function preg_matchOnly($pattern, $subject)
{
    $match = null;

    preg_match($pattern, $subject, $match);

    if ($match && is_array($match)) {
        $match = end($match);
    }

    return $match;
}

function getInsideCharacters($str, $start = "[", $end = "]")
{
    if (!$str) return "";

    return str_replace([$start, $end], [], $str);
}

function baseDirScanAction(string $path, $action, array $except = [])
{

    $the_except = arrayToLowerCase($except);

    $status = false;

    if (!is_dir($path)) return $status;

    $list = scandir($path);

    // remove current and parent directory sign
    unset($list[array_search(".", $list)]);
    unset($list[array_search("..", $list)]);

    $list = arrayToLowerCase($list);

    $triggered_loop_success = false;
    foreach ($list as $entity) {
        $entity_lowername = strtolower($entity);
        if (in_array($entity_lowername, $the_except)) {
            continue;
        }

        $cbk = $action['cbk'];
        $res = $cbk($entity);

        if (!$res) return $status;

        $triggered_loop_success = true;
    }

    if ($triggered_loop_success) {
        $status = true;
    }

    return $status;
}

function baseChangeSlashDirectory($path, $realPath = false)
{
    $thePath = str_replace(["\\"], "/", $path);
    return $realPath ? realpath($thePath) : $thePath;
}

function removeFiles(string $path, array $except = [])
{
    return baseDirScanAction($path, ["cbk" => function ($entity) use ($path) {
        $thePath = baseChangeSlashDirectory($path . SPE . $entity);
        return file_exists($thePath) ? @unlink($thePath) : false;
    }], $except);
}

function copyFiles(string $old_path, $new_path, array $except = [])
{
    return baseDirScanAction($old_path, ["cbk" => function ($entity) use ($old_path, $new_path) {
        $the_old_path = baseChangeSlashDirectory($old_path . SPE . $entity);
        return file_exists($the_old_path) ? copy($the_old_path, baseChangeSlashDirectory($new_path . SPE . $entity)) : false;
    }], $except);
}

function searchTheCollection($collection, $childName, $searchFor, $preserveKey = false)
{
    $save_component = [];
    $collection->each(function ($item) use (&$save_component, $childName, $searchFor) {
        $item = (collect($item[$childName]));
        $save_component = $item->filter(function ($item2) use ($searchFor) {
            return is_int(stripos($item2, $searchFor));
        });
    });

    if (!$preserveKey && is_object($save_component))
        $save_component = $save_component->values();

    return $save_component;
}

function getReplaceCallback($firstCbk, $secondCbk)
{
    return function_exists($firstCbk) ? $firstCbk : $secondCbk;
}

function headerForForceDownload($filename, $removeFile = false, $useObClean = true)
{
    if (!file_exists($filename)) {
        exit;
    }

    $base_filename = basename($filename);
    $filetype = mime_content_type($filename);
    $filesize = filesize($filename);
    header("Content-Type: {$filetype}");
    header("Content-Length: {$filesize}", true);
    header("Content-Disposition: attachment; filename=\"{$base_filename}\"", true);
    if ($useObClean) {
        ob_clean();
    }

    readfile($filename);
    if ($removeFile) {
        unlink($filename);
    }

    exit;
}

function showTotalRecord($item)
{
    if (!method_exists($item, "total")) return false;
    $align = trnsAlignCls();
    $typeLabel = __local('record found');
    return "<p class=\"{$align} total\">" . sprintf("<span data-seperator=\"true\" class=\"badge badge-primary\">%s</span> {$typeLabel}", $item->total()) . "</p>";
}

function showExportButtons($item)
{

    if (!count($item)) {
        return false;
    }

    $formatList = array_keys(getExportFormatList());
    if (!count($formatList)) {
        return false;
    }

    $str = "<div class=\"format-list text-right mb-3\">";
    foreach ($formatList as $format) {
        $type = ucfirst($format);
        $typeLabel = __local($type);
        $id_export = strtolower("export-{$type}");
        $str .= "<a class=\"export-btn btn active-bg mr-2\" data-format=\"{$format}\" href=\"\" id=\"{$id_export}\">$typeLabel</a>";
    }

    $str .= "</div>";
    return $str;
}

function showPageLink($item, $cbk = "", $class_pagination = "")
{
    if (!count($item) || !method_exists($item, "links")) return false;

    $class_pagination = $class_pagination ?: "justify-content-center";

    if (is_callable($cbk)) {
        $item = $cbk($item);
    }

    $content = $item->links()->render();

    if ($content) {
        loadHTMLParser();

        $doc = str_get_html($content);

        $pagination = $doc->find(".pagination", 0);
        $pagination->addClass($class_pagination);

        $content = $doc->save();
    }

    return $content;
}

function areOrIsByItems(array $items)
{
    return 1 < count($items) ? "are" : "is";
}

function getStringTemplateByValue($template, $value)
{
    $template = trim($template);

    if ($template == "") return $value;


    return str_replace("x-value", $value, $template);
}

function getTagTemplateWithComplex($tag, $attr, $text)
{
    $str = "";
    $attrs = "";
    foreach ($attr as $k => $atr) {

        $attrs .= $k == "data-filter" ? " {$k}" : " {$k}='{$atr}'";
    }
    $attrs .= ">";

    $str .= str_replace(" >", $attrs, $tag[0]);
    $str .= "{$text}{$tag[1]}";

    return $str;
}

function convertToNumber($num, $func = "intval")
{
    $valNumber = $num;
    if (is_array($num)) {
        foreach ($num as $keyVal => $vl) {
            if (is_numeric($vl)) {
                $num[$keyVal] = $func($vl);
            }
        }
        $valNumber =  $num;
    } else {
        if (is_numeric($num))
            $valNumber =  $func($num);
    }

    return $valNumber;
}

function getTagTemplate($tag)
{
    return ["<{$tag} >", "</{$tag}>"];
}

function generateCustomTemplateByArray(string $template, array $mArray)
{
    $tmpTemplate = $template;
    foreach ($mArray as $key => $element) {
        $serach = "x-{$key}";
        $tmpTemplate = str_replace($serach, $element, $tmpTemplate);
    }

    return $tmpTemplate;
}

function getElementByExplodePart(string $path, $index, $count = 1, $explodeBy = "/")
{
    $element = "";
    if (!$path) return $element;

    $pathList = explode($explodeBy, $path);
    $pathList = cleanTheArray($pathList, true);

    if (!$pathList) return $element;

    $element = array_slice($pathList, $index, $count);

    return !isset($element[1]) && isset($element[0]) ? $element[0] : $element;
}

function mustBetweenNumber($input, $min, $max)
{
    return $min <= $input && $input <= $max;
}

function hex2rgba($color, $opacity = false)
{

    $default = 'rgb(0,0,0)';

    if (empty($color))
        return $default;


    if ($color[0] == '#') {
        $color = substr($color, 1);
    }

    if (strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }


    $rgb =  array_map('hexdec', $hex);


    if ($opacity) {
        if (abs($opacity) > 1)
            $opacity = 1.0;
        $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
    } else {
        $output = 'rgb(' . implode(",", $rgb) . ')';
    }


    return $output;
}

function getRootURL($noSchmea = true)
{
    $url = request()->root();

    if ($noSchmea) {
        $url = str_replace(["http://", "https://"], ["", ""], $url);
    }

    return $url;
}

function getURLSchema($url, $which = null)
{
    // which -> scheme |  host  |  path  |  query

    $thePart = parse_url($url);

    if ($which && !empty($thePart[$which])) {
        $thePart = $thePart[$which];
    }

    return $thePart;
}

function arrayToUrl($root_url, $list, $is_root = false)
{

    if (!$is_root) {
        $root_url = getURLSchema($root_url);
        $root_url = "{$root_url['scheme']}://{$root_url['host']}";
    }

    $url = $root_url;

    if ($list) {
        foreach ($list as $getKey => &$getValue) {
            if (is_array($getValue)) {
                $getValue = json_encode($getValue);
            }
        }
        $url .= "?" . http_build_query($list);
    }


    return $url;
}

function getPartOfUrl($part = 0, $theURL = null)
{
    $theURL = $theURL ?? @$_SERVER['REQUEST_URI'];

    if (!$theURL) return false;

    $str = $theURL;
    $url = parse_url($str);
    $path = @$url['path'];

    if (!$path) return false;

    return getElementByExplodePart($path, $part);
}

function optionRadioElement($key, $element, $selected)
{
    return "<input class=\"{$element['class']}\" data-group-id=\"{$element['name']}\" data-label=\"{$element['label']}\" name=\"{$element['name']}\" type=\"radio\" id=\"{$element['id']}\" value=\"{$element['value']}\"{$selected}>";
}

function getOptionRadioByArray($mArr, $index = -1, $cbk = null)
{
    $i = 0;
    $optionList = [];
    foreach ($mArr as $key => $element) {

        $str  = "";

        $selected = $key == $index ? " checked" : "";

        $name = $element['name'] ?? $key;
        $class = $element['class'] ?? $name . "-cls";
        $id = $element['id'] ?? $name . "-id";
        $value = $element['value'] ?? $name;

        // finalization 
        $element["name"] = $name;
        $element["class"] = $class;
        $element["id"] = $id;
        $element["value"] = $value;

        if (is_callable($cbk)) {
            $str = $cbk($key, $element, $selected);
        } else {
            $str = optionRadioElement($key, $element, $selected);
        }

        $optionList[] = $str;
        $i++;
    }

    return $optionList;
}

function callback_option_radio_bootstrap($key, $element, $selected)
{

    $icon = !empty($element['icon']) ? " <i class=\"{$element['icon']}\"></i></label>" : "";

    $cbk = $key . "_cbk_radio_child_element";
    $extraElement = is_callable($cbk) ? $cbk($key, $element, $selected) : "";


    $defaultLabel = $label = "<label class=\"form-check-label\" for=\"{$element['id']}\">{$element['label']}{$icon}</label>";
    $cbkLabel = $key . "_cbk_radio_label_element";
    $label = is_callable($cbkLabel) ? $cbkLabel($key, $element, $selected, $icon) : $defaultLabel;

    return "<div class=\"form-check mb-3\">"  .
        optionRadioElement($key, $element, $selected) .
        $label .
        $extraElement .
        "</div>";
}

/*
# sample
function getDeliveryTypeOptionRadio($index = -1, $list = [])
{
    $the_list = $list ?: getDeliveryType("*");
    return getOptionRadioByArray($the_list, $index, "callback_option_radio_bootstrap");
}

function getDeliveryType(string $export = "label")
{
    $list = [
        "peyk" => [
            "name" => "delivery",
            "class" => "form-check-input",
            "id" => "peyk",
            "value" => "peyk",
            "label" => "Peyk",
            "icon" => "bi bi-box-seam",
        ],

        "cod" => [
            "name" => "delivery",
            "class" => "form-check-input",
            "id" => "cod",
            "value" => "cod",
            "label" => "Cod",
            "icon" => "bi bi-person-workspace"
        ],
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}
*/

function getOptionByArray($mArr, $index = -1, $extraText = "")
{
    $i = 0;
    $optionList = [];
    foreach ($mArr as $key => $element) {

        if (is_array($index)) {
            $selected = in_array($key, $index) ? " selected" : "";
        } else {
            $selected = $key == $index ? " selected" : "";
        }
        $theText = $selected ? $extraText : "";

        $optionList[] = "<option{$selected} value=\"{$key}\">{$element}{$theText}</option>";
        $i++;
    }

    return $optionList;
}

function doModificationOnRoles($users_role)
{

    $result = $users_role;

    $listActionCbk = [
        // "admin" => function ($item_role) {


        //     return $item_role;
        // },
    ];

    foreach ($result as $key_role => &$item_role) {
        if (isset($listActionCbk[$key_role])) {
            $item_role = $listActionCbk[$key_role]($item_role);
        }
    }

    return $result;
}

function getUserRolesOption($index = -1, $list = [])
{
    $the_list = $list ?: getUserRoles();

    // force last element to show if index not set
    $index = $index == -1 || !$index ? array_key_last($the_list) : $index;

    return getOptionByArray($the_list, $index);
}

function OTPType(string $export = "label")
{
    $list = [
        "verify_email" => ["label" => "verify Email", "description" => __local("active your email enter your code below message")],
        "verify_phone" => ["label" => "verify Phone", "description" => __local("active your phone number enter your code below message")],
        "verify_phone_sign_in" => ["label" => "verify Phone for Sign-in", "description" => __local("Sign-in enter your code below message")],
        "reset_password" => ["label" => "reset Password", "description" => __local("reset your password enter your code below message")]
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function OTPTypeOption($index = -1)
{
    return getOptionByArray(OTPType(), $index);
}

function OTPVia()
{
    return ["sms" => "SMS", "email" => "Email", "notification" => "Notification"];
}

function OTPViaOption($index = -1)
{
    return getOptionByArray(OTPVia(), $index);
}

function getCommentsType()
{
    $list = getAllType('comment');
    $options = [];

    foreach ($list as $item) {
        $options[$item['slug']] = $item['label'];
    }

    return $options;
}

function getCommentsTypeOption($index = -1, $extraText = "", $list = [])
{
    $theList = $list ? $list : getCommentsType();

    return getOptionByArray($theList, $index, $extraText);
}

function getMenuLocation()
{
    $list = getAllType('menu');
    $options = [];

    foreach ($list as $item) {
        $options[$item['slug']] = $item['label'];
    }

    return $options;
}

function getMenuLocationOption($index = -1, $extraText = "")
{
    return getOptionByArray(getMenuLocation(), $index, $extraText);
}

function getFileGroupTypes()
{
    $allTypes = getAllType('group_type');
    $theGroupTypes = [];

    foreach ($allTypes as $theKey => $groupTypes) {
        $theGroupTypes[strtolower($theKey)] = __local(ucwords(strtolower($theKey)));
    }

    $theGroupTypes["misc"] = __local("Misc");

    return $theGroupTypes;
}

function getFileGroupTypesOption($index = -1)
{
    return getOptionByArray(getFileGroupTypes(), $index);
}

function getFileTypes()
{
    $allTypes = getAllType('group_type');
    $theAllTypes = [];

    foreach ($allTypes as $groupTypes) {
        $theKeys = array_keys($groupTypes);
        foreach ($theKeys as $theKey) {
            $theAllTypes[strtolower($theKey)] = ucwords(strtolower($theKey));
        }
    }

    return $theAllTypes;
}

function getFileTypesOption($index = -1)
{
    return getOptionByArray(getFileTypes(), $index);
}

function getSourceFile()
{
    return ["dashboard" => "Dashboard", "authorize" => "Authorize"];
}

function getSourceFileOption($index = -1)
{
    return getOptionByArray(getSourceFile(), $index);
}

function getStatusPage()
{
    return ["publish" => __local("Publish"), "draft" => __local("Draft")];
}

function getStatusPageOption($index = -1)
{
    return getOptionByArray(getStatusPage(), $index);
}

function getFormInputTypes(string $export = "label")
{

    $label_OK = __local("OK");
    $label_YES = __local("YES");

    $propertiesHtmlAttributes = '.extra-html-attributes-properties';

    $list = [
        "text" => [
            "label" => __local("text"),
            "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "select" => [
            "label" => __local("select"), "cbk" => [
                "onSelect" => "addTipForProperties($('{$propertiesHtmlAttributes}'), __local('to add option you can use sample code below') , '<pre class=\"bg-secondary p-2 rounded\" dir=\"ltr\"><code class=\"text-white\">options=\"{\"ir\":\"iran\",\"de\":\"germany\"}\"</code></pre>')",
                "onBlur" => "resetTipForProperties($('{$propertiesHtmlAttributes}'))",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "textarea" => [
            "label" => __local("textarea"), "cbk" => [
                "onSelect" => "addTipForProperties($('{$propertiesHtmlAttributes}'), __local('to add content for textarea you can use sample code below') , '<pre class=\"bg-secondary p-2 rounded\" dir=\"ltr\"><code class=\"text-white\">html_content=\"my content goes here\"</code></pre>');",
                "onBlur" => "resetTipForProperties($('{$propertiesHtmlAttributes}'));",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "url" => [
            "label" => __local("url"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "search" => [
            "label" => __local("search"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "number" => [
            "label" => __local("number"), "cbk" => [
                "onSelect" => "addTipForProperties($('{$propertiesHtmlAttributes}'), __local('to change to FLOAT you can use sample code below also regex will NUMBERTYPE_range:range1,range2') , '<pre class=\"bg-secondary p-2 rounded\" dir=\"ltr\"><code class=\"text-white\">_type=\"float\"</code></pre>');",
                "onBlur" => "resetTipForProperties($('{$propertiesHtmlAttributes}'));",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "checkbox" => [
            "label" => __local("checkbox"), "cbk" => [
                "onSelect" => "addTipForProperties($('{$propertiesHtmlAttributes}'), __local('to add value and other values you can use below sample') , '<pre class=\"bg-secondary p-2 rounded\" dir=\"ltr\"><code class=\"text-white\">value=\"{$label_OK}\" item_gr_1=\"[\"yes\",\"{$label_YES}\"]\" </code></pre>');",
                "onBlur" => "resetTipForProperties($('{$propertiesHtmlAttributes}'));",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "radio" => [
            "label" => __local("radio"), "cbk" => [
                "onSelect" => "addTipForProperties($('{$propertiesHtmlAttributes}'), __local('to add value and other values you can use below sample') , '<pre class=\"bg-secondary p-2 rounded\" dir=\"ltr\"><code class=\"text-white\">value=\"{$label_OK}\" item_gr_1=\"[\"yes\",\"{$label_YES}\"]\" </code></pre>');",
                "onBlur" => "resetTipForProperties($('{$propertiesHtmlAttributes}'));",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "color" => [
            "label" => __local("color"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "date" => [
            "label" => __local("date"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "time" => [
            "label" => __local("time"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "email" => [
            "label" => __local("email"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "file" => [
            "label" => __local("file"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "image" => [
            "label" => __local("image"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "month" => [
            "label" => __local("month"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "week" => [
            "label" => __local("week"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
        "range" => [
            "label" => __local("range"), "cbk" => [
                "onSelect" => "",
                "onBlur" => "",
                "onLoadData" => "loadFormTemplateData(tagName , details)",
                "onShowHtml" => "onShowHtmlFormGeneral"
            ],
        ],
    ];

    // append `ID` from `TAG NAME` to list
    foreach ($list as $itemKey => &$itemValue) {
        $itemValue['id'] = $itemKey;
    }

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getFormInputTypesOption($index = -1)
{
    return getOptionByArray(getFormInputTypes(), $index);
}

function getFormInputPropertiesRequired()
{
    $list = [
        "name",
        "type-input-form",
        "id-form",
    ];

    return $list;
}

function isFormInputPropertiesRequiredHtml($key)
{
    $result = "";

    $list = getFormInputPropertiesRequired();

    $result = in_array($key, $list) ? "required=\"required\"" : "";

    return $result;
}

function getFormInputPropertiesRequiredOption($index = -1)
{
    return getOptionByArray(getFormInputPropertiesRequired(), $index);
}

function getClonePage()
{
    return ["post_type_and_taxonomy_meta" => __local("Post Type") . " + " . __local("Taxonomy") . " + "  .  __local("Meta"), "post_type_and_taxonomy" => __local("Post Type") . " + " . __local("Taxonomy"), "post_type_and_meta" => __local("Post Type") . " + " . __local("Meta"), "post_type" => __local("Post Type")];
}

function getClonePageOption($index = -1)
{
    return getOptionByArray(getClonePage(), $index);
}

function getStatusComment()
{
    return ["pending" => __local("Pending"), "confirmed" => __local("Confirmed")];
}

function getStatusCommentOption($index = -1)
{
    return getOptionByArray(getStatusComment(), $index);
}

function getFormStatus()
{
    return ["pending" => __local("Pending"), "confirm" => __local("Confirm")];
}

function getFormStatusOption($index = -1)
{
    return getOptionByArray(getFormStatus(), $index);
}

function getAnswer()
{
    return ["no" => __local("NO"), "yes" => __local("YES"),];
}

function getAnswerOption($index = -1, $makeArrayValues = false)
{
    $list = getAnswer();

    if ($makeArrayValues) {
        $list = array_values($list);
    }

    return getOptionByArray($list, $index);
}

function getStatusUser($export = "label")
{
    $list = [
        "active" => [
            "label" => __local("Active"),
            "onAction" => function () {
                $data = getModelAndInputsByID("User");

                // check user permission for action active
                $status_res = checkUserStatusPermission('active');
                if (is_object($status_res)) {
                    return $status_res;
                }

                checkUpdateClientIDON($data['inputs'], $data['model']);
                return true;
            }
        ],
        "deactive" => [
            "label" => __local("Deactive"),
            "onAction" => function () {
                return true;
            }
        ],
        "deactive_block" => [
            "label" => __local("Deactive (Block)"),
            "onAction" => function () {
                $data = getModelAndInputsByID("User");

                // check user permission for action deactive_block
                $status_res = checkUserStatusPermission('deactive_block');
                if (is_object($status_res)) {
                    return $status_res;
                }

                $data['inputs']['status'] = 'deactive_block';
                $data['model']['status'] = 'deactive_block';
                checkClientIDByStatus($data['inputs'], $data['model']);
                return true;
            }
        ]
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getStatusUserOption($index = -1, $list = [])
{
    $the_list = $list ?: getStatusUser();

    // force deactive element to show if index not set
    $index = $index == -1 || !$index ? "deactive" : $index;

    return getOptionByArray($the_list, $index);
}

function getAccountTypeRegisteriation()
{
    return ["email" => __local("Email"), "phone" => __local("Phone")];
}

function getAccountTypeRegisteriationOption($index = -1, $list = [])
{
    $the_list = $list ?: getAccountTypeRegisteriation();

    // force deactive element to show if index not set
    $index = $index == -1 || !$index ? "deactive" : $index;

    return getOptionByArray($the_list, $index);
}

function getTermSlugs()
{
    return [
        "user" => __local("User Terms"),
        "privacy" => __local("Privacy Terms"),
    ];
}

function getTermSlugsOption($index = -1, $extraText = "")
{
    return getOptionByArray(getTermSlugs(), $index, $extraText);
}

function getPopupDisplayType(string $export = "label")
{
    $list = [
        "popup" => [
            "id" => 1,
            "label" => "Popup",
        ],
        "notification" => [
            "id" => 2,
            "label" => "Notification",
        ],
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getPopupDisplayTypeOption($index = -1, $extraText = "")
{
    return getOptionByArray(getPopupDisplayType(), $index, $extraText);
}

function getPopupCondition(string $export = "label")
{
    $list = [
        "exact" => [
            "id" => 1,
            "label" => "Exact URL Page",
            "type" => "url",
            "sample" => "/product/1/razer-stealth",
        ],
        "contain" => [
            "id" => 2,
            "label" => "Contain URL Page",
            "type" => "url",
            "sample" => "/product",
        ],
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getPopupConditionOption($index = -1, $extraText = "")
{
    return getOptionByArray(getPopupCondition(), $index, $extraText);
}

function getCookieActions(string $export = "label")
{
    $list = [
        "agree" => [
            "label" => __local("Agree"),
            "action" => "agree"
        ],
        "disagree" => [
            "label" => __local("Disagree"),
            "action" => "disagree"
        ]
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getCookieActionsOption($index = -1, $extraText = "")
{
    return getOptionByArray(getCookieActions(), $index, $extraText);
}

function getNewsletterType(string $export = "label")
{
    $list = [
        "email" => [
            "label" => __local("Email"),
        ]
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getNewsletterTypeOption($index = -1, $extraText = "")
{
    return getOptionByArray(getNewsletterType(), $index, $extraText);
}

function getHttpCodeRedirect()
{
    $list = [
        "301" => __local("Moved Permanently"),
        "302" => __local("Found (Previously \"Moved temporarily\")"),
        "303" => __local("See Other"),
        "304" => __local("Not Modified"),
        "307" => __local("Temporary Redirect"),
        "308" => __local("Permanent Redirect"),
    ];

    // add `http code` to `label`
    foreach ($list as $itemKey => &$itemValue) {
        $itemValue .= " - {$itemKey}";
    }

    return $list;
}

function getHttpCodeRedirectOption($index = -1)
{
    return getOptionByArray(getHttpCodeRedirect(), $index);
}

function getHistoryActionModelsName(string $export = "label")
{
    $list = [
        "App\Models\PostType" => [
            "label" => __local("PostType"),
            "class_name" => "App\Models\PostType",
            "observe" => "App\Observers\ModelObserver"
        ],

        "App\Models\Taxonomy" => [
            "label" => __local("Taxonomy"),
            "class_name" => "App\Models\Taxonomy",
            "observe" => "App\Observers\ModelObserver"
        ],

        "App\Models\Menu" => [
            "label" => __local("Menu"),
            "class_name" => "App\Models\Menu",
            "observe" => "App\Observers\ModelObserver"
        ],

        "App\Models\File" => [
            "label" => __local("File"),
            "class_name" => "App\Models\File",
            "observe" => "App\Observers\ModelObserver"
        ],

        "App\Models\Comment" => [
            "label" => __local("Comment"),
            "class_name" => "App\Models\Comment",
            "observe" => "App\Observers\ModelObserver"
        ],

        "App\Models\Newsletter" => [
            "label" => __local("Newsletter"),
            "class_name" => "App\Models\Newsletter",
            "observe" => "App\Observers\ModelObserver"
        ],

        "App\Models\User" => [
            "label" => __local("User"),
            "class_name" => "App\Models\User",
            "observe" => "App\Observers\ModelObserver"
        ],

        "App\Models\Option" => [
            "label" => __local("Option"),
            "class_name" => "App\Models\Option",
            "observe" => "App\Observers\ModelObserver"
        ],

    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getHistoryActionModelsNameOption($index = -1, $list = [], $extraText = "")
{
    $the_list = $list ?: getHistoryActionModelsName();
    return getOptionByArray($the_list, $index, $extraText);
}

function getHistoryActionTheAction(string $export = "label")
{
    $list = [
        "create" => [
            "label" => __local("Create"),
        ],

        "update" => [
            "label" => __local("Update"),
        ],

        "delete" => [
            "label" => __local("Delete"),
        ]
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getHistoryActionTheActionOption($index = -1, $list = [], $extraText = "")
{
    $the_list = $list ?: getHistoryActionTheAction();
    return getOptionByArray($the_list, $index, $extraText);
}

function getSeoPublicTypeMeta(string $export = "label")
{
    $list = [
        "App\Models\PostType" => [
            "label" => "PostType",
            "class_name" => "App\Models\PostType",
            "robot_index" => "*",
            "date_publish" => true, // show date modified
            "date_modify" => true, // show date published
            "published_by" => true, // show twitter author by x
        ],
        "App\Models\Taxonomy" => [
            "label" => "Taxonomy",
            "class_name" => "App\Models\Taxonomy",
            "robot_index" => "*",
            "date_publish" => false,
            "date_modify" => false,
            "published_by" => false,
        ],
        "App\Models\User" => [
            "label" => "User",
            "class_name" => "App\Models\User",
            "robot_index" => "*",
            "date_publish" => false,
            "date_modify" => false,
            "published_by" => false,
        ],
        "Home" => [
            "label" => "Home",
            "class_name" => "Home",
            "robot_index" => "*",
            "date_publish" => false,
            "date_modify" => true,
            "published_by" => false,
        ],
        "Search" => [
            "label" => "Search",
            "class_name" => "Search",
            "robot_index" => "noindex, follow",
            "date_publish" => false,
            "date_modify" => false,
            "published_by" => false,
        ],
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getSeoPublicTypeMetaOption($index = -1, $list = [], $extraText = "")
{
    $the_list = $list ?: getSeoPublicTypeMeta();
    return getOptionByArray($the_list, $index, $extraText);
}

function getSeoPublicTypeSchema(string $export = "label")
{
    $list = [
        "App\Models\PostType" => [
            "label" => "PostType",
            "class_name" => "App\Models\PostType",
            "callback" => "getSchemaSeoPostType",
        ],
        "App\Models\Taxonomy" => [
            "label" => "Taxonomy",
            "class_name" => "App\Models\Taxonomy",
            "callback" => "getSchemaSeoTaxonomy",
        ],
        "App\Models\User" => [
            "label" => "User",
            "class_name" => "App\Models\User",
            "callback" => "getSchemaSeoUser",
        ],
        "Home" => [
            "label" => "Home",
            "class_name" => "Home",
            "callback" => "getSchemaSeoHome",
        ],
        "Search" => [
            "label" => "Search",
            "class_name" => "Search",
            "callback" => "getSchemaSeoSearch",
        ],
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getSeoPublicTypeSchemaOption($index = -1, $list = [], $extraText = "")
{
    $the_list = $list ?: getSeoPublicTypeSchema();
    return getOptionByArray($the_list, $index, $extraText);
}

function getTitlePageAction(string $export = "label")
{
    $list = [
        "create" => [
            "label" => __local("Add"),
        ],

        "edit" => [
            "label" => __local("Edit"),
        ],

        "index" => [
            "label" => __local("List"),
        ],

        "show" => [
            "label" => __local("List"),
        ],

        "sign_up" => [
            "label" => __local("Sign Up"),
        ],

        "sign_in" => [
            "label" => __local("Sign in"),
        ],

        "password" => [
            "label" => __local("Password"),
        ],

        "verify" => [
            "label" => __local("Verify Account"),
        ],

        "user_create" => [
            "label" => __local("Generate by User"),
        ],
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getTitlePageActionOption($index = -1, $list = [], $extraText = "")
{
    $the_list = $list ?: getTitlePageAction();
    return getOptionByArray($the_list, $index, $extraText);
}

function getTitlePage(string $export = "label")
{
    $list = [
        "Overview" => [
            "label" => __local("Overview"),
            "x-type" => false,
        ],

        "PostType" => [
            "label" => __local("PostType"),
            "x-type" => true,
        ],

        "Taxonomy" => [
            "label" => __local("Taxonomy"),
            "x-type" => true,
        ],

        "Form" => [
            "label" => __local("Form"),
            "template" => true,
            "x-type" => false,
        ],

        "FormsSchema" => [
            "label" => __local("Form Schema"),
            "x-type" => false,
        ],

        "Menu" => [
            "label" => __local("Menu"),
            "x-type" => false,
        ],

        "View" => [
            "label" => __local("Views"),
            "x-type" => false,
        ],

        "File" => [
            "label" => __local("Files"),
            "x-type" => false,
        ],

        "Comment" => [
            "label" => __local("Comments"),
            "x-type" => true,
        ],

        "Newsletter" => [
            "label" => __local("Newsletter"),
            "x-type" => false,
        ],

        "User" => [
            "label" => __local("Users"),
            "x-type" => false,
        ],

        "Redirect" => [
            "label" => __local("Redirect"),
            "x-type" => false,
        ],

        "HistoryAction" => [
            "label" => __local("HistoryAction"),
            "x-type" => false,
        ],

        "Option" => [
            "label" => __local("Settings"),
            "x-type" => false,
        ],

        "Search" => [
            "label" => __local("search result : x-title"),
            "template" => true,
            "x-type" => false,
        ],

        "Home" => [
            "label" => "x-title",
            "template" => true,
            "x-type" => false,
        ],

    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getTitlePageOption($index = -1, $list = [], $extraText = "")
{
    $the_list = $list ?: getTitlePage();
    return getOptionByArray($the_list, $index, $extraText);
}

function getExportTypeRemote()
{
    $list = [
        "pdf" => __local("PDF"),
    ];
    return $list;
}

function getExportTypeRemoteOption($index = -1, $extraText = "")
{
    return getOptionByArray(getExportTypeRemote(), $index, $extraText);
}

function getMaxDepthComment()
{
    return [0, 1, 5, 10];
}

function getCustom403Content()
{
    return file_get_contents(getErrorViewPath("403.cs.blade.php"));
}

function getMaxDepthCommentOption($index = -1)
{
    return getOptionByArray(getMaxDepthComment(), $index);
}

function getDashboardViewPath($path = '')
{
    return realpath(resource_path('views/dashboard/' . $path));
}

function getFrontViewPath($path = '')
{
    return realpath(resource_path('views/frontend/' . $path));
}

function getErrorViewPath($path = '')
{
    return realpath(resource_path('views/errors/' . $path));
}

function accessUnaccessableProperty($obj, $prop)
{
    $reflection = new ReflectionClass($obj);
    $property = $reflection->getProperty($prop);
    $property->setAccessible(true);
    return $property->getValue($obj);
}

function get_remote_mime_type($url)
{

    $res = get_headers($url, true);
    $mime_type = @$res['Content-Type'];

    return $mime_type;
}

function image_to_base64($url, $prefix = true, $mime_type = null)
{

    $img_data = isBinary($url) ? $url : @file_get_contents($url);

    if (!$img_data) {
        return false;
    }

    $prefix_data = "";

    if ($prefix) {
        $mime_type = $mime_type ?: get_remote_mime_type($url);
        $prefix_data = "data:{$mime_type};base64,";
    }

    $base64 = $prefix_data . base64_encode($img_data);

    return $base64;
}

// for huge traffic env will disable to initial so to make it active should use this in config/database.php
function get_env_value($key)
{
    $env = file_get_contents(baseChangeSlashDirectory(base_path() . '/.env'));

    $env = explode("\n", $env);

    $search_key = $key . "=";
    $target_key = "";

    foreach ($env as $env_element) {
        if (is_int(strpos($env_element, $search_key))) {
            $target_key = str_replace($search_key, "", $env_element);
            break;
        }
    }

    return $target_key;
}

function baseClassHtmlActionInput($key)
{
    $inCommon = "d-block w-100 mt-3 btn float-right";

    $list = [
        "green" => "{$inCommon} btn-success",
        "yellow" => "{$inCommon} btn-warning",
        "blue" => "{$inCommon} btn-primary",
        "red" => "{$inCommon} btn-danger",
        "grey" => "{$inCommon} btn-secondary",
    ];

    $target = $list[$key] ?? "";

    return $target;
}

function createHtmlActionInput($action, $own_id, $form_id, $callback, $actionLabel, $label, $class = "d-block w-100 mt-3 btn btn-success float-right", $hasPrompt = true, $extraAttr = "")
{
    $dataPrompt = "";

    if ($hasPrompt) {
        $dataPrompt = "data-prompt=\"" . str_replace(["x-action", "x-text"], [$actionLabel, $label], __local("Do You Want To x-action this x-text")) . "\"";
    }

    $input = "<input data-id-form=\"#{$form_id}\" data-callback=\"{$callback}\" data-label=\"{$label}\" class=\"{$class}\" onclick=\"buttonFormAction(event)\" {$dataPrompt} data-action=\"{$action}\" id=\"{$own_id}\" type=\"button\" value=\"{$actionLabel}\" {$extraAttr}>";
    return $input;
}

function createHtmlActionInputSetStatus($action, $actionLabel, $typeLabel, $buttonType = "yellow", $hasPrompt = true, $extraAttr = "")
{
    $buttonInput = createHtmlActionInput($action, "set-status_action", "status-form", "setStatusFormActionClick", $actionLabel, $typeLabel, baseClassHtmlActionInput($buttonType), $hasPrompt, $extraAttr);
    return $buttonInput;
}

function getCurrentColorModeKey()
{
    $result = "color_mode";

    return $result;
}

function getCurrentColorMode()
{
    $result = "light";

    $currentUser = getCurrentUser();

    $fromCookie = $_COOKIE[getCurrentColorModeKey()] ?? "";

    if ($currentUser) {
        $_result = getExtraFromType($currentUser, getCurrentColorModeKey());

        if (!$_result) {
            $_result = $fromCookie;
        }
    } else {
        $_result = $fromCookie;
    }

    if ($_result) {
        $result = $_result;
    }


    return $result;
}

function getCurrentColorModeToggle($export = "KEY")
{

    $colorModes = getColorModes("*");
    $colorModesSub = array_keys($colorModes);
    $targetColorMode = toggleBetweenToItems(getCurrentColorMode(), $colorModesSub[0], $colorModesSub[1]);

    if ($export == "ALL") {
        $targetColorMode = $colorModes[$targetColorMode];
    }

    return $targetColorMode;
}

function getColorModes(string $export = "label")
{
    $list = [
        "light" => [
            "icon-class" => "bi-brightness-high-fill",
            "label" => __local("Light"),
        ],
        "dark" => [
            "icon-class" => "bi-moon-stars-fill",
            "label" => __local("Dark"),
        ],
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getColorModesOption($index = -1, $extraText = "")
{
    return getOptionByArray(getColorModes(), $index, $extraText);
}
