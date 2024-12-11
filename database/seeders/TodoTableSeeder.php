<?php

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        Todo::create([
            'title' => 'Read a book',
            'details' => 'I want to read the book called Dairies of Fairytales',
            'status' => '0'
        ]);

        Todo::create([
            'title' => 'Write blog post',
            'details' => 'I need to write a blog post about Laravel seeding.',
            'status' => '1'
        ]);

        Todo::create([
            'title' => 'Go for a run',
            'details' => 'Running is a great way to stay healthy.',
            'status' => '0'
        ]);

        Todo::create([
            'title' => 'Learn Laravel',
            'details' => 'I need to get better at Laravel by building more projects.',
            'status' => '1'
        ]);

        Todo::create([
            'title' => 'Complete assignment',
            'details' => 'I need to finish the software engineering assignment.',
            'status' => '0'
        ]);

        Todo::create([
            'title' => 'Clean the house',
            'details' => 'I should clean the kitchen and living room.',
            'status' => '2'
        ]);

        Todo::create([
            'title' => 'Watch a movie',
            'details' => 'Relaxing with a good movie is always fun.',
            'status' => '2'
        ]);

        Todo::create([
            'title' => 'Visit a friend',
            'details' => 'I haven\'t seen my friend in a while. Let\'s meet up.',
            'status' => '0'
        ]);

        Todo::create([
            'title' => 'Buy groceries',
            'details' => 'Need to buy milk, eggs, bread, and fruits.',
            'status' => '1'
        ]);

        Todo::create([
            'title' => 'Attend conference presentation meeting',
            'details' => 'There\'s an important meeting at the conference tomorrow presenting on AI.',
            'status' => '0'
        ]);
    }
}
