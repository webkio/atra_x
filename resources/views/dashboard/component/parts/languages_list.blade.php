<?php

function getLanguageList(string $export = "label")
{
    $list = [
        "af" => [
            "label" => "Afrikaans",
            "direction" => "ltr",
        ],
        "sq" => [
            "label" => "Albanian _ shqip",
            "direction" => "ltr",
        ],
        "am" => [
            "label" => "Amharic",
            "direction" => "ltr",
        ],
        "ar" => [
            "label" => "Arabic _ العربية",
            "direction" => "rtl",
        ],
        "an" => [
            "label" => "Aragonese _ aragonés",
            "direction" => "ltr",
        ],
        "hy" => [
            "label" => "Armenian _ հայերեն",
            "direction" => "ltr",
        ],
        "ast" => [
            "label" => "Asturian _ asturianu",
            "direction" => "ltr",
        ],
        "az" => [
            "label" => "Azerbaijani _ azərbaycan dili",
            "direction" => "ltr",
        ],
        "eu" => [
            "label" => "Basque _ euskara",
            "direction" => "ltr",
        ],
        "be" => [
            "label" => "Belarusian _ беларуская",
            "direction" => "ltr",
        ],
        "bn" => [
            "label" => "Bengali _ বাংলা",
            "direction" => "ltr",
        ],
        "bs" => [
            "label" => "Bosnian _ bosanski",
            "direction" => "ltr",
        ],
        "br" => [
            "label" => "Breton _ brezhoneg",
            "direction" => "ltr",
        ],
        "bg" => [
            "label" => "Bulgarian _ български",
            "direction" => "ltr",
        ],
        "ca" => [
            "label" => "Catalan _ català",
            "direction" => "ltr",
        ],
        "ckb" => [
            "label" => "Central Kurdish _ کوردی (دەستنوسی عەرەبی)",
            "direction" => "ltr",
        ],
        "zh" => [
            "label" => "Chinese _ 中文",
            "direction" => "ltr",
        ],
        "zh_HK" => [
            "label" => "Chinese (Hong Kong) _ 中文（香港）",
            "direction" => "ltr",
        ],
        "zh_CN" => [
            "label" => "Chinese (Simplified) _ 中文（简体）",
            "direction" => "ltr",
        ],
        "zh_TW" => [
            "label" => "Chinese (Traditional) _ 中文（繁體）",
            "direction" => "ltr",
        ],
        "co" => [
            "label" => "Corsican",
            "direction" => "ltr",
        ],
        "hr" => [
            "label" => "Croatian _ hrvatski",
            "direction" => "ltr",
        ],
        "cs" => [
            "label" => "Czech _ čeština",
            "direction" => "ltr",
        ],
        "da" => [
            "label" => "Danish _ dansk",
            "direction" => "ltr",
        ],
        "nl" => [
            "label" => "Dutch _ Nederlands",
            "direction" => "ltr",
        ],
        "en" => [
            "label" => "English",
            "direction" => "ltr",
        ],
        "en_AU" => [
            "label" => "English (Australia)",
            "direction" => "ltr",
        ],
        "en_CA" => [
            "label" => "English (Canada)",
            "direction" => "ltr",
        ],
        "en_IN" => [
            "label" => "English (India)",
            "direction" => "ltr",
        ],
        "en_NZ" => [
            "label" => "English (New Zealand)",
            "direction" => "ltr",
        ],
        "en_ZA" => [
            "label" => "English (South Africa)",
            "direction" => "ltr",
        ],
        "en_GB" => [
            "label" => "English (United Kingdom)",
            "direction" => "ltr",
        ],
        "en_US" => [
            "label" => "English (United States)",
            "direction" => "ltr",
        ],
        "eo" => [
            "label" => "Esperanto _ esperanto",
            "direction" => "ltr",
        ],
        "et" => [
            "label" => "Estonian _ eesti",
            "direction" => "ltr",
        ],
        "fo" => [
            "label" => "Faroese _ føroyskt",
            "direction" => "ltr",
        ],
        "fil" => [
            "label" => "Filipino",
            "direction" => "ltr",
        ],
        "fi" => [
            "label" => "Finnish _ suomi",
            "direction" => "ltr",
        ],
        "fr" => [
            "label" => "French _ français",
            "direction" => "ltr",
        ],
        "fr_CA" => [
            "label" => "French (Canada) _ français (Canada)",
            "direction" => "ltr",
        ],
        "fr_FR" => [
            "label" => "French (France) _ français (France)",
            "direction" => "ltr",
        ],
        "fr_CH" => [
            "label" => "French (Switzerland) _ français (Suisse)",
            "direction" => "ltr",
        ],
        "gl" => [
            "label" => "Galician _ galego",
            "direction" => "ltr",
        ],
        "ka" => [
            "label" => "Georgian _ ქართული",
            "direction" => "ltr",
        ],
        "de" => [
            "label" => "German _ Deutsch",
            "direction" => "ltr",
        ],
        "de_AT" => [
            "label" => "German (Austria) _ Deutsch (Österreich)",
            "direction" => "ltr",
        ],
        "de_DE" => [
            "label" => "German (Germany) _ Deutsch (Deutschland)",
            "direction" => "ltr",
        ],
        "de_LI" => [
            "label" => "German (Liechtenstein) _ Deutsch (Liechtenstein)",
            "direction" => "ltr",
        ],
        "de_CH" => [
            "label" => "German (Switzerland) _ Deutsch (Schweiz)",
            "direction" => "ltr",
        ],
        "el" => [
            "label" => "Greek _ Ελληνικά",
            "direction" => "ltr",
        ],
        "gn" => [
            "label" => "Guarani",
            "direction" => "ltr",
        ],
        "gu" => [
            "label" => "Gujarati _ ગુજરાતી",
            "direction" => "ltr",
        ],
        "ha" => [
            "label" => "Hausa",
            "direction" => "ltr",
        ],
        "haw" => [
            "label" => "Hawaiian _ ʻŌlelo Hawaiʻi",
            "direction" => "ltr",
        ],
        "he" => [
            "label" => "Hebrew _ עברית",
            "direction" => "ltr",
        ],
        "hi" => [
            "label" => "Hindi _ हिन्दी",
            "direction" => "ltr",
        ],
        "hu" => [
            "label" => "Hungarian _ magyar",
            "direction" => "ltr",
        ],
        "is" => [
            "label" => "Icelandic _ íslenska",
            "direction" => "ltr",
        ],
        "id" => [
            "label" => "Indonesian _ Indonesia",
            "direction" => "ltr",
        ],
        "ia" => [
            "label" => "Interlingua",
            "direction" => "ltr",
        ],
        "ga" => [
            "label" => "Irish _ Gaeilge",
            "direction" => "ltr",
        ],
        "it" => [
            "label" => "Italian _ italiano",
            "direction" => "ltr",
        ],
        "it_IT" => [
            "label" => "Italian (Italy) _ italiano (Italia)",
            "direction" => "ltr",
        ],
        "it_CH" => [
            "label" => "Italian (Switzerland) _ italiano (Svizzera)",
            "direction" => "ltr",
        ],
        "ja" => [
            "label" => "Japanese _ 日本語",
            "direction" => "ltr",
        ],
        "kn" => [
            "label" => "Kannada _ ಕನ್ನಡ",
            "direction" => "ltr",
        ],
        "kk" => [
            "label" => "Kazakh _ қазақ тілі",
            "direction" => "ltr",
        ],
        "km" => [
            "label" => "Khmer _ ខ្មែរ",
            "direction" => "ltr",
        ],
        "ko" => [
            "label" => "Korean _ 한국어",
            "direction" => "ltr",
        ],
        "ku" => [
            "label" => "Kurdish _ Kurdî",
            "direction" => "ltr",
        ],
        "ky" => [
            "label" => "Kyrgyz _ кыргызча",
            "direction" => "ltr",
        ],
        "lo" => [
            "label" => "Lao _ ລາວ",
            "direction" => "ltr",
        ],
        "la" => [
            "label" => "Latin",
            "direction" => "ltr",
        ],
        "lv" => [
            "label" => "Latvian _ latviešu",
            "direction" => "ltr",
        ],
        "ln" => [
            "label" => "Lingala _ lingála",
            "direction" => "ltr",
        ],
        "lt" => [
            "label" => "Lithuanian _ lietuvių",
            "direction" => "ltr",
        ],
        "mk" => [
            "label" => "Macedonian _ македонски",
            "direction" => "ltr",
        ],
        "ms" => [
            "label" => "Malay _ Bahasa Melayu",
            "direction" => "ltr",
        ],
        "ml" => [
            "label" => "Malayalam _ മലയാളം",
            "direction" => "ltr",
        ],
        "mt" => [
            "label" => "Maltese _ Malti",
            "direction" => "ltr",
        ],
        "mr" => [
            "label" => "Marathi _ मराठी",
            "direction" => "ltr",
        ],
        "mn" => [
            "label" => "Mongolian _ монгол",
            "direction" => "ltr",
        ],
        "ne" => [
            "label" => "Nepali _ नेपाली",
            "direction" => "ltr",
        ],
        "no" => [
            "label" => "Norwegian _ norsk",
            "direction" => "ltr",
        ],
        "nb" => [
            "label" => "Norwegian Bokmål _ norsk bokmål",
            "direction" => "ltr",
        ],
        "nn" => [
            "label" => "Norwegian Nynorsk _ nynorsk",
            "direction" => "ltr",
        ],
        "oc" => [
            "label" => "Occitan",
            "direction" => "ltr",
        ],
        "or" => [
            "label" => "Oriya _ ଓଡ଼ିଆ",
            "direction" => "ltr",
        ],
        "om" => [
            "label" => "Oromo _ Oromoo",
            "direction" => "ltr",
        ],
        "ps" => [
            "label" => "Pashto _ پښتو",
            "direction" => "rtl",
        ],
        "fa_IR" => [
            "label" => "Persian _ فارسی",
            "direction" => "rtl",
        ],
        "pl" => [
            "label" => "Polish _ polski",
            "direction" => "ltr",
        ],
        "pt" => [
            "label" => "Portuguese _ português",
            "direction" => "ltr",
        ],
        "pt_BR" => [
            "label" => "Portuguese (Brazil) _ português (Brasil)",
            "direction" => "ltr",
        ],
        "pt_PT" => [
            "label" => "Portuguese (Portugal) _ português (Portugal)",
            "direction" => "ltr",
        ],
        "pa" => [
            "label" => "Punjabi _ ਪੰਜਾਬੀ",
            "direction" => "ltr",
        ],
        "qu" => [
            "label" => "Quechua",
            "direction" => "ltr",
        ],
        "ro" => [
            "label" => "Romanian _ română",
            "direction" => "ltr",
        ],
        "mo" => [
            "label" => "Romanian (Moldova) _ română (Moldova)",
            "direction" => "ltr",
        ],
        "rm" => [
            "label" => "Romansh _ rumantsch",
            "direction" => "ltr",
        ],
        "ru" => [
            "label" => "Russian _ русский",
            "direction" => "ltr",
        ],
        "gd" => [
            "label" => "Scottish Gaelic",
            "direction" => "ltr",
        ],
        "sr" => [
            "label" => "Serbian _ српски",
            "direction" => "ltr",
        ],
        "sh" => [
            "label" => "Serbo_Croatian _ Srpskohrvatski",
            "direction" => "ltr",
        ],
        "sn" => [
            "label" => "Shona _ chiShona",
            "direction" => "ltr",
        ],
        "sd" => [
            "label" => "Sindhi",
            "direction" => "ltr",
        ],
        "si" => [
            "label" => "Sinhala _ සිංහල",
            "direction" => "ltr",
        ],
        "sk" => [
            "label" => "Slovak _ slovenčina",
            "direction" => "ltr",
        ],
        "sl" => [
            "label" => "Slovenian _ slovenščina",
            "direction" => "ltr",
        ],
        "so" => [
            "label" => "Somali _ Soomaali",
            "direction" => "ltr",
        ],
        "st" => [
            "label" => "Southern Sotho",
            "direction" => "ltr",
        ],
        "es" => [
            "label" => "Spanish _ español",
            "direction" => "ltr",
        ],
        "es_AR" => [
            "label" => "Spanish (Argentina) _ español (Argentina)",
            "direction" => "ltr",
        ],
        "es_419" => [
            "label" => "Spanish (Latin America) _ español (Latinoamérica)",
            "direction" => "ltr",
        ],
        "es_MX" => [
            "label" => "Spanish (Mexico) _ español (México)",
            "direction" => "ltr",
        ],
        "es_ES" => [
            "label" => "Spanish (Spain) _ español (España)",
            "direction" => "ltr",
        ],
        "es_US" => [
            "label" => "Spanish (United States) _ español (Estados Unidos)",
            "direction" => "ltr",
        ],
        "su" => [
            "label" => "Sundanese",
            "direction" => "ltr",
        ],
        "sw" => [
            "label" => "Swahili _ Kiswahili",
            "direction" => "ltr",
        ],
        "sv" => [
            "label" => "Swedish _ svenska",
            "direction" => "ltr",
        ],
        "tg" => [
            "label" => "Tajik _ тоҷикӣ",
            "direction" => "ltr",
        ],
        "ta" => [
            "label" => "Tamil _ தமிழ்",
            "direction" => "ltr",
        ],
        "tt" => [
            "label" => "Tatar",
            "direction" => "ltr",
        ],
        "te" => [
            "label" => "Telugu _ తెలుగు",
            "direction" => "ltr",
        ],
        "th" => [
            "label" => "Thai _ ไทย",
            "direction" => "ltr",
        ],
        "ti" => [
            "label" => "Tigrinya _ ትግርኛ",
            "direction" => "ltr",
        ],
        "to" => [
            "label" => "Tongan _ lea fakatonga",
            "direction" => "ltr",
        ],
        "tr" => [
            "label" => "Turkish _ Türkçe",
            "direction" => "ltr",
        ],
        "tk" => [
            "label" => "Turkmen",
            "direction" => "ltr",
        ],
        "tw" => [
            "label" => "Twi",
            "direction" => "ltr",
        ],
        "uk" => [
            "label" => "Ukrainian _ українська",
            "direction" => "ltr",
        ],
        "ur" => [
            "label" => "Urdu _ اردو",
            "direction" => "ltr",
        ],
        "ug" => [
            "label" => "Uyghur",
            "direction" => "ltr",
        ],
        "uz" => [
            "label" => "Uzbek _ o‘zbek",
            "direction" => "ltr",
        ],
        "vi" => [
            "label" => "Vietnamese _ Tiếng Việt",
            "direction" => "ltr",
        ],
        "wa" => [
            "label" => "Walloon _ wa",
            "direction" => "ltr",
        ],
        "cy" => [
            "label" => "Welsh _ Cymraeg",
            "direction" => "ltr",
        ],
        "fy" => [
            "label" => "Western Frisian",
            "direction" => "ltr",
        ],
        "xh" => [
            "label" => "Xhosa",
            "direction" => "ltr",
        ],
        "yi" => [
            "label" => "Yiddish",
            "direction" => "ltr",
        ],
        "yo" => [
            "label" => "Yoruba _ Èdè Yorùbá",
            "direction" => "ltr",
        ],
        "zu" => [
            "label" => "Zulu _ isiZulu",
            "direction" => "ltr",
        ],
    ];

    $data = getArrayElementForOption($list, $export);

    return $data;
}

function getLanguageListOption($index = -1, $extraText = "")
{
    return getOptionByArray(getLanguageList(), $index, $extraText);
}

?>
