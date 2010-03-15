                '<?php echo $field ?>' => array
                (
                        'label'                   => &$GLOBALS['TL_LANG']['<?php echo $this->table ?>']['<?php echo $field ?>'],
                        'exclude'                 => true,
                        'inputType'               => 'text',
                        'eval'                    => array('rgxp'=>'date', 'datepicker'=>$this->getDatePickerString(), 'tl_class'=>'w50 wizard')

                ),

