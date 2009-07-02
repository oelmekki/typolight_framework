<input type="checkbox" 
       name="<?php echo $this->strName ?>" 
       id="opt_<?php echo $this->strId ?>" 
       class="tl_checkbox" 
       value="<?php echo $this->value ?>"
       <?php if ( $this->checked ) echo 'checked="checked"' ?>
       <?php echo $this->attrs ?>
       onfocus="Backend.getScrollOffset();" /> 
<label for="opt_<?php echo $this->strId ?>"><?php echo $this->label ?></label>
