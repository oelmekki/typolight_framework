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


define( 'TL_MODE', 'FE' );
require_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/initialize.php' );


/**
 * Class ImageRenderer
 *
 * @copyright  Olivier El Mekki, 2009 
 * @author     Olivier El Mekki <olivier@el-mekki.com>
 * @package    Renderer
 */

class ImageRenderer extends System
{
  protected $original_size;
  protected $imageSRC;
  protected $ratio;
  protected $imageType;


  public function __construct()
  {
    parent::__construct();
    $action = $this->Input->get( 'action' );
    if ( method_exists( $this, $action ) )
    {
      $this->$action();
    }

    die();
  }


  /**
   * Resize an image
   */
  protected function resizer()
  {
    $this->imageSRC = urldecode( $this->Input->get( 'file' ) );
    if ( ! file_exists( TL_ROOT . '/' . $this->imageSRC ) )
    {
      die();
    }

    $this->getImageType();

    $value = $this->Input->get( 'value' );
    if ( ! is_numeric( $value ) )
    {
      die();
    }


    $type = $this->Input->get( 'type' );
    switch( $type )
    {
    case 'width':
      if ( $value > 1000 )
      {
        $value = 1000;
      }
      $resized = $this->getSizeByWidth( $value );
      break;

    case 'height':
      if ( $value > 1000 )
      {
        $value = 1000;
      }
      $resized = $this->getSizeByHeight( $value );
      break;

    default:
      die();
    }


    $cache_name =  TL_ROOT . '/system/html/' . md5( $this->imageSRC . $type . $value ) . '.' . $this->imageType;
    if ( file_exists( $cache_name ) )
    {
      header( "Cache-Control: no-cache, must-revalidate" );
      header( "Content-Type: image/" . $this->imageType );
      echo file_get_contents( $cache_name );
      return;
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


    /* prepare the png */
    imageantialias( $dest_img, true );

    /* resize */
    imagecopyresampled( $dest_img, $orig_img, 0, 0, 0, 0, $resized[0], $resized[1], $this->original_size[0], $this->original_size[1] );


    /* send */
    header( "Cache-Control: no-cache, must-revalidate" );
    header( "Content-Type: image/" . $this->imageType );

    if ( $this->imageType == 'png' )
    {
      imagepng( $dest_img, $cache_name, 0, PNG_ALL_FILTERS );
      imagepng( $dest_img, NULL, 0, PNG_ALL_FILTERS );
    }

    else
    {
      imagejpeg( $dest_img, $cache_name, 100 );
      imagejpeg( $dest_img, NULL, 100 );
    }
    /* tear down */
    imagedestroy( $orig_img );
    imagedestroy( $dest_img );
    die();
  }




  /**
   * Do the flip
   *
   * @arg resource    the image to flip
   */
  function imageFlip(&$img) {
    $size_x = imagesx($img);
    $size_y = imagesy($img);
    $temp = imagecreatetruecolor($size_x, $size_y);
    $bg_layer = imagecolorallocatealpha($temp, 0, 0, 0, 127);
    imagefill($temp, 0, 0, $bg_layer);
    imagealphablending( $temp, false );
    imagesavealpha( $temp, true );
    $x = imagecopyresampled($temp, $img, 0, 0, ($size_x-1), 0, $size_x, $size_y, 0-$size_x, $size_y);
    if ($x) {
      imagedestroy( $img );
      $img = $temp;
    }
    else {
      die("Unable to flip image");
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
        die();
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
    return array( $width, (int) ( $width * $this->ratio ) );
  }



  protected function getSizeByHeight( $height )
  {
    $this->getOriginalSize();
    $this->getRatio();
    return array( (int) ( $height / $this->ratio ), $height );
  }
}

new ImageRenderer();
