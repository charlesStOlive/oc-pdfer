<?php namespace Waka\Pdfer\Models;

use Model;

/**
 * layout Model
 */

class Layout extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    use \Winter\Storm\Database\Traits\Sortable;
    use \Waka\Utils\Classes\Traits\DataSourceHelpers;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_pdfer_layouts';


    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'name' => 'required',
        'contenu' => 'required',
        'baseCss' => 'required',
    ];

    public $customMessages = [
    ];

    /**
     * @var array attributes send to datasource for creating document
     */
    public $attributesToDs = [
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [
        'options',
    ];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [
    ];

    /**
     * @var array Attributes to be removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array Attributes to be cast to Argon (Carbon) instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
    ];
    public $hasOneThrough = [];
    public $hasManyThrough = [
    ];
    public $belongsTo = [
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [
    ];
    public $morphMany = [
    ];
    public $attachOne = [
    ];
    public $attachMany = [
    ];

    /**
     *EVENTS
     **/

    /**
     * LISTS
     **/
    public function listWconfigLayout()
    {
        return \Config::get('wcli.wconfig::assets.css.pdf');
    }
    public function listBlocs() {
        return Bloc::lists('name', 'id');
    }

    /**
     * GETTERS
     **/

    /**
     * SCOPES
     */

    /**
     * SETTERS
     */
 
    /**
     * FILTER FIELDS
     */

    /**
     * OTHERS
     */
    
}