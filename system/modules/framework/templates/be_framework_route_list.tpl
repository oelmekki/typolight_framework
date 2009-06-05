<div id="mod_hardRoutesList" class="block">
  <?php if ( count( $this->routes ) ) : ?>
  <form action="<?php echo $this->pagename ?>" method="post">
    <div id="routes-list">
      <?php foreach ( $this->routes as $index => $route ) : ?>
      <div class="route <?php echo ( $route->inDatabase ? 'inDatabase' : 'notInDatabase' ) ?>">
        <h3 class="name"><?php echo $route->name ?><?php if ( $route->inDatabase ) echo ' - ' . $this->lang[ 'inDatabase' ] ?> <a href="#" class="toggable-switch show-<?php echo $this->lang[ 'toggle-show' ] ?> hide-<?php echo $this->lang[ 'toggle-hide' ] ?>"><?php echo $this->lang[ 'toggle-show' ] ?></a></h3>

        <div class="toggable-body">
          <div class="definition"><?php echo $this->lang[ 'definition' ] ?> : <?php echo $route->route ?></div>
          <div class="resolveTo"><?php echo $this->lang[ 'resolveTo' ] ?> : <?php echo $route->resolveTo ?></div>

          <div class="staticParams">
            <p><?php echo $this->lang[ 'staticParams' ] ?></p>
            <?php if ( count( $route->params ) ) : ?>
            <ul>
              <?php foreach ( $route->params as $param => $value ) : ?>
              <li><?php echo $param ?> = <?php echo $value ?></li>
              <?php endforeach ?>
            </ul>
            <?php else : ?>
            <p><?php echo $this->lang[ 'none' ] ?></p>
            <?php endif ?>
          </div><!-- staticParams -->

          <div class="postRoute"><?php echo $this->lang[ 'isPostRoute' ] ?> : <?php echo ( $route->POSTroute ? $this->lang[ 'true' ] : $this->lang[ 'false' ] ) ?></div>

          <?php if ( ! $route->inDatabase ) : ?>
          <p class="load"><a href="<?php echo $this->pagename ?>&action=load_route&routeIndex=<?php echo $index ?>" class="load-link"><?php echo $this->lang[ 'loadInDb' ] ?></a></p>
          <?php endif ?>
        </div><!-- toggable-body -->
      </div><!-- route -->
      <?php endforeach ?>
    </div>
    <p class="button-line"><input type="submit" name="action[load_all_routes]" value="<?php echo $this->lang[ 'loadAll' ] ?>" /></p>
  </form>

  <?php else : ?>
  <p><?php echo $this->lang[ 'noRoute' ] ?></p>
  <?php endif ?>
</div><!-- mod_hardRoutesList -->
