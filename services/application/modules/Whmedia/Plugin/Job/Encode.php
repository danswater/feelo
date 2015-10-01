<?php

class Whmedia_Plugin_Job_Encode extends Core_Plugin_Job_Abstract
{
  protected $_ffmpeg_path;
  protected $_video;

  public function  __construct(Zend_Db_Table_Row_Abstract $job, $jobType = null) {
      parent::__construct($job, $jobType);
      $this->_ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->whvideo_ffmpeg_path;
  }

  protected function _execute() {
    // Get job and params
    $job = $this->getJob();

    // No video id?
    if( !($media_id = $this->getParam('media_id')) ) {
      $this->_setState('failed', 'No video identity provided.');
      $this->_setWasIdle();
      return;
    }

    // Get video object
    $video = Engine_Api::_()->getItem('whmedia_media', $media_id);
    if( !$video || !($video instanceof Whmedia_Model_Media) ) {
      $this->_setState('failed', 'Video is missing.');
      $this->_setWasIdle();
      return;
    }
    $this->_video = $video;
    // Check video encode
    if( 1 != $video->encode ) {
      $this->_setState('failed', 'Video has already been encoded, or has already failed encoding.');
      $this->_setWasIdle();
      return;
    }

    // Process
    try {
      $this->_process($video);
      $this->_setIsComplete(true);
    } catch( Exception $e ) {
      $this->_setState('failed', 'Exception: ' . $e->getMessage());
    }
  }

  protected function _process($video)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    try {
        // Make sure FFMPEG path is set
        $ffmpeg_path = $this->_ffmpeg_path;
        if( !$ffmpeg_path ) {
          throw new Engine_Exception('Ffmpeg not configured');
        }
        // Make sure FFMPEG can be run
        if( !@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path) ) {
          $output = null;
          $return = null;
          exec($ffmpeg_path . ' -version', $output, $return);
          if( $return > 0 ) {
            throw new Engine_Exception('Ffmpeg found, but is not executable');
          }
        }

        // Make sure flvtool2 path is set
        $flvtool2_path = $settings->whvideo_flvtool2_path;
        if( !$flvtool2_path ) {
          $flvtool2_path = false;
        }
        // Make sure flvtool2 can be run
        if( !@file_exists($flvtool2_path) || !@is_executable($flvtool2_path) ) {
          $output = null;
          $return = null;
          exec($flvtool2_path, $output, $return);
          if( $return > 0 ) {
            $flvtool2_path = false;
          }
        }

        // Make sure faad path is set
        $faad_path = $settings->whvideo_flvtool2_faad;
        if( !empty($faad_path) ) {
            // Make sure flvtool2 can be run
            if( !@file_exists($faad_path) || !@is_executable($faad_path) ) {
              $output = null;
              $return = null;
              exec($faad_path . ' -h', $output, $return);
              if( $return != 1 ) {
                $faad_path = false;
              }
            }
        }
        else
            $faad_path = false;

        // Check we can execute
        if( !function_exists('shell_exec') ) {
          throw new Engine_Exception('Unable to execute shell commands using shell_exec(); the function is disabled.');
        }

        // Check the video temporary directory
        $tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'whmedia';
        if( !is_dir($tmpDir) ) {
          if( !mkdir($tmpDir, 0777, true) ) {
            throw new Engine_Exception('Media temporary directory did not exist and could not be created.');
          }
        }
        if( !is_writable($tmpDir) ) {
          throw new Engine_Exception('Media temporary directory is not writable.');
        }

        // Get the video object
        if( is_numeric($video) ) {
          $video = Engine_Api::_()->getItem('whmedia_media', $video_id);
        }

