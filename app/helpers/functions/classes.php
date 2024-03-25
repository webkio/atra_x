<?php

class GenerateSeoFriendlyURL
{

    public $url = "";
    public $patterns = [
        "/[`_\"'\/\\\\]{1,}/i",
        "/[\s+*&^%$#@=.,?!-]{2,}/i",
    ];

    function __construct(string $url, int $limit, $patterns = [])
    {
        if ($patterns) {
            $this->patterns = $patterns;
        }
        $this->url = $this->generateUrl($url);
        $this->url = $this->limitWords($this->url, $limit);
    }

    function generateUrl(string $url)
    {
        $newUrl = $url;

        foreach ($this->patterns as $pattern) {
            $newUrl = preg_replace($pattern, " ", $newUrl);
        }

        $newUrl = trim($newUrl);
        $newUrl = str_replace(" ", "-", $newUrl);
        $newUrl = strtolower($newUrl);

        return $newUrl;
    }

    function limitWords(string $url, int $limit = 6)
    {

        if ($limit < 2) $limit = 2;

        $urlList = explode("-", $url);
        $urlList = array_slice($urlList, 0, $limit);
        $newUrl = join("-", $urlList);
        return $newUrl;
    }

    function getUrl()
    {
        return $this->url;
    }
}

class SchemaSeoCreator
{

    private $json = [];

    public function __construct($addContext = true)
    {
        if ($addContext)
            $this->addElement("https://schema.org", "@context");
    }

    public function addElement($element, $key = null, $replace = true)
    {
        if (is_null($key)) {
            $this->json[] = $element;
        } else {
            if ($replace)
                $this->json[$key] = $element;
            else
                $this->json[$key][] = $element;
        }
    }

    public function getJson($flags = 0)
    {
        return json_encode($this->json, $flags);
    }

    // Partials
    public static function addElementPartialAuthor(object &$Json, $data, $otherEntity = "")
    {
        $theList = [
            "@type" => "Person",
            "@id" => "{$data['root_url']}/#/schema/person/" . md5($data['author_name']),
            "name" => $data['author_name'],
            "image" => [
                "@type" => "ImageObject",
                "inLanguage" => $data['language'],
                "@id" => "{$data['root_url']}/#/schema/person/image/",
                "url" => "{$data['author_thumb_url']}/",
                "contentUrl" => "{$data['author_thumb_url']}/",
                "caption" => $data['author_name'],
            ],
        ];

        if ($otherEntity) {
            $theList[$otherEntity] = [
                "@id" => "{$data['author_url']}/#webpage"
            ];
        } else {
            $theList["url"] = "{$data['author_url']}/";
        }

        $Json->addElement($theList, "@graph", false);
    }

