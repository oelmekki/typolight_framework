<table cellspacing="0" cellpadding="0" class="tl_paramwizard" id="ctrl_<?php echo $this->strId ?>" summary="Param wizard">
  <thead>
    <tr>
      <th><?php echo $GLOBALS['TL_LANG'][$this->strTable]['routeParam'] ?></th>
      <th><?php echo $GLOBALS['TL_LANG'][$this->strTable]['routeValue'] ?></th>
    </tr>
  </thead>
  <tbody>

    <?php for ( $i = 0; $i < count( $this->values ); $i++ ) : ?>
    <tr>
      <td><input type="text" name="<?php echo $this->strId . '[' . $i ?>][param]" id="<?php $this->strId ?>_param_<?php echo $i ?>" class="tl_text_2" value="<?php echo specialchars($this->values[$i]['param']) ?>" /></td>
      <td><input type="text" name="<?php echo $this->strId . '[' . $i ?>][value]" id="<?php $this->strId ?>_value_<?php echo $i ?>" class="tl_text_2" value="<?php echo specialchars($this->values[$i]['value']) ?>" /></td>
      <td style="white-space:nowrap; padding-left:3px;">
        <?php foreach ($this->buttons as $button) : ?>
        <a href="<?php echo $this->addToUrl('&amp;' . $strCommand . '=' . $button . '&amp;cid=' . $i . '&amp;id=' . $this->currentRecord) ?>" title="<?php echo specialchars($GLOBALS['TL_LANG'][$this->strTable][$button][0]) ?>" onclick="Backend.optionsWizard(this, '<?php echo $button ?>', 'ctrl_<?php echo $this->strId ?>'); return false;"><?php echo $this->generateImage($button.'.gif', $GLOBALS['TL_LANG'][$this->strTable][$button][0]) ?></a>
        <?php endforeach ?>
      </td>
    </tr>
    <?php endfor ?>
  </tbody>
</table>