        if( !($video instanceof Whmedia_Model_Media) ) {
          throw new Engine_Exception('Argument was not a valid media');
        }
    }
    catch (Exception $e) {
        $video->encode = 8;
        $video->save();
        throw $e;
    }
    // Update to encoding encode
    $video->encode = 2;
    $video->save();
    $project = $video->getParent();
    // Prepare information
    $owner = $project->getOwner();

    // Pull video from storage system for encoding
    $storageObject = Engine_Api::_()->getItemTable('storage_file')->fetchRow(array('parent_type = ?' => 'whmedia_media',
                                                                                   'parent_id = ?' => $video->media_id,
                                                                                   'type is null'));
    if( !$storageObject ) {
      $video->encode = 9;
      $video->save();
      throw new Engine_Exception('Video storage file was missing');
    }

    $originalPath = $storageObject->temporary();
    if( !file_exists($originalPath) ) {
      $video->encode = 10;
      $video->save();
      throw new Engine_Exception('Could not pull to temporary file');
    }
    // Prepare logger
    $log = null;

    $log = new Zend_Log();
    $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/whmedia.log'));

    $outMediaInfo = 'User: ' . $this->_video->getOwner()->getTitle() . PHP_EOL .
                    'Project: ' . $this->_video->getProject()->getTitle() . PHP_EOL .
                    'Media ID: ' . $this->_video->getIdentity() . PHP_EOL;

    if ($storageObject->mime_major == 'audio') {
        $outputPath   = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_converted.ogg';
        $audioCommand = $ffmpeg_path . ' '
          . '-i ' . escapeshellarg($originalPath) . ' '
          . ' -acodec libvorbis -f ogg' . ' '
          . '-y ' . escapeshellarg($outputPath) . ' '
          . '2>&1'
          ;

        // Prepare output header
        $output  = PHP_EOL;
        $output .= $outMediaInfo;
        $output .= $originalPath . PHP_EOL;
        $output .= $outputPath . PHP_EOL;

        // Execute video encode command
        $Output = $output .
          $audioCommand . PHP_EOL .
          shell_exec($audioCommand);

        // Log
        if( $log ) {
          $log->log($Output, Zend_Log::INFO);
        }

        // Check for failure
        $success = true;
       
        // Unsupported format
        if( preg_match('/Unknown format/i', $videoOutput) ||
            preg_match('/Unsupported codec/i', $videoOutput) ||
            preg_match('/patch welcome/i', $videoOutput) ||
            preg_match('/Audio encoding failed/i', $videoOutput) ||
            !is_file($outputPath) ||
            filesize($outputPath) <= 0 ) {
          $success = false;
          $video->encode = 3;
        }
        if( !$success ) {
            $exceptionMessage = '';
            $translate = Zend_Registry::get('Zend_Translate');
          $db = $video->getTable()->getAdapter();
          $db->beginTransaction();
          try {
            $video->save();
            $exceptionMessage ='Audio format is not supported by FFMPEG.';
              $notificationMessage = $translate->translate(sprintf(
                'Audio conversion failed. Audio format is not supported by FFMPEG. Please try %1$sagain%2$s.',
                '',
                ''
              ), $language);
            Engine_Api::_()->getDbtable('notifications', 'activity')
              ->addNotification($owner, $owner, $project, 'whmedia_processed_failed', array(
                'message' => $notificationMessage,
                'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'project_id' => $project->project_id), 'whmedia_project', true),
              ));

            $db->commit();
         } catch( Exception $e ) {
            $videoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;
            if( $log ) {
              $log->write($outMediaInfo . $e->__toString(), Zend_Log::ERR);
            }
            $db->rollBack();
          }
          // Write to additional log in dev
          if( APPLICATION_ENV == 'development' ) {
            file_put_contents($tmpDir . '/' . $video->media_id . '.txt', $videoOutput);
          }

          throw new Engine_Exception($exceptionMessage);
        }
        else {
           $params = array(
                        'parent_id' => $video->getIdentity(),
                        'parent_type' => $video->getType(),
                        'user_id' => $owner->getIdentity()
                      );

          $db = $video->getTable()->getAdapter();
          $db->beginTransaction();
          try {
              $secondFileRow = Engine_Api::_()->storage()->create($outputPath, array_merge($params, array('type' => 'audio.html5',
                                                                                                          'parent_file_id' => $storageObject->file_id)));
              $secondFileRow->setFromArray(array('mime_major' => 'audio',
                                                 'mime_minor' => 'ogg') )
                            ->save();
              $db->commit();
              unlink($secondPath);
              unlink($originalPath);
           } catch( Exception $e ) {
            $db->rollBack();

            // delete the files from temp dir
            unlink($secondPath);
            unlink($originalPath);

            $video->encode = 7;
            $video->save();

            // notify the owner
            $translate = Zend_Registry::get('Zend_Translate');
            $notificationMessage = '';
            $language = ( !empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
            if( $video->encode == 7 ) {
              $notificationMessage = $translate->translate(sprintf(
                'Audio conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.',
                '',
                ''
              ), $language);
            }
            Engine_Api::_()->getDbtable('notifications', 'activity')
              ->addNotification($owner, $owner, $project, 'whmedia_processed_failed', array(
                'message' => $notificationMessage,
                'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'project_id' => $project->project_id), 'whmedia_project', true),
              ));

            throw $e; // throw
          }
          $video->encode = 0;
          $video->save();
          
        }

    }
    else {
        $outputPath   = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_converted.flv';
        $thumbPath    = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_vthumb.jpg';
        if ($settings->getSetting( 'both.video.format', 0))
          $secondPath    = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_sconverted.mp4';
        if ($settings->getSetting('hd_video_format', 0))
          $hdPath    = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_hdconverted.flv';
        if ($faad_path !== false) {
            try {
                $file_audio_info = Engine_Api::_()->whmedia()->getAudioInfo($originalPath);
                $channels = (int)$file_audio_info['channels'];
                $rate = (int)$file_audio_info['rate'];
                if ( $channels > 2 and $rate > 45000) {
                    /*
                     *  ffmpeg -i ./28.mkv -acodec copy ./source.6.aac
                        faad -d -o source.2.aac source.6.aac
                        ffmpeg -y -i ./28.mkv -i source.2.aac -map 0:0 -map 1:0 -vcodec copy -acodec copy output.avi
                     */
                    $audioFAADPath    = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_faad.' . $file_audio_info['codec'];
                    $audioFAADPath2    = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_faad2.aac';
                    $tmp_originalPath = $originalPath . '.avi';
                    //Get audio channel
                    $acodecCommand = $ffmpeg_path . ' '
                                    . '-i ' . escapeshellarg($originalPath) . ' '
                                    . ' -acodec copy ' . escapeshellarg($audioFAADPath) . ' '
                                    . '2>&1'
                                    ;
                    // Prepare output header
                    $output  = PHP_EOL;
                    $output .= $outMediaInfo;
                    $output .= $originalPath . PHP_EOL;
                    $output .= $audioFAADPath . PHP_EOL;
                    $output .= 'Get audio channel for FAAD' . PHP_EOL;

                    // Execute video encode command
                    $acodecOutput = $output . $acodecCommand . PHP_EOL .
                                    shell_exec($acodecCommand);

                    // Log
                    if( $log ) {
                      $log->log($acodecOutput, Zend_Log::INFO);
                    }
                    if ($file_audio_info['codec'] != 'aac' and file_exists($audioFAADPath)) {
                        $tmp_audioFAADPath    = $tmpDir . DIRECTORY_SEPARATOR . $video->getIdentity() . '_faad.aac';
                        // convert to acc audio
                        $acodecCommand = $ffmpeg_path . ' '
                                    . '-i ' . escapeshellarg($audioFAADPath) . ' '
                                    . ' -acodec libfaac ' . escapeshellarg($tmp_audioFAADPath) . ' '
                                    . '2>&1'
                                    ;
                        // Prepare output header
                        $output  = PHP_EOL;
                        $output .= $outMediaInfo;
                        $output .= $originalPath . PHP_EOL;
                        $output .= $audioFAADPath . PHP_EOL;
                        $output .= 'convert to acc audio' . PHP_EOL;

                        // Execute video encode command
                        $acodecOutput = $output . $acodecCommand . PHP_EOL .
                                        shell_exec($acodecCommand);

                        // Log
                        if( $log ) {
                          $log->log($acodecOutput, Zend_Log::INFO);
                        }
                        if (file_exists($tmp_audioFAADPath)) {
                            $audioFAADPath = $tmp_audioFAADPath;
                        }
                    }
                    if (file_exists($audioFAADPath)) {
                        // downmix audio
                        $acodecCommand = $faad_path . ' '
                                        . ' -d -o ' . escapeshellarg($audioFAADPath2) . ' ' . escapeshellarg($audioFAADPath) . ' '
                                        . '2>&1'
                                        ;
                        // Prepare output header
                        $output  = PHP_EOL;
                        $output .= $outMediaInfo;
                        $output .= $originalPath . PHP_EOL;
                        $output .= $audioFAADPath . PHP_EOL;
                        $output .= 'FAAD work' . PHP_EOL;

                        // Execute video encode command
                        $acodecOutput = $output . $acodecCommand . PHP_EOL .
                                        shell_exec($acodecCommand);

                        // Log
                        if( $log ) {
                          $log->log($acodecOutput, Zend_Log::INFO);
                        }
                    }
                    if (file_exists($audioFAADPath2)) {
                        //Mix audio with video
                        $acodecCommand = $ffmpeg_path . ' '
                                        . ' -y -i ' . escapeshellarg($originalPath) . ' '
                                        . ' -i ' . escapeshellarg($audioFAADPath2) . ' -map 0:0 -map 1:0 -vcodec copy -acodec copy '
                                        . escapeshellarg($tmp_originalPath) . ' '
                                        . '2>&1'
                                        ;
                        // Prepare output header
                        $output  = PHP_EOL;
                        $output .= $outMediaInfo;
                        $output .= $originalPath . PHP_EOL;
                        $output .= $audioFAADPath . PHP_EOL;
                        $output .= 'Mix audio with video' . PHP_EOL;

                        // Execute video encode command
                        $acodecOutput = $output . $acodecCommand . PHP_EOL .
                                        shell_exec($acodecCommand);

                        // Log
                        if( $log ) {
                          $log->log($acodecOutput, Zend_Log::INFO);
                        }
                    }
                    if (file_exists($tmp_originalPath)) {
                        $originalPath = $tmp_originalPath;
                    }
                }
            }
            catch(Exception $e){}
            file_exists($audioFAADPath) && unlink($audioFAADPath);
            file_exists($audioFAADPath2) && unlink($audioFAADPath2);
            file_exists($tmp_audioFAADPath) && unlink($tmp_audioFAADPath);
        }
        $video_width = $settings->getSetting('video_width', '320');
        $video_height = $settings->getSetting('video_height', '240');
        $videoDimension = Engine_Api::_()->whmedia()->getVideoDimension($originalPath);
        $VideoEncodeDimension = $this->_getVideoEncodeDimension($video_width, $video_height, $videoDimension['width'], $videoDimension['height']);
        
	// get video orientation
    switch( Engine_Api::_()->whmedia()->getVideoOrientation( $originalPath ) ) {
      case 90:
        $rotate    = '-vf transpose=1';
        //$VideoEncodeDimension = $this->_getVideoEncodeDimension( $video_width, $video_height, $videoDimension[ 'height' ], $videoDimension[ 'width' ] );
      break;
	  
	  case 180:
		$rotate = '-vf transpose=1,transpose=1';
	  break;
	  
	  case 270:
		$rotate = '-vf transpose=1,transpose=1,transpose=1';
      break;
	  
      default:
        $rotate = '';
      break;
    }
	
    $log->log( 'ROTATION' . $rotate, Zend_Log::INFO );
    $log->log( 'ORIENTATION' . Engine_Api::_()->whmedia()->getVideoOrientation( $originalPath ), Zend_Log::INFO );
        $log->log( 'DOES VIDEO ROTATED?'. $videoDimension[ 'height' ] .' > '. $videoDimension[ 'width' ], Zend_Log::INFO);    
    $videoCommand = $ffmpeg_path . ' '
          . '-i ' . escapeshellarg($originalPath) . ' '
          . '-ab 64k' . ' '
          . '-ar 44100' . ' '
          . '-qscale 5' . ' '
          . '-vcodec flv' . ' '
          . '-f flv' . ' '
          . '-r 25' . ' '
      . $rotate . ' '
          . "-s {$VideoEncodeDimension['width']}x{$VideoEncodeDimension['height']}" . ' '
          . '-v 2' . ' '
          . '-y ' . escapeshellarg($outputPath) . ' '
          . '2>&1'
          ;

        // Prepare output header
        $output  = PHP_EOL;
        $output .= $outMediaInfo;
        $output .= $originalPath . PHP_EOL;
        $output .= $outputPath . PHP_EOL;
        $output .= $thumbPath . PHP_EOL;

        // Execute video encode command
        $videoOutput = $output .
          $videoCommand . PHP_EOL .
          shell_exec($videoCommand);

        // Log
        if( $log ) {
          $log->log( 'FLV LOG'. $videoOutput, Zend_Log::INFO);
        }

        // Check for failure
        $success = true;

        // Unsupported format
        if( preg_match('/Unknown format/i', $videoOutput) ||
            preg_match('/Unsupported codec/i', $videoOutput) ||
            preg_match('/patch welcome/i', $videoOutput) ||
            preg_match('/Audio encoding failed/i', $videoOutput) ||
            !is_file($outputPath) ||
            filesize($outputPath) <= 0 ) {
          $success = false;
          $video->encode = 4;
        }

        // This is for audio files
        else if( preg_match('/video:0kB/i', $videoOutput) ) {
          $success = false;
          $video->encode = 5;
        }

        // Failure
        if( !$success ) {

          $exceptionMessage = '';

          $db = $video->getTable()->getAdapter();
          $db->beginTransaction();
          try {
            $video->save();


            // notify the owner
            $translate = Zend_Registry::get('Zend_Translate');
            $language = ( !empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
            $notificationMessage = '';

            if( $video->encode == 4 ) {
              $exceptionMessage ='Video format is not supported by FFMPEG.';
              $notificationMessage = $translate->translate(sprintf(
                'Video conversion failed. Video format is not supported by FFMPEG. Please try %1$sagain%2$s.',
                '',
                ''
              ), $language);
            } else if( $video->encode == 5 ) {
              $exceptionMessage = 'Incorrect video format.';
              $notificationMessage = $translate->translate(sprintf(
                'Video conversion failed. Incorrect video format. Please try %1$sagain%2$s.',
                '',
                ''
              ), $language);
            } else {
              $exceptionMessage = 'Unknown encoding error.';
              $notificationMessage = 'Encoding error.';
            }

            Engine_Api::_()->getDbtable('notifications', 'activity')
              ->addNotification($owner, $owner, $project, 'whmedia_processed_failed', array(
                'message' => $notificationMessage,
                'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'project_id' => $project->project_id), 'whmedia_project', true),
              ));

            $db->commit();
          } catch( Exception $e ) {
            $videoOutput .= PHP_EOL . $e->__toString() . PHP_EOL;
            if( $log ) {
              $log->write($outMediaInfo . $e->__toString(), Zend_Log::ERR);
            }
            $db->rollBack();
          }

          // Write to additional log in dev
          if( APPLICATION_ENV == 'development' ) {
            file_put_contents($tmpDir . '/' . $video->media_id . '.txt', $videoOutput);
          }

          throw new Engine_Exception($exceptionMessage);
        }

        // Success
        else
        {
            if (!empty($flvtool2_path)) {
                $videoCommand = $flvtool2_path . ' -UP ' . escapeshellarg($outputPath) . ' 2>&1' ;


                // Execute video encode command
                $videoOutput_flvtool = $videoCommand . PHP_EOL .
                                                                shell_exec($videoCommand);

                // Log
                if( $log ) {
                    $log->log(PHP_EOL .$outMediaInfo . $videoOutput_flvtool, Zend_Log::INFO);
                }
            }
            if ($settings->getSetting('both.video.format', 0)) {
                $videoCommand = $ffmpeg_path . ' '
                                . '-i ' . escapeshellarg($originalPath)
                               // . ' -acodec libfaac -ab 128k '
                                . " -s {$VideoEncodeDimension['width']}x{$VideoEncodeDimension['height']} "
                                . ' -ac 2 -vcodec libx264 -crf 22 -threads 0 -f mp4 '
                                . escapeshellarg($secondPath) . ' 2>&1' ;
                // Execute video encode command
                $videoOutput_mp4 = $videoCommand . PHP_EOL .
                                                            shell_exec($videoCommand);

                // Log
                if( $log ) {
                    $log->log(PHP_EOL .$outMediaInfo . $videoOutput_mp4, Zend_Log::INFO);
                }
            }

            if ($settings->getSetting('hd_video_format', 0)) {
                $VideoEncodeDimension = $this->_getVideoEncodeDimension(1280, 720, $videoDimension['width'], $videoDimension['height']);

        if( !empty( $rotate )) {
          $VideoEncodeDimension = $this->_getVideoEncodeDimension( 1280, 720, $videoDimension[ 'height' ], $videoDimension[ 'width' ] );
        }
        
          if ($VideoEncodeDimension['height'] >= 720) {
                    $videoCommand = $ffmpeg_path . ' '
                                                 . '-i ' . escapeshellarg($originalPath) . ' '
                                                 . '-ab 128k' . ' '
                                                 . '-ar 44100' . ' '
                                                 . '-qscale 5' . ' '
                                                 . '-vcodec flv' . ' '
                                                 . '-f flv' . ' '
                                                 . '-r 25' . ' '
                          . $rotate . ' '
                                                 . "-s {$VideoEncodeDimension['width']}x{$VideoEncodeDimension['height']}" . ' '
                                                 . '-v 2' . ' '
                                                 . '-y ' . escapeshellarg($hdPath) . ' '
                                                 . '2>&1'
                                                 ;
                    // Execute video encode command
                    $videoOutputHD = $videoCommand . PHP_EOL .
                                   shell_exec($videoCommand);

                    $hd = true;
                    // Log
                    if( $log ) {
                        $log->log(PHP_EOL .$outMediaInfo . $videoOutputHD, Zend_Log::INFO);
                    }
                    if (!empty($flvtool2_path)) {
                        $videoCommand = $flvtool2_path . ' -UP ' . escapeshellarg($hdPath) . ' 2>&1' ;

                        // Execute video encode command
                        $videoOutputHD = $videoCommand . PHP_EOL . shell_exec($videoCommand);

                        // Log
                        if( $log ) {
                            $log->log(PHP_EOL .$outMediaInfo . $videoOutputHD, Zend_Log::INFO);
                        }
                    }
                }
            }
          // Get duration of the video to caculate where to get the thumbnail
          if( preg_match('/Duration:\s+(.*?)[.]/i', $videoOutput, $matches) ) {
            list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
            $duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
          } else {
            $duration = 0; // Hmm
          }
    
          // Log duration
          if( $log ) {
            $log->log(PHP_EOL .$outMediaInfo . 'Duration: ' . $duration, Zend_Log::INFO);
          }
          $video->duration = $duration;
          // Fetch where to take the thumbnail
          $thumb_splice = $duration / 2; 

      // Thumbnail proccess command
          $thumbCommand = $ffmpeg_path . ' '
          . '-i ' . escapeshellarg($originalPath) . ' '
          . '-an -r 1' . ' '
          . '-ss '. $thumb_splice . ' '
          . '-t 00:00:01 -v 2' . ' '
       . $rotate . ' '
          . '-y ' . escapeshellarg($thumbPath) . ' '
          . '2>&1'
          ;

          // Process thumbnail
          $thumbOutput = $output .
            $thumbCommand . PHP_EOL .
            shell_exec($thumbCommand);

          // Log thumb output
          if( $log ) {
            $log->log(PHP_EOL .$outMediaInfo . $thumbOutput, Zend_Log::INFO);
          }

          // Check output message for success
          $thumbSuccess = true;
          if( preg_match('/video:0kB/i', $thumbOutput) ) {
            $thumbSuccess = false;
          }
          if (!file_exists($thumbPath)) {
              $thumbSuccess = false;
          }
          $image_width = $settings->getSetting('image_width', '600');
          $image_height = $settings->getSetting('image_height', '900');
          // Resize thumbnail
          if( $thumbSuccess ) {
            $image = Engine_Image::factory(array('quality' => 100));
            $image->open($thumbPath)
              ->resize($image_width, $image_height)
              ->write($thumbPath)
              ->destroy();
          }
          else {
              $log->log(PHP_EOL . $outMediaInfo . PHP_EOL . 'Thumb Error. Thumb not created.', Zend_Log::INFO);
          }
          // Save video and thumbnail to storage system
          $params = array(
            'parent_id' => $video->getIdentity(),
            'parent_type' => $video->getType(),
            'user_id' => $owner->getIdentity()
          );

          $db = $video->getTable()->getAdapter();
          $db->beginTransaction();
          $tmpParams = array_merge($params,array('name' => $storageObject->name,
                                                 'mime_major' => $storageObject->mime_major,
                                                 'mime_minor' => strtolower($storageObject->mime_minor) ));
          try {
            $storageObject->store($outputPath);
            $storageObject->setFromArray($tmpParams);
            $storageObject->save();
      
      if( pathinfo( $originalPath, PATHINFO_EXTENSION  )  == 'mp4' ) {
                $secondFileRow = Engine_Api::_()->storage()->create($originalPath, array_merge($params, array('type' => 'video.html5',
                                                                                                            'parent_file_id' => $storageObject->file_id,
                                                                                                            'mime_major' => 'video',
                                                                                                            'mime_minor' => 'mp4'    )));
                $secondFileRow->setFromArray(array('mime_major' => 'video',
                                                   'mime_minor' => 'mp4') )
                              ->save();
                unlink($secondPath);
      }     
      
            if ($settings->getSetting('both.video.format', 0)) {
                $secondFileRow = Engine_Api::_()->storage()->create($secondPath, array_merge($params, array('type' => 'video.html5',
                                                                                                            'parent_file_id' => $storageObject->file_id,
                                                                                                            'mime_major' => 'video',
                                                                                                            'mime_minor' => 'mp4'    )));
                $secondFileRow->setFromArray(array('mime_major' => 'video',
                                                   'mime_minor' => 'mp4') )
                              ->save();
                unlink($secondPath);
            }
            if (isset ($hd) and $hd === true) {
                $hdFileRow = Engine_Api::_()->storage()->create($hdPath, array_merge($params, array('type' => 'video.hd',
                                                                                                    'parent_file_id' => $storageObject->file_id,
                                                                                                    'mime_major' => 'video',
                                                                                                    'mime_minor' => 'flv'    )));
                $hdFileRow->setFromArray(array('mime_major' => 'video',
                                               'mime_minor' => 'flv') )
                           ->save();
                unlink($hdPath);
            }
            if( $thumbSuccess ) {
              $thumbFileRow = Engine_Api::_()->storage()->create($thumbPath, array_merge($params, array('type' => 'thumb.etalon', 'parent_file_id' => $storageObject->file_id)));
              
               /*** video 4 images ***/
              
              /*** video 4 images ***/ 

            }
            $db->commit();

          } catch( Exception $e ) {
            $db->rollBack();

            // delete the files from temp dir
            unlink($originalPath);
            unlink($outputPath);
            if( $thumbSuccess ) {
              unlink($thumbPath);
            }

            $video->encode = 7;
            $video->save();

            // notify the owner
            $translate = Zend_Registry::get('Zend_Translate');
            $notificationMessage = '';
            $language = ( !empty($owner->language) && $owner->language != 'auto' ? $owner->language : null );
            if( $video->encode == 7 ) {
              $notificationMessage = $translate->translate(sprintf(
                'Video conversion failed. You may be over the site upload limit.  Try %1$suploading%2$s a smaller file, or delete some files to free up space.',
                '',
                ''
              ), $language);
            }
            Engine_Api::_()->getDbtable('notifications', 'activity')
              ->addNotification($owner, $owner, $project, 'whmedia_processed_failed', array(
                'message' => $notificationMessage,
                'message_link' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index', 'project_id' => $project->project_id), 'whmedia_project', true),
              ));

            throw $e; // throw
          }
      
      if($rotate != ''){
      $tempDim = $videoDimension;
      $videoDimension['width'] = $tempDim['height'];
      $videoDimension['height'] = $tempDim['width'];
      }
      
          $video->size = json_encode($videoDimension);
          $video->encode = 0;
          $video->save();

          // delete the files from temp dir
          unlink($originalPath);
          unlink($outputPath);
          unlink($thumbPath);

         }
    }
  }

 
  protected function _getVideoEncodeDimension($maxWidth, $maxHeight, $originalWidth, $originalHeight) {
    if ($originalHeight <= $maxHeight) {
        return array('width' => $originalWidth,
                     'height' => $originalHeight);
    }
    else {
        $outWidth = (int)(($maxHeight/$originalHeight)*$originalWidth);
        if (($outWidth%2))
            $outWidth++;
        return array('width' => $outWidth,
                     'height' => $maxHeight);
    }
  }

  protected function  _setState($state, $message = null, $doSave = true) {
    if (empty ($this->_video)) {
        $message = 'Media ID: ' . $this->getParam('media_id', '-') . PHP_EOL . $message;
    }
    else {
        $message = 'User: ' . $this->_video->getOwner()->getTitle() . PHP_EOL .
                   'Project: ' . $this->_video->getProject()->getTitle() . PHP_EOL .
                   'Media ID: ' . $this->_video->getIdentity() . PHP_EOL .
                   $message . PHP_EOL .
                   'There was a problem with ffmpeg video processing. Check log file for details "temporary/log/whmedia.log"';
    }
    parent::_setState($state, $message, $doSave);
    return $this;
  }
}