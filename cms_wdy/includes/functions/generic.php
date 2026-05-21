<?php

function return_date($num, $type)
{
	if (strpos($num, '0') === 0) {
		$num = substr($num, 1);
	}
	
	switch($type){ 
		case 'month': 
			$month_name = array("", "Januari", "Februari", "Maart", "April", "Mei", "Juni", "Juli", "Augustus", "September", "Oktober", "November", "December");
			return $month_name[$num];
			break; 

		case 'day': 
			$day_name = array('', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'); 
			return $day_name[$num]; 
			break; 
	}
}

function show_checked($x, $y)
{
	echo stripcslashes($x) == stripcslashes($y) ? 'checked="checked"' : '';
}

function show_selected($x, $y)
{
	echo stripcslashes($x) == stripcslashes($y) ? 'selected="selected"' : '';
}

function show_fckeditor($name, $content = '', $placeholder = '')
{
	$ckeditor = '<textarea name="'.$name.'" id="ck'.$name.'" placeholder="'.$placeholder.'" class="newckeditor">'.$content.'</textarea>';
	echo $ckeditor;
}

// function show_fckeditor($name, $content = '', $width = 590, $height = 400)
// {
// 	include_once 'resources/fckeditor/fckeditor.php';
// 	$base_path = ADMIN_URL.'/resources/fckeditor/';
	
// 	$fckeditor = new FCKeditor($name);
// 	$fckeditor->BasePath = $base_path;
	
// 	$fckeditor->Config['SkinPath'] = $base_path . 'editor/skins/office2003/' ;
// 	$fckeditor->Config['AutoDetectLanguage'] = false;
// 	$fckeditor->Config['DefaultLanguage'] = 'en';
// 	$fckeditor->Width = $width;
// 	$fckeditor->Height = $height;
	
// 	$fckeditor->Value = stripslashes($content);
// 	$fckeditor->Create();
// }

function show_rows($rows, $table, $fields, $operations = array('edit', 'delete'), $showHtml = true)
{
	foreach ($rows as $row) {
		$id = $row['id'];
        $class = '';
		if (isset($row['status'])) { 
            $class = strtolower(str_replace(' ', '-', $row['status']));
        };
        if (isset($row['order_status'])) { 
            $class .= ' '.strtolower(str_replace(' ', '-', $row['order_status']));
        };
		printf('<tr class="%s" table="%s" row_id="%s">',$class, $table, $id);
		
		echo '<td valign="top">';
		foreach ($operations as $operation) {
			printf('<a href="?module=%s&action=%s&id=%d" class="operation-%s operation" value="%d"></a> ', $table, $operation, $id, $operation, $id);
		}
		echo '</td>';
    	
		foreach ($fields as $field) {
			if ($field == 'date') {
				printf('<td valign="top">%s</td>', date('d-m-Y', strtotime($row[$field])));
			} else {
                    if($showHtml) {
                        printf('<td valign="top">%s</td>', htmlentities($row[$field]));
                    }
                    else {
                        printf('<td valign="top">%s</td>', $row[$field]);
                    }
			}
		}
    
		echo '</tr>';
	}
}




function make_get_url($params)
{
	foreach ($params as $key => $val) {
		$url_params[] = sprintf('%s=%s', $key, urlencode($val));
	}
	
	return implode('&', $url_params);
}

function get_id()
{
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	} elseif (isset($_POST['id'])) {
		$id = $_POST['id'];
	}
	
	return $id;
}

