<?php
 
class NewAudioType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "newaudio";
    public function __construct()
    {
        parent::__construct( NewAudioType::WORKFLOW_TYPE_STRING, 'New Audio' );
    }
 
    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        /*  YOUR CODE GOES HERE */
        
        $objectID = $parameters['object_id']; 
        
        $convert = ezAudioConvert::fetch( $objectID );

        $convert->convertAudio();        
        
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}
eZWorkflowEventType::registerEventType( NewAudioType::WORKFLOW_TYPE_STRING, 'newaudiotype' );
?>