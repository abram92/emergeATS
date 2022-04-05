<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;

class LongFullText extends Model implements Auditable
{
    //
	
	use \OwenIt\Auditing\Auditable;

    /**
     * Attributes to include in the Audit.
     *
     * @var array
     */
    protected $auditInclude = [
        'field_type', 'editor_id', 'chunk'
    ];
	
	public $fillable = ['field_type', 'editor_id', 'chunk', 'search_transl', 'chunk_tokens'];
	
	public function getLongText(string $field_type) {
		return $this->morphTo('long_text')->where('field_type', '=', $field_type);
	}
	
	public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id')->withTrashed();
    }
	
	public function longtextable() {
		return $this->morphTo();
	}
}
