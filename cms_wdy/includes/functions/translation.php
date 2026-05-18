<?php

function delete_language($language)
{
	global $db;
	
	$where = sprintf('language = "%s"', mysqli_real_escape_string($db, $language));
	table_delete_row(TBL_TRANSLATIONS, $where);
}

function refresh_language($language)
{
	global $db;
	
	$where = 'language = "us"';
	$language_us = table_fetch_rows(TBL_TRANSLATIONS, $where, 'translation ASC');
	
	foreach ($language_us as $translation) {
		$where = sprintf('text = "%s" AND language = "%s"',
						 mysqli_real_escape_string($db, $translation['text']), mysqli_real_escape_string($db, $language));
		$count = table_row_count(TBL_TRANSLATIONS, $where);
		
		if ($count == 0) {
			$fields = array('text', 'translation', 'language');
			$values = array('text' => $translation['text'], 'translation' => $translation['translation'], 'language' => $language);
			
			table_insert(TBL_TRANSLATIONS, $fields, $values);
		}
	}
}

function get_languages()
{
	$languages = array(
					  	'us' => 'English (United States)',
						'ca' => 'English (Canada)',
						'au' => 'English (Australia)',
						'nl' => 'Dutch (Netherlands)',
						'it' => 'Italian',
						'be' => 'Dutch (Belgium)',
						'fr' => 'French',
						'ru' => 'Russian',
						'de' => 'German',
						'iq' => 'Arabic (Iraq)',
						'ir' => 'Farsi (Iran)',
						'kw' => 'Arabic (Kuwait)',
						'eg' => 'Arabic (Egypt)',
						'sa' => 'Arabic (Saudi Arabia)',
						'ye' => 'Arabic (Yemen)',
						'es' => 'Spanish (Spain)',
						'se' => 'Swedish',
						'pk' => 'Urdu (Pakistan)',
						
						'england' => 'English (England)'
					  );
	ksort($languages);
	
	return $languages;
}

?>