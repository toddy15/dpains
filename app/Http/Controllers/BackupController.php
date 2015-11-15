<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Employee;
use App\Episode;
use App\Staffgroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BackupController extends Controller
{
    /**
     * Export all relevant items into a backup file
     */
    public function download()
    {
        // Enable direct download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=dpains-backup.csv');

        $handle = fopen('php://output', 'w');

        // Export staffgroups
        fputs($handle, "#\n# Staffgroups\n#\n");
        $entries = Staffgroup::all();
        foreach ($entries as $entry) {
            fputcsv($handle, $entry->toArray());
        }

        // Export comments
        fputs($handle, "#\n# Comments\n#\n");
        $entries = Comment::all();
        foreach ($entries as $entry) {
            fputcsv($handle, $entry->toArray());
        }

        // Export employees
        fputs($handle, "#\n# Employees\n#\n");
        $entries = Employee::all();
        foreach ($entries as $entry) {
            fputcsv($handle, $entry->toArray());
        }

        // Export episodes
        fputs($handle, "#\n# Episodes\n#\n");
        $entries = Episode::all();
        foreach ($entries as $entry) {
            fputcsv($handle, $entry->toArray());
        }

        fclose($handle);
    }

    public function restore()
    {
    }
}
