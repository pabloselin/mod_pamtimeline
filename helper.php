<?php
/**
 * Helper class for Pam Timeline module
 * @package Joomla
 * @subpackage Modules
 * @link https://apie.cl
 * @license MIT
 *
 */

 /*
 *TODO
 *	 - Módulo de relaciones globales
 *	 - Grupo de línea de tiempo para artistas
 *	 - Llamar obras en línea de tiempo
 *	 - Separar por tabs cada era 
 */  

//K2 Route para los links
require_once JPATH_SITE.'/components/com_k2/helpers/route.php';

class ModPamTimelineHelper 
{	
	/**
	 * Asociaciones de campos con IDs:
	 *
	 *  7 Tipo de Documento
	 *  8 Año
	 *  22 Lugar de Residencia
	 *  23 Lenguajes
	 *  24 Temáticas
	 *  25 Herramientas
	 *  26 Sitio Web
	 *  27 Tipo de Persona
	 *  29 Tipo de Hito
	 *  30 Tipo de Evento
	 *  31 Lugar
	 *  32 Tipo de Video
	 *  33 Tipo de Publicación
	 *  34 Sitio web Persentaciones
	 *  35 ID Persona
	 */

	static $pamfieldstable = '#__k2_extra_fields';
	static $pamitemstable = '#__k2_items';
	static $pamcategories = '#__k2_categories';

	static $pamworkscat = 6;
	static $pampersonscat = 31;
	static $pameventscat = 19;
	static $pamlatameventscat = 18;
	static $pamglobaeventscat = 39;
	static $obrascat = 6;

	static $pamfieldassocs = array(
								'typedoc' 			=> 7,
								'year' 				=> 8,
								'livingplace'		=> 22,
								'languages'			=> 23,
								'themes'			=> 24,
								'tools'				=> 25,
								'website'			=> 26,
								'persontype'		=> 27,
								'milestonetype'		=> 29,
								'eventtype'			=> 30,
								'place'				=> 31,
								'videotype'			=> 32,
								'pubtype'			=> 33,
								'presentationweb'	=> 34,
								'personid'			=> 38
								);
	static $eras = array(
								'era1' => array(
									'title' => '1900-1950',
									'range' => array(1900, 1950),
									'events' => array()
								),
								'era2' => array(
									'title' => '1951-1990',
									'range' => array(1951, 1990),
									'events' => array()
								),
								'era3' => array(
									'title' => '1991-2030',
									'range' => array(1991, 2030),
									'events' => array()
								)
						);

	public static function getPersons( ) {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select($db->quoteName( array('id', 'title', 'alias', 'catid', 'extra_fields', 'trash') ))
					->from($db->quoteName( self::$pamitemstable))
					->where($db->quoteName('catid') . ' LIKE ' . $db->quote( self::$pampersonscat ) . ' AND ' . $db->quoteName('trash') . ' NOT LIKE ' . $db->quote( 1 ) );

		$db->setQuery($query);

		$result = $db->loadAssocList();

		return $result;

	}
	
	public static function makeEdges($nodes, $tax) {
		$matchids = [];
		$edges = [];
		$edgeids = [];
		foreach($nodes as $node) {
			if(count($node['data'][$tax]) >= 1):
				foreach($nodes as $innernode) {
					if(count($innernode['data'][$tax]) >= 1 && $innernode['data']['id'] != $node['data']['id']):
						$intersect = array_intersect($node['data'][$tax], $innernode['data'][$tax]);
						if(count($intersect) >= 1):
							foreach($intersect as $match){
								$curedgeid = 'edge-' . $node['data']['id'] . '-' . $innernode['data']['id'];
								if(!in_array($curedgeid, $edgeids)):
									$label = self::valuesToNames($match, $tax);
									$edges[] = array(
										'data' => array(
												'id' => $curedgeid,
												'label' => $label,
												'source' => $node['data']['id'],
												'target' => $innernode['data']['id']
											)
									);
									$edgeids[] = $curedgeid;
								endif;
							}
						endif;
					endif;
				}
			endif;
		}

		return $edges;
	}

	public static function getItemLink( $itemid, $alias, $catid) {

		$link = K2HelperRoute::getItemRoute($itemid . ':' . $alias, $catid);
		$seflink = JRoute::_($link);

		return $seflink;

	}

