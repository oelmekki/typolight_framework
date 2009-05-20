<?php
require_once( 'specs_init.php' );

class DescribeExample extends PHPSpec_Context
{
  public function itShouldGetType()
  {
    $m = CartesModele::find(1);
    $c = $m->type();
    $this->spec( $c->id )->should->be( 1 );
  }

  public function itShouldGenerateImage()
  { 
    $m = CartesModele::find(1);
    $res = $m->generate_image();
    $this->spec( is_resource($res) )->should->beTrue();
  }
}

?>
