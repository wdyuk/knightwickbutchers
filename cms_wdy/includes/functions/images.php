<?php

function delete_images($path)
{
	foreach (array('jpeg', 'jpg', 'JPG', 'JPEG', 'png', 'gif') as $ext) {
		if (strpos($path, UPLOADS_DIR) !== false) {
			@unlink($path . '.' . $ext);
		} else {
			@unlink(UPLOADS_DIR . $path . '.' . $ext);
		}
	}
}

function get_image_path($path, $include_uploads_path = true)
{
	foreach (array('jpeg', 'jpg', 'JPG', 'JPEG', 'png', 'gif') as $ext) {
		if (file_exists(UPLOADS_DIR . $path . '.' . $ext)) {
			
			if ($include_uploads_path) {
				return UPLOADS_DIR . $path . '.' . $ext;
			} else {
				return $path . '.' . $ext;
			}
		}
	}
	
	return false;
}

function get_image($path)
{
	foreach (array('jpeg', 'jpg', 'JPG', 'JPEG', 'png', 'gif') as $ext) {
		if (file_exists(UPLOADS_DIR . $path . '.' . $ext)) {
			return UPLOADS_URL . $path . '.' . $ext;
		}
	}
	
	return false;
}

function show_image($path, $attributes_arr = array())
{
	$img = get_image($path);
	$img_path = get_image_path($path);
	
	if ($img_path !== FALSE) {
		$info = getimagesize($img_path);
		$width = $info[0];
		$height = $info[1];
		
		$arr = array();
		foreach ($attributes_arr as $key => $value) {
			$arr[] = $key . '="' . $value . '"';
		}
		$attributes = implode(' ', $arr);
		
		printf('<img src="%s" %s width="%s" height="%s" />', $img, $attributes, $width, $height);
	}
}

function upload_image($image, $id, $folder)
{
	$supported_mime_types = array('jpeg' => 'image/jpeg',
								  'jpeg' => 'image/pjpeg',
								  'jpg' => 'image/jpeg',
								  'gif' => 'image/gif',
								  'png' => 'image/png',
								  'tif' => 'image/tiff',
								  'tiff' => 'image/tiff',
								  'bmp' => 'image/bmp');
	$mime_type = $image['type'];
	
	if (in_array($mime_type, $supported_mime_types)) {
		$ext = array_search($mime_type, $supported_mime_types);
		delete_images(UPLOADS_DIR . "$folder/$id");
		
		
		$upload_path =  UPLOADS_DIR . "$folder/$id.$ext";
		
		//=============== new
				
			if (($mime_type =='image/jpg')||($mime_type =='image/jpeg')){	
			
					
					$imagex = new SimpleImage();
					$imagex->load($image['tmp_name']);
					$imagex->resizeToWidth(650);
						$imagex->save($upload_path);
						echo '<p> upload path: '.$upload_path.'</p>';
						
											} else {
						
						move_uploaded_file($image['tmp_name'], $upload_path);
			}
		
		//============== end new
		
		
		
		/* olde
		
		move_uploaded_file($image['tmp_name'], $upload_path);
		end old */ 	
		return $upload_path;
	}
	
	return false;
}

function get_file_extension($file)
{
	return strtolower(substr($file['name'], strrpos($file, '.')));
}

function check_extension_valid($file, $ext)
{
	$filename = $file['name'];
	$file_ext = substr($filename, strrpos($filename, '.') + 1);
	
	if (strtolower($file_ext) == strtolower($ext)) {
		return true;
	}
	
	return false;
}

