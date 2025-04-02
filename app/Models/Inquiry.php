<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'number',
        'first_name',
        'email',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function createInquiryNumber($id) {
        $inquiryCount = 0;
        $latestInquiry = Inquiry::select('id')->whereRaw('DATE(created_at) = DATE("'.date('Y-m-d H:i:s').'")')->limit(1)->first();
        $inquiryCount = ((int) $id - $latestInquiry->id) + 1;
        $newId = sprintf("%04s", $inquiryCount);
        $inquiryNumber = '02'.date('ymd').$newId;
        return $this->update(['number' => $inquiryNumber]);
    }
}
