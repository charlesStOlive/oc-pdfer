<?php namespace Waka\Pdfer\Models;

use Model;

/**
 * wakaPdf Model
 */

class WakaPdf extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    use \Winter\Storm\Database\Traits\SoftDelete;
    use \Winter\Storm\Database\Traits\Sortable;
    use \Waka\Utils\Classes\Traits\DataSourceHelpers;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_pdfer_waka_pdfs';


    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id'];

    /**
     * @var array Fillable fields
     */
    //protected $fillable = [];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:waka_mailer_waka_mails',
        'pdf_name' => 'required',
        'data_source' => 'required',
    ];

    public $customMessages = [
        'test_id.required' => 'waka.pdfer::wakapdf.e.test_id',
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
        'model_functions',
        'images',
        'scopes',
        'asks',
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
        'deleted_at',
    ];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
    ];
    public $hasOneThrough = [
    ];
    public $hasManyThrough = [
    ];
    public $belongsTo = [
       'layout' => [
            'Waka\Pdfer\Models\Layout'
        ],
    ];
    public $belongsToMany = [
    ];        
    public $morphTo = [];
    public $morphOne = [
    ];
    public $morphMany = [
    ];
    public $attachOne = [
    ];
    public $attachMany = [
    ];

    //startKeep/

    /**
     *EVENTS
     **/

    /**
     * LISTS
     **/

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
    public function filterFields($fields, $context = null) {
        $user = \BackendAuth::getUser();
        //La limite du  nombre de asks est géré dans le controller.
        if(!$user->hasAccess(['waka.pdfer.admin.super'])) {
            if(isset($fields->code)) {
                    $fields->code->readOnly = true;
            }
            if(isset($fields->has_asks)) {
                    $fields->has_asks->readOnly = true;
            }
        }
    }

    /**
     * OTHERS
     */

//endKeep/
}
