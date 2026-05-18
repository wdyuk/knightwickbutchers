<?php

function get_galleries($galleries = null)
{
	$where = '';
	if (!is_null($galleries)) {
		$where = 'id IN (' . implode(',', $galleries) . ')';
	}
	
	$gallery = table_fetch_rows(TBL_PHOTOGALLERY, $where, 'position ASC');
	
	$arr = array();
	
	foreach ($gallery as $album) { 
		$where = sprintf('photogallery_id=%d', intval($album['id']));
		$photo = table_fetch_row(TBL_PHOTOS, $where, 'position DESC');
		$img_path = get_image_path('photos/' . $photo['id'], false);
		
		$arr[] = array(
						'id' => $album['id'],
						'img_path' => $img_path,
						'title' => $album['title'],
						'description' => $album['description']
					);
	}
	
	return $arr;
}

function get_fotos($gallery_id)
{
	$gallery_id = intval($gallery_id);
	$where = sprintf('photogallery_id=%d', $gallery_id);
	$photos = table_fetch_rows(TBL_PHOTOS, $where, 'position ASC');
	
	foreach ($photos as $photo) { 
		
		$img_path = get_image_path('photos/' . $photo['id'], false);
		
		$arr[] = array(
						'id' => $photo['id'],
						'img_path' => $img_path,
						'title' => $photo['title'],
						'description' => $photo['description']
					);
	}
	
	return $arr;
}


?>