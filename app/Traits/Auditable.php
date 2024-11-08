<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::audit('audit:created', $model);
        });

        static::updated(function (MOdel $model) {
            self::audit('audit:updated', $model);
        });

        static::deleted(function (Model $model) {
            self::audit('audit:deleted', $model);
        });
    }

    public static function audit($description, $model, $changes = [])
    {
        AuditLog::create([
            'description' => $description,
            'subject_id' => $model->id,
            'subject_type' => sprintf('%s#%s', get_class($model), $model->id) ?? null,
            'user_id' => auth()->id() ?? null,
            'properties' => count($changes) > 0 ? json_encode($changes) : $model,

        ]);
    }
}
