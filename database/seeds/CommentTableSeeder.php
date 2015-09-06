<?php

use Illuminate\Database\Seeder;

class CommentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comments = [
            'Attest',
            'Betriebsrat',
            'Elternzeit',
            'Mutterschutz',
            'Rotation Harburg',
            'Rotation Intensiv',
            'Schmerztherapie',
            'Vertragsende',
            'Einarbeitung',
            'Reanimationsregister',
        ];
        foreach ($comments as $comment) {
            DB::table('comments')->insert([
                'comment' => $comment,
            ]);
        }
    }
}
