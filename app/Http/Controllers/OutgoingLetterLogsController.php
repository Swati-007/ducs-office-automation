<?php

namespace App\Http\Controllers;

use Auth;
use App\OutgoingLetterLog;
use App\User;
use Illuminate\Http\Request;
use DB;

class OutgoingLetterLogsController extends Controller
{
    public function index(Request $request)
    {
        $after_date = $request->after ?? OutgoingLetterLog::min('date');
        $before_date = $request->before ?? OutgoingLetterLog::max('date');
        $outgoing_letter_logs = OutgoingLetterLog::whereBetween('date',[$after_date,$before_date])->get();

        return view('outgoing_letter_logs.index', compact('outgoing_letter_logs'));
    }

    public function create()
    {
        return view('outgoing_letter_logs.create');
    }
    
    protected function store(Request $request)
    {
        $validData = $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'type' => 'required',
            'recipient' => 'required',
            'sender_id' => 'required|exists:users,id',
            'description' => 'nullable|string|max:400',
            'amount' => 'nullable|numeric',
        ]);
        
        OutgoingLetterLog::create($validData);
        
        return redirect('/outgoing-letter-logs');
    }

    public function edit(OutgoingLetterLog $outgoing_letter)
    {
        return view('outgoing_letter_logs.edit', compact('outgoing_letter'));
    }

    public function update(OutgoingLetterLog $outgoing_letter, Request $request)
    {
        $validData = $request->validate([
            'date' => 'sometimes|required|date|before_or_equal:today',
            'type' => 'sometimes|required',
            'recipient' =>  'sometimes|required|',
            'description' => 'nullable|string|max:400',
            'amount' => 'nullable|numeric',
            'sender_id' => 'sometimes|required|exists:users,id'
        ]);

        $outgoing_letter->update($validData);
        
        return redirect('/outgoing-letter-logs');
    }

    public function destroy(OutgoingLetterLog $outgoing_letter) 
    {
        $outgoing_letter->delete();
        
        return redirect('/outgoing-letter-logs');
    }
}
