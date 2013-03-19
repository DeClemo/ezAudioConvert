<?php
 
    // Which operators will load automatically?
    $eZTemplateOperatorArray = array();
     
    // Operator: jacdata
    $eZTemplateOperatorArray[] = array( 'class' => 'eZAudioConvertOperator',
                                        'operator_names' => array( 'eZAudio' ) );
?>