<?php
/**
 * Veranstaltungstermin
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
 * Swagger Codegen version: 2.4.10
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
 * Veranstaltungstermin Class Doc Comment
 *
 * @category Class
 * @description Ein Veranstaltungstermin mit seinen relevanten Daten
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Veranstaltungstermin implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Veranstaltungstermin';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'oid' => 'string',
        'anz_teilnehmertage_pro_teilnehmer' => 'float',
        'anzahl_freier_plaetze' => 'int',
        'anzahl_freier_wartelistenplaetze' => 'int',
        'anzahl_teilnehmerbuchungen' => 'int',
        'anzahl_ue' => 'float',
        'anzahl_wartelistenbuchungen' => 'int',
        'auslastungsstatus' => 'string',
        'beschreibung' => 'string',
        'dauer_in_tagen' => 'float',
        'hinweise' => 'string',
        'inhalt' => 'string',
        'links' => '\Swagger\Client\Model\Link[]',
        'methodik' => 'string',
        'preis' => 'float',
        'preise_je_teilnehmergruppe' => '\Swagger\Client\Model\TeilnehmergruppePreis[]',
        'reg_uhrzeit_bis' => 'string',
        'reg_uhrzeit_von' => 'string',
        'sprache' => 'string',
        'teilnehmermaximum' => 'int',
        'teilnehmerminimum' => 'int',
        'termin_bis' => '\DateTime',
        'termin_von' => '\DateTime',
        'thema' => 'string',
        'thema2' => 'string',
        'veranstaltungs_nr' => 'string',
        'veranstaltungsanbieter' => '\Swagger\Client\Model\Veranstaltungsanbieter',
        'veranstaltungsart' => 'string',
        'veranstaltungskategorie' => '\Swagger\Client\Model\Veranstaltungskategorie',
        'veranstaltungsort' => '\Swagger\Client\Model\Veranstaltungsort',
        'veranstaltungstyp_id' => 'string',
        'voraussetzung' => 'string',
        'wartelistenmaximum' => 'int',
        'wbd_relevant' => 'bool',
        'wbd_thema' => 'string',
        'ziel' => 'string',
        'zielgruppen' => '\Swagger\Client\Model\Zielgruppe[]'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        'oid' => null,
        'anz_teilnehmertage_pro_teilnehmer' => null,
        'anzahl_freier_plaetze' => null,
        'anzahl_freier_wartelistenplaetze' => null,
        'anzahl_teilnehmerbuchungen' => null,
        'anzahl_ue' => null,
        'anzahl_wartelistenbuchungen' => null,
        'auslastungsstatus' => null,
        'beschreibung' => null,
        'dauer_in_tagen' => null,
        'hinweise' => null,
        'inhalt' => null,
        'links' => null,
        'methodik' => null,
        'preis' => null,
        'preise_je_teilnehmergruppe' => null,
        'reg_uhrzeit_bis' => null,
        'reg_uhrzeit_von' => null,
        'sprache' => null,
        'teilnehmermaximum' => null,
        'teilnehmerminimum' => null,
        'termin_bis' => 'date-time',
        'termin_von' => 'date-time',
        'thema' => null,
        'thema2' => null,
        'veranstaltungs_nr' => null,
        'veranstaltungsanbieter' => null,
        'veranstaltungsart' => null,
        'veranstaltungskategorie' => null,
        'veranstaltungsort' => null,
        'veranstaltungstyp_id' => null,
        'voraussetzung' => null,
        'wartelistenmaximum' => null,
        'wbd_relevant' => null,
        'wbd_thema' => null,
        'ziel' => null,
        'zielgruppen' => null
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
        'oid' => 'oid',
        'anz_teilnehmertage_pro_teilnehmer' => 'anzTeilnehmertageProTeilnehmer',
        'anzahl_freier_plaetze' => 'anzahlFreierPlaetze',
        'anzahl_freier_wartelistenplaetze' => 'anzahlFreierWartelistenplaetze',
        'anzahl_teilnehmerbuchungen' => 'anzahlTeilnehmerbuchungen',
        'anzahl_ue' => 'anzahlUE',
        'anzahl_wartelistenbuchungen' => 'anzahlWartelistenbuchungen',
        'auslastungsstatus' => 'auslastungsstatus',
        'beschreibung' => 'beschreibung',
        'dauer_in_tagen' => 'dauerInTagen',
        'hinweise' => 'hinweise',
        'inhalt' => 'inhalt',
        'links' => 'links',
        'methodik' => 'methodik',
        'preis' => 'preis',
        'preise_je_teilnehmergruppe' => 'preiseJeTeilnehmergruppe',
        'reg_uhrzeit_bis' => 'regUhrzeitBis',
        'reg_uhrzeit_von' => 'regUhrzeitVon',
        'sprache' => 'sprache',
        'teilnehmermaximum' => 'teilnehmermaximum',
        'teilnehmerminimum' => 'teilnehmerminimum',
        'termin_bis' => 'terminBis',
        'termin_von' => 'terminVon',
        'thema' => 'thema',
        'thema2' => 'thema2',
        'veranstaltungs_nr' => 'veranstaltungsNr',
        'veranstaltungsanbieter' => 'veranstaltungsanbieter',
        'veranstaltungsart' => 'veranstaltungsart',
        'veranstaltungskategorie' => 'veranstaltungskategorie',
        'veranstaltungsort' => 'veranstaltungsort',
        'veranstaltungstyp_id' => 'veranstaltungstypID',
        'voraussetzung' => 'voraussetzung',
        'wartelistenmaximum' => 'wartelistenmaximum',
        'wbd_relevant' => 'wbdRelevant',
        'wbd_thema' => 'wbdThema',
        'ziel' => 'ziel',
        'zielgruppen' => 'zielgruppen'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'oid' => 'setOid',
        'anz_teilnehmertage_pro_teilnehmer' => 'setAnzTeilnehmertageProTeilnehmer',
        'anzahl_freier_plaetze' => 'setAnzahlFreierPlaetze',
        'anzahl_freier_wartelistenplaetze' => 'setAnzahlFreierWartelistenplaetze',
        'anzahl_teilnehmerbuchungen' => 'setAnzahlTeilnehmerbuchungen',
        'anzahl_ue' => 'setAnzahlUe',
        'anzahl_wartelistenbuchungen' => 'setAnzahlWartelistenbuchungen',
        'auslastungsstatus' => 'setAuslastungsstatus',
        'beschreibung' => 'setBeschreibung',
        'dauer_in_tagen' => 'setDauerInTagen',
        'hinweise' => 'setHinweise',
        'inhalt' => 'setInhalt',
        'links' => 'setLinks',
        'methodik' => 'setMethodik',
        'preis' => 'setPreis',
        'preise_je_teilnehmergruppe' => 'setPreiseJeTeilnehmergruppe',
        'reg_uhrzeit_bis' => 'setRegUhrzeitBis',
        'reg_uhrzeit_von' => 'setRegUhrzeitVon',
        'sprache' => 'setSprache',
        'teilnehmermaximum' => 'setTeilnehmermaximum',
        'teilnehmerminimum' => 'setTeilnehmerminimum',
        'termin_bis' => 'setTerminBis',
        'termin_von' => 'setTerminVon',
        'thema' => 'setThema',
        'thema2' => 'setThema2',
        'veranstaltungs_nr' => 'setVeranstaltungsNr',
        'veranstaltungsanbieter' => 'setVeranstaltungsanbieter',
        'veranstaltungsart' => 'setVeranstaltungsart',
        'veranstaltungskategorie' => 'setVeranstaltungskategorie',
        'veranstaltungsort' => 'setVeranstaltungsort',
        'veranstaltungstyp_id' => 'setVeranstaltungstypId',
        'voraussetzung' => 'setVoraussetzung',
        'wartelistenmaximum' => 'setWartelistenmaximum',
        'wbd_relevant' => 'setWbdRelevant',
        'wbd_thema' => 'setWbdThema',
        'ziel' => 'setZiel',
        'zielgruppen' => 'setZielgruppen'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'oid' => 'getOid',
        'anz_teilnehmertage_pro_teilnehmer' => 'getAnzTeilnehmertageProTeilnehmer',
        'anzahl_freier_plaetze' => 'getAnzahlFreierPlaetze',
        'anzahl_freier_wartelistenplaetze' => 'getAnzahlFreierWartelistenplaetze',
        'anzahl_teilnehmerbuchungen' => 'getAnzahlTeilnehmerbuchungen',
        'anzahl_ue' => 'getAnzahlUe',
        'anzahl_wartelistenbuchungen' => 'getAnzahlWartelistenbuchungen',
        'auslastungsstatus' => 'getAuslastungsstatus',
        'beschreibung' => 'getBeschreibung',
        'dauer_in_tagen' => 'getDauerInTagen',
        'hinweise' => 'getHinweise',
        'inhalt' => 'getInhalt',
        'links' => 'getLinks',
        'methodik' => 'getMethodik',
        'preis' => 'getPreis',
        'preise_je_teilnehmergruppe' => 'getPreiseJeTeilnehmergruppe',
        'reg_uhrzeit_bis' => 'getRegUhrzeitBis',
        'reg_uhrzeit_von' => 'getRegUhrzeitVon',
        'sprache' => 'getSprache',
        'teilnehmermaximum' => 'getTeilnehmermaximum',
        'teilnehmerminimum' => 'getTeilnehmerminimum',
        'termin_bis' => 'getTerminBis',
        'termin_von' => 'getTerminVon',
        'thema' => 'getThema',
        'thema2' => 'getThema2',
        'veranstaltungs_nr' => 'getVeranstaltungsNr',
        'veranstaltungsanbieter' => 'getVeranstaltungsanbieter',
        'veranstaltungsart' => 'getVeranstaltungsart',
        'veranstaltungskategorie' => 'getVeranstaltungskategorie',
        'veranstaltungsort' => 'getVeranstaltungsort',
        'veranstaltungstyp_id' => 'getVeranstaltungstypId',
        'voraussetzung' => 'getVoraussetzung',
        'wartelistenmaximum' => 'getWartelistenmaximum',
        'wbd_relevant' => 'getWbdRelevant',
        'wbd_thema' => 'getWbdThema',
        'ziel' => 'getZiel',
        'zielgruppen' => 'getZielgruppen'
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
        $this->container['oid'] = isset($data['oid']) ? $data['oid'] : null;
        $this->container['anz_teilnehmertage_pro_teilnehmer'] = isset($data['anz_teilnehmertage_pro_teilnehmer']) ? $data['anz_teilnehmertage_pro_teilnehmer'] : null;
        $this->container['anzahl_freier_plaetze'] = isset($data['anzahl_freier_plaetze']) ? $data['anzahl_freier_plaetze'] : null;
        $this->container['anzahl_freier_wartelistenplaetze'] = isset($data['anzahl_freier_wartelistenplaetze']) ? $data['anzahl_freier_wartelistenplaetze'] : null;
        $this->container['anzahl_teilnehmerbuchungen'] = isset($data['anzahl_teilnehmerbuchungen']) ? $data['anzahl_teilnehmerbuchungen'] : null;
        $this->container['anzahl_ue'] = isset($data['anzahl_ue']) ? $data['anzahl_ue'] : null;
        $this->container['anzahl_wartelistenbuchungen'] = isset($data['anzahl_wartelistenbuchungen']) ? $data['anzahl_wartelistenbuchungen'] : null;
        $this->container['auslastungsstatus'] = isset($data['auslastungsstatus']) ? $data['auslastungsstatus'] : null;
        $this->container['beschreibung'] = isset($data['beschreibung']) ? $data['beschreibung'] : null;
        $this->container['dauer_in_tagen'] = isset($data['dauer_in_tagen']) ? $data['dauer_in_tagen'] : null;
        $this->container['hinweise'] = isset($data['hinweise']) ? $data['hinweise'] : null;
        $this->container['inhalt'] = isset($data['inhalt']) ? $data['inhalt'] : null;
        $this->container['links'] = isset($data['links']) ? $data['links'] : null;
        $this->container['methodik'] = isset($data['methodik']) ? $data['methodik'] : null;
        $this->container['preis'] = isset($data['preis']) ? $data['preis'] : null;
        $this->container['preise_je_teilnehmergruppe'] = isset($data['preise_je_teilnehmergruppe']) ? $data['preise_je_teilnehmergruppe'] : null;
        $this->container['reg_uhrzeit_bis'] = isset($data['reg_uhrzeit_bis']) ? $data['reg_uhrzeit_bis'] : null;
        $this->container['reg_uhrzeit_von'] = isset($data['reg_uhrzeit_von']) ? $data['reg_uhrzeit_von'] : null;
        $this->container['sprache'] = isset($data['sprache']) ? $data['sprache'] : null;
        $this->container['teilnehmermaximum'] = isset($data['teilnehmermaximum']) ? $data['teilnehmermaximum'] : null;
        $this->container['teilnehmerminimum'] = isset($data['teilnehmerminimum']) ? $data['teilnehmerminimum'] : null;
        $this->container['termin_bis'] = isset($data['termin_bis']) ? $data['termin_bis'] : null;
        $this->container['termin_von'] = isset($data['termin_von']) ? $data['termin_von'] : null;
        $this->container['thema'] = isset($data['thema']) ? $data['thema'] : null;
        $this->container['thema2'] = isset($data['thema2']) ? $data['thema2'] : null;
        $this->container['veranstaltungs_nr'] = isset($data['veranstaltungs_nr']) ? $data['veranstaltungs_nr'] : null;
        $this->container['veranstaltungsanbieter'] = isset($data['veranstaltungsanbieter']) ? $data['veranstaltungsanbieter'] : null;
        $this->container['veranstaltungsart'] = isset($data['veranstaltungsart']) ? $data['veranstaltungsart'] : null;
        $this->container['veranstaltungskategorie'] = isset($data['veranstaltungskategorie']) ? $data['veranstaltungskategorie'] : null;
        $this->container['veranstaltungsort'] = isset($data['veranstaltungsort']) ? $data['veranstaltungsort'] : null;
        $this->container['veranstaltungstyp_id'] = isset($data['veranstaltungstyp_id']) ? $data['veranstaltungstyp_id'] : null;
        $this->container['voraussetzung'] = isset($data['voraussetzung']) ? $data['voraussetzung'] : null;
        $this->container['wartelistenmaximum'] = isset($data['wartelistenmaximum']) ? $data['wartelistenmaximum'] : null;
        $this->container['wbd_relevant'] = isset($data['wbd_relevant']) ? $data['wbd_relevant'] : null;
        $this->container['wbd_thema'] = isset($data['wbd_thema']) ? $data['wbd_thema'] : null;
        $this->container['ziel'] = isset($data['ziel']) ? $data['ziel'] : null;
        $this->container['zielgruppen'] = isset($data['zielgruppen']) ? $data['zielgruppen'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if ($this->container['oid'] === null) {
            $invalidProperties[] = "'oid' can't be null";
        }
        if ($this->container['termin_bis'] === null) {
            $invalidProperties[] = "'termin_bis' can't be null";
        }
        if ($this->container['termin_von'] === null) {
            $invalidProperties[] = "'termin_von' can't be null";
        }
        if ($this->container['thema'] === null) {
            $invalidProperties[] = "'thema' can't be null";
        }
        if ($this->container['veranstaltungs_nr'] === null) {
            $invalidProperties[] = "'veranstaltungs_nr' can't be null";
        }
        if ($this->container['veranstaltungsart'] === null) {
            $invalidProperties[] = "'veranstaltungsart' can't be null";
        }
        if ($this->container['veranstaltungstyp_id'] === null) {
            $invalidProperties[] = "'veranstaltungstyp_id' can't be null";
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
     * Gets oid
     *
     * @return string
     */
    public function getOid()
    {
        return $this->container['oid'];
    }

    /**
     * Sets oid
     *
     * @param string $oid UUID des Datensatzes
     *
     * @return $this
     */
    public function setOid($oid)
    {
        $this->container['oid'] = $oid;

        return $this;
    }

    /**
     * Gets anz_teilnehmertage_pro_teilnehmer
     *
     * @return float
     */
    public function getAnzTeilnehmertageProTeilnehmer()
    {
        return $this->container['anz_teilnehmertage_pro_teilnehmer'];
    }

    /**
     * Sets anz_teilnehmertage_pro_teilnehmer
     *
     * @param float $anz_teilnehmertage_pro_teilnehmer Anzahl Teilnehmertage pro Teilnehmer des Veranstaltungstermins
     *
     * @return $this
     */
    public function setAnzTeilnehmertageProTeilnehmer($anz_teilnehmertage_pro_teilnehmer)
    {
        $this->container['anz_teilnehmertage_pro_teilnehmer'] = $anz_teilnehmertage_pro_teilnehmer;

        return $this;
    }

    /**
     * Gets anzahl_freier_plaetze
     *
     * @return int
     */
    public function getAnzahlFreierPlaetze()
    {
        return $this->container['anzahl_freier_plaetze'];
    }

    /**
     * Sets anzahl_freier_plaetze
     *
     * @param int $anzahl_freier_plaetze Anzahl freier Plätze des Veranstaltungstermins
     *
     * @return $this
     */
    public function setAnzahlFreierPlaetze($anzahl_freier_plaetze)
    {
        $this->container['anzahl_freier_plaetze'] = $anzahl_freier_plaetze;

        return $this;
    }

    /**
     * Gets anzahl_freier_wartelistenplaetze
     *
     * @return int
     */
    public function getAnzahlFreierWartelistenplaetze()
    {
        return $this->container['anzahl_freier_wartelistenplaetze'];
    }

    /**
     * Sets anzahl_freier_wartelistenplaetze
     *
     * @param int $anzahl_freier_wartelistenplaetze Anzahl freier Wartelistenplätze des Veranstaltungstermins
     *
     * @return $this
     */
    public function setAnzahlFreierWartelistenplaetze($anzahl_freier_wartelistenplaetze)
    {
        $this->container['anzahl_freier_wartelistenplaetze'] = $anzahl_freier_wartelistenplaetze;

        return $this;
    }

    /**
     * Gets anzahl_teilnehmerbuchungen
     *
     * @return int
     */
    public function getAnzahlTeilnehmerbuchungen()
    {
        return $this->container['anzahl_teilnehmerbuchungen'];
    }

    /**
     * Sets anzahl_teilnehmerbuchungen
     *
     * @param int $anzahl_teilnehmerbuchungen Anzahl Teilnehmerbuchungen des Veranstaltungstermins
     *
     * @return $this
     */
    public function setAnzahlTeilnehmerbuchungen($anzahl_teilnehmerbuchungen)
    {
        $this->container['anzahl_teilnehmerbuchungen'] = $anzahl_teilnehmerbuchungen;

        return $this;
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
     * @param float $anzahl_ue Anzahl der Unterrichtseinheiten des Veranstaltungstermins
     *
     * @return $this
     */
    public function setAnzahlUe($anzahl_ue)
    {
        $this->container['anzahl_ue'] = $anzahl_ue;

        return $this;
    }

    /**
     * Gets anzahl_wartelistenbuchungen
     *
     * @return int
     */
    public function getAnzahlWartelistenbuchungen()
    {
        return $this->container['anzahl_wartelistenbuchungen'];
    }

    /**
     * Sets anzahl_wartelistenbuchungen
     *
     * @param int $anzahl_wartelistenbuchungen Anzahl Wartelistenbuchungen des Veranstaltungstermins
     *
     * @return $this
     */
    public function setAnzahlWartelistenbuchungen($anzahl_wartelistenbuchungen)
    {
        $this->container['anzahl_wartelistenbuchungen'] = $anzahl_wartelistenbuchungen;

        return $this;
    }

    /**
     * Gets auslastungsstatus
     *
     * @return string
     */
    public function getAuslastungsstatus()
    {
        return $this->container['auslastungsstatus'];
    }

    /**
     * Sets auslastungsstatus
     *
     * @param string $auslastungsstatus Auslastungsstatus des Veranstaltungstermins
     *
     * @return $this
     */
    public function setAuslastungsstatus($auslastungsstatus)
    {
        $this->container['auslastungsstatus'] = $auslastungsstatus;

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
     * @param string $beschreibung Beschreibung des Veranstaltungstermins
     *
     * @return $this
     */
    public function setBeschreibung($beschreibung)
    {
        $this->container['beschreibung'] = $beschreibung;

        return $this;
    }

    /**
     * Gets dauer_in_tagen
     *
     * @return float
     */
    public function getDauerInTagen()
    {
        return $this->container['dauer_in_tagen'];
    }

    /**
     * Sets dauer_in_tagen
     *
     * @param float $dauer_in_tagen Die Dauer in Tagen des Veranstaltungstermins
     *
     * @return $this
     */
    public function setDauerInTagen($dauer_in_tagen)
    {
        $this->container['dauer_in_tagen'] = $dauer_in_tagen;

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
     * @param string $hinweise Hinweise zum Veranstaltungstermin
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
     * @param string $inhalt Inhalte  des Veranstaltungstermins
     *
     * @return $this
     */
    public function setInhalt($inhalt)
    {
        $this->container['inhalt'] = $inhalt;

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
     * @param string $methodik Methodik des Veranstaltungstermins
     *
     * @return $this
     */
    public function setMethodik($methodik)
    {
        $this->container['methodik'] = $methodik;

        return $this;
    }

    /**
     * Gets preis
     *
     * @return float
     */
    public function getPreis()
    {
        return $this->container['preis'];
    }

    /**
     * Sets preis
     *
     * @param float $preis Der Standardpreis des Veranstaltungstermins.
     *
     * @return $this
     */
    public function setPreis($preis)
    {
        $this->container['preis'] = $preis;

        return $this;
    }

    /**
     * Gets preise_je_teilnehmergruppe
     *
     * @return \Swagger\Client\Model\TeilnehmergruppePreis[]
     */
    public function getPreiseJeTeilnehmergruppe()
    {
        return $this->container['preise_je_teilnehmergruppe'];
    }

    /**
     * Sets preise_je_teilnehmergruppe
     *
     * @param \Swagger\Client\Model\TeilnehmergruppePreis[] $preise_je_teilnehmergruppe Die Preise je nach Teilnehmergruppe des Veranstaltungstermins.
     *
     * @return $this
     */
    public function setPreiseJeTeilnehmergruppe($preise_je_teilnehmergruppe)
    {
        $this->container['preise_je_teilnehmergruppe'] = $preise_je_teilnehmergruppe;

        return $this;
    }

    /**
     * Gets reg_uhrzeit_bis
     *
     * @return string
     */
    public function getRegUhrzeitBis()
    {
        return $this->container['reg_uhrzeit_bis'];
    }

    /**
     * Sets reg_uhrzeit_bis
     *
     * @param string $reg_uhrzeit_bis Reguläres Ende des Veranstaltungstermins
     *
     * @return $this
     */
    public function setRegUhrzeitBis($reg_uhrzeit_bis)
    {
        $this->container['reg_uhrzeit_bis'] = $reg_uhrzeit_bis;

        return $this;
    }

    /**
     * Gets reg_uhrzeit_von
     *
     * @return string
     */
    public function getRegUhrzeitVon()
    {
        return $this->container['reg_uhrzeit_von'];
    }

    /**
     * Sets reg_uhrzeit_von
     *
     * @param string $reg_uhrzeit_von Reguläre Startzeit des Veranstaltungstermins
     *
     * @return $this
     */
    public function setRegUhrzeitVon($reg_uhrzeit_von)
    {
        $this->container['reg_uhrzeit_von'] = $reg_uhrzeit_von;

        return $this;
    }

    /**
     * Gets sprache
     *
     * @return string
     */
    public function getSprache()
    {
        return $this->container['sprache'];
    }

    /**
     * Sets sprache
     *
     * @param string $sprache Die Sprache, in der der Veranstaltungstermin durchgeführt wird.
     *
     * @return $this
     */
    public function setSprache($sprache)
    {
        $this->container['sprache'] = $sprache;

        return $this;
    }

    /**
     * Gets teilnehmermaximum
     *
     * @return int
     */
    public function getTeilnehmermaximum()
    {
        return $this->container['teilnehmermaximum'];
    }

    /**
     * Sets teilnehmermaximum
     *
     * @param int $teilnehmermaximum Teilnehmermaximum des Veranstaltungstermins
     *
     * @return $this
     */
    public function setTeilnehmermaximum($teilnehmermaximum)
    {
        $this->container['teilnehmermaximum'] = $teilnehmermaximum;

        return $this;
    }

    /**
     * Gets teilnehmerminimum
     *
     * @return int
     */
    public function getTeilnehmerminimum()
    {
        return $this->container['teilnehmerminimum'];
    }

    /**
     * Sets teilnehmerminimum
     *
     * @param int $teilnehmerminimum Teilnehmerminimum des Veranstaltungstermins
     *
     * @return $this
     */
    public function setTeilnehmerminimum($teilnehmerminimum)
    {
        $this->container['teilnehmerminimum'] = $teilnehmerminimum;

        return $this;
    }

    /**
     * Gets termin_bis
     *
     * @return \DateTime
     */
    public function getTerminBis()
    {
        return $this->container['termin_bis'];
    }

    /**
     * Sets termin_bis
     *
     * @param \DateTime $termin_bis Termin bis des Veranstaltungstermins
     *
     * @return $this
     */
    public function setTerminBis($termin_bis)
    {
        $this->container['termin_bis'] = $termin_bis;

        return $this;
    }

    /**
     * Gets termin_von
     *
     * @return \DateTime
     */
    public function getTerminVon()
    {
        return $this->container['termin_von'];
    }

    /**
     * Sets termin_von
     *
     * @param \DateTime $termin_von Termin von des Veranstaltungstermins
     *
     * @return $this
     */
    public function setTerminVon($termin_von)
    {
        $this->container['termin_von'] = $termin_von;

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
     * @param string $thema Das Thema des Veranstaltungstermins.
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
     * @param string $thema2 Thema 2 des Veranstaltungstermins.
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
     * @param string $veranstaltungs_nr Die Veranstaltungs-Nr. des Veranstaltungstermins.
     *
     * @return $this
     */
    public function setVeranstaltungsNr($veranstaltungs_nr)
    {
        $this->container['veranstaltungs_nr'] = $veranstaltungs_nr;

        return $this;
    }

    /**
     * Gets veranstaltungsanbieter
     *
     * @return \Swagger\Client\Model\Veranstaltungsanbieter
     */
    public function getVeranstaltungsanbieter()
    {
        return $this->container['veranstaltungsanbieter'];
    }

    /**
     * Sets veranstaltungsanbieter
     *
     * @param \Swagger\Client\Model\Veranstaltungsanbieter $veranstaltungsanbieter Der Veranstaltungsanbieter des Veranstaltungstermins.
     *
     * @return $this
     */
    public function setVeranstaltungsanbieter($veranstaltungsanbieter)
    {
        $this->container['veranstaltungsanbieter'] = $veranstaltungsanbieter;

        return $this;
    }

    /**
     * Gets veranstaltungsart
     *
     * @return string
     */
    public function getVeranstaltungsart()
    {
        return $this->container['veranstaltungsart'];
    }

    /**
     * Sets veranstaltungsart
     *
     * @param string $veranstaltungsart Veranstaltungsart des Veranstaltungstermins, zulässig sind hier VIRTUELL für Virtuell und PRAESENZ für Präsenz.
     *
     * @return $this
     */
    public function setVeranstaltungsart($veranstaltungsart)
    {
        $this->container['veranstaltungsart'] = $veranstaltungsart;

        return $this;
    }

    /**
     * Gets veranstaltungskategorie
     *
     * @return \Swagger\Client\Model\Veranstaltungskategorie
     */
    public function getVeranstaltungskategorie()
    {
        return $this->container['veranstaltungskategorie'];
    }

    /**
     * Sets veranstaltungskategorie
     *
     * @param \Swagger\Client\Model\Veranstaltungskategorie $veranstaltungskategorie Die Kategorie, der der Veranstaltungstermin zugeordnet ist.
     *
     * @return $this
     */
    public function setVeranstaltungskategorie($veranstaltungskategorie)
    {
        $this->container['veranstaltungskategorie'] = $veranstaltungskategorie;

        return $this;
    }

    /**
     * Gets veranstaltungsort
     *
     * @return \Swagger\Client\Model\Veranstaltungsort
     */
    public function getVeranstaltungsort()
    {
        return $this->container['veranstaltungsort'];
    }

    /**
     * Sets veranstaltungsort
     *
     * @param \Swagger\Client\Model\Veranstaltungsort $veranstaltungsort Der Veranstaltungsanbieter des Veranstaltungstermins.
     *
     * @return $this
     */
    public function setVeranstaltungsort($veranstaltungsort)
    {
        $this->container['veranstaltungsort'] = $veranstaltungsort;

        return $this;
    }

    /**
     * Gets veranstaltungstyp_id
     *
     * @return string
     */
    public function getVeranstaltungstypId()
    {
        return $this->container['veranstaltungstyp_id'];
    }

    /**
     * Sets veranstaltungstyp_id
     *
     * @param string $veranstaltungstyp_id Die ID des Veranstaltungstyps des Veranstaltungstermins. Ist die ID gesetzt so wird zusätzlich ein Link auf den Veranstaltungstypen geliefert.
     *
     * @return $this
     */
    public function setVeranstaltungstypId($veranstaltungstyp_id)
    {
        $this->container['veranstaltungstyp_id'] = $veranstaltungstyp_id;

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
     * @param string $voraussetzung Voraussetzungen  des Veranstaltungstermins
     *
     * @return $this
     */
    public function setVoraussetzung($voraussetzung)
    {
        $this->container['voraussetzung'] = $voraussetzung;

        return $this;
    }

    /**
     * Gets wartelistenmaximum
     *
     * @return int
     */
    public function getWartelistenmaximum()
    {
        return $this->container['wartelistenmaximum'];
    }

    /**
     * Sets wartelistenmaximum
     *
     * @param int $wartelistenmaximum Wartelistenmaximum des Veranstaltungstermins
     *
     * @return $this
     */
    public function setWartelistenmaximum($wartelistenmaximum)
    {
        $this->container['wartelistenmaximum'] = $wartelistenmaximum;

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
     * @param bool $wbd_relevant Flag der Veranstaltungstermins WBD relevant ist oder nicht
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
     * @param string $wbd_thema WBD Thema des Veranstaltungstermins
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
     * @param string $ziel Ziele  des Veranstaltungstermins
     *
     * @return $this
     */
    public function setZiel($ziel)
    {
        $this->container['ziel'] = $ziel;

        return $this;
    }

    /**
     * Gets zielgruppen
     *
     * @return \Swagger\Client\Model\Zielgruppe[]
     */
    public function getZielgruppen()
    {
        return $this->container['zielgruppen'];
    }

    /**
     * Sets zielgruppen
     *
     * @param \Swagger\Client\Model\Zielgruppe[] $zielgruppen Die Zielgruppen für den Veranstaltungstermin.
     *
     * @return $this
     */
    public function setZielgruppen($zielgruppen)
    {
        $this->container['zielgruppen'] = $zielgruppen;

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


