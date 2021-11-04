<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class GenerateTestingData extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:test-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate test data for the API.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        User::query()->delete();
        Book::query()->delete();
        Category::query()->delete();

        $user = User::factory()->hasBooks(1)->create([
            'name' => 'Jorge',
            'email' => 'jorge@aprendible.com'
        ]);

        $books = Book::factory()->count(14)->create();

        $this->info('User UUID:');
        $this->line($user->id);

        $this->info('Token:');
        $this->line($user->createToken('Jorge')->plainTextToken);

        $this->info('Book ID:');
        $this->line($user->books->first()->slug);

        $this->info('Category ID:');
        $this->line($books->first()->category->slug);
    }
}