	public static function getItems( $catid ) {
		
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select($db->quoteName( array('id', 'title', 'alias', 'catid', 'extra_fields', 'introtext', 'fulltext')))
					->from($db->quoteName( self::$pamitemstable))
					->where($db->quoteName('catid')) . ' LIKE ' . $db->quote( $catid );

		$db->setQuery($query);

		$result = $db->loadAssocList();

		return $result;

	}

	public static function getChildCats( $catid ) {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select($db->quoteName( array('id', 'name', 'alias', 'parent')))
					->from($db->quoteName( self::$pamcategories))
					->where($db->quoteName('parent')) . ' LIKE ' . $db->quote($catid);
		
		$db->setQuery($query);

		$result = $db->loadAssocList();

		return $result;
	}

	public static function getFieldValues( $taxid ) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select($db->quoteName( 'value' ))
					->from($db->quoteName( self::$pamfieldstable))
					->where($db->quoteName('id')) . ' LIKE ' . $db->quote($taxid);
		
		$db->setQuery($query);

		$result = $db->loadResult();
		return $result;
	}

	public static function getItemImageUrl( $item_id, $size = 'S' ) {

		return Juri::base() . 'media/k2/items/cache/' . md5('Image' . $item_id ) . '_' . $size . '.jpg';

	}

	public static function getItemFields( $item_id ) {

		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
					->select($db->quoteName( 'extra_fields'))
					->from($db->quoteName(self::$pamitemstable))
					->where($db->quoteName('id') . ' LIKE ' . $db->quote( $item_id ));

		$db->setQuery($query);

		$fieldcontents = $db->loadResult();

		return $fieldcontents;


	}

	public static function getItemField( $item_id, $fieldfromassoc ) {

		$itemFields = self::getItemFields( $item_id );

		$decoded = json_decode( $itemFields );
		$values = [];

		foreach( $decoded as $decode ) {

			if( $decode->id == self::$pamfieldassocs[$fieldfromassoc]) {

				//$values[$fieldfromassoc] = $decode->value;
				if(is_array($decode->value)) {

					foreach($decode->value as $fieldvalue) {

						$values[$fieldfromassoc][] = array(
							'fieldvaluename' => self::valuesToNames( $fieldvalue, $fieldfromassoc ),
							'fieldvalueid'   => $fieldvalue
							);

					}

				} else {

					$values = $decode->value; 

				}

				
				
			}

		}

		return $values;

	}

	public static function getItemFieldIds( $item_id, $fieldfromassoc ) {

		$itemFields = self::getItemFields( $item_id );

		$decoded = json_decode( $itemFields );
		$values = [];

		foreach( $decoded as $decode ) {

			if( $decode->id == self::$pamfieldassocs[$fieldfromassoc]) {

				//$values[$fieldfromassoc] = $decode->value;
				if(is_array($decode->value)) {

					foreach($decode->value as $fieldvalue) {

						$values[$fieldfromassoc][] = $fieldvalue;

					}

				} else {

					$values = $decode->value; 

				}

				
				
			}

		}

		return $values;

	}

	public static function getFieldIdByName( $field_name ) {

		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
					->select($db->quoteName( 'id'))
					->from($db->quoteName(self::$pamfieldstable))
					->where($db->quoteName('name') . ' LIKE ' . $db->quote( $field_name ));

		$db->setQuery($query);

		$fieldcontents = $db->loadResult();

		return $fieldcontents;


	}

	public static function getItemYears( $item_id ) {

		$fields = self::getItemFields( $item_id );

		$decoded = json_decode( $fields );

		//var_dump($decoded);
		foreach($decoded as $decode) {

			if( $decode->id == self::$pamfieldassocs['year'] ) {

				$years = $decode->value;

			}

		}

		if( isset($years) && count($years) >= 1 ) {

			foreach($years as $year) {

				$years_names[] = self::valuesToNames($year, 'year');

			}

			return $years_names;

		} else {

			return false;

		}		

	}

	public static function valuesToNames( $field_value, $fieldfromassoc ) {
		/**
		 * Transforma el ID de un field del campo al nombre del field en cuestión
		 */
		
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select($db->quoteName( 'value' ))
					->from($db->quoteName(self::$pamfieldstable))
					->where($db->quoteName('id') . ' LIKE ' . self::$pamfieldassocs[$fieldfromassoc] );

		$db->setQuery($query);

		$result = $db->loadResult();
		$fields = json_decode( $result );

		foreach($fields as $field) {

			if($field_value == $field->value ) {

				$field_name = $field->name;

			}

		}

		if(isset($field_name)):

			return $field_name;
		
		else: 

			return false;

		endif;

	}
	
	public static function getFieldIdFromName( $field_name ) {

		$db = JFactory::getDbo();
		



	}

	public static function translateFieldtoLabel( $field_id ) {
		/**
		 * Devuelve el nombre del campo a partir de su ID
		 */
		
		$db = JFactory::getDbo();
		

		$query = $db->getQuery(true)
					->select($db->quoteName( 'name' ))
					->from($db->quoteName(self::$pamfieldstable))
					->where($db->quoteName('id') . ' LIKE ' . $db->quote( $field_id ));

		$db->setQuery($query);

		$fieldname = $db->loadResult();

		return $fieldname;
	}

	public static function prepareEventsForTimeline( $events_cat, $timeline_title, $timeline_description ) {

		/**
		 * Devuelve un string Json para el timeline
		 */

		$timeline_array = [];

		$timeline_array['title'] = array(
			'text' => array(
				'headline' => $timeline_title,
				'text' => htmlentities($timeline_description)
				)
			);

		$ranges = array(
					'S XX Primera Mitad' => array(1900, 1945),
					'S XX Segunda Mitad' => array(1946, 1970),
					'S XX Masificación Computadores - Internet' => array(1971, 2017)
				);

		foreach($ranges as $key=>$range) {

			$timeline_array['eras'][] = array(
				'start_date' => array( 'year' => $range[0] ),
				'end_date' => array( 'year' => $range[1] ),
				'text'	=> array(
							'headline' => $key
							)
				);

		}

		$events = self::getItems($events_cat);

		foreach($events as $event) {

			$title = $event['title'];
			$introtext = htmlentities($event['introtext']);
			//$introtext = 'debug';

	
			$timeline_array['events'][] = array(
											'media'		 => array(
												'url' => self::getItemImageUrl( $event['id'])
												),
											'start_date' => array(
												'year' => self::getItemYears( $event['id'] )
												),
											'text'		 => array(
												'headline'	=> $title,
												'text'	=> $introtext
												),
											'autolink' => false
											);
	
		}

		$json = json_encode( $timeline_array, JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP );

		return $json;

	}


	public static function prepareEventsForGroupedTimeline( $timeline_title, $timeline_description ) {

		/**
		 * Devuelve un string Json para el timeline agrupado en distintas instancias para distintas ERAS y grupos para arte global y latinoamericano
		 */

		$timeline_array['era1']['title'] = array(
			'headline' => '1900-1950',
			'text' => '1900-1950'
		);

		$timeline_array['era2']['title'] = array(
			'headline' => '1951-1990',
			'text' => '1951-1990'
		);

		$timeline_array['era3']['title'] = array(
			'headline' => '1991-2030',
			'text' => '1991-2030'
		);
		$events_global = self::getItems(self::$pamglobaeventscat);
		$events_latam = self::getItems(self::$pamlatameventscat);

		$artists_cats = self::getChildCats(self::$obrascat);
		
		foreach($artists_cats as $artist_cat) {

		$artist_items = self::getItems($artist_cat['id']);

			foreach($artist_items as $event_artist) {
				
				$title = $event_artist['title'];
				$introtext = htmlentities($event_artist['introtext']);
				$fulltext = htmlentities($event_artist['fulltext']);
				
				$eventyear = self::getItemYears( $event_artist['id']);
				$startyear = $eventyear[0];
				$endyear = ($eventyear ? array_pop($eventyear) : false);
				
				$seflink = self::getItemLink($event_artist['id'], $event_artist['alias'], $event_artist['catid']);
				//var_dump($seflink);

				$artist_text = '<p>' . $artist_cat['name'] . '</p>';
				$artist_text .= '<p><a href="' . $seflink . '">Ver más</a></p>';
				
				$artist_text = htmlspecialchars($artist_text);
				
				//Elementos para timeline separados por rangos de años
				//Falta meter los artistas
				if($startyear > self::$eras['era1']['range'][0] && $startyear < self::$eras['era1']['range'][1]) {

					$timeline_array['era1']['events'][] = array(
												'media'		 => array(
													'url' => self::getItemImageUrl( $event_artist['id'])
													),
												'start_date' => array(
													'year' => $startyear
													),
												'end_date' => array(
													'year' => $endyear
												),
												'text'		 => array(
													'headline'	=> $title,
													'text'	=> $artist_text
													),
												'autolink' => false,
												'group' => 'Obras',
												'evtype' => 'Obra',
												'evclass' => 'obra'
												);

				} elseif($startyear > self::$eras['era2']['range'][0] && $startyear < self::$eras['era2']['range'][1]) {

					$timeline_array['era2']['events'][] = array(
												'media'		 => array(
													'url' => self::getItemImageUrl( $event_artist['id'])
													),
												'start_date' => array(
													'year' => $startyear
													),
												'end_date' => array(
													'year' => $endyear
												),
												'text'		 => array(
													'headline'	=> $title,
													'text'	=> $artist_text
													),
												'autolink' => false,
												'group' => 'Obras',
												'evtype' => 'Obra',
												'evclass' => 'obra'
												);

				} elseif( $startyear > self:: $eras['era3']['range'][0]) {

					$timeline_array['era3']['events'][] = array(
												'media'		 => array(
													'url' => self::getItemImageUrl( $event_artist['id'])
													),
												'start_date' => array(
													'year' => $startyear
													),
												'end_date' => array(
													'year' => $endyear
												),
												'text'		 => array(
													'headline'	=> $title,
													'text'	=> $artist_text
													),
												'autolink' => false,
												'group' => 'Obras',
												'evtype' => 'Obra',
												'evclass' => 'obra'
												);

				}
			}	
		}

		//$events_artists = self::getItems(41);

		foreach($events_global as $event_global) {

			$title = $event_global['title'];
			$introtext = htmlentities($event_global['introtext']);
			$fulltext = htmlentities($event_global['fulltext']);
			
			$eventyear = self::getItemYears( $event_global['id']);
			$startyear = $eventyear[0];
			$endyear = array_pop($eventyear);
			
			$mstone_type = self::getItemField( $event_global['id'],'milestonetype');
			$mstone_typename = $mstone_type['milestonetype'][0]['fieldvaluename'];
			$evclass = 'tipohito-' . $mstone_type['milestonetype'][0]['fieldvalueid'];
			//xdebug_break();
			
			//Elementos para timeline separados por rangos de años
			//Falta meter los artistas
			if($startyear > self::$eras['era1']['range'][0] && $startyear < self::$eras['era1']['range'][1]) {

				$timeline_array['era1']['events'][] = array(
											'media'		 => array(
												'url' => self::getItemImageUrl( $event_global['id'])
												),
											'start_date' => array(
												'year' => $startyear
												),
											'end_date' => array(
												'year' => $endyear
											),
											'text'		 => array(
												'headline'	=> $title,
												'text'	=> self::prepareFullTextforTimeline($event_global)
												),
											'autolink' => false,
											'group' => 'Hitos Mundiales',
											'evtype' => $mstone_typename,
											'evclass' => $evclass
											);

			} elseif($startyear > self::$eras['era2']['range'][0] && $startyear < self::$eras['era2']['range'][1]) {

				$timeline_array['era2']['events'][] = array(
											'media'		 => array(
												'url' => self::getItemImageUrl( $event_global['id'])
												),
											'start_date' => array(
												'year' => $startyear
												),
											'end_date' => array(
												'year' => $endyear
											),
											'text'		 => array(
												'headline'	=> $title,
												'text'	=> self::prepareFullTextforTimeline($event_global)
												),
											'autolink' => false,
											'group' => 'Hitos Mundiales',
											'evtype' => $mstone_typename,
											'evclass' => $evclass
											);

			} elseif( $startyear > self:: $eras['era3']['range'][0]) {

				$timeline_array['era3']['events'][] = array(
											'media'		 => array(
												'url' => self::getItemImageUrl( $event_global['id'])
												),
											'start_date' => array(
												'year' => $startyear
												),
											'end_date' => array(
												'year' => $endyear
											),
											'text'		 => array(
												'headline'	=> $title,
												'text'	=> self::prepareFullTextforTimeline($event_global)
												),
											'autolink' => false,
											'group' => 'Hitos Mundiales',
											'evtype' => $mstone_typename,
											'evclass' => $evclass
											);

			}
	
		}

		foreach($events_latam as $event_latam) {

			$title = $event_latam['title'];
			$introtext = htmlentities($event_latam['introtext']);
			$fulltext = htmlentities($event_latam['fulltext']);
			
			$eventyear = self::getItemYears( $event_latam['id']);
			$startyear = $eventyear[0];
			$endyear = array_pop($eventyear);

			$mstone_type = self::getItemField( $event_latam['id'],'milestonetype');
			$mstone_typename = $mstone_type['milestonetype'][0]['fieldvaluename'];
			$evclass = 'tipohito-' . $mstone_type['milestonetype'][0]['fieldvalueid'];

			//Elementos para timeline1
			if($startyear > self::$eras['era1']['range'][0] && $startyear < self::$eras['era1']['range'][1]) {

				$timeline_array['era1']['events'][] = array(
											'media'		 => array(
												'url' => self::getItemImageUrl( $event_latam['id'])
												),
											'start_date' => array(
												'year' => $startyear
												),
											'end_date' => array(
												'year' => $endyear
											),
											'text'		 => array(
												'headline'	=> $title,
												'text'	=> self::prepareFullTextforTimeline($event_latam)
												),
											'autolink' => false,
											'group' => 'Hitos Latinoamericanos',
											'evtype' => $mstone_typename,
											'evclass' => $evclass
											);

			} elseif($startyear > self::$eras['era2']['range'][0] && $startyear < self::$eras['era2']['range'][1]) {

				$timeline_array['era2']['events'][] = array(
											'media'		 => array(
												'url' => self::getItemImageUrl( $event_latam['id'])
												),
											'start_date' => array(
												'year' => $startyear
												),
											'end_date' => array(
												'year' => $endyear
											),
											'text'		 => array(
												'headline'	=> $title,
												'text'	=> self::prepareFullTextforTimeline($event_latam)
												),
											'autolink' => false,
											'group' => 'Hitos Latinoamericanos',
											'evtype' => $mstone_typename,
											'evclass' => $evclass
											);

			} elseif( $startyear > self:: $eras['era3']['range'][0]) {

				$timeline_array['era3']['events'][] = array(
											'media'		 => array(
												'url' => self::getItemImageUrl( $event_latam['id'])
												),
											'start_date' => array(
												'year' => $startyear
												),
											'end_date' => array(
												'year' => $endyear
											),
											'text'		 => array(
												'headline'	=> $title,
												'text'	=> self::prepareFullTextforTimeline($event_latam)
												),
											'autolink' => false,
											'group' => 'Hitos Latinoamericanos',
											'evtype' => $mstone_typename,
											'evclass' => $evclass
											);

			}
		}
		$json = array();

		$json['era1'] = json_encode( $timeline_array['era1'], JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP );
		$json['era2'] = json_encode( $timeline_array['era2'], JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP );
		$json['era3'] = json_encode( $timeline_array['era3'], JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP );
		
		return $json;


	}

	public static function prepareFullTextforTimeline($event) {
		/*
		Devuelve Una versión acortada del texto de un evento
		*/

		
		$link = self::getItemLink($event['id'], $event['alias'], $event['catid']);
		
		$mtype = self::getItemField($event['id'], 'milestonetype');
		//xdebug_break();
		$fulltext = htmlspecialchars('<span class="mtype">' . $mtype['milestonetype'][0]['fieldvaluename'] . '</span>');

		//$fulltext .= htmlspecialchars(self::trim_text($event['fulltext'], 200, true, true, '<p><strong>'));
		$fulltext .= htmlspecialchars('<p><a target="_blank" href="' . $link . '">Ver más</a></p>');

		return $fulltext;
		
	}

	public static function trim_text($input, $length, $ellipses = true, $strip_html = true, $allowed_tags) {
    //strip tags, if desired
    if ($strip_html) {
        $input = strip_tags($input, $allowed_tags);
    }
  
    //no need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }
  
    //find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');
    $trimmed_text = substr($input, 0, $last_space);
  
    //add ellipses (...)
    if ($ellipses) {
        $trimmed_text .= '...';
    }
  
    return $trimmed_text;
}

}