<table cellspacing="0" cellpadding="0" class="tl_routesWizard" id="ctrl_'.$this->strId.'" summary="Routes wizard">
  <thead>
    <tr>
      <th><?php echo $GLOBALS['TL_LANG'][ 'MSC' ][ 'framework' ]['routeName'] ?></th>
      <th><?php echo $GLOBALS['TL_LANG'][ 'MSC' ][ 'framework' ]['routeParams'] ?></th>
      <th><?php echo $GLOBALS['TL_LANG'][ 'MSC' ][ 'framework' ]['altName'] ?></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ( $this->values as $i => $value ) : ?>
    <tr>
      <td>
        <select name="<?php echo $this->strId ?>[<?php echo $i ?>][routeName]" 
                id="<?php echo $this->strId . '_routeName_'. $i ?>" 
                class="tl_text_2">
        <?php foreach ( $this->allRoutes as $route ) : ?>
        <option value="<?php echo $route->name ?>" <?php if ( $value[ 'routeName' ] == $route->name ) echo 'selected="1"' ?>><?php echo $route->name ?></option>
        <?php endforeach ?>
        </select>
      </td>

      <td><input type="text" 
        name="<?php echo $this->strId ?>[<?php echo $i ?>][params] ?>" 
                 id="<?php echo $this->strId ?>_params_<?php echo $i ?>" 
                 class="tl_text_2" 
                 value="<?php echo specialchars($value['params']) ?>" /></td>

      <td><input type="text" 
        name="<?php echo $this->strId ?>[<?php echo $i ?>][altName] ?>" 
                 id="<?php echo $this->strId ?>_params_<?php echo $i ?>" 
                 class="tl_text_2" 
                 value="<?php echo specialchars($value['altName']) ?>" /></td>

      <td style="white-space:nowrap; padding-left:3px;">
      <?php foreach ( $this->buttons as $button ) : ?>
        <a href="<?php echo $this->addToUrl('&amp;'.$this->strCommand. '=' . $button. '&amp;cid='. $i . '&amp;id='.$this->currentRecord) ?>" 
           title="<?php echo specialchars($GLOBALS['TL_LANG'][ 'MSC' ][ 'framework' ][$button]) ?>" 
           onclick="/* TODO */">
             <?php echo $this->generateImage( $button . '.gif', $GLOBALS['TL_LANG'][ 'MSC' ][ 'framework' ][$button]) ?>
        </a>
      <?php endforeach ?>
      </td>
    </tr>
  <?php endforeach ?>
  </tbody>
</table>
