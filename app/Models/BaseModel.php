<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class BaseModel extends Model
{
    use HasFactory, LogsActivity;


    // Definir los eventos que deseas que se registren (create, update, delete, etc.)
    public static $logAttributes = ['*']; // Registrar todos los atributos
    public static $logName = 'default'; // Nombre del log por defecto
    public static $logFillable = true;
    public static $logUnguarded = true;

    // También puedes personalizar si deseas registrar los eventos create, update, delete, etc.
    public static $logOnlyDirty = true; // Solo registrar los cambios cuando el atributo cambie
    public static $submitEmptyLogs = false; // No registrar si no hay cambios

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}")->logAll()->logExcept(['created_at', 'updated_at', 'guard_name']);
    }
}
