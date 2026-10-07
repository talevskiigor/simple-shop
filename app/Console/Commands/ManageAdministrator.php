<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ManageAdministrator extends Command
{
    protected $signature = 'admin:manage {email} {--name=Administrator} {--promote-existing : Explicitly authorize an existing account without changing its password}';
    protected $description = 'Create an administrator, or explicitly promote an existing account';

    public function handle(): int
    {
        $email = strtolower($this->argument('email'));
        Validator::make(['email' => $email], ['email' => 'required|email|max:255'])->validate();
        $user = User::where('email', $email)->first();
        if ($user) {
            if (!$this->option('promote-existing')) {
                $this->error('Account exists. Use --promote-existing to authorize it explicitly.');
                return self::FAILURE;
            }
            $user->forceFill(['is_admin' => true])->save();
        } else {
            $password = $this->secret('New administrator password (at least 12 characters)');
            Validator::make(['password' => $password], ['password' => ['required', Password::min(12)]])->validate();
            User::create(['name' => $this->option('name'), 'email' => $email, 'password' => $password, 'is_admin' => true]);
        }
        $this->info('Administrator access configured.');
        return self::SUCCESS;
    }
}
