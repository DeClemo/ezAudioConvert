{ezcss_require( array( 'miniAudioPlayer/miniplayer.css' ) )} 
{ezscript_require( array( 'ezjsc::jquery', 'miniAudioPlayer/jquery.jplayer.min.js', 'miniAudioPlayer/jquery.mb.miniPlayer.js', 'audio.js' ) )}

<a id="audio-{$eZAudio.id}" class="audio {ldelim}ogg: '{concat($filePath, '/', $fileName, '.mp3')|ezroot('no', 'full')}', swfPath: '{'javascript/miniAudioPlayer/Jplayer.swf'|ezdesign('no')}' {rdelim}" href={concat($filePath, '/', $fileName, '.mp3')|ezroot('double', 'full')}>{$eZAudio.content_object.name|wash()}</a>
