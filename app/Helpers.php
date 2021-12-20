<?php
// mklink /D storage F:\wamp64\www\api-stockop-lumen\storage\app\public
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

if( !function_exists("saveAndResizeImage") )
{
    function saveAndResizeImage( $image, $type, $dir_name, $width, $height, $old_image = null )
    {
        if( isset( $old_image) )
            unlinkFile( $old_image );

        $dir        =   $type . '/' . $dir_name;
    
        // Create directory first
        if(!File::exists( $dir ))
        {
            File::makeDirectory($dir, 0755, true, true);
            // mkdir( $dir, 0755, true, true );
        }

        $file_name  =   uniqid().'.' .$image->getClientOriginalExtension();
        $str_path   =   $dir . '/' . $file_name;
        $path       =   public_path($str_path);


        // Create new Canvas and insert the image
        $img        =   Image::make( $image )->resize( $width, $height, function($constraint)
                        {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });

        $img->save( $path );

        return $str_path;
    }
}

if( !function_exists("fdatastatus"))
{
    function fdatastatus( $status )
    {
        $status = 0;
        if(empty($data->updated_at)){
            $status = 0;
        }elseif($data->qty != $data->stockop){
            $status = 1;
        }else{
            $status = 2;
        }

        return $status;
    }
}

if (!function_exists('public_path')) {
    /**
     * Return the path to public dir
     *
     * @param null $path
     *
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}

