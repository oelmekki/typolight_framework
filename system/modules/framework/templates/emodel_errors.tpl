<?php if ( $this->model->hasErrors() ) : ?>
  <ul class="errors">
    <?php foreach ( $this->model->errors as $attr => $errors ) : ?>
    <?php   foreach ( $errors as  $error ) : ?>
    <li class="error"><?php echo $error ?></li>
    <?php   endforeach ?>
    <?php endforeach ?>
  </ul>
<?php endif ?>
