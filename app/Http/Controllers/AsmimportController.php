<?php
    

namespace App\Http\Controllers;

use App\Imports\AsmprogramImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class AsmimportController extends Controller
{
    public function import(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:2048', // Added max size limit
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $file = $request->file('file');
        // phpinfo();

        // exit;
        // Import the file
        Excel::import(new AsmprogramImport, $file);
        // dd('File imported successfully.');
        return redirect()->back()->with('success', 'File imported successfully.');
    }
}