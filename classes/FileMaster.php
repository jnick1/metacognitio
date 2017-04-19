<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/14/2017
 * Time: 9:11 PM
 */
class FileMaster
{
    const ALLOWED_DOC_MIME_TYPES = [
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'application/vnd.oasis.opendocument.text',
        'text/rtf',
        'text/plain'
    ];
    const ALLOWED_IMG_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/tiff'
    ];

    //TODO: any implementation (needs to handle all downloads/uploads from/to the server to/from the client)
}