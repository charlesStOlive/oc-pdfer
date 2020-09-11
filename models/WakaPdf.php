<?php namespace Waka\Pdfer\Models;

use Model;

/**
 * WakaPdf Model
 */
class WakaPdf extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sortable;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'waka_pdfer_waka_pdfs';

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
    public $rules = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = ['scopes', 'model_functions', 'images'];

    /**
     * @var array Attributes to be appended to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

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
    public $hasMany = [];
    public $belongsTo = [
        'data_source' => ['Waka\Utils\Models\DataSource'],
        'pdf_layout' => ['Waka\Pdfer\Models\PdfLayout'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [
        'informs' => ['Waka\Informer\Models\Inform', 'name' => 'informeable'],
    ];
    public $attachOne = [];
    public $attachMany = [];

    public function getRapidLinksAttribute()
    {
        $layout_id = null;
        if ($this->pdf_layout) {
            $layout_id = $this->pdf_layout->id;
        }
        $links = [
            "CSS du PDF" => \Backend::url('waka/pdfer/pdflayouts/update/' . $layout_id),
        ];
        return $links;
    }
}
