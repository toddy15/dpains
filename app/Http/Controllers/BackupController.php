<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Employee;
use App\Episode;
use App\Staffgroup;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BackupController extends Controller
{
    /**
     * Overview of backup functionality
     */
    public function index()
    {
        return view('backups.index');
    }

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
        fputs($handle, "#\n# Table: staffgroups\n");
        $show_fields = true;
        $entries = Staffgroup::all();
        foreach ($entries as $entry) {
            // Show fields upon first iteration
            if ($show_fields) {
                $fields = array_keys($entry->getAttributes());
                fputs($handle, "# Fields: " . implode(', ', $fields) . "\n#\n");
                $show_fields = false;
            }
            fputcsv($handle, $entry->toArray());
        }

        // Export comments
        fputs($handle, "#\n# Table: comments\n");
        $show_fields = true;
        $entries = Comment::all();
        foreach ($entries as $entry) {
            // Show fields upon first iteration
            if ($show_fields) {
                $fields = array_keys($entry->getAttributes());
                fputs($handle, "# Fields: " . implode(', ', $fields) . "\n#\n");
                $show_fields = false;
            }
            fputcsv($handle, $entry->toArray());
        }

        // Export employees
        fputs($handle, "#\n# Table: employees\n");
        $show_fields = true;
        $entries = Employee::all();
        foreach ($entries as $entry) {
            // Show fields upon first iteration
            if ($show_fields) {
                $fields = array_keys($entry->getAttributes());
                fputs($handle, "# Fields: " . implode(', ', $fields) . "\n#\n");
                $show_fields = false;
            }
            fputcsv($handle, $entry->toArray());
        }

        // Export episodes
        fputs($handle, "#\n# Table: episodes\n");
        $show_fields = true;
        $entries = Episode::all();
        foreach ($entries as $entry) {
            // Show fields upon first iteration
            if ($show_fields) {
                $fields = array_keys($entry->getAttributes());
                fputs($handle, "# Fields: " . implode(', ', $fields) . "\n#\n");
                $show_fields = false;
            }
            fputcsv($handle, $entry->toArray());
        }

        fclose($handle);
    }

    /**
     * Restore database from file backup
     */
    public function restore(Request $request)
    {
        if (!$request->hasFile('backup') or !$request->file('backup')->isValid()) {
            $request->session()->flash('danger', 'Fehler beim Hochladen des Backups.');
            return redirect(action('BackupController@index'));
        }

        // Try to open the file
        $handle = fopen($request->file('backup'), 'r');
        if (!$handle) {
            $request->session()->flash('danger', 'Die Backup-Datei konnte nicht geöffnet werden.');
            return redirect(action('BackupController@index'));
        }

        // Delete tables
        DB::table('staffgroups')->truncate();
        DB::table('comments')->truncate();
        DB::table('employees')->truncate();
        DB::table('episodes')->truncate();

        // Read in the file line by line
        $table = '';
        $fields = '';
        while (($line = fgets($handle)) !== false) {
            // Determine current table and fields
            if (starts_with($line, '#')) {
                if (strlen($line) > 2) {
                    $info = explode(':', trim(substr($line, 2)));
                    switch ($info[0]) {
                        case 'Table':
                            $table = trim($info[1]);
                            break;
                        case 'Fields':
                            $fields = explode(', ', trim($info[1]));
                            break;
                    }
                }
                continue;
            }
            $entry = str_getcsv($line);
            $db_entry = array_combine($fields, $entry);
            DB::table($table)->insert($db_entry);
        }
        fclose($handle);

        $request->session()->flash('info', 'Das Backup wurde eingespielt.');
        return redirect(action('BackupController@index'));
    }
}
