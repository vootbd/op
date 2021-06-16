<?php
use Jenssegers\Agent\Agent;

$agent = new Agent();

return [
    'LOC' => [
        'PUBLIC' => 'public/',
    ],
    'DEFAULT_MAX_LENGTH' => 100,
    "NOT_FOUND" => "images/common/not-found.jpg",
    "DEFAULT_DATE_FORMAT" => "Y.m.d H:i",
    "DEFAULT_DATE_FORMAT_INFLUNCER_PROFILE" => "Y/m/d H:i",
    "PAGINATION_NUM_FORMAT"=>"%02d",
    'UNASSIGN_DIRECTORY' => '1',
    "ROUTE_LIST" => ['HomePage', 'about', 'categories'],
    'NUMBERS' => [
        'ZERO' => 0,
        'ONE' => 1,
        'TWO' => 2,
        'THREE' => 3,
        'FOUR' => 4,
        'FIVE' => 5,
        'SIX' => 6,
        'SEVEN' => 7,
        'EIGHT' => 8,
        'NINE' => 9,
        'TEN' => 10,
    ],
    'IMG' => [
        'UPLOAD' => [
            'LOC' => [
                'BLOG_CATEGORY' => 'images/admin/category/featured/',
                'PREFECTURE' => 'images/admin/prefecture/featured/',
            ],
            'ERROR' => 'image_upload_error',
            'FAILED' => [
                'CATEGORY' => 'Category image upload failed.',
                'ARTICLE' => 'Article image upload failed.',
                'ROUTE' => 'Category image upload failed.',
                'PG_HEADER' => 'Page header image upload failed.',
            ],
        ],
        'MEDIA' => [
            'UPLOAD_PATH' => 'public/upload/medias/',
            'LOAD_PATH' => 'upload/medias/',
            'LOAD_PATH_MD' => 'upload/medias/md/',
            'LOAD_PATH_SM' => 'upload/medias/sm/'
        ],
        "LINE" => 'images/articles/Line.png',
        "NOT_FOUND" => "images/common/not-found-big.jpg",
    ],
    'USER_TYPE' => [
        "SUPER_ADMIN" => 1,
        "ADMIN" => 2,
        "ADMIN_USER" => 3,
        "USER" => 4,
    ],
    'USER_STATUS' => [
        "1" => "Active",
        "2" => "In-Active",
        "3" => "Blocked",
    ],
    'STATUS' => [
        "ACTIVE" => 1,
        "INACTIVE" => 2,
        "BLOCKED" => 3,
        "INVITED" => 4,
    ],
    'STATUS_NAME' => [
        1 => "ACTIVE",
        2 => "INACTIVE",
        3 => "BLOCKED",
        4 => "INVITED",
    ],
    "HOMEPAGE" => [
        "CATEGORIES_LIMIT" => 6,
        "CATEGORIES_LEFT_LIMIT" => 3,
        "CATEGORIES_RIGHT_LIMIT" => 6,
        "TOP_ARTICLES_DES_LIMIT" => $agent->isMobile() ? 71 : 200,
        "TOPICS_DES_LIMIT" => $agent->isMobile() ? 71 : 200,
        "TOP_ARTICLES_TITLE_LIMIT" => 80,
        "TOPIC_TITLE_LIMIT" => 80,
        'PREFECTURE_IS_MOBILE' => 1,
        "PREFECTURE_DES_LIMIT" => 400,
        "PREFECTURE_DES_LIMIT_SP" => 125,
        "PREFECTURE_DES_LIMIT_SE" => 25,
        "SUB_LENGTH" => 1,
        "PREVIOUS_NEXT_TEXT_LIMIT" =>6,
        "INFLUENCER_ARTICLE_LIMIT" => 4,
        "READ_MORE_LIMIT" => 100,
    ],
    "MAX_RATING_LIMIT" => 5,
    'SUCCESS_STATUS' => 'success',
    'FAILED_STATUS' => 'failed',
    "RIGHT_ARROW_IMG" => "/images/top/Arrow_right.svg",
    "EDIT_ICON" => "/images/edit_icon.svg",
    "LINE" => "/images/line.svg",
    "COVER_DASH" => "/images/eye_catch_cover_dash.svg",
    "NETWORK_SUCCESS_STATUS" => 200,
    "GOOGLE_MAP" => 'http://maps.google.com/?q=',
    "CSV"=>[
        "FILE_PATH"=> 'public/csvformat/'
    ],
    'DIRECTORY_UNASSIGN' => [
        'id'=> 1,
        'name'=> 'unassigned'
    ],
    'DIRECTORY_ROOT' => [
        'id'=> 2,
        'name'=> '/'
    ]
];
