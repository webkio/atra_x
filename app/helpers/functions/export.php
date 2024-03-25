<?php

function exportTheData($callback, $data, $format)
{
    $format = $format ?? "excel";
    call_user_func($callback, $data, $format);
}

function getExportFormatList()
{
    $formatList = [
        "excel" => "generateXls",
        "pdf" => "generatePdf"
    ];

    return $formatList;
}

function formatExportToFunc($format)
{
    $formatList = getExportFormatList();

    $callback = $formatList[$format] ?? false;

    if (!$callback) die("NO CALLBACK");

    return $callback;
}

function getDomText($str)
{
    $iso = 'ISO-8859-1';
    $utf = 'UTF-8';
    $str = iconv($utf, $iso, $str);
    return $str;
}

function encodeEmojiCharactersToHtml($text)
{
    $encodedString = '';

    if (!$text) return $text;

    $length = mb_strlen($text, 'UTF-8');

    $convertCharacterToDecimal = function ($character) {

        $unicodeCodePoint = iconv('UTF-8', 'UTF-32BE', $character);
        $hexUnicodeCodePoint = bin2hex($unicodeCodePoint);
        $dec = hexdec($hexUnicodeCodePoint);
        return $dec;
    };

    $isInEmojiRange = function ($dec) {
        $status = false;

        $unicodeRanges = array(
            '1F600-1F64F',
            '1F300-1F5FF',
            '1F680-1F6FF',
            '2700-27BF',
        );

        foreach ($unicodeRanges as $range) {
            list($start, $end) = explode('-', $range);

            if ($start <= $dec && $dec <= $end) {
                $status = true;
                break;
            }
        }

        return $status;
    };

    for ($i = 0; $i < $length; $i++) {
        $character = mb_substr($text, $i, 1, 'UTF-8');

        $dec = $convertCharacterToDecimal($character);
        $isInEmojiRangeList = $isInEmojiRange($dec);

        if (!$isInEmojiRangeList) {
            $encodedString .= $character;
            continue;
        }

        $encodedString .= "&#x" . dechex($dec) . ";";
    }

    return $encodedString;
}

# ====> export types page list

function export_data_all_type_general($data, $format)
{
    $callback = formatExportToFunc($format);

    $dom = new DOMDocument('1.0', "UTF-8");
    @$dom->loadHTML($data);


    $th = $dom->getElementsByTagName("th");
    $tbody = $dom->getElementsByTagName("tbody")[0];
    $trBody = $tbody->getElementsByTagName("tr");

    $head = [];
    $body = [];

    $noPrintListTextContent = ["action"];
    $indexBreakList = [];

    for ($i = 0; $i < count($th); $i++) {
        $item = $th[$i];
        $text = getDomText($item->textContent);
        if (in_array(strtolower($text), $noPrintListTextContent)) {
            $indexBreakList[] = $i;
            continue;
        }
        $head[] = $text;
    }

    foreach ($trBody as $item) {
        $item = $item->getElementsByTagName("td");
        $tmpBody = [];
        foreach ($item as $i => $theTd) {
            if (in_array($i, $indexBreakList)) {
                continue;
            }
            $child = $theTd->childNodes;
            $theText = "";
            if (count($child)) {
                foreach ($child as $childIndex => $ch) {
                    $camma = ",";
                    if ($childIndex + 1 == count($child))
                        $camma = "";

                    $theText .= getDomText($ch->textContent) . $camma;
                }
            } else {
                $theText = getDomText($theTd->textContent);
            }


            $tmpBody[] = $theText;
        }

        $body[] = $tmpBody;
    }

    $callback($head, $body);
}

function export_data_post_type_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_taxonomy_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_form_schema_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_form_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_menu_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_view_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_file_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_comment_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_newsletter_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_user_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_redirect_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

function export_data_history_action_general($data, $format)
{
    export_data_all_type_general($data, $format);
}

# =====> excel

function getChrByAscii($num)
{
    $list = [
        "A" => 1,
        "B" => 2,
        "C" => 3,
        "D" => 4,
        "E" => 5,
        "F" => 6,
        "G" => 7,
        "H" => 8,
        "I" => 9,
        "J" => 10,
        "K" => 11,
        "L" => 12,
        "M" => 13,
        "N" => 14,
        "O" => 15,
        "P" => 16,
        "Q" => 17,
        "R" => 18,
        "S" => 19,
        "T" => 20,
        "U" => 21,
        "V" => 22,
        "W" => 23,
        "X" => 24,
        "Y" => 25,
        "Z" => 26
    ];

    $chr = "";
    $max = 26;
    $repeat = 0;
    while ($max < $num) {
        $num -= $max;
        $repeat++;
    }

    if ($repeat) {
        $chr .= getChrByAscii($repeat);
    }

    foreach ($list as $key => $ls) {
        if ($num === $ls) {
            $chr .= $key;
            break;
        }
    }

    return $chr;
}

function setHeader(int $number = 1)
{
    $stuffix = 1;
    return getChrByAscii($number) . $stuffix;
}

function setBody(string $header, int $number = 1)
{
    $header = preg_replace("/\d/", "", $header);
    $body = $header . $number + 1;
    return $body;
}

function generateXls(array $heads, array $rows)
{

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spreadsheet->getActiveSheet()->getStyle('1:1')->getFont()->setBold(true);
    $sheet = $spreadsheet->getActiveSheet();
    $headPos = [];

    // add head
    foreach ($heads as $key => $head) {
        $headPos[] = setHeader($key + 1);
        $pos = $headPos[$key];
        $sheet->setCellValue($pos, $head);
    }

    // add body
    foreach ($rows as $i1 => $row) {
        foreach ($row as $i2 => $element) {
            $sheet->setCellValue($headPos[$key], $head);
            if (!isset($headPos[$i2])) continue;
            $pos = setBody($headPos[$i2], $i1 + 1);
            $sheet->setCellValue($pos, $element);
        }
    }

    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $time = time();
    $filename = "data-{$time}.xlsx";
    $writer->save($filename);
    headerForForceDownload($filename, true);
}

# =======> end excel

# =======> pdf

// not ready for list export yet !!!
function generatePdf($str, $prefix = "report-", $isRtl = false, $forceDonwload = false)
{
    $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $font = [
        'fontDir' => array_merge($fontDirs, [
            ROOT . '/public/static/fonts/lato',
        ]),
        'fontdata' => $fontData + [
            'lato' => [
                'R' => 'lato-r.ttf',
                'B' => 'lato-b.ttf',
             
             
            ],
        ],
        'default_font' => 'lato',
    ];

    $mpdf = new \Mpdf\Mpdf($font);
    if ($isRtl)
        $mpdf->SetDirectionality('rtl');

    $mpdf->WriteHTML($str);

    $pdf_file_name = $prefix . getUniqueIDByTimestamp(randomInteger()) . ".pdf";

    $result = $mpdf->Output($pdf_file_name, "S");

    if ($forceDonwload) {
        file_put_contents($pdf_file_name, $result);
        headerForForceDownload($pdf_file_name, true);
        exit;
    }

    return $result;
}

# =======> END pdf