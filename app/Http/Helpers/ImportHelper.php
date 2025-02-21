<?php

namespace App\Http\Helpers;

class ImportHelper
{
    public static function getErrorText(\Maatwebsite\Excel\Validators\ValidationException $e)
    {
        $failures = $e->failures();
        $message = '';
        $last_row = null;
        $total_row = 0;
        $total_row_message = '';
        foreach ($failures as $failure) {
            if ($last_row != $failure->row()) {
                $total_row += 1;
                $message .= '<br/><br/>' . __('Row :number', ['number' => $failure->row()]) . ' :';
            }
            $message .= '<br/>- ' . implode('<br/>- ', $failure->errors());

            $last_row = $failure->row();
        }
        $total_row_message = __('There are invalid rows (:number rows)', ['number' => $total_row]);
        return $total_row_message . $message;
    }
}
