<?php 

class FileScanner{

   public static function dirToArray( $dir ){
      $result = array(); 
      $cdir = scandir($dir); 
      foreach ($cdir as $key => $value) 
      { 
         if (!in_array($value,array(".",".."))) 
         { 
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
            { 
               $result[$value] = FileScanner::dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
            } 
            else 
            { 
               $result[] = $value; 
            } 
         } 
      } 
      return $result; 
   }


   public static function __pathFiles( $pathFiles, $extra_path = "" ){

      $result = array();

      foreach( $pathFiles as $ext_path =>$pathFile ){

         if( is_array( $pathFile ) ){

            $result = array_merge( $result, FileScanner::__pathFiles( $pathFile, $ext_path . "/" ) ); 
         }
         else{
            $result[] =  $extra_path . $pathFile;
         }

      }

      return $result;

   }

   public static function dirFileToArray( $dir ){
     $pathFiles = FileScanner::dirToArray( $dir );
     return FileScanner::__pathFiles( $pathFiles );

   }


}
