<?php
 
$Module = array( 'name' => 'ezaudio' );
$ViewList = array();
 
$ViewList['convert'] = array( 'script' => 'convert.php',
                              'functions' => array( 'read' ),
                              'params' => array('objectId') );
 
// The entries in the user rights
// are used in the View definition, to assign rights to own View functions
// in the user roles
 
$FunctionList = array();
$FunctionList['read'] = array();
 
?>