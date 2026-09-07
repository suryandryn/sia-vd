<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE materis MODIFY file JSON NULL');
        }

        DB::table('materis')->whereNotNull('file')->orderBy('id')->chunkById(200, function ($rows): void {
            foreach ($rows as $row) {
                $value = $row->file;

                if ($value === null || $value === '') {
                    continue;
                }

                $decoded = json_decode((string) $value, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    continue;
                }

                DB::table('materis')->where('id', $row->id)->update(['file' => json_encode([(string) $value])]);
            }
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE materis MODIFY file VARCHAR(255) NULL');
        }
    }
};
