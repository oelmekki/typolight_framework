<!-- indexer::stop -->
<div class="mod_routedNav <?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<a href="<?php echo $this->request; ?>#<?php echo $this->skipId; ?>" class="invisible" title="<?php echo $this->skipNavigation; ?>"></a>

<ul>
  <?php foreach ( $this->routes as $route ) : ?>
  <li <?php if ( $route[ 'active' ] ) echo 'class="active"' ?>>
    <a href="<?php echo $route[ 'path' ] ?>" onclick="this.blur();"><?php echo $route[ 'name' ]; ?></a>
  </li>
  <?php endforeach ?>
</ul>

<a id="<?php echo $this->skipId; ?>" class="invisible" title="<?php echo $this->skipNavigation; ?>"></a>

</div>
<!-- indexer::continue -->

