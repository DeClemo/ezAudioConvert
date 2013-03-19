<?php
/**
 * Class file for the ezaudioconvert extension
 *
 * Contains the used functions and an object.
 *
 * Written By: Daniel Clements
 * Date: 19/03/2013
 * Copyright: Think Walsh Creative Solutions
 * Versione 0.1
 *
 */

class ezAudioConvert extends eZPersistentObject
{
    function ezAudioConvert( $row )
    {
        $this->eZPersistentObject( $row );
    }
    
    static function definition()
    {
        static $definition = array( 'fields' => array( 'id' => array( 'name' => 'contentObject_ID',
                                                                      'datatype' => 'integer',
                                                                      'default' => 0,
                                                                      'required' => true,
                                                                      'foreign_class' => 'eZContentObject',
                                                                      'foreign_attribute' => 'id',
                                                                      'multiplicity' => '1..*' ),
                                                       "current_version" => array( 'name' => "CurrentVersion",
                                                                                   'datatype' => 'integer',
                                                                                   'default' => 0,
                                                                                   'required' => true ) ),
                                                       'function_attributes' => array( 'filesize' => 'fileSize',
                                                                                       'filepath' => 'filePath',
                                                                                       'content_object' => 'contentObject',
                                                                                       'mime_type_category' => 'mimeTypeCategory',
                                                                                       'mime_type_part' => 'mimeTypePart',
                                                                                       'main_node_id' => 'mainNodeID' ),
                                                       "keys" => array( "id" ),
                                                       'class_name' => 'ezAudioConvert',
                                                       'name' => 'ezcontentobject' );
    
    
/*   
        static $definition = array( 'fields' => array( 'contentobject_id' => array( 'name' => 'ContentObject_ID',
                                                                                    'datatype' => 'integer',
                                                                                    'default' => 0,
                                                                                    'required' => true ) ),
                                   'function_attributes' => array( 'filesize' => 'fileSize',
                                                                   'filepath' => 'filePath',
                                                                   'content_object' => 'contentObject',
                                                                   'mime_type_category' => 'mimeTypeCategory',
                                                                   'mime_type_part' => 'mimeTypePart' ),
                                   'class_name' => 'eZBinaryFile',
                                   'name' => 'ezbinaryfile' );
*/
        return $definition;
    }
    
    function mainNodeID()
    {
        $object = $this->contentObject();
        $mainNodeId = $object->attribute( 'main_node_id' );
        return $mainNodeId;
    }
    
    function convertAudio()
    {
        $ini = eZINI::instance( "ezaudioconvert.ini" );
        
        // get the settings for the file
        $rootPath = $ini->variable( "FileSettings", "rootPath" );
        $ffmpeg = $ini->variable( "FileSettings", "ffmpegExecutable" );
        $attributeIdentifier = $ini->variable( "FileSettings", "attributeIdentifier" );
        
        $current_version = $this->CurrentVersion;
        
        $object = $this->contentObject();      
        $datamap = $object->dataMap();
        
        $filePathArray = $this->originalFilePath();
        
        //create convert directory
        if($this->createFolder($filePathArray['convertedPath']))
        {
            //create convert object directory
            if($this->createFolder($filePathArray['currentVerstionConvertedObjectPath']))
            {
                //create convert object current version directory
                if($this->createFolder($filePathArray['currentVerstionConvertedPath']))
                {
                    $this->doConvertAudio();
                }
            }
        }
    }
    
    function doConvertAudio()
    {
        $fileAttribute = $this->fileAttribute();
        
        $fileAttributeContent = $fileAttribute->content();
        
        $mimeTypeCategory = $fileAttributeContent->mimeTypeCategory();
        $mimeTypePart = $fileAttributeContent->mimeTypePart();
        
        //check that file is an audio file
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
                                  'ezaudioconvert.php' );
                                  
            //do conversion
            if ($convertMp3)
            {
                $this->convertFile('mp3');
            }
            
            if ($convertOgg)
            {
                $this->convertFile('ogg');
            }
        }
        else
        {
            $LogEntry = 'File is not of an audio type.';
            eZLog::write( $LogEntry, 
                          $logName = 'convert.log', 
                          $dir = 'var/log' );
            eZDebug::writeError( $LogEntry, 
                                  'ezaudioconvert.php' );
        }

    }
    
    function convertFile($type = 'mp3')
    {
        $ini = eZINI::instance( "ezaudioconvert.ini" );
        
        // get the settings for the file
        $ffmpeg = $ini->variable( "FileSettings", "ffmpegExecutable" );

        $LogEntry = 'Converting file to '.$type;
        eZLog::write( $LogEntry, 
                      $logName = 'convert.log', 
                      $dir = 'var/log' );
        eZDebug::writeNotice( $LogEntry, 
                              'ezaudioconvert.php' );
        
        $continue = true;
        $filePathArray = $this->originalFilePath();
        
        $filePath = $filePathArray['currentVerstionConvertedPath'];
        
        $fileName = $filePathArray['fileName'];
        
        $originalFilePath = $filePathArray['filePath'].$filePathArray['fileName'].'.'.$filePathArray['fileSuffix'];
        
        //check if file already exists. If so remove the file
        if (file_exists($filePath.'/'.$fileName.'.'.$type))
        {
            if (unlink($filePath.'/'.$fileName.'.'.$type))
            {
                $LogEntry = 'File exists. Removal Success';
                eZLog::write( $LogEntry, 
                              $logName = 'convert.log', 
                              $dir = 'var/log' );
                eZDebug::writeNotice( $LogEntry, 
                                      'ezaudioconvert.php' );
            }
            else
            {
                $LogEntry = 'File exists. Removal FAILED';
                eZLog::write( $LogEntry, 
                              $logName = 'convert.log', 
                              $dir = 'var/log' );
                eZDebug::writeNotice( $LogEntry, 
                                      'ezaudioconvert.php' );
                
                $continue = false;
            }
        }
        
        //make sure we are good to continue
        if ($continue)
        {
            if ($type == 'mp3')
            {
                $command = $ffmpeg.' -i '.$originalFilePath.' -b 192k '.$filePath.'/'.$fileName.'.'.$type;
            }
            else
            {
                $command = $ffmpeg.' -i '.$originalFilePath.' -acodec libvorbis -aq 60 '.$filePath.'/'.$fileName.'.'.$type;
            }
        
            $LogEntry = 'Converting file with command: '.$command;
            eZLog::write( $LogEntry, 
                          $logName = 'convert.log', 
                          $dir = 'var/log' );
            eZDebug::writeNotice( $LogEntry, 
                                  'ezaudioconvert.php' );
            
            //run the conversion command.                   
            exec( $command );
        }
    }

    function fileAttribute()
    {
        $ini = eZINI::instance( "ezaudioconvert.ini" );
        
        // get the settings for the file
        $attributeIdentifier = $ini->variable( "FileSettings", "attributeIdentifier" );

        $object = $this->contentObject();
        $datamap = $object->dataMap();
        
        return $datamap[$attributeIdentifier];
    }
    
    function createFolder($directory)
    {
        if (!is_dir($directory))
        {
            $LogEntry = 'Directory '.$directory.'/ needs to be created';
            eZLog::write( $LogEntry, 
                          $logName = 'convert.log', 
                          $dir = 'var/log' );
            eZDebug::writeNotice( $LogEntry, 
                                  'ezaudioconvert.php' );

            if(mkdir($directory, 0777))
            {
                $LogEntry = 'Create directory ('.$directory.'): SUCCESS';
                eZLog::write( $LogEntry, 
                              $logName = 'convert.log', 
                              $dir = 'var/log' );
                eZDebug::writeNotice( $LogEntry, 
                                      'ezaudioconvert.php' );
                                      
                return true;
            }
            else
            {
                $LogEntry = 'Create directory ('.$directory.'): FAILURE';
                eZLog::write( $LogEntry, 
                              $logName = 'convert.log', 
                              $dir = 'var/log' );
                eZDebug::writeNotice( $LogEntry, 
                                      'ezaudioconvert.php' );
                
                return false;
            }
        }
        else
        {
            return true;
        }
    }
    
    function originalFilePath()
    {
        $ini = eZINI::instance( "ezaudioconvert.ini" );
        
        // get the settings for the file
        $rootPath = $ini->variable( "FileSettings", "rootPath" );
        $attributeIdentifier = $ini->variable( "FileSettings", "attributeIdentifier" );
        
        $object = $this->contentObject();      
        $datamap = $object->dataMap();
        
        $fileAttribute = $datamap[$attributeIdentifier];
        
        $fileAttributeContent = $fileAttribute->content();
        
        $filePath = $fileAttributeContent->filePath();
        $fileName = $fileAttributeContent->Filename;
        
        $fileNameArray = explode('.', $fileName);
        
        $filePath = str_replace($fileName, '', $filePath);
        
        if (count($fileNameArray) > 2)
        {
            $fileName = '';
            foreach ($fileNameArray as $key => $fileNamePart)
            {
                if ($fileNamePart != end($fileNameArray))
                {
                    if ($key != 0)
                    {
                        $fileName .= '.';
                    }
                    $fileName .= $fileNamePart;
                }
            }
        }
        else
        {
            $fileName = $fileNameArray[0];
        }

        return array(
            'fileName' => $fileName,
            'fileSuffix' => end($fileNameArray),
            'filePath' => $filePath,
            'convertedPath' => $rootPath.$filePath.'converted',
            'currentVerstionConvertedObjectPath' => $rootPath.$filePath.'converted/'.$this->contentObject_ID,
            'currentVerstionConvertedPath' => $rootPath.$filePath.'converted/'.$this->contentObject_ID.'/'.$this->CurrentVersion,
            'playerPath' => $filePath.'converted/'.$this->contentObject_ID.'/'.$this->CurrentVersion,
            'fileNameArray' => $fileNameArray
        );
    }
    
    function contentObject()
    {
        $object = eZContentObject::fetch($this->contentObject_ID);
        return $object;
    }
    
    function displayPlayer()
    {
        // initialise Template object
        $tpl = eZTemplate::factory();
        
        $filePath = $this->originalFilePath();
        $fileName = $filePath['fileName'];
        $filePath = $filePath['playerPath'];
        
        $tpl->setVariable( 'fileName', $fileName );
        $tpl->setVariable( 'filePath', $filePath );
        $tpl->setVariable( 'eZAudio', $this );
        
        return $tpl->fetch( 'design:ezaudio/player.tpl' );
    }
    
    static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( ezAudioConvert::definition(),
                                                null,
                                                array( 'id' => $id ),
                                                $asObject );
    }
    
    static function fetchByNodeId($id, $asObject = true )
    {
        $node = eZContentObjectTreeNode::fetch($id);
        $object = $node->object();
    
        return eZPersistentObject::fetchObject( ezAudioConvert::definition(),
                                                null,
                                                array( 'id' => $object->ID ),
                                                $asObject );
    }
    
    
    public $mainNodeID;
    
}

?>