<?php
// app/Models/PsyQuestion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PsyQuestion extends Model
{
    protected $fillable = ['test_id','prompt','trait_key','qtype','ordering'];

    public function test(): BelongsTo {
        return $this->belongsTo(PsyTest::class,'test_id');
    }

    public function options(): HasMany {
        // likert/mcq options
        return $this->hasMany(PsyOption::class,'question_id')->orderBy('ordering')->orderBy('id');
    }

    // helper: apakah pakai opsi?
    public function hasOptions(): bool {
        return $this->options()->exists();
    }
}