function upload_file($file, $id, $folder, $retain_file_name = true, $ext = false)
{
	if (is_null($file) || strlen(trim($file['name'])) == 0) {
		return;
	}
	
	if ($ext && !check_extension_valid($file, $ext)) {
		die();
	}
	
	if (isset($file) && strlen(trim($file['name'])) > 0) {
	
		$restricted_mime_types = array(
										'txt' => 'text/plain',
										'htm' => 'text/html',
										'html' => 'text/html',
										'php' => 'text/html',
										'css' => 'text/css',
										'js' => 'application/javascript',
										'json' => 'application/json',
										'xml' => 'application/xml',
										'swf' => 'application/x-shockwave-flash',
										'flv' => 'video/x-flv',
										
										// archives
										'zip' => 'application/zip',
										'rar' => 'application/x-rar-compressed',
										'exe' => 'application/x-msdownload',
										'msi' => 'application/x-msdownload',
										'cab' => 'application/vnd.ms-cab-compressed'
									);
		$mime_type = $file['type'];
		
		if (in_array($mime_type, $restricted_mime_types)) {
			return false;
		}
		
		delete_files($id, $folder);
		
		if ($retain_file_name) {
			$upload_path =  UPLOADS_DIR . "$folder/" . $id . '-' . $file['name'];
		} else {
			$upload_path =  UPLOADS_DIR . "$folder/" . $id . '-' . substr($file['name'], 0, strrpos($file['name'], '.')) . '.' . $ext;
		}
		
		move_uploaded_file($file['tmp_name'], $upload_path);
		
		return $upload_path;
	}
	
	return false;
}
function upload_media($file,$folder, $filename)
{
	if (is_null($file) || strlen(trim($file['name'])) == 0 || strlen(trim($filename)) == 0) {
		return;
	}
	
	
	if (isset($file) && strlen(trim($file['name'])) > 0 && strlen(trim($filename)) > 0) {
	
		$supported_mime_types = array(
			'application/pdf',
			'application/x-pdf',
			'application/x-download',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/msword'
		);
	$mime_type = $file['type'];
	}
	if (in_array($mime_type, $supported_mime_types)) {
		$upload_path =  MEDIA_DIR . "$folder/" . $filename;
		echo $file['tmp_name']. '<br>'. $upload_path.'<br>*****<br>';
		move_uploaded_file($file['tmp_name'], $upload_path);
		
		return $upload_path;
	}
	
	return false;
}
function delete_files($id, $folder)
{
	$dir = UPLOADS_DIR . $folder;
	
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				
				if (strpos($file, $id . '-') === 0) {
					unlink($dir . '/' . $file);
				}
				
			}
			closedir($dh);
		}
	}
}

function get_file($id, $folder, $upload_path = false)
{
	$dir = UPLOADS_DIR . $folder;
	
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				
				if (strpos($file, $id . '-') === 0) {
					if ($upload_path) {
						return $dir . '/' . $file;
					} else {
						return $folder . '/' . $file;
					}
				}
				
			}
			closedir($dh);
		}
	}
	
	return false;
}

function generate_thumbnail_imagick($src_img_path, $target_thumbnail_path, $width, $height)
{
	$add_extension = false;
	if (strpos($target_thumbnail_path, ':') > 0) {
		// do nothing as the path is fully formed
	} else {
		$target_thumbnail_path = UPLOADS_DIR . $target_thumbnail_path;
		$add_extension = true;
	}
	
	$img = new Imagick($src_img_path);
	$img->thumbnailImage($width, $height);
	
	$extension = substr($src_img_path, strrpos($src_img_path,".")+1);
	$extension = strtolower(str_replace("jpg","jpeg",$extension));
	
	switch ($extension)
	{
		case 'jpg':
			if ($add_extension) {
				@imagejpeg($img, $target_thumbnail_path . '.jpg', 100);
			} else {
				@imagejpeg($img, $target_thumbnail_path, 100);
			}
			break;
		
		case 'jpeg':
			if ($add_extension) {
				@imagejpeg($img, $target_thumbnail_path . '.jpeg', 100);
			} else {
				@imagejpeg($img, $target_thumbnail_path, 100);
			}
			break;
		
		case 'png':
			if ($add_extension) {
				@imagepng($img, $target_thumbnail_path . '.png');
			} else {
				@imagepng($img, $target_thumbnail_path);
			}
			break;
		
		case 'gif':
			if ($add_extension) {
				@imagegif($img, $target_thumbnail_path . '.gif');
			} else {
				@imagegif($img, $target_thumbnail_path);
			}
			break;
	}
}