function show_pagination($total_pages, $current_page)
{
	$params = $_GET;
	unset($params['page']);
	
	if ($total_pages > 1) {
		$url = make_get_url($params);
		
		echo '<nav aria-label="Page navigation">
  <ul class="pagination">';
		
		if ($current_page > 1) {
			$temp_params = $params;
			$temp_params['page'] = $current_page - 1;
			$url = make_get_url($temp_params);
			
			printf('<li class="page-item">
      <a class="page-link" href="?%s" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
        <span class="sr-only">Previous</span>
      </a>
    </li>', $url);
		}
		
		$start_page = $current_page;
		
		$start_page = 1;
		if (($current_page - 4) > 0) {
			$start_page = $current_page - 4;
		}
		
		$end_page = $start_page + 8;
		if ($end_page > $total_pages) {
			$end_page = $total_pages;
			
			if (($current_page - 10) > 0) {
				$start_page = $current_page - 10;
			} else {
				$start_page = 1;
			}
		}
		
		for ($page = $start_page; $page <= $end_page; $page++) {
			$link = sprintf('?%s&page=%d', $url, $page);
			
			if ($page == $current_page) {
				
				printf('<li class="page-item active"><a class="page-link" href="%s">%d</a></li>', $link, $page);
			} else {
				printf('<li class="page-item"><a class="page-link" href="%s">%d</a></li>', $link, $page);
			}
		}
		
		if ($current_page < $total_pages) {
			$temp_params = $params;
			$temp_params['page'] = $current_page + 1;
			$url = make_get_url($temp_params);
			
			printf('<li class="page-item">
      <a class="page-link" href="?%s" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
        <span class="sr-only">Next</span>
      </a>
    </li>', $url);
		}
			
		echo '</ul></nav>';
	}
}

function show_id()
{
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	} elseif (isset($_POST['id'])) {
		$id = $_POST['id'];
	}
	
	printf('<input type="hidden" name="id" id="id" value="%d" />', $id);
}

function redirect($link)
{
	$location = sprintf('Location: %s', $link);
	header($location);
	die();
}


function show_big_button($name, $text, $class = 'btn-primary')
{
	$translated_text = $text;
	
	echo <<<HTML
	<button name="{$name}" class="big btn-wdy {$class}" type="submit">
		{$translated_text}
	</button>
HTML;
}

function show_link_btn_arrow($link, $text)
{
	echo <<<HTML
	<a class="button-arrow" href="logout.php">
		<span class="left"><span class="right"><span class="text">{$text}</span></span></span>
	</a>
HTML;
}

function show_messages($messages)
{
	if (count($messages)) {
		echo '<div class="alert alert-success messages">';
		echo '<div class="row">
            <div class="col-1 alert-icon-col">
                <span class="fa fa-check fa-fw"></span>
            </div>
            <div class="col">';
            foreach ($messages as $message) {
				printf('%s<br>', $message);
			}
            echo '</div>
        </div></div>';
		
	}
}

function show_errors($errors)
{
	if (count($errors)) {

		echo '<div class="alert alert-danger errors">';
		echo '<div class="row">
            <div class="col-1 alert-icon-col">
                <span class="fas fa-exclamation-triangle fa-fw"></span>
            </div>
            <div class="col">';
            foreach ($errors as $error) {
				printf('%s<br>', $error);
			}
            echo '</div>
        </div></div>';
	}
}


/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                                                                         :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::    unit = the unit you desire for results                               :*/
/*::           where: 'M' is statute miles (default)                         :*/
/*::                  'K' is kilometers                                      :*/
/*::                  'N' is nautical miles                                  :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at http://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: http://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2015		   		     :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}
// Folder Generator 
	function generateRandomToken($length = 10) {
	    $chars = '0123456789';
	    $charsLength = strlen($chars);

	    $randomString = '';

	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $chars[rand(0, $charsLength - 1)];
	    }
	    return $randomString;
	}
    function get_preferred_fulfilment_max_date($reference = null) {
        return get_preferred_fulfilment_min_date($reference)->modify('+8 weeks');
    }
    function get_preferred_fulfilment_min_date($reference = null) {
        $timezone = new DateTimeZone(date_default_timezone_get());

        if ($reference instanceof DateTimeInterface) {
            $now = DateTimeImmutable::createFromInterface($reference)->setTimezone($timezone);
        } elseif (!empty($reference)) {
            $now = new DateTimeImmutable($reference, $timezone);
        } else {
            $now = new DateTimeImmutable('now', $timezone);
        }

        $beforeCutoff = ((int) $now->format('Hi') < 2000);
        $candidate = $now->setTime(0, 0, 0);
        $candidate = $candidate->modify($beforeCutoff ? '+1 day' : '+2 days');

        while (in_array((int) $candidate->format('w'), array(0, 1), true)) {
            $candidate = $candidate->modify('+1 day');
        }

        return $candidate;
    }

    function preferred_fulfilment_date_is_valid($date, $reference = null, $fulfilmentType = 'delivery') {
        $normalizedDate = preferred_fulfilment_date_normalize($date);

        if ($normalizedDate === false) {
            return false;
        }

        $date = $normalizedDate;

        $timezone = new DateTimeZone(date_default_timezone_get());
        $selected = DateTimeImmutable::createFromFormat('Y-m-d', trim($date), $timezone);

        if (!$selected || $selected->format('Y-m-d') !== trim($date)) {
            return false;
        }

        if (in_array((int) $selected->format('w'), array(0, 1), true)) {
            return false;
        }

        $minDate = get_preferred_fulfilment_min_date($reference);
        $maxDate = get_preferred_fulfilment_max_date($reference);

        if ($selected < $minDate->setTime(0, 0, 0) || $selected > $maxDate->setTime(0, 0, 0)) {
            return false;
        }

        if (blocked_fulfilment_date_is_blocked($normalizedDate, $fulfilmentType)) {
            return false;
        }

        return true;
    }
    function preferred_fulfilment_date_normalize($date) {
        if (!is_string($date) || trim($date) === '') {
            return false;
        }

        $date = trim($date);
        $timezone = new DateTimeZone(date_default_timezone_get());
        $selected = DateTimeImmutable::createFromFormat('Y-m-d', $date, $timezone);

        if ($selected && $selected->format('Y-m-d') === $date) {
            return $selected->format('Y-m-d');
        }

        $selected = DateTimeImmutable::createFromFormat('d/m/Y', $date, $timezone);

        if ($selected && $selected->format('d/m/Y') === $date) {
            return $selected->format('Y-m-d');
        }

        return false;
    }
    function preferred_fulfilment_date_display($date) {
        $normalizedDate = preferred_fulfilment_date_normalize($date);

        if ($normalizedDate === false) {
            return '';
        }

        $timezone = new DateTimeZone(date_default_timezone_get());
        $selected = DateTimeImmutable::createFromFormat('Y-m-d', $normalizedDate, $timezone);

        if (!$selected || $selected->format('Y-m-d') !== $normalizedDate) {
            return false;
        }

        return $selected->format('d/m/Y');
    }
	function get_delivery_cost($miles, $cart_total) {
        global $store_settings;
        //Delivery Zone 1 if set
        if($miles <= $store_settings['delivery_zone1_radius_miles']){
            $deliverycost = $store_settings['delivery_zone1_cost'];
            if ($store_settings['free_delivery_zone_1'] == 1) {
                if ($cart_total >= $store_settings['free_delivery_zone1_minimum_spend']) {
                    $deliverycost = 0.00;
                }
            }
            $delivery = 'ok'; 
        }
        //Delivery Zone 2 if set
        elseif(($miles > $store_settings['delivery_zone1_radius_miles']) && ($miles <= $store_settings['delivery_zone2_radius_miles'])){
            $deliverycost = $store_settings['delivery_zone2_cost'];
            if ($store_settings['free_delivery_zone_2'] == 1) {
                if ($cart_total >= $store_settings['free_delivery_zone2_minimum_spend']) {
                    $deliverycost = 0.00;
                }
            }
            $delivery = 'ok'; 
        } 
        //Delivery zone 3 if set
        elseif(($miles > $store_settings['delivery_zone2_radius_miles']) && ($miles <= $store_settings['delivery_zone3_radius_miles'])){
            $deliverycost = $store_settings['delivery_zone3_cost'];
            if ($store_settingcart_totals['free_delivery_zone_3'] == 1) {
                if ($cart_total >= $store_settings['free_delivery_zone3_minimum_spend']) {
                    $deliverycost = 0.00;
                }
            }
            $delivery = 'ok'; 
        } 
        //Greater than 21 Miles = No  
        else{ 
            $delivery = 'notok'; 
            $deliverycost = 0.00;
        }
        return ['delivery' => $delivery, 'deliverycost' => $deliverycost, 'miles' => $miles];
    }

    function order_requires_preauth($cartItems) {
        foreach($cartItems as $cartItem) {
            if ( isset ($cartItem->weight) ) {
                return true;
            }
        }
        return false;
    }
    function process_order($order_id) {
        $order_id = (int) $order_id;
        $order_items = table_fetch_rows('order_items','order_id = "'.$order_id.'" AND quantities_processed = 0');
        if (! $order_items)  {
            return false;
        }
        foreach($order_items as $order_item) {
            $product = table_fetch_row('products','id="'.$order_item['product_id'].'"');
            if ($product['type'] == 'item') {
                
                $new_stock_level = max(($product['stock_level'] - $order_item['quantity']) ,0);
                table_update('products',['stock_level'],['stock_level' => $new_stock_level],'id="'.$order_item['product_id'].'"');
            }elseif($product['type'] == 'weight' && !empty($order_item['product_weight_id'])) {
                table_update('product_weights',['status'], ['status' => "Sold"], 'id="'.$order_item['product_weight_id'].'"');
            }
            table_update('order_items',['quantities_processed'],['quantities_processed' => 1],'id = "'.$order_item['id'].'"');
            
        }
    }
    
    function csv_to_array($filename='', $delimiter=',')
    {
        
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;
        
        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }

?>
