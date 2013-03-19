<?php
/**
 * Operator: jac('list') and jac('count') <br>
 * Count: {jac('count')} <br>
 * Liste: {jac('list')|attribute(show)}
 */
class eZAudioConvertOperator
{
    public $Operators;
 
    public function __construct( $name = 'eZAudio' )
    {
        $this->Operators = array( $name );
    }
 
    /**
     * Returns the template operators.
     * @return array
     */
    function operatorList()
    {
        return $this->Operators;
    }
 
    /**
     * Returns true to tell the template engine that the parameter list
     * exists per operator type.
     */
    public function namedParameterPerOperator()
    {
        return true;
    }
 
    /**
     * @see eZTemplateOperator::namedParameterList
     **/
    public function namedParameterList()
    {
        return array( 'eZAudio' => array( 'type' => array( 'type' => 'string',
                                                           'required' => true,
                                                           'default' => 'player' ))
                    );
    }
 
    /**
     * Depending of the parameters that have been transmitted, fetch objects JACExtensionData
     * {jac('list)} or count data {jac('count')}
     */
     public function modify( $tpl, $operatorName, $operatorParameters,  $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters  )
    {
        $result_type = $namedParameters['type'];
        if( $result_type == 'player')
        {
            if ( $operatorValue instanceof eZContentObject )
            {
                $convert = ezAudioConvert::fetch($operatorValue->ID);
            }
            else if ( $operatorValue instanceof eZContentObjectTreeNode )
            {
                $object = $operatorValue->object();
                $convert = ezAudioConvert::fetch($object->ID);
            }
            else
            {
                $LogEntry = 'Incorrect object sent to eZAudio operator. Please pass either an eZContentObject or an eZContentObjectTreeNode object.';
                eZLog::write( $LogEntry, $logName = 'eZAudioConvertOperator.log', $dir = 'var/log' );
                eZDebug::writeError( $LogEntry, 'eZAudioConvertOperator.php' );
            }
            
            $operatorValue = $convert->displayPlayer();
        }
        else if( $result_type == 'download')
        {
            $operatorValue = 0;
        }
    }
}
?>