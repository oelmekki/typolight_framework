<?php if ( count( $this->routes ) ) : ?>
<ul>
  <?php foreach ( $this->routes as $routeName => $options ) : ?>
  <li>
    <span class="routeName"><?php echo $routeName ?></span> :
    <?php echo $options[ 'route' ] ?> => <?php echo $options[ 'resolveTo' ] ?>,

    <?php foreach ( $options[ 'staticParams' ] as $param => $value ) : ?>
      <?php echo $param ?> = <?php echo $value ?>,
    <?php endforeach ?>

    Post route: <?php echo ( $options[ POSTroute ] ? 'true' : 'false' ) ?>
  </li>
  <?php endforeach ?>
</ul>

<?php else : ?>
<p>No route for now</p>
<?php endif ?>
