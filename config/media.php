<?php

use App\Domain\File\Enums\FileTypeEnum;

return [

    'path' => 'images',

    'max_upload_file_size' => [
        'default' => env('MEDIA_DEFAULT_MAX_UPLOAD_FILE_SIZE', 5),
        'size_in' => env('MEDIA_UPLOAD_FILE_SIZE_TYPE', 'MB')
    ],

    'allowed_mime_types' => [

//        FileTypeEnum::IMAGE->value => [
//            'image/jpeg',
//            'image/jpg',
//            'image/webp',
//            'image/png',
//            'image/svg+xml'
//        ],
//
//        FileTypeEnum::VIDEO->value => [
//            'video/3gpp',
//            'video/h261',
//            'video/h263',
//            'video/h264',
//            'video/mp4',
//            'video/mpeg',
//            'video/ogg',
//            'video/webm',
//            'video/x-matroska',
//            'video/x-f4v',
//            'video/x-fli',
//            'video/x-flv',
//            'video/x-m4v',
//            'video/x-ms-wm',
//            'video/x-ms-wmv',
//            'video/x-msvideo'
//        ]
    ],
];
