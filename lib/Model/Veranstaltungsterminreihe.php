<?php
/**
 * Veranstaltungsterminreihe
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
 * Veranstaltungsterminreihe Class Doc Comment
 *
 * @category Class
 * @description Eine Veranstaltungsterminreihe mit ihren relevanten Daten
 * @package  Swagger\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class Veranstaltungsterminreihe implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'Veranstaltungsterminreihe';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        'oid' => 'string',
        'anzahl_freier_plaetze' => 'int',
        'anzahl_freier_wartelistenplaetze' => 'int',
        'anzahl_teilnehmerbuchungen' => 'int',
        'anzahl_wartelistenbuchungen' => 'int',
        'auslastungsstatus' => 'string',
        'beschreibung' => 'string',
        'geschlossen' => 'bool',
        'hinweise' => 'string',
        'inhalt' => 'string',
        'kategorien' => '\Swagger\Client\Model\KategorieUndUnterkategorieApiDto[]',
        'links' => '\Swagger\Client\Model\Link[]',
        'methodik' => 'string',
        'preis' => 'float',
        'preise_je_teilnehmergruppe' => '\Swagger\Client\Model\TeilnehmergruppePreis[]',
        'sachbearbeiter' => '\Swagger\Client\Model\Sachbearbeiter',
        'schlagwoerter' => '\Swagger\Client\Model\Schlagwort[]',
        'sprache' => 'string',
        'standardzahlungsbedingung' => '\Swagger\Client\Model\Zahlungsbedingung',
        'teilnehmermaximum' => 'int',
        'teilnehmerminimum' => 'int',
        'termin_bis' => '\DateTime',
        'termin_von' => '\DateTime',
        'termine' => '\Swagger\Client\Model\VeranstaltungsterminDerReiheApiDto[]',
        'thema' => 'string',
        'thema2' => 'string',
        'veranstaltungs_nr' => 'string',
        'veranstaltungsanbieter' => '\Swagger\Client\Model\Veranstaltungsanbieter',
        'veranstaltungsform' => '\Swagger\Client\Model\Veranstaltungsform',
        'veranstaltungskategorie' => '\Swagger\Client\Model\Veranstaltungskategorie',
        'veranstaltungsort' => '\Swagger\Client\Model\Veranstaltungsort',
        'veranstaltungsterminreihen_nr' => 'string',
        'veranstaltungstyp_id' => 'string',
        'veranstaltungsunterkategorie' => '\Swagger\Client\Model\Veranstaltungsunterkategorie',
        'voraussetzung' => 'string',
        'wartelistenmaximum' => 'int',
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
        'anzahl_freier_plaetze' => null,
        'anzahl_freier_wartelistenplaetze' => null,
        'anzahl_teilnehmerbuchungen' => null,
        'anzahl_wartelistenbuchungen' => null,
        'auslastungsstatus' => null,
        'beschreibung' => null,
        'geschlossen' => null,
        'hinweise' => null,
        'inhalt' => null,
        'kategorien' => null,
        'links' => null,
        'methodik' => null,
        'preis' => null,
        'preise_je_teilnehmergruppe' => null,
        'sachbearbeiter' => null,
        'schlagwoerter' => null,
        'sprache' => null,
        'standardzahlungsbedingung' => null,
        'teilnehmermaximum' => null,
        'teilnehmerminimum' => null,
        'termin_bis' => 'date-time',
        'termin_von' => 'date-time',
        'termine' => null,
        'thema' => null,
        'thema2' => null,
        'veranstaltungs_nr' => null,
        'veranstaltungsanbieter' => null,
        'veranstaltungsform' => null,
        'veranstaltungskategorie' => null,
        'veranstaltungsort' => null,
        'veranstaltungsterminreihen_nr' => null,
        'veranstaltungstyp_id' => null,
        'veranstaltungsunterkategorie' => null,
        'voraussetzung' => null,
        'wartelistenmaximum' => null,
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
        'anzahl_freier_plaetze' => 'anzahlFreierPlaetze',
        'anzahl_freier_wartelistenplaetze' => 'anzahlFreierWartelistenplaetze',
        'anzahl_teilnehmerbuchungen' => 'anzahlTeilnehmerbuchungen',
        'anzahl_wartelistenbuchungen' => 'anzahlWartelistenbuchungen',
        'auslastungsstatus' => 'auslastungsstatus',
        'beschreibung' => 'beschreibung',
        'geschlossen' => 'geschlossen',
        'hinweise' => 'hinweise',
        'inhalt' => 'inhalt',
        'kategorien' => 'kategorien',
        'links' => 'links',
        'methodik' => 'methodik',
        'preis' => 'preis',
        'preise_je_teilnehmergruppe' => 'preiseJeTeilnehmergruppe',
        'sachbearbeiter' => 'sachbearbeiter',
        'schlagwoerter' => 'schlagwoerter',
        'sprache' => 'sprache',
        'standardzahlungsbedingung' => 'standardzahlungsbedingung',
        'teilnehmermaximum' => 'teilnehmermaximum',
        'teilnehmerminimum' => 'teilnehmerminimum',
        'termin_bis' => 'terminBis',
        'termin_von' => 'terminVon',
        'termine' => 'termine',
        'thema' => 'thema',
        'thema2' => 'thema2',
        'veranstaltungs_nr' => 'veranstaltungsNr',
        'veranstaltungsanbieter' => 'veranstaltungsanbieter',
        'veranstaltungsform' => 'veranstaltungsform',
        'veranstaltungskategorie' => 'veranstaltungskategorie',
        'veranstaltungsort' => 'veranstaltungsort',
        'veranstaltungsterminreihen_nr' => 'veranstaltungsterminreihenNr',
        'veranstaltungstyp_id' => 'veranstaltungstypID',
        'veranstaltungsunterkategorie' => 'veranstaltungsunterkategorie',
        'voraussetzung' => 'voraussetzung',
        'wartelistenmaximum' => 'wartelistenmaximum',
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
        'anzahl_freier_plaetze' => 'setAnzahlFreierPlaetze',
        'anzahl_freier_wartelistenplaetze' => 'setAnzahlFreierWartelistenplaetze',
        'anzahl_teilnehmerbuchungen' => 'setAnzahlTeilnehmerbuchungen',
        'anzahl_wartelistenbuchungen' => 'setAnzahlWartelistenbuchungen',
        'auslastungsstatus' => 'setAuslastungsstatus',
        'beschreibung' => 'setBeschreibung',
        'geschlossen' => 'setGeschlossen',
        'hinweise' => 'setHinweise',
        'inhalt' => 'setInhalt',
        'kategorien' => 'setKategorien',
        'links' => 'setLinks',
        'methodik' => 'setMethodik',
        'preis' => 'setPreis',
        'preise_je_teilnehmergruppe' => 'setPreiseJeTeilnehmergruppe',
        'sachbearbeiter' => 'setSachbearbeiter',
        'schlagwoerter' => 'setSchlagwoerter',
        'sprache' => 'setSprache',
        'standardzahlungsbedingung' => 'setStandardzahlungsbedingung',
        'teilnehmermaximum' => 'setTeilnehmermaximum',
        'teilnehmerminimum' => 'setTeilnehmerminimum',
        'termin_bis' => 'setTerminBis',
        'termin_von' => 'setTerminVon',
        'termine' => 'setTermine',
        'thema' => 'setThema',
        'thema2' => 'setThema2',
        'veranstaltungs_nr' => 'setVeranstaltungsNr',
        'veranstaltungsanbieter' => 'setVeranstaltungsanbieter',
        'veranstaltungsform' => 'setVeranstaltungsform',
        'veranstaltungskategorie' => 'setVeranstaltungskategorie',
        'veranstaltungsort' => 'setVeranstaltungsort',
        'veranstaltungsterminreihen_nr' => 'setVeranstaltungsterminreihenNr',
        'veranstaltungstyp_id' => 'setVeranstaltungstypId',
        'veranstaltungsunterkategorie' => 'setVeranstaltungsunterkategorie',
        'voraussetzung' => 'setVoraussetzung',
        'wartelistenmaximum' => 'setWartelistenmaximum',
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
        'anzahl_freier_plaetze' => 'getAnzahlFreierPlaetze',
        'anzahl_freier_wartelistenplaetze' => 'getAnzahlFreierWartelistenplaetze',
        'anzahl_teilnehmerbuchungen' => 'getAnzahlTeilnehmerbuchungen',
        'anzahl_wartelistenbuchungen' => 'getAnzahlWartelistenbuchungen',
        'auslastungsstatus' => 'getAuslastungsstatus',
        'beschreibung' => 'getBeschreibung',
        'geschlossen' => 'getGeschlossen',
        'hinweise' => 'getHinweise',
        'inhalt' => 'getInhalt',
        'kategorien' => 'getKategorien',
        'links' => 'getLinks',
        'methodik' => 'getMethodik',
        'preis' => 'getPreis',
        'preise_je_teilnehmergruppe' => 'getPreiseJeTeilnehmergruppe',
        'sachbearbeiter' => 'getSachbearbeiter',
        'schlagwoerter' => 'getSchlagwoerter',
        'sprache' => 'getSprache',
        'standardzahlungsbedingung' => 'getStandardzahlungsbedingung',
        'teilnehmermaximum' => 'getTeilnehmermaximum',
        'teilnehmerminimum' => 'getTeilnehmerminimum',
        'termin_bis' => 'getTerminBis',
        'termin_von' => 'getTerminVon',
        'termine' => 'getTermine',
        'thema' => 'getThema',
        'thema2' => 'getThema2',
        'veranstaltungs_nr' => 'getVeranstaltungsNr',
        'veranstaltungsanbieter' => 'getVeranstaltungsanbieter',
        'veranstaltungsform' => 'getVeranstaltungsform',
        'veranstaltungskategorie' => 'getVeranstaltungskategorie',
        'veranstaltungsort' => 'getVeranstaltungsort',
        'veranstaltungsterminreihen_nr' => 'getVeranstaltungsterminreihenNr',
        'veranstaltungstyp_id' => 'getVeranstaltungstypId',
        'veranstaltungsunterkategorie' => 'getVeranstaltungsunterkategorie',
        'voraussetzung' => 'getVoraussetzung',
        'wartelistenmaximum' => 'getWartelistenmaximum',
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
        $this->container['anzahl_freier_plaetze'] = isset($data['anzahl_freier_plaetze']) ? $data['anzahl_freier_plaetze'] : null;
        $this->container['anzahl_freier_wartelistenplaetze'] = isset($data['anzahl_freier_wartelistenplaetze']) ? $data['anzahl_freier_wartelistenplaetze'] : null;
        $this->container['anzahl_teilnehmerbuchungen'] = isset($data['anzahl_teilnehmerbuchungen']) ? $data['anzahl_teilnehmerbuchungen'] : null;
        $this->container['anzahl_wartelistenbuchungen'] = isset($data['anzahl_wartelistenbuchungen']) ? $data['anzahl_wartelistenbuchungen'] : null;
        $this->container['auslastungsstatus'] = isset($data['auslastungsstatus']) ? $data['auslastungsstatus'] : null;
        $this->container['beschreibung'] = isset($data['beschreibung']) ? $data['beschreibung'] : null;
        $this->container['geschlossen'] = isset($data['geschlossen']) ? $data['geschlossen'] : null;
        $this->container['hinweise'] = isset($data['hinweise']) ? $data['hinweise'] : null;
        $this->container['inhalt'] = isset($data['inhalt']) ? $data['inhalt'] : null;
        $this->container['kategorien'] = isset($data['kategorien']) ? $data['kategorien'] : null;
        $this->container['links'] = isset($data['links']) ? $data['links'] : null;
        $this->container['methodik'] = isset($data['methodik']) ? $data['methodik'] : null;
        $this->container['preis'] = isset($data['preis']) ? $data['preis'] : null;
        $this->container['preise_je_teilnehmergruppe'] = isset($data['preise_je_teilnehmergruppe']) ? $data['preise_je_teilnehmergruppe'] : null;
        $this->container['sachbearbeiter'] = isset($data['sachbearbeiter']) ? $data['sachbearbeiter'] : null;
        $this->container['schlagwoerter'] = isset($data['schlagwoerter']) ? $data['schlagwoerter'] : null;
        $this->container['sprache'] = isset($data['sprache']) ? $data['sprache'] : null;
        $this->container['standardzahlungsbedingung'] = isset($data['standardzahlungsbedingung']) ? $data['standardzahlungsbedingung'] : null;
        $this->container['teilnehmermaximum'] = isset($data['teilnehmermaximum']) ? $data['teilnehmermaximum'] : null;
        $this->container['teilnehmerminimum'] = isset($data['teilnehmerminimum']) ? $data['teilnehmerminimum'] : null;
        $this->container['termin_bis'] = isset($data['termin_bis']) ? $data['termin_bis'] : null;
        $this->container['termin_von'] = isset($data['termin_von']) ? $data['termin_von'] : null;
        $this->container['termine'] = isset($data['termine']) ? $data['termine'] : null;
        $this->container['thema'] = isset($data['thema']) ? $data['thema'] : null;
        $this->container['thema2'] = isset($data['thema2']) ? $data['thema2'] : null;
        $this->container['veranstaltungs_nr'] = isset($data['veranstaltungs_nr']) ? $data['veranstaltungs_nr'] : null;
        $this->container['veranstaltungsanbieter'] = isset($data['veranstaltungsanbieter']) ? $data['veranstaltungsanbieter'] : null;
        $this->container['veranstaltungsform'] = isset($data['veranstaltungsform']) ? $data['veranstaltungsform'] : null;
        $this->container['veranstaltungskategorie'] = isset($data['veranstaltungskategorie']) ? $data['veranstaltungskategorie'] : null;
        $this->container['veranstaltungsort'] = isset($data['veranstaltungsort']) ? $data['veranstaltungsort'] : null;
        $this->container['veranstaltungsterminreihen_nr'] = isset($data['veranstaltungsterminreihen_nr']) ? $data['veranstaltungsterminreihen_nr'] : null;
        $this->container['veranstaltungstyp_id'] = isset($data['veranstaltungstyp_id']) ? $data['veranstaltungstyp_id'] : null;
        $this->container['veranstaltungsunterkategorie'] = isset($data['veranstaltungsunterkategorie']) ? $data['veranstaltungsunterkategorie'] : null;
        $this->container['voraussetzung'] = isset($data['voraussetzung']) ? $data['voraussetzung'] : null;
        $this->container['wartelistenmaximum'] = isset($data['wartelistenmaximum']) ? $data['wartelistenmaximum'] : null;
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
     * @param int $anzahl_freier_plaetze anzahl_freier_plaetze
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
     * @param int $anzahl_freier_wartelistenplaetze anzahl_freier_wartelistenplaetze
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
     * @param int $anzahl_teilnehmerbuchungen anzahl_teilnehmerbuchungen
     *
     * @return $this
     */
    public function setAnzahlTeilnehmerbuchungen($anzahl_teilnehmerbuchungen)
    {
        $this->container['anzahl_teilnehmerbuchungen'] = $anzahl_teilnehmerbuchungen;

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
     * @param int $anzahl_wartelistenbuchungen anzahl_wartelistenbuchungen
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
     * @param string $auslastungsstatus auslastungsstatus
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
     * @param string $beschreibung beschreibung
     *
     * @return $this
     */
    public function setBeschreibung($beschreibung)
    {
        $this->container['beschreibung'] = $beschreibung;

        return $this;
    }

    /**
     * Gets geschlossen
     *
     * @return bool
     */
    public function getGeschlossen()
    {
        return $this->container['geschlossen'];
    }

    /**
     * Sets geschlossen
     *
     * @param bool $geschlossen Gibt an, ob die Veranstaltungsterminreihe geschlossen ist.
     *
     * @return $this
     */
    public function setGeschlossen($geschlossen)
    {
        $this->container['geschlossen'] = $geschlossen;

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
     * @param string $hinweise hinweise
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
     * @param string $inhalt inhalt
     *
     * @return $this
     */
    public function setInhalt($inhalt)
    {
        $this->container['inhalt'] = $inhalt;

        return $this;
    }

    /**
     * Gets kategorien
     *
     * @return \Swagger\Client\Model\KategorieUndUnterkategorieApiDto[]
     */
    public function getKategorien()
    {
        return $this->container['kategorien'];
    }

    /**
     * Sets kategorien
     *
     * @param \Swagger\Client\Model\KategorieUndUnterkategorieApiDto[] $kategorien Die Liste der Kategorien, die der Veranstaltungsterminreihe zugeordnet sind.
     *
     * @return $this
     */
    public function setKategorien($kategorien)
    {
        $this->container['kategorien'] = $kategorien;

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
     * @param string $methodik methodik
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
     * @param float $preis Der Standardpreis der Veranstaltungsterminreihe.
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
     * @param \Swagger\Client\Model\TeilnehmergruppePreis[] $preise_je_teilnehmergruppe Die Preise je nach Teilnehmergruppe der Veranstaltungsterminreihe.
     *
     * @return $this
     */
    public function setPreiseJeTeilnehmergruppe($preise_je_teilnehmergruppe)
    {
        $this->container['preise_je_teilnehmergruppe'] = $preise_je_teilnehmergruppe;

        return $this;
    }

    /**
     * Gets sachbearbeiter
     *
     * @return \Swagger\Client\Model\Sachbearbeiter
     */
    public function getSachbearbeiter()
    {
        return $this->container['sachbearbeiter'];
    }

    /**
     * Sets sachbearbeiter
     *
     * @param \Swagger\Client\Model\Sachbearbeiter $sachbearbeiter Der Sachbearbeiter TN der Veranstaltungsterminreihe.
     *
     * @return $this
     */
    public function setSachbearbeiter($sachbearbeiter)
    {
        $this->container['sachbearbeiter'] = $sachbearbeiter;

        return $this;
    }

    /**
     * Gets schlagwoerter
     *
     * @return \Swagger\Client\Model\Schlagwort[]
     */
    public function getSchlagwoerter()
    {
        return $this->container['schlagwoerter'];
    }

    /**
     * Sets schlagwoerter
     *
     * @param \Swagger\Client\Model\Schlagwort[] $schlagwoerter Die Schlagwörter für die Veranstaltungsterminreihe.
     *
     * @return $this
     */
    public function setSchlagwoerter($schlagwoerter)
    {
        $this->container['schlagwoerter'] = $schlagwoerter;

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
     * @param string $sprache Die Sprache, in der die Veranstaltungsterminreihe durchgeführt wird.
     *
     * @return $this
     */
    public function setSprache($sprache)
    {
        $this->container['sprache'] = $sprache;

        return $this;
    }

    /**
     * Gets standardzahlungsbedingung
     *
     * @return \Swagger\Client\Model\Zahlungsbedingung
     */
    public function getStandardzahlungsbedingung()
    {
        return $this->container['standardzahlungsbedingung'];
    }

    /**
     * Sets standardzahlungsbedingung
     *
     * @param \Swagger\Client\Model\Zahlungsbedingung $standardzahlungsbedingung Die Standard-Zahlungsbedingungen des Veranstaltungstyps.
     *
     * @return $this
     */
    public function setStandardzahlungsbedingung($standardzahlungsbedingung)
    {
        $this->container['standardzahlungsbedingung'] = $standardzahlungsbedingung;

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
     * @param int $teilnehmermaximum teilnehmermaximum
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
     * @param int $teilnehmerminimum teilnehmerminimum
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
     * @param \DateTime $termin_bis termin_bis
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
     * @param \DateTime $termin_von termin_von
     *
     * @return $this
     */
    public function setTerminVon($termin_von)
    {
        $this->container['termin_von'] = $termin_von;

        return $this;
    }

    /**
     * Gets termine
     *
     * @return \Swagger\Client\Model\VeranstaltungsterminDerReiheApiDto[]
     */
    public function getTermine()
    {
        return $this->container['termine'];
    }

    /**
     * Sets termine
     *
     * @param \Swagger\Client\Model\VeranstaltungsterminDerReiheApiDto[] $termine Die einzelnen Termine der Reihe.
     *
     * @return $this
     */
    public function setTermine($termine)
    {
        $this->container['termine'] = $termine;

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
     * @param string $thema thema
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
     * @param string $thema2 thema2
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
     * @param string $veranstaltungs_nr Die Veranstaltungs-Nr aus dem Veranstaltungstypen.
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
     * @param \Swagger\Client\Model\Veranstaltungsanbieter $veranstaltungsanbieter Der Veranstaltungsanbieter der Veranstaltungsterminreihe.
     *
     * @return $this
     */
    public function setVeranstaltungsanbieter($veranstaltungsanbieter)
    {
        $this->container['veranstaltungsanbieter'] = $veranstaltungsanbieter;

        return $this;
    }

    /**
     * Gets veranstaltungsform
     *
     * @return \Swagger\Client\Model\Veranstaltungsform
     */
    public function getVeranstaltungsform()
    {
        return $this->container['veranstaltungsform'];
    }

    /**
     * Sets veranstaltungsform
     *
     * @param \Swagger\Client\Model\Veranstaltungsform $veranstaltungsform Die Veranstaltungsform der Veranstaltungsreihe.
     *
     * @return $this
     */
    public function setVeranstaltungsform($veranstaltungsform)
    {
        $this->container['veranstaltungsform'] = $veranstaltungsform;

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
     * @param \Swagger\Client\Model\Veranstaltungskategorie $veranstaltungskategorie Die Kategorie, der die Veranstaltungsterminreihe zugeordnet ist.
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
     * @param \Swagger\Client\Model\Veranstaltungsort $veranstaltungsort Der Veranstaltungsort der Veranstaltungsterminreihe.
     *
     * @return $this
     */
    public function setVeranstaltungsort($veranstaltungsort)
    {
        $this->container['veranstaltungsort'] = $veranstaltungsort;

        return $this;
    }

    /**
     * Gets veranstaltungsterminreihen_nr
     *
     * @return string
     */
    public function getVeranstaltungsterminreihenNr()
    {
        return $this->container['veranstaltungsterminreihen_nr'];
    }

    /**
     * Sets veranstaltungsterminreihen_nr
     *
     * @param string $veranstaltungsterminreihen_nr veranstaltungsterminreihen_nr
     *
     * @return $this
     */
    public function setVeranstaltungsterminreihenNr($veranstaltungsterminreihen_nr)
    {
        $this->container['veranstaltungsterminreihen_nr'] = $veranstaltungsterminreihen_nr;

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
     * @param string $veranstaltungstyp_id veranstaltungstyp_id
     *
     * @return $this
     */
    public function setVeranstaltungstypId($veranstaltungstyp_id)
    {
        $this->container['veranstaltungstyp_id'] = $veranstaltungstyp_id;

        return $this;
    }

    /**
     * Gets veranstaltungsunterkategorie
     *
     * @return \Swagger\Client\Model\Veranstaltungsunterkategorie
     */
    public function getVeranstaltungsunterkategorie()
    {
        return $this->container['veranstaltungsunterkategorie'];
    }

    /**
     * Sets veranstaltungsunterkategorie
     *
     * @param \Swagger\Client\Model\Veranstaltungsunterkategorie $veranstaltungsunterkategorie Die Unterkategorie, der die Veranstaltungsterminreihe zugeordnet ist.
     *
     * @return $this
     */
    public function setVeranstaltungsunterkategorie($veranstaltungsunterkategorie)
    {
        $this->container['veranstaltungsunterkategorie'] = $veranstaltungsunterkategorie;

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
     * @param string $voraussetzung voraussetzung
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
     * @param int $wartelistenmaximum wartelistenmaximum
     *
     * @return $this
     */
    public function setWartelistenmaximum($wartelistenmaximum)
    {
        $this->container['wartelistenmaximum'] = $wartelistenmaximum;

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
     * @param string $ziel ziel
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
     * @param \Swagger\Client\Model\Zielgruppe[] $zielgruppen Die Zielgruppen für die Veranstaltungsterminreihe.
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