function generate_thumbnail($src_img_path, $target_thumbnail_path, $width, $height)
{
	$add_extension = false;
	if (strpos($target_thumbnail_path, ':') > 0) {
		// do nothing as the path is fully formed
	} else {
		$target_thumbnail_path = UPLOADS_DIR . $target_thumbnail_path;
		$add_extension = true;
	}
	
	
	$ext = strtolower(substr($src_img_path, -3));

	switch ($ext)
	{
		case 'jpg':
		case 'jpeg':
			$src_img = imagecreatefromjpeg($src_img_path);
			break;
		
		case 'png':
			$src_img = imagecreatefrompng($src_img_path);
			break;
		
		case 'gif':
			$src_img = imagecreatefromgif($src_img_path);
			break;
	}
	
	@list($src_width, $src_height) = getimagesize($src_img_path);
	
	$dest_img = imagecreatetruecolor($width, $height);
	@imagecopyresized($dest_img, $src_img, 0, 0, 0, 0, $width, $height, $src_width, $src_height);
	
	switch ($ext)
	{
		case 'jpg':
			if ($add_extension) {
				@imagejpeg($dest_img, $target_thumbnail_path . '.jpg', 100);
			} else {
				@imagejpeg($dest_img, $target_thumbnail_path, 100);
			}
			break;
		
		case 'jpeg':
			if ($add_extension) {
				@imagejpeg($dest_img, $target_thumbnail_path . '.jpeg', 100);
			} else {
				@imagejpeg($dest_img, $target_thumbnail_path, 100);
			}
			break;
		
		case 'png':
			if ($add_extension) {
				@imagepng($dest_img, $target_thumbnail_path . '.png');
			} else {
				@imagepng($dest_img, $target_thumbnail_path);
			}
			break;
		
		case 'gif':
			if ($add_extension) {
				@imagegif($dest_img, $target_thumbnail_path . '.gif');
			} else {
				@imagegif($dest_img, $target_thumbnail_path);
			}
			break;
	}
}
	
function smart_resize_image($file, $width = 0, $height = 0, $proportional = false, $output = 'file', $delete_original = true, $use_linux_commands = false)
{
	if ( $height <= 0 && $width <= 0 ) {
		return false;
	}
	
	$info = getimagesize($file);
	$image = '';
	
	$final_width = 0;
	$final_height = 0;
	list($width_old, $height_old) = $info;
	
	if ($proportional) {
		if ($width == 0) $factor = $height/$height_old;
		elseif ($height == 0) $factor = $width/$width_old;
		else $factor = min ( $width / $width_old, $height / $height_old);   
	
		$final_width = round ($width_old * $factor);
		$final_height = round ($height_old * $factor);
	
	} else {
		$final_width = ( $width <= 0 ) ? $width_old : $width;
		$final_height = ( $height <= 0 ) ? $height_old : $height;
	}
	
	switch ( $info[2] ) {
		case IMAGETYPE_GIF:
			$image = imagecreatefromgif($file);
			break;

		case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($file);
			break;
			
		case IMAGETYPE_PNG:
			$image = imagecreatefrompng($file);
			break;
			
		default:
			return false;
	}
	
	$image_resized = imagecreatetruecolor( $final_width, $final_height );
	
	if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
		$trnprt_indx = imagecolortransparent($image);
		
		// If we have a specific transparent color
		if ($trnprt_indx >= 0) {
		
		// Get the original image's transparent color's RGB values
		$trnprt_color    = imagecolorsforindex($image, $trnprt_indx);
		
		// Allocate the same color in the new image resource
		$trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
		
		// Completely fill the background of the new image with allocated color.
		imagefill($image_resized, 0, 0, $trnprt_indx);
		
		// Set the background color for new image to transparent
		imagecolortransparent($image_resized, $trnprt_indx);
		
		
		}
		// Always make a transparent background color for PNGs that don't have one allocated already
		elseif ($info[2] == IMAGETYPE_PNG) {
		
		// Turn off transparency blending (temporarily)
		imagealphablending($image_resized, false);
		
		// Create a new transparent color for image
		$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
		
		// Completely fill the background of the new image with allocated color.
		imagefill($image_resized, 0, 0, $color);
		
		// Restore transparency blending
		imagesavealpha($image_resized, true);
		}
	}
	
	imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
	
	if ( $delete_original ) {
		if ( $use_linux_commands )
			exec('rm '.$file);
		else
			@unlink($file);
	}
	
	switch ( strtolower($output) ) {
		case 'browser':
			$mime = image_type_to_mime_type($info[2]);
			header("Content-type: $mime");
			$output = NULL;
			break;
			
		case 'file':
			$output = $file;
			break;
		
		case 'return':
			return $image_resized;
			break;
		
		default:
			break;
	}
	
	switch ( $info[2] ) {
		case IMAGETYPE_GIF:
			imagegif($image_resized, $output);
			break;
		
		case IMAGETYPE_JPEG:
			imagejpeg($image_resized, $output);
			break;
		
		case IMAGETYPE_PNG:
			imagepng($image_resized, $output);
			break;
		
		default:
			return false;
	}
	
	return true;
}

function get_table_photos($tablename, $tableid, $type = 'FULL')
{
    $data = table_fetch_rows('module_images', 'type = "' . $type . '" AND table_name = "' . $tablename .'" AND table_id="' . $tableid . '"', 'id ASC');

    $photos = NULL;
    
    if(count($data) > 0)
    {
        foreach($data as $photo)
        {
            $file = get_image('module_images/' . $photo['id']);
            
            if($file !== false)
            {
                $photos[$photo['id']]['file'] = $file;
                $photos[$photo['id']]['link'] = $photo['link'];
		$photos[$photo['id']]['group'] = $photo['groupings'];
            }
        }
    }
    
    return $photos;
}

function bulk_upload_image($image, $table, $tableid, $links = NULL)
{
    $folder = 'module_images';
    
    $supported_mime_types = array('jpeg'   => 'image/jpeg',
                                  'jpeg'   => 'image/pjpeg',
                                  'jpg'    => 'image/jpeg',
                                  'gif'    => 'image/gif',
                                  'png'    => 'image/png',
                                  'tif'    => 'image/tiff',
                                  'tiff'   => 'image/tiff',
		                  'bmp'    => 'image/bmp');

    $simpleimage = new SimpleImage();


    foreach($image['name'] as $key => $value)
    {
        $mime_type = $image['type'][$key];
        
        $link = ( $links != NULL && isset($links[$key]) ) ? $links[$key] : '';
        
        if (in_array($mime_type, $supported_mime_types)) {

		$ext = array_search($mime_type, $supported_mime_types);

                $fields = array('table_name','table_id','link','type','groupings');

                $data = array();
                $data['table_name'] = $table; 
                $data['table_id'] = $tableid;
                $data['link'] = $link;
		$data['type'] = 'THUMB';
		$data['groupings'] =  date('YmdHis') . rand(100,999);

        $id = table_insert('module_images', $fields, $data);

		

                delete_images(UPLOADS_DIR . "$folder/$id");

                $upload_path =  UPLOADS_DIR . "$folder/$id.$ext";

		$simpleimage->load($image['tmp_name'][$key]);

		$simpleimage->resize(190,150);

		$simpleimage->save($upload_path);
		
		$data['type'] = 'FULL';

                $id = table_insert('module_images', $fields, $data);

                delete_images(UPLOADS_DIR . "$folder/$id");

                $upload_path =  UPLOADS_DIR . "$folder/$id.$ext";

                move_uploaded_file($image['tmp_name'][$key], $upload_path);
        }
    }
}


function get_image_from_base64_url($data, $type) {
	if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
        $data = substr($data, strpos($data, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            return 'invalid image type';
        }

        $data = base64_decode($data);

        if ($data === false) {
            return 'base64_decode failed';
        }
        return $data;
    } else {
        return 'did not match data URI with image data';
    }
} 
?>
