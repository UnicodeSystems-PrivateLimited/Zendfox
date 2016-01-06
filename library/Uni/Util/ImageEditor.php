<?php

/**
 * Zendfox Framework
 *
 * LICENSE
 *
 * This file is part of Zendfox.
 *
 * Zendfox is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zendfox is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Class Uni_Util_ImageEditor
 * provides resize functionality for images
 * 
 * @category    Uni
 * @package     Uni_Util 
 * @author      Zendfox Team of Unicode Systems <zendfox@unicodesystems.in> 
 * @copyright   Copyright (c) 2011 Unicode Systems. (http://www.unicodesystems.in)
 * @license     http://www.gnu.org/licenses/  GNU General Public License
 */
class Uni_Util_ImageEditor {

    /**
     * Source image path with image name
     * 
     * @var string 
     */
    private $sourcePath;
    /**
     * target image path without image name
     * 
     * @var string
     */
    private $destinationPath;
    /**
     * target image width
     * 
     * @var int
     */
    private $newWidth;
    /**
     * target image height
     *
     * @var int 
     */
    private $newHeight;
    /**
     * target image name
     * 
     * @var string 
     */
    private $newImageName;
    /**
     * The actual image (before manipulation)
     * 
     * @var string
     */
    private $image;
    /**
     * source image extension
     * 
     * @var string
     */
    private $imageType;
    /**
     * target image extension
     * 
     * @var string
     */
    private $newImageExt;
    /**
     * maintain aspect ratio, byDefault True
     * 
     * @var boolean
     */
    private $ratio;
    /**
     * The options for this class
     * 
     * @var array
     */
    private $options;

    /**
     *
     * @param string $sourcePath Source image path with image name
     * @param int $newWidth Target image width
     * @param int $newHeight Target image height
     * @param string $newImageName Target image name
     * @param string $destinationPath Target image path without image name
     * @param boolean $ratio Maintain aspect ratio (ByDefault TRUE)
     */
    function resize($sourcePath, $newWidth, $newHeight, $newImageName, $destinationPath, $ratio=true) {
        $this->sourcePath = $sourcePath;
        $this->newWidth = $newWidth;
        $this->newHeight = $newHeight;
        $this->ratio = $ratio;
        $this->newImageName = $newImageName;
        $this->destinationPath = $destinationPath;
        if (!file_exists($this->sourcePath)) {
            exit("File " . $this->sourcePath . " does not exist.");
        }

        $this->load($this->sourcePath);
        $this->setPreserveOptions(array());
        if ($this->ratio) {
            if ($this->newWidth >= $this->getWidth() && $this->newHeight >= $this->getHeight()) {
                $this->resizeImg($this->getWidth(), $this->getHeight());
            } else if ($this->newWidth <= $this->getWidth() && $this->newHeight <= $this->getHeight()) {
                if (($this->getWidth() / $this->getHeight()) > ($this->newWidth / $this->newHeight)) {
                    $this->resizeToWidth($this->newWidth);
                } else if (($this->getWidth() / $this->getHeight()) < ($this->newWidth / $this->newHeight)) {
                    $this->resizeToHeight($this->newHeight);
                } else {
                    $this->resizeImg($this->newWidth, $this->newHeight);
                }
            } else if ($this->newWidth > $this->getWidth() && $this->newHeight < $this->getHeight()) {
                $this->resizeToHeight($this->newHeight);
            } else if ($this->newWidth < $this->getWidth() && $this->newHeight > $this->getHeight()) {
                $this->resizeToWidth($this->newWidth);
            }
        } else {
            $this->resizeImg($this->newWidth, $this->newHeight);
        }
        $this->save($this->destinationPath . $this->newImageName);
    }

    /**
     *
     * @param string $filename File name with path
     */
    function load($filename) {
        $image_info = getimagesize($filename);
        $this->imageType = $image_info[2];
        if ($this->imageType == IMAGETYPE_JPEG) {
            if (strtolower(substr(strrchr($this->sourcePath, "."), 1)) == 'jpeg')
                $this->newImageExt = 'jpeg';
            else
                $this->newImageExt = 'jpg';
            $this->image = imagecreatefromjpeg($filename);
        } elseif ($this->imageType == IMAGETYPE_GIF) {
            $this->newImageExt = 'gif';
            $this->image = imagecreatefromgif($filename);
        } elseif ($this->imageType == IMAGETYPE_PNG) {
            $this->newImageExt = 'png';
            $this->image = imagecreatefrompng($filename);
        } else {
            $this->newImageExt = 'jpg';
            $this->image = imagecreatefromjpeg($filename);
        }
    }

