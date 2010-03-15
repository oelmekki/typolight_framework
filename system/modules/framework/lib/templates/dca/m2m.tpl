                '<?php echo $field ?>' => array
                (
                        'label'                   => &$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['<?php echo $field ?>'],
                        'exclude'                 => true,
                        'inputType'               => 'manyToManyCheckbox',
                        'foreignKey'              => '',
                        'eval'                    => array('mandatory'=>true, 'thisModel' => '', 'thatModel' => '', 'multiple' => true),
                ),

