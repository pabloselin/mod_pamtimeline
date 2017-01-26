<?php
/**
 * Helper class for Pam Timeline module
 * @package Joomla
 * @subpackage Modules
 * @link https://apie.cl
 * @license MIT
 *
 */

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

	static $pamworkscat = 6;
	static $pampersonscat = 7;
	static $pameventscat = 19;
	static $pammilestonecat = 18;

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
								'personid'			=> 35
								);

	public static function getPersons( ) {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select($db->quoteName( array('id', 'title', 'alias', 'catid', 'extra_fields') ))
					->from($db->quoteName( self::$pamitemstable))
					->where($db->quoteName('catid') . ' LIKE ' . $db->quote( self::$pampersonscat ));

		$db->setQuery($query);

		$result = $db->loadAssocList();

		return $result;

	}

	public static function getItems( $catid ) {
		
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
					->select($db->quoteName( array('id', 'title', 'alias', 'catid', 'extra_fields', 'introtext')))
					->from($db->quoteName( self::$pamitemstable))
					->where($db->quoteName('catid')) . ' LIKE ' . $db->quote( $catid );

		$db->setQuery($query);

		$result = $db->loadAssocList();

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

		if( count($years) >= 1 ) {

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

		return $field_name;

	}
	
	public static function getFieldIdFromName( $field_name ) {

		$db = JFactory::getDbo();
		



	}

	public static function getFieldsOfPerson( $person_id ) {
		/**
		 * Devuelve un array con la info de los campos de cada persona
		 */
		
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


}