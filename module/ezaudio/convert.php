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

$rootPath = '/home/musiclab/public_html/';
$ffmpeg = 'ffmpeg';

$object = eZContentObject::fetch( $objectID );
$nodeID = $object->attribute( 'main_node_id' );
$node = eZContentObjectTreeNode::fetch( $nodeID );
$datamap = $object->dataMap();

$fileAttribute = $datamap['file'];

$fileAttributeContent = $fileAttribute->content();

$filePath = $fileAttributeContent->filePath();
$fileName = $fileAttributeContent->Filename;
$fileNameArray = explode('.', $fileName);

$filePath = str_replace($fileName, '', $filePath);

$mimeTypeCategory = $fileAttributeContent->mimeTypeCategory();
$mimeTypePart = $fileAttributeContent->mimeTypePart();

$absolutePath = $rootPath.$filePath.$fileName;

if ($mimeTypeCategory == 'audio')
{
    //check if we need to create an mp3 version of the file.
    if ($mimeTypePart != 'mp3')
    {
        $LogEntry = 'File is not MP3. Conversion to MP3 and OGG will occur';
        $convertMp3 = true;
        $convertOgg = true;
    }
    else
    {
        $LogEntry = 'File is MP3. Conversion to OGG will occur';
        $convertMp3 = false;
        $convertOgg = true;
    }
    
    eZLog::write( $LogEntry, 
                  $logName = 'convert.log', 
                  $dir = 'var/log' );
    eZDebug::writeNotice( $LogEntry, 
                          'newaudiotype.php' );
                          
    if ($convertMp3)
    {
        $LogEntry = 'Converting file to MP3';
        eZLog::write( $LogEntry, 
                      $logName = 'convert.log', 
                      $dir = 'var/log' );
        eZDebug::writeNotice( $LogEntry, 
                              'newaudiotype.php' );

        $continue = true;
        
        //check if file already exists. If so remove the file
        if (file_exists($rootPath.$filePath.$fileNameArray[0].'.mp3'))
        {
            if (unlink($rootPath.$filePath.$fileNameArray[0].'.mp3'))
            {
                $LogEntry = 'File exists. Removal Success';
                eZLog::write( $LogEntry, 
                              $logName = 'convert.log', 
                              $dir = 'var/log' );
                eZDebug::writeNotice( $LogEntry, 
                                      'newaudiotype.php' );
            }
            else
            {
                $LogEntry = 'File exists. Removal FAILED';
                eZLog::write( $LogEntry, 
                              $logName = 'convert.log', 
                              $dir = 'var/log' );
                eZDebug::writeNotice( $LogEntry, 
                                      'newaudiotype.php' );
                
                $continue = false;
            }
        }
        
        //make sure we are good to continue
        if ($continue)
        {
            $command = $ffmpeg.' -i '.$absolutePath.' -b 192k '.$rootPath.$filePath.$fileNameArray[0].'.mp3';
        
            $LogEntry = 'Converting file with command: '.$command;
            eZLog::write( $LogEntry, 
                          $logName = 'convert.log', 
                          $dir = 'var/log' );
            eZDebug::writeNotice( $LogEntry, 
                                  'newaudiotype.php' );
            
            //run the conversion command.                   
            exec( $command );
        }
    }
    
    if ($convertOgg)
    {
        $LogEntry = 'Converting file to OGG';
        eZLog::write( $LogEntry, 
                      $logName = 'convert.log', 
                      $dir = 'var/log' );
        eZDebug::writeNotice( $LogEntry, 
                              'newaudiotype.php' );

        $continue = true;
        
        //check if file already exists. If so remove the file
        if (file_exists($rootPath.$filePath.$fileNameArray[0].'.ogg'))
        {
            if (unlink($rootPath.$filePath.$fileNameArray[0].'.ogg'))
            {
                $LogEntry = 'File exists. Removal Success';
                eZLog::write( $LogEntry, 
                              $logName = 'convert.log', 
                              $dir = 'var/log' );
                eZDebug::writeNotice( $LogEntry, 
                                      'newaudiotype.php' );
            }
            else
            {
                $LogEntry = 'File exists. Removal FAILED';
                eZLog::write( $LogEntry, 
                              $logName = 'convert.log', 
                              $dir = 'var/log' );
                eZDebug::writeNotice( $LogEntry, 
                                      'newaudiotype.php' );
                
                $continue = false;
            }
        }
        
        //make sure we are good to continue
        if ($continue)
        {
            $command = $ffmpeg.' -i '.$absolutePath.' -acodec libvorbis -aq 60 '.$rootPath.$filePath.$fileNameArray[0].'.ogg';
        
            $LogEntry = 'Converting file with command: '.$command;
            eZLog::write( $LogEntry, 
                          $logName = 'convert.log', 
                          $dir = 'var/log' );
            eZDebug::writeNotice( $LogEntry, 
                                  'newaudiotype.php' );
            
            //run the conversion command.                   
            exec( $command );
        }
    }
}
else
{
    $LogEntry = 'File is not of an audio type.';
    eZLog::write( $LogEntry, 
                  $logName = 'convert.log', 
                  $dir = 'var/log' );
    eZDebug::writeError( $LogEntry, 
                          'newaudiotype.php' );
}

//Define rendering options
$Result = array();
$Result['content'] = $tpl->fetch( 'design:profile/convert.tpl' );
$Result['path'] = array( array( 'url'  => 'profile/view/', 
                                'text' => $userProfile->Name ) );

?>