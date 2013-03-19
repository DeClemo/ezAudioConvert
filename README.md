ezAudioConvert
==============

Audio conversion extension for eZ Publish 4.x

Dependancies
==============
For this extension to work you will need to have ffpmeg installed.

I use an open source installer that can be found here http://www.ffmpeginstaller.com/

I recommend using this so you end up with the same plugins installed.

If you manually install you will ned at a bare minimum LAME and libvorbis. As this extension will try to encode to mp3 and ogg. (for html5 audio support across all browsers)

Installation
==============
To install simply place the extension files into /<path/to/ezpublish>/extension/ezaudioconvert/

1) In site.ini.append.php add the below. [ExtensionSettings] will already exist. Just add the ActiveExtensions[]=ezaudioconvert to the list.

[ExtensionSettings]
ActiveExtensions[]=ezaudioconvert

2) Regenerate the auto load arrays either in command line or the admin interface.

3) Clear all caches.

Usage
==============
There is an event which you can place on the publish after trigger which will convert the audio files on input.

Set the required settings in the .ini file (path to ez publish install, ffpmeg executable path and file, attribute identifier for the attribute in the content object you will be using.)

You could use the normal file content class but I would advise against it I have written in rudimentary checks to make sure the file is an audio file before conversion but I still like to use a seperate class for audio files to other files.

Template Operator
==============
You can call the player in a template using the template operator like this:
{$node|eZAudio('player')}

You can also call the player using the object {$node.object|eZAudio('player')}

I will also build a download link into the operator soon which will be accessed like this:
{$node|eZAudio('download')}

My plan is to have that build a download link that you can then attach to buttons etc. It will utilise the normal content/download function.

Credits
==============

The audio player used can be found at http://pupunzi.open-lab.com/mb-jquery-components/jquery-mb-miniaudioplayer/

