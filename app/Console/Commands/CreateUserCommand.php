<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Position;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultIndex = 1;
        $user = [];

        $user['first_name'] = $this->ask('First Name of the new user');
        $user['last_name'] = $this->ask('Last Name of the new user');
        $user['email'] = $this->ask('Email of the new user');
        $user['password'] = $this->secret('Password of the new user');
        $user['phone'] = $this->ask('Phone number of the new user');
        $user['employee_no'] = $this->ask('Employee number of the new user');
        $user['join_at'] = $this->ask('Join date of the new user (YYYY-MM-DD)');
        $user['username'] = 'Asj#' . $user['employee_no'];


        // validate user input
        $validator = Validator::make($user, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
            'employee_no' => ['required', 'string', 'max:50', 'unique:users'],
            'join_at' => ['required', 'date'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return -1;
        }

        $postionName = $this->choice(
            'Position of the new User',
            Position::pluck('name')->toArray(),
            $defaultIndex,
        );

        $roleName = $this->choice(
            'Role of the new User',
            Role::pluck('name')->toArray(),
            $defaultIndex,
        );

        $departmentNames = $this->choice(
            'Departments of the new User (select multiple with comma)',
            Department::pluck('name')->toArray(),
            $defaultIndex,
            $maxAttempts = null,
            $allowMultipleSelections = true
        );

        $departments = [];

        foreach ($departmentNames as $name) {
            $department = Department::where('name', $name)->first();
            if (! $department) {
                $this->error("Department $name not found.");
                return -1;
            }
            $departments[] = $department;
        }

        $position = Position::where('name', $postionName)->first();
        $role = Role::where('name', $roleName)->first();

        if (!$position || !$role) {
            $this->error('Position or role not found.');
            return -1;
        }

        DB::transaction(function () use ($user, $position, $role, $departments) {
            $user['password'] = Hash::make($user['password']);
            $user['username'] = 'Asj#' . $user['employee_no'];

            $newUser = User::create($user);
            $newUser->position()->associate($position);
            $newUser->role()->associate($role);
            $newUser->save();

            foreach ($departments as $dept) {
                $newUser->departments()->attach($dept);
            }
        });
    }
}
