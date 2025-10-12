<?php

namespace Database\Seeders;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintLogs;
use App\Models\Housing;
use App\Models\HousingUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $housings = Housing::select('id')->get();
        foreach ($housings as $housing) {
            $housingId = $housing->id;
            $housingUsers = HousingUser::where('housing_id', $housingId)->whereNotNull('user_id')->get();
            foreach ($housingUsers as $housingUser) {
                $randomCategory = ComplaintCategory::inRandomOrder()->first();
                $complaint = new Complaint();
                $complaint->housing_id = $housingId;
                $complaint->user_id = $housingUser->user_id;
                $complaint->category_code = $randomCategory->code;
                $complaint->status_code = 'new';
                $complaint->title = 'Pengaduan ' . $randomCategory->name . ' ' . fake()->name();
                $complaint->description = fake()->sentence(50);
                $complaint->submitted_at = now()->subDays(rand(0, 30));
                $complaint->updated_by = $housingUser->user_id;
                $complaint->save();

                $logComplaint = new ComplaintLogs();
                $logComplaint->complaint_id = $complaint->id;
                $logComplaint->logged_by = $housingUser->user_id;
                $logComplaint->logged_at = $complaint->submitted_at;
                $logComplaint->status_code = $complaint->status_code;
                $logComplaint->note = 'Pengaduan baru';
                $logComplaint->save();

            }
        }
    }
}
