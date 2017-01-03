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
	 *  
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
								'website'			=> 26,
								'persontype'		=> 27,
								'milestonetype'		=> 29,
								'eventtype'			=> 30,
								'place'				=> 31,
								'videotype'			=> 32,
								'pubtype'			=> 33,
								'presentationweb'	=> 34 
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

	public static function getPersonImageUrl( $person_id, $size = 'S' ) {

		return Juri::base() . 'media/k2/items/cache/' . md5('Image' . $person_id ) . '_' . $size . '.jpg';

	}

	public static function getPersonFields( $person_id ) {

		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
					->select($db->quoteName( 'extra_fields'))
					->from($db->quoteName(self::$pamitemstable))
					->where($db->quoteName('id') . ' LIKE ' . $db->quote( $person_id ));

		$db->setQuery($query);

		$fieldcontents = $db->loadResult();

		return $fieldcontents;


	}

	public static function getPersonYears( $person_id ) {

		$fields = self::getPersonFields( $person_id );

		$decoded = json_decode( $fields );



		//var_dump($decoded);
		foreach($decoded as $decode) {

			if( $decode->id == self::$pamfieldassocs['year'] ) {

				$years = $decode->value;

			}

		}

		if( count($years) > 1 ) {

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


	

}