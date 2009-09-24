<?php if ( $this->page_count > 1 ) : ?>
<?php echo $this->pagename ?>
<div class="pagination">
  <?php if ( $this->selected > 1 ) : ?>
  <span class="previous"><a href="<?php echo $this->links[ $this->selected - 1 ] ?>"><?php echo $this->lang[ 'previous' ] ?></a></span>
  <?php endif ?>

  <?php foreach ( $this->links as $i => $link ) : ?>
  <?php   if ( $i == $this->selected ) : ?>
  <span class="page active"><?php echo $i ?></span>
  <?php   else : ?>
  <span class="page"><a href="<?php echo $link ?>"><?php echo $i ?></a></span>
  <?php   endif ?>
  <?php endforeach ?>


  <?php if ( $this->selected < $this->page_count ) : ?>
  <span class="next"><a href="<?php echo $this->links[ $this->selected + 1 ] ?>"><?php echo $this->lang[ 'next' ] ?></a></span>
  <?php endif ?>
</div>
<?php endif ?>
