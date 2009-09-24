<!-- indexer::stop -->
<div class="mod_routedNav <?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<ul>
  <?php foreach ( $this->routes as $route ) : ?>
  <li <?php if ( $route[ 'active' ] ) echo 'class="active"' ?>>
    <a href="<?php echo $route[ 'path' ] ?>" onclick="this.blur();"><?php echo $route[ 'name' ]; ?></a>
  </li>
  <?php endforeach ?>
</ul>

</div>
<!-- indexer::continue -->

