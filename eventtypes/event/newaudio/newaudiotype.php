<?php
 
class NewAudioType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = "newaudio";
    public function __construct()
    {
        parent::__construct( TwitterStatusUpdateType::WORKFLOW_TYPE_STRING, 'New Audio' );
    }
 
    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        /*  YOUR CODE GOES HERE */
        
        $objectID = $parameters['object_id']; 
        $object = eZContentObject::fetch( $objectID );
        $nodeID = $object->attribute( 'main_node_id' );
        $node = eZContentObjectTreeNode::fetch( $nodeID );
        $datamap = $object->dataMap();
        
        $fileAttribute = $datamap['file'];
        
        
        
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}
eZWorkflowEventType::registerEventType( TwitterStatusUpdateType::WORKFLOW_TYPE_STRING, 'newaudiotype' );
?>