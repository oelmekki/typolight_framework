<div id="ctrl_<?php echo $this->strId ?>" class="<?php echo $this->strClass ?>">

  <?php if ( $this->checkAll ) : ?>
  <input type="checkbox" id="check_all_<?php echo $this->strId ?>" class="tl_checkbox" onclick="Backend.toggleCheckboxGroup(this, \'ctrl_<?php echo $this->strId ?>\')" /> <label for="check_all_<?php echo $this->strId ?>" style="color:#a6a6a6;"><em><?php echo $GLOBALS['TL_LANG']['MSC']['selectAll'] ?></em></label><br />
  <?php endif ?>

  <?php foreach ( $this->options as $option ) : ?>
    <?php echo $option ?><br />
  <?php endforeach ?>
</div>
