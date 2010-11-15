		'<?php echo $field ?>' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['<?php echo $field ?>'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array(),
			'options_callback'        => '',
			'foreignKey'              => '',
			'eval'                    => array('mandatory'=>true, 'multiple'=> false)
		),

