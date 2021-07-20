<?php
/**
 * CreateWebBasedTraining
 *
 * PHP version 5
 *
 * @category Class
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * VEDA Bildungsmanager API
 *
 * Dokumentation der REST-Schnittstellen des VEDA Bildungsmanagers für die Version 2. Die Dokumentation zu speziellen Versionen kann über die Angabe des zusätzlichen Parameters \"group\" angezeigt werden. Beispiel: .../api/docs?group=v1 für die Dokumentation der Version 1, die aktuelle Version ist unter .../api/docs erreichbar.
 *
 * OpenAPI spec version: 2
 * Contact: info@veda.net
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.21
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Swagger\Client\Model;

use \ArrayAccess;
use \Swagger\Client\ObjectSerializer;

/**
 * CreateWebBasedTraining Class Doc Comment
 *
 * @category Class
 * @description Ein Web Based Training mit seinen relevanten Daten, zurErstellung eines neuen Web Based Training.
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class CreateWebBasedTraining implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'CreateWebBasedTraining';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'anzahl_ue' => 'float',
        'beschreibung' => 'string',
        'gueltig_ab' => '\DateTime',
        'gueltig_bis' => '\DateTime',
        'hinweise' => 'string',
        'inhalt' => 'string',
        'kurzbezeichnung' => 'string',
        'links' => '\Swagger\Client\Model\Link[]',
        'methodik' => 'string',
        'thema' => 'string',
        'thema2' => 'string',
        'veranstaltungs_nr' => 'string',
        'voraussetzung' => 'string',
        'wbd_relevant' => 'bool',
        'wbd_thema' => 'string',
        'ziel' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'anzahl_ue' => null,
        'beschreibung' => null,
        'gueltig_ab' => 'date',
        'gueltig_bis' => 'date',
        'hinweise' => null,
        'inhalt' => null,
        'kurzbezeichnung' => null,
        'links' => null,
        'methodik' => null,
        'thema' => null,
        'thema2' => null,
        'veranstaltungs_nr' => null,
        'voraussetzung' => null,
        'wbd_relevant' => null,
        'wbd_thema' => null,
        'ziel' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'anzahl_ue' => 'anzahlUE',
        'beschreibung' => 'beschreibung',
        'gueltig_ab' => 'gueltigAb',
        'gueltig_bis' => 'gueltigBis',
        'hinweise' => 'hinweise',
        'inhalt' => 'inhalt',
        'kurzbezeichnung' => 'kurzbezeichnung',
        'links' => 'links',
        'methodik' => 'methodik',
        'thema' => 'thema',
        'thema2' => 'thema2',
        'veranstaltungs_nr' => 'veranstaltungsNr',
        'voraussetzung' => 'voraussetzung',
        'wbd_relevant' => 'wbdRelevant',
        'wbd_thema' => 'wbdThema',
        'ziel' => 'ziel'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'anzahl_ue' => 'setAnzahlUe',
        'beschreibung' => 'setBeschreibung',
        'gueltig_ab' => 'setGueltigAb',
        'gueltig_bis' => 'setGueltigBis',
        'hinweise' => 'setHinweise',
        'inhalt' => 'setInhalt',
        'kurzbezeichnung' => 'setKurzbezeichnung',
        'links' => 'setLinks',
        'methodik' => 'setMethodik',
        'thema' => 'setThema',
        'thema2' => 'setThema2',
        'veranstaltungs_nr' => 'setVeranstaltungsNr',
        'voraussetzung' => 'setVoraussetzung',
        'wbd_relevant' => 'setWbdRelevant',
        'wbd_thema' => 'setWbdThema',
        'ziel' => 'setZiel'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'anzahl_ue' => 'getAnzahlUe',
        'beschreibung' => 'getBeschreibung',
        'gueltig_ab' => 'getGueltigAb',
        'gueltig_bis' => 'getGueltigBis',
        'hinweise' => 'getHinweise',
        'inhalt' => 'getInhalt',
        'kurzbezeichnung' => 'getKurzbezeichnung',
        'links' => 'getLinks',
        'methodik' => 'getMethodik',
        'thema' => 'getThema',
        'thema2' => 'getThema2',
        'veranstaltungs_nr' => 'getVeranstaltungsNr',
        'voraussetzung' => 'getVoraussetzung',
        'wbd_relevant' => 'getWbdRelevant',
        'wbd_thema' => 'getWbdThema',
        'ziel' => 'getZiel'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['anzahl_ue'] = isset($data['anzahl_ue']) ? $data['anzahl_ue'] : null;
        $this->container['beschreibung'] = isset($data['beschreibung']) ? $data['beschreibung'] : null;
        $this->container['gueltig_ab'] = isset($data['gueltig_ab']) ? $data['gueltig_ab'] : null;
        $this->container['gueltig_bis'] = isset($data['gueltig_bis']) ? $data['gueltig_bis'] : null;
        $this->container['hinweise'] = isset($data['hinweise']) ? $data['hinweise'] : null;
        $this->container['inhalt'] = isset($data['inhalt']) ? $data['inhalt'] : null;
        $this->container['kurzbezeichnung'] = isset($data['kurzbezeichnung']) ? $data['kurzbezeichnung'] : null;
        $this->container['links'] = isset($data['links']) ? $data['links'] : null;
        $this->container['methodik'] = isset($data['methodik']) ? $data['methodik'] : null;
        $this->container['thema'] = isset($data['thema']) ? $data['thema'] : null;
        $this->container['thema2'] = isset($data['thema2']) ? $data['thema2'] : null;
        $this->container['veranstaltungs_nr'] = isset($data['veranstaltungs_nr']) ? $data['veranstaltungs_nr'] : null;
        $this->container['voraussetzung'] = isset($data['voraussetzung']) ? $data['voraussetzung'] : null;
        $this->container['wbd_relevant'] = isset($data['wbd_relevant']) ? $data['wbd_relevant'] : null;
        $this->container['wbd_thema'] = isset($data['wbd_thema']) ? $data['wbd_thema'] : null;
        $this->container['ziel'] = isset($data['ziel']) ? $data['ziel'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['thema'] === null) {
            $invalidProperties[] = "'thema' can't be null";
        }
        if ($this->container['veranstaltungs_nr'] === null) {
            $invalidProperties[] = "'veranstaltungs_nr' can't be null";
        }
        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets anzahl_ue
     *
     * @return float
     */
    public function getAnzahlUe()
    {
        return $this->container['anzahl_ue'];
    }

    /**
     * Sets anzahl_ue
     *
     * @param float $anzahl_ue Die Anzahl der Unterrichtseinheiten des Web Based Trainings
     *
     * @return $this
     */
    public function setAnzahlUe($anzahl_ue)
    {
        $this->container['anzahl_ue'] = $anzahl_ue;

        return $this;
    }

    /**
     * Gets beschreibung
     *
     * @return string
     */
    public function getBeschreibung()
    {
        return $this->container['beschreibung'];
    }

    /**
     * Sets beschreibung
     *
     * @param string $beschreibung Die Beschreibung des Web Based Trainings (HTML möglich)
     *
     * @return $this
     */
    public function setBeschreibung($beschreibung)
    {
        $this->container['beschreibung'] = $beschreibung;

        return $this;
    }

    /**
     * Gets gueltig_ab
     *
     * @return \DateTime
     */
    public function getGueltigAb()
    {
        return $this->container['gueltig_ab'];
    }

    /**
     * Sets gueltig_ab
     *
     * @param \DateTime $gueltig_ab Der Gültigkeitsbeginn des Web Based Trainings
     *
     * @return $this
     */
    public function setGueltigAb($gueltig_ab)
    {
        $this->container['gueltig_ab'] = $gueltig_ab;

        return $this;
    }

    /**
     * Gets gueltig_bis
     *
     * @return \DateTime
     */
    public function getGueltigBis()
    {
        return $this->container['gueltig_bis'];
    }

    /**
     * Sets gueltig_bis
     *
     * @param \DateTime $gueltig_bis Das Gültigkeitsende des Web Based Trainings
     *
     * @return $this
     */
    public function setGueltigBis($gueltig_bis)
    {
        $this->container['gueltig_bis'] = $gueltig_bis;

        return $this;
    }

    /**
     * Gets hinweise
     *
     * @return string
     */
    public function getHinweise()
    {
        return $this->container['hinweise'];
    }

    /**
     * Sets hinweise
     *
     * @param string $hinweise Hinweise zum Web Based Training
     *
     * @return $this
     */
    public function setHinweise($hinweise)
    {
        $this->container['hinweise'] = $hinweise;

        return $this;
    }

    /**
     * Gets inhalt
     *
     * @return string
     */
    public function getInhalt()
    {
        return $this->container['inhalt'];
    }

    /**
     * Sets inhalt
     *
     * @param string $inhalt Der Inhalt des Web Based Trainings (HTML möglich)
     *
     * @return $this
     */
    public function setInhalt($inhalt)
    {
        $this->container['inhalt'] = $inhalt;

        return $this;
    }

    /**
     * Gets kurzbezeichnung
     *
     * @return string
     */
    public function getKurzbezeichnung()
    {
        return $this->container['kurzbezeichnung'];
    }

    /**
     * Sets kurzbezeichnung
     *
     * @param string $kurzbezeichnung Die Kurzbezeichnung des Web Based Trainings
     *
     * @return $this
     */
    public function setKurzbezeichnung($kurzbezeichnung)
    {
        $this->container['kurzbezeichnung'] = $kurzbezeichnung;

        return $this;
    }

    /**
     * Gets links
     *
     * @return \Swagger\Client\Model\Link[]
     */
    public function getLinks()
    {
        return $this->container['links'];
    }

    /**
     * Sets links
     *
     * @param \Swagger\Client\Model\Link[] $links links
     *
     * @return $this
     */
    public function setLinks($links)
    {
        $this->container['links'] = $links;

        return $this;
    }

    /**
     * Gets methodik
     *
     * @return string
     */
    public function getMethodik()
    {
        return $this->container['methodik'];
    }

    /**
     * Sets methodik
     *
     * @param string $methodik Die Methodik des Web Based Trainings (HTML möglich)
     *
     * @return $this
     */
    public function setMethodik($methodik)
    {
        $this->container['methodik'] = $methodik;

        return $this;
    }

    /**
     * Gets thema
     *
     * @return string
     */
    public function getThema()
    {
        return $this->container['thema'];
    }

    /**
     * Sets thema
     *
     * @param string $thema Das Thema des Web Based Trainings
     *
     * @return $this
     */
    public function setThema($thema)
    {
        $this->container['thema'] = $thema;

        return $this;
    }

    /**
     * Gets thema2
     *
     * @return string
     */
    public function getThema2()
    {
        return $this->container['thema2'];
    }

    /**
     * Sets thema2
     *
     * @param string $thema2 Thema 2 des Web Based Trainings
     *
     * @return $this
     */
    public function setThema2($thema2)
    {
        $this->container['thema2'] = $thema2;

        return $this;
    }

    /**
     * Gets veranstaltungs_nr
     *
     * @return string
     */
    public function getVeranstaltungsNr()
    {
        return $this->container['veranstaltungs_nr'];
    }

    /**
     * Sets veranstaltungs_nr
     *
     * @param string $veranstaltungs_nr Die Veranstaltungs-Nr. des Web Based Trainings
     *
     * @return $this
     */
    public function setVeranstaltungsNr($veranstaltungs_nr)
    {
        $this->container['veranstaltungs_nr'] = $veranstaltungs_nr;

        return $this;
    }

    /**
     * Gets voraussetzung
     *
     * @return string
     */
    public function getVoraussetzung()
    {
        return $this->container['voraussetzung'];
    }

    /**
     * Sets voraussetzung
     *
     * @param string $voraussetzung Die Voraussetzungen für das Web Based Trainings (HTML möglich)
     *
     * @return $this
     */
    public function setVoraussetzung($voraussetzung)
    {
        $this->container['voraussetzung'] = $voraussetzung;

        return $this;
    }

    /**
     * Gets wbd_relevant
     *
     * @return bool
     */
    public function getWbdRelevant()
    {
        return $this->container['wbd_relevant'];
    }

    /**
     * Sets wbd_relevant
     *
     * @param bool $wbd_relevant Dieses Kennzeichen gibt an, ob das Web Based Training relevant für die Weiterbildungsdatenbank(WBD) ist
     *
     * @return $this
     */
    public function setWbdRelevant($wbd_relevant)
    {
        $this->container['wbd_relevant'] = $wbd_relevant;

        return $this;
    }

    /**
     * Gets wbd_thema
     *
     * @return string
     */
    public function getWbdThema()
    {
        return $this->container['wbd_thema'];
    }

    /**
     * Sets wbd_thema
     *
     * @param string $wbd_thema Das WBD-Thema des Web Based Trainings
     *
     * @return $this
     */
    public function setWbdThema($wbd_thema)
    {
        $this->container['wbd_thema'] = $wbd_thema;

        return $this;
    }

    /**
     * Gets ziel
     *
     * @return string
     */
    public function getZiel()
    {
        return $this->container['ziel'];
    }

    /**
     * Sets ziel
     *
     * @param string $ziel Die Ziele des Web Based Trainings (HTML möglich)
     *
     * @return $this
     */
    public function setZiel($ziel)
    {
        $this->container['ziel'] = $ziel;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


