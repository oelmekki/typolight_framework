                '<?php echo $field ?>' => array
                (
                        'label'                   => &$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['<?php echo $field ?>'],
                        'exclude'                 => true,
                        'inputType'               => 'eFileTree',
                        'eval'                    => array('mandatory'=>true, 'fieldType' => 'radio', 'files' => true,  'path' => 'tl_files/')
                ),