    /**
     *
     * @param string $filename File name with path
     * @param string $image_type Extension
     * @param int $compression 
     */
    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=100) {
        $image_info = getimagesize($this->sourcePath);
        $image_type = $image_info[2];
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image, $filename, $compression);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image, $filename);
        } elseif ($image_type == IMAGETYPE_PNG) {
            // *** Scale quality from 0-100 to 0-9
            $scaleQuality = round(($compression / 100) * 9);

            // *** Invert quality setting as 0 is best, not 9
            $invertScaleQuality = 9 - $scaleQuality;
            imagepng($this->image, $filename, $invertScaleQuality);
        }
    }

    /**
     *
     * @param string $image_type 
     */
    function output($image_type=IMAGETYPE_JPEG) {
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($this->image);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($this->image);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($this->image);
        }
    }

    /**
     *
     * @return int Get image width
     */
    function getWidth() {
        return imagesx($this->image);
    }

    /**
     *
     * @return int Get image height
     */
    function getHeight() {
        return imagesy($this->image);
    }

    /**
     *
     * @param int $height Resized height
     */
    function resizeToHeight($height) {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resizeImg($width, $height);
    }

    /**
     *
     * @param int $width Resized width
     */
    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resizeImg($width, $height);
    }

    /**
     *
     * @param int $scale 
     */
    function scale($scale) {
        $width = $this->getWidth() * $scale / 100;
        $height = $this->getheight() * $scale / 100;
        $this->resizeImg($width, $height);
    }

    /**
     *
     * @param int $width Target image width
     * @param int $height Target image height
     */
    function resizeImg($width, $height) {
        // create the working image
        if (function_exists('imagecreatetruecolor')) {
            $newImage = imagecreatetruecolor($width, $height);
        } else {
            $newImage = imagecreate($width, $height);
        }

        $this->preserveImageAlpha($newImage);
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $newImage;
    }

    /**
     *
     * @param string $newImage Preserve image Alpha (For PNG and GIF)
     */
    function preserveImageAlpha($newImage) {
        if ($this->newImageExt == 'png' && $this->options['preserveAlpha'] === true) {
            imagealphablending($newImage, false);

            $colorTransparent = imagecolorallocatealpha
                    (
                    $newImage, $this->options['alphaMaskColor'][0], $this->options['alphaMaskColor'][1], $this->options['alphaMaskColor'][2], 0
            );

            imagefill($newImage, 0, 0, $colorTransparent);
            imagesavealpha($newImage, true);
        }
        // preserve transparency in GIFs... this is usually pretty rough tho
        if ($this->newImageExt == 'gif' && $this->options['preserveTransparency'] === true) {
            $colorTransparent = imagecolorallocate
                    (
                    $newImage, $this->options['transparencyMaskColor'][0], $this->options['transparencyMaskColor'][1], $this->options['transparencyMaskColor'][2]
            );

            imagecolortransparent($newImage, $colorTransparent);
            imagetruecolortopalette($newImage, true, 256);
        }
    }

    /**
     *
     * @param array $options 
     */
    public function setPreserveOptions($options = array()) {
        // make sure we've got an array for $this->options (could be null)
        if (!is_array($this->options)) {
            $this->options = array();
        }

        // make sure we've gotten a proper argument
        if (!is_array($options)) {
            throw new InvalidArgumentException('setOptions requires an array');
        }

        // we've yet to init the default options, so create them here
        if (sizeof($this->options) == 0) {
            $defaultOptions = array
                (
                'resizeUp' => false,
                'jpegQuality' => 100,
                'correctPermissions' => false,
                'preserveAlpha' => true,
                'alphaMaskColor' => array(255, 255, 255),
                'preserveTransparency' => true,
                'transparencyMaskColor' => array(0, 0, 0)
            );
        }
        // otherwise, let's use what we've got already
        else {
            $defaultOptions = $this->options;
        }

        $this->options = array_merge($defaultOptions, $options);
    }

}
