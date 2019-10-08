<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OutgoingLetter;

class RemarksController extends Controller
{

    public function store(OutgoingLetter $outgoingletter)
    {
        $outgoingletter->addRemark(request()->validate([
            'description'=>'required|min:10|max:255|string'
        ]));
        
        return back();
    }

    public function update(Remark $remark)
    {
        $remark->update(request()->validate([
            'description'=>'required|min:10|max:255|string' 
        ]));

        return back();
    }

    public function delete(Remark $remark)
    {
        $remark->delete();
        
        return back();
    }
}
