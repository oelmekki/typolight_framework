<!-- indexer::stop -->
<div class="<?php echo $this->class; ?> block"<?php echo $this->cssID; ?><?php if ($this->style): ?> style="<?php echo $this->style; ?>"<?php endif; ?>>
<?php if ($this->headline): ?>

<<?php echo $this->hl; ?>><?php echo $this->headline; ?></<?php echo $this->hl; ?>>
<?php endif; ?>

<a href="<?php echo $this->request; ?>#<?php echo $this->skipId; ?>" class="invisible" title="<?php echo $this->skipNavigation; ?>"></a>

<?php foreach ( $this->routes as $route ) : ?>

<ul>
<li><a href="<?php echo Route::compose( $route[0], $route[1] ) ?>" onclick="this.blur();<?php echo $item['target']; ?>"><?php echo $route[2]; ?></a><?php echo $item['subitems']; ?></li>
</ul>

<?php endforeach ?>

<a id="<?php echo $this->skipId; ?>" class="invisible" title="<?php echo $this->skipNavigation; ?>"></a>

</div>
<!-- indexer::continue -->

