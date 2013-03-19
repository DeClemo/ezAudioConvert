<?php
/**
 * Profile View
 *
 * This module is aimed to allow for a user profile to be viewed without having to use the a link that displays the location in the content tree.
 *
 * Written By: Daniel Clements
 * Date: 22/02/13
 * Copyright: Think Walsh Creative Solutions
 * Versione 0.1
 *
 */

// take current object of type eZModule 
$Module = $Params['Module'];
$view_parameters = $Params['UserParameters'];

// initialize Template object
$tpl = eZTemplate::factory();

// read parameter Ordered View 
// http://.../modul1/list/ $Params['ParamOne'] / $Params['ParamTwo'] 
// for example .../modul1/list/view/5
//param names are defined in the module.php file

$objectID = $Params['objectId'];

$convert = ezAudioConvert::fetch( $objectID );

$convert->convertAudio();

//Define rendering options
$Result = array();
$Result['content'] = $tpl->fetch( 'design:profile/convert.tpl' );
$Result['path'] = array( array( 'url'  => 'profile/view/', 
                                'text' => $userProfile->Name ) );

?>