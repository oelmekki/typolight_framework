		'<?php echo $field ?>' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['<?php echo $field ?>'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'eval'                    => array('mandatory'=>true, 'rte'=> 'tinyMCE')
		),

