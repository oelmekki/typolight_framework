<?php if ( count( $this->messages ) ) : ?>
<div class="mod_messages">
  <?php foreach ( $this->messages as $level => $messages ) : ?>
  <div class="message <?php echo $level ?>">
    <?php foreach ( $messages as $i => $message ) : ?>
    <?php echo $message ?>
    <?php if ( $i + 1 < count( $messages ) ) : ?><br /><?php endif ?>
    <?php endforeach ?>
  </div>
  <<?php endforeach ?>
</div><!-- .mod_messages -->
<?php endif ?>
