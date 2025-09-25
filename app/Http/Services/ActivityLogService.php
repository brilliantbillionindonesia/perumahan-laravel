<?php

namespace App\Http\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public static function logModel(
        string $model,
        string $rowId,
        array $json,
        string $type = 'create',
        array $meta = []
    ): void {

        $allowed = ['create', 'update', 'delete', 'restore'];
        if (! in_array($type, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid type: {$type}. Must be one of: " . implode(', ', $allowed));
        }

        ActivityLog::create([
            'table' => $model,
            'row_id' => $rowId,
            'type' => $type,
            'json' => json_encode(array_merge($json, $meta)),
            'logged_by' => auth()->user()->id,
            'logged_at' => now(),
        ]);
    }
}
