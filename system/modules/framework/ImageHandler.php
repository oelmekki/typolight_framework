<?php
/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Framework 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class ImageHandler
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Renderer
 */

class ImageHandler extends System
{
  protected $original_size;
  protected $imageSRC;
  protected $ratio;
  protected $imageType;


  /**
   * Resize an image
   *
   * @param string    the type of resizing, 'width' or 'height'
   * @param int       the value of the ref side
   * @param string    the path of the initial image
   * @param string    the path of the dest image. A new one is created in system/tmp if none
   * @param bool      force cache regeneration
   */
  public function resizer( $resizingType, $value, $orig, $dest = null, $clearCache = false )
  {
    $this->imageSRC = $orig;
    if ( ! file_exists( TL_ROOT . '/' . $this->imageSRC ) )
    {
      return false;
    }

    $this->getImageType();

    if ( ! is_numeric( $value ) )
    {
      return false;
    }


    if ( $value > 1000 )
    {
      $value = 1000;
    }

    if ( $resizingType == 'width' )
    {
      $resized = $this->getSizeByWidth( $value );
    }

    else
    {
      $resized = $this->getSizeByHeight( $value );
    }



    $cache_name     = 'system/html/' . md5( $this->imageSRC . $resizingType . $value ) . '.' . $this->imageType;
    $abs_cache_name = TL_ROOT . '/' . $cache_name;
    if ( ! $clearCache and file_exists( $abs_cache_name ) )
    {
      return $cache_name;
    }


    $dest_img = imagecreatetruecolor( $resized[0], $resized[1] );

    if ( $this->imageType == 'png' )
    {
      $orig_img = imagecreatefrompng( TL_ROOT . '/' . $this->imageSRC );
      imagealphablending( $dest_img, false );
      $background = imagecolorallocatealpha($dest_img, 0, 0, 0, 127);
      imagefill($dest_img, 0, 0, $background);
      imagesavealpha( $dest_img, true );
    }

    else
    {
      $orig_img = imagecreatefromjpeg( TL_ROOT . '/' . $this->imageSRC );
    }


    /* resize */
    imagecopyresampled( $dest_img, $orig_img, 0, 0, 0, 0, $resized[0], $resized[1], $this->original_size[0], $this->original_size[1] );


    if ( ! strlen( $dest ) )
    {
      $dest = $abs_cache_name;
    }

    /* send */
    if ( $this->imageType == 'png' )
    {
      imagepng( $dest_img, $abs_cache_name, 0, PNG_ALL_FILTERS );

      if ( $abs_cache_name != $dest )
      {
        imagepng( $dest_img, $dest, 0, PNG_ALL_FILTERS );
      }
    }

    else
    {
      imagejpeg( $dest_img, $abs_cache_name, 100 );

      if ( $abs_cache_name != $dest )
      {
        imagejpeg( $dest_img, TL_ROOT . '/' . $dest, 100 );
      }
    }

    /* tear down */
    imagedestroy( $orig_img );
    imagedestroy( $dest_img );

    return $cache_name;
  }




  /**
   * Do the flip
   *
   * @arg resource    the image to flip
   */
  public function flipper(&$img) {
    $size_x = imagesx($img);
    $size_y = imagesy($img);
    $temp = imagecreatetruecolor($size_x, $size_y);
    $bg_layer = imagecolorallocatealpha($temp, 0, 0, 0, 127);
    imagefill($temp, 0, 0, $bg_layer);
    imagealphablending( $temp, false );
    imagesavealpha( $temp, true );
    $x = imagecopyresampled($temp, $img, 0, 0, ($size_x-1), 0, $size_x, $size_y, 0-$size_x, $size_y);

    if ($x) 
    {
      imagedestroy( $img );
      $img = $temp;
      return true;
    }
    else 
    {
      return false;
    }
  }



  protected function getImageType()
  {
    if ( ! isset( $this->imageType ) )
    {
      if ( strpos( $this->imageSRC, '.png' ) )
      {
        $this->imageType = 'png';
      }

      elseif ( strpos( $this->imageSRC, '.jpg' ) )
      {
        $this->imageType = 'jpg';
      }

      else
      {
        throw new Exception( 'Only jpg and png are supported for now' );
      }
    }

    return $this->imageType;
  }



  protected function getOriginalSize()
  {
    if ( ! isset( $this->original_size ) )
    {
      $this->original_size = getimagesize( TL_ROOT . '/' . $this->imageSRC );
    }

    return $this->original_size;
  }



  protected function getRatio()
  {
    if ( ! isset( $this->ratio ) )
    {
      $this->ratio = $this->original_size[0] / $this->original_size[1];
    }

    return $this->ratio;
  }



  protected function getSizeByWidth( $width )
  {
    $this->getOriginalSize();
    $this->getRatio();
    return array( $width, (float) ( $width / $this->ratio ) );
  }



  protected function getSizeByHeight( $height )
  {
    $this->getOriginalSize();
    $this->getRatio();
    return array( (float) ( $height * $this->ratio ), $height );
  }
}