    public static function addElementPartialSiteInfo(object &$Json, &$data)
    {

        $data["root_url"] = ROOT_URL;
        $data["site_name"] = $GLOBALS['site_name'];
        $data["language"] = $GLOBALS['site_language'];
        $data["site_description_short"] = getTypeExcerpt($GLOBALS['site_description_raw'], 5, false, 12);

        $Json->addElement([
            "@type" => "WebSite",
            "@id" => "{$data['root_url']}/#website",
            "url" => "{$data['root_url']}",
            "name" => "{$data['site_name']}",
            "description" => "{$data['site_description_short']}", // 5 word
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => "{$data['root_url']}/search/{search_term_string}",
                "query-input" => "required name=search_term_string",
            ],
            "inLanguage" => "{$data['language']}",
        ], "@graph", false);
    }

    // Templates
    public static function getThingBreadcrumb(array $data, $addContext = true)
    {
        $Json = new SchemaSeoCreator($addContext);
        $Json->addElement("BreadcrumbList", "@type");

        $Json->addElement([], "itemListElement");

        foreach ($data as $i => $item) {
            $Json->addElement([
                "@type" => "ListItem",
                "position" => $i,
                "item" => [
                    "@type" => "WebPage",
                    "@id" => $item['id'],
                    "url" => $item['url'],
                    "name" => $item['name'],
                ]
            ], "itemListElement", false);
        }

        return $Json->getJson();
    }

    public static function getThingOrganization(array $data, $addContext = true)
    {
        $Json = new SchemaSeoCreator($addContext);
        $Json->addElement([], "@graph");

        // STAGE 1
        self::addElementPartialSiteInfo($Json, $data);

        // STAGE 2
        $Json->addElement([
            "@type" => "CollectionPage",
            "@id" => "{$data['root_url']}/#webpage",
            "url" => "{$data['root_url']}/",
            "name" => "{$data['title']}",
            "isPartOf" => [
                "@id" => "{$data['root_url']}/#website"
            ],
            "description" => $data['site_description_ideal'],
            "breadcrumb" => [
                "@id" => "{$data['root_url']}/#breadcrumb"
            ],
            "inLanguage" => $data['language'],
            "potentialAction" => [
                [
                    "@type" => "ReadAction",
                    "target" => [
                        "{$data['root_url']}/"
                    ]
                ]
            ],
        ], "@graph", false);

        // STAGE breadcrumb
        $Json->addElement(
            [
                "@type" => "BreadcrumbList",
                "@id" => "{$data['root_url']}/#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "home"
                    ]
                ]

            ],
            "@graph",
            false
        );

        return $Json->getJson();
    }

    public static function getThingPostSingle(array $data, $addContext = true)
    {
        $Json = new SchemaSeoCreator($addContext);
        $Json->addElement([], "@graph");

        // STAGE 1
        self::addElementPartialSiteInfo($Json, $data);

        // STAGE 2
        if (isset($data['thumb_url'])) {
            $Json->addElement([
                "@type" => "ImageObject",
                "inLanguage" => $data['language'],
                "@id" => "{$data['url']}/#primaryimage",
                "url" => "{$data['thumb_url']}/",
                "contentUrl" => "{$data['thumb_url']}/",
                "width" => intval($data['thumb_width']),
                "height" => intval($data['thumb_height']),
                "caption" => "{$data['post_title']}",
            ], "@graph", false);
        }

        // STAGE 3
        $Json->addElement([
            "@type" => "WebPage",
            "@id" => "{$data['url']}/#webpage",
            "url" => "{$data['url']}/",
            "name" => "{$data['title']}",
            "isPartOf" => [
                "@id" => "{$data['root_url']}/#website"
            ],
            "primaryImageOfPage" => [
                "@id" => "{$data['url']}/#primaryimage"
            ],
            "datePublished" => $data['post_date_published'],
            "dateModified" => $data['post_date_modified'],
            "author" => [
                "@id" => "{$data['root_url']}/#/schema/person/" . md5(isset($data['author_name']) ? $data['author_name'] : ""),
            ],
            "description" => $data['site_description_ideal'],
            "breadcrumb" => [
                "@id" => "{$data['url']}/#breadcrumb"
            ],
            "inLanguage" => $data['language'],
            "potentialAction" => [
                [
                    "@type" => "ReadAction",
                    "target" => [
                        "{$data['url']}/"
                    ]
                ]
            ],
        ], "@graph", false);

        // STAGE breadcrumb
        $Json->addElement(
            [
                "@type" => "BreadcrumbList",
                "@id" => "{$data['url']}/#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "home",
                        "item" => "{$data['root_url']}/"
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => $data['post_title'],
                    ]
                ]

            ],
            "@graph",
            false
        );

        // STAGE author
        if (isset($data['author_thumb_url'])) {
            self::addElementPartialAuthor($Json, $data);
        }

        return $Json->getJson();
    }

    public static function getThingAuthor(array $data, $addContext = true)
    {
        $Json = new SchemaSeoCreator($addContext);
        $Json->addElement([], "@graph");

        // STAGE 1
        self::addElementPartialSiteInfo($Json, $data);

        // STAGE 2
        $Json->addElement([
            "@type" => "ProfilePage",
            "@id" => "{$data['author_url']}/#webpage",
            "url" => "{$data['author_url']}/",
            "name" => "{$data['author_name']} author in {$data['site_name']}",
            "isPartOf" => [
                "@id" => "{$data['root_url']}/#website"
            ],
            "breadcrumb" => [
                "@id" => "{$data['author_url']}/#breadcrumb"
            ],
            "inLanguage" => $data['language'],
            "potentialAction" => [
                [
                    "@type" => "ReadAction",
                    "target" => [
                        "{$data['author_url']}/"
                    ]
                ]
            ],

        ], "@graph", false);

        // STAGE breadcrumb
        $Json->addElement(
            [
                "@type" => "BreadcrumbList",
                "@id" => "{$data['author_url']}/#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "home",
                        "item" => "{$data['root_url']}/"
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => "archive {$data['author_name']}",
                    ]
                ]

            ],
            "@graph",
            false
        );

        // STAGE author
        if (isset($data['author_thumb_url'])) {
            self::addElementPartialAuthor($Json, $data, "mainEntityOfPage");
        }

        return $Json->getJson();
    }

    public static function getThingTaxonomy(array $data, $addContext = true)
    {
        $Json = new SchemaSeoCreator($addContext);
        $Json->addElement([], "@graph");

        // STAGE 1
        self::addElementPartialSiteInfo($Json, $data);

        // STAGE 2
        $Json->addElement([
            "@type" => "CollectionPage",
            "@id" => "{$data['url']}/#webpage",
            "url" => "{$data['url']}/",
            "name" => "{$data['title']}",
            "isPartOf" => [
                "@id" => "{$data['root_url']}/#website"
            ],
            "description" => $data['site_description_ideal'],
            "breadcrumb" => [
                "@id" => "{$data['url']}/#breadcrumb"
            ],
            "inLanguage" => $data['language'],
            "potentialAction" => [
                [
                    "@type" => "ReadAction",
                    "target" => [
                        "{$data['url']}/"
                    ]
                ]
            ],
        ], "@graph", false);

        // STAGE breadcrumb
        $Json->addElement(
            [
                "@type" => "BreadcrumbList",
                "@id" => "{$data['url']}/#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "home",
                        "item" => "{$data['root_url']}/"
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => $data['taxonomy_name'],
                    ]
                ]

            ],
            "@graph",
            false
        );

        return $Json->getJson();
    }

    public static function getThingSearchResult(array $data, $addContext = true)
    {
        $Json = new SchemaSeoCreator($addContext);
        $Json->addElement([], "@graph");

        // STAGE 1
        self::addElementPartialSiteInfo($Json, $data);

        // STAGE 2
        $Json->addElement([
            "@type" => ["CollectionPage", "SearchResultsPage"],
            "@id" => "#webpage",
            "url" => "{$data['search_url']}/",
            "name" => "{$data['title']}",
            "isPartOf" => [
                "@id" => "{$data['root_url']}/#website"
            ],
            "breadcrumb" => [
                "@id" => "#breadcrumb"
            ],
            "inLanguage" => $data['language'],
            "potentialAction" => [
                [
                    "@type" => "ReadAction",
                    "target" => [
                        "{$data['search_url']}/"
                    ]
                ]
            ],
        ], "@graph", false);

        // STAGE breadcrumb
        $Json->addElement(
            [
                "@type" => "BreadcrumbList",
                "@id" => "#breadcrumb",
                "itemListElement" => [
                    [
                        "@type" => "ListItem",
                        "position" => 1,
                        "name" => "home",
                        "item" => $data['root_url']
                    ],
                    [
                        "@type" => "ListItem",
                        "position" => 2,
                        "name" => $data['search_title'],

                    ]
                ]

            ],
            "@graph",
            false
        );

        return $Json->getJson();
    }

    public static function getThingOrganizationOnly($data, $addContext = true)
    {
        $Json = new SchemaSeoCreator($addContext);
        $Json->addElement([], "@graph");

        // STAGE 1
        self::addElementPartialSiteInfo($Json, $data);

        return $Json->getJson();
    }
}

?>