<?php

/**
 * Created by PhpStorm.
 * User: Jacob
 * Date: 4/25/2017
 * Time: 9:54 PM
 */
class ImageMaster
{
//
//    //TODO: convert function from procedural to OOP
//    /**
//     * This function generates a thumbnail image of width $width and height $height
//     * from $filename in the uploads directory, and saves it in the downloads
//     * directory under the name $newfilename.
//     *
//     * @param $filename
//     * @param $newfilename
//     * @param $newWidth
//     * @param $newHeight
//     * @return bool
//     */
//    public static function generateThumbnail($filename, $newfilename, $newWidth, $newHeight) {
//        global $homedirInclude;
//
//        $imageType = exif_imagetype($homedirInclude."uploads/".$filename);
//
//        $newImage = null;
//        $oldImage = null;
//        $imageSize = null;
//        if($imageType === false) {
//            return false;
//        } else {
//            $newImage = imagecreatetruecolor($newWidth, $newHeight);
//            $oldImage = imageCreateFromAny($homedirInclude."uploads/".$filename);
//
//            $imageSize = array(imagesx($oldImage), imagesy($oldImage));
//        }
//
//        /*
//         * Scaling algorithm for CSS cover:
//         * Regardless of which, if either, dimension is larger or
//         * smaller than its respective maximum or minimum, the
//         * smaller dimension will always be scaled to either meet
//         * the minimum or maximum (conveniently the same value),
//         * depending on whichever it is closest to. The image scales
//         * up on a tie when the dimensions aren't the same.
//         *
//         * (the logic in the last sentence has not been coded in, as
//         * it would be unnecessary for this case)
//         */
//
//        $smallerDim = min(array_keys($imageSize, min($imageSize)));
//
//        if($smallerDim === 0) { //width smaller than height
//            $src_w = $imageSize[0];
//            $src_h = (int) floor($newHeight*$imageSize[0]/$newWidth);
//            $src_x = 0;
//            $src_y = (int) floor(($imageSize[1]-$src_h)/2);
//        } else {
//            $src_h = $imageSize[1];
//            $src_w = (int) floor($newHeight*$imageSize[1]/$newWidth);
//            $src_x = (int) floor(($imageSize[0]-$src_w)/2);
//            $src_y = 0;
//        }
//
//        $params = [
//            $newImage,
//            $oldImage,
//            0,0,
//            $src_x,$src_y,
//            $newWidth,$newHeight,
//            $src_w,$src_h
//        ];
//        $result = call_user_func_array("imagecopyresampled", $params);
//
//        if($result) {
//            imageWriteAny($newImage, $homedirInclude."downloads/".$newfilename, $imageType);
//        } else {
//            return false;
//        }
//    }
//
//    /**
//     * Function below if from
//     * http://php.net/manual/en/function.imagecreatefromjpeg.php
//     * written by:  matt dot squirrell dot php at hsmx dot com
//     */
//    function imageCreateFromAny($filepath) {
//        $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
//        $allowedTypes = array(
//            1,  // [] gif
//            2,  // [] jpg
//            3,  // [] png
//            6   // [] bmp
//        );
//        if (!in_array($type, $allowedTypes)) {
//            return false;
//        }
//        switch ($type) {
//            case 1 :
//                $im = imageCreateFromGif($filepath);
//                break;
//            case 2 :
//                $im = imageCreateFromJpeg($filepath);
//                break;
//            case 3 :
//                $im = imageCreateFromPng($filepath);
//                break;
//            case 6 :
//                $im = imageCreateFromBmp($filepath);
//                break;
//        }
//        return $im;
//    }
//    function imageWriteAny($image, $filePath, $imageType) {
//        switch($imageType) {
//            case IMAGETYPE_GIF:
//                return imagegif($image, $filePath);
//            case IMAGETYPE_JPEG:
//                return imagejpeg($image, $filePath);
//            case IMAGETYPE_PNG:
//                return imagepng($image, $filePath);
//            case IMAGETYPE_WBMP:
//                return imagewbmp($image, $filePath);
//            case IMAGETYPE_XBM:
//                return imagexbm($image, $filePath);
//            default:
//                return false;
//        }
//    }

}