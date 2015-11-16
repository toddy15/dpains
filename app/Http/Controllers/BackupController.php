<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Employee;
use App\Episode;
use App\Rawplan;
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

        // Write header in first line
        fputs($handle, "# Backup data for dpains\n");

        // Export tables
        $this->exportTable($handle, 'staffgroups', Staffgroup::all());
        $this->exportTable($handle, 'comments', Comment::all());
        $this->exportTable($handle, 'employees', Employee::all());
        $this->exportTable($handle, 'episodes', Episode::all());
        $this->exportTable($handle, 'rawplans', Rawplan::all());

        fclose($handle);
    }

    /**
     * Helper method to expert all data from a table
     */
    private function exportTable($handle, $table, $entries)
    {
        fputs($handle, "#\n# Table: $table\n");
        $show_fields = true;
        foreach ($entries as $entry) {
            // Show fields upon first iteration
            if ($show_fields) {
                $fields = array_keys($entry->getAttributes());
                fputs($handle, "# Fields: " . implode(', ', $fields) . "\n#\n");
                $show_fields = false;
            }
            fputcsv($handle, $entry->toArray());
        }
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

        // Minimal safety net before erasing all tables
        $first_line = trim(fgets($handle));
        if ($first_line != "# Backup data for dpains") {
            $request->session()->flash('danger', 'Die Backup-Datei hat kein gültiges Format.');
            return redirect(action('BackupController@index'));
        }

        // Delete tables
        DB::table('staffgroups')->truncate();
        DB::table('comments')->truncate();
        DB::table('employees')->truncate();
        DB::table('episodes')->truncate();
        DB::table('rawplans')->truncate();

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
