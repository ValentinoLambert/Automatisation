<?php

namespace App\Console;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Faker\Factory;
use Illuminate\Support\Facades\Schema;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDatabaseCommand extends Command
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database');
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int
    {
        $output->writeln('Populate database...');

        /** @var \Illuminate\Database\Capsule\Manager $db */
        $db = $this->app->getContainer()->get('db');

        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $db->getConnection()->statement("TRUNCATE `employees`");
        $db->getConnection()->statement("TRUNCATE `offices`");
        $db->getConnection()->statement("TRUNCATE `companies`");
        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");

        $faker = Factory::create('fr_FR');

        $companies = [];
        for ($i = 0; $i < rand(2, 4); $i++) {
            $company = new Company();
            $company->name = $faker->company();
            $company->phone = $faker->phoneNumber();
            $company->email = $faker->companyEmail();
            $company->website = $faker->url();
            $company->image = $faker->imageUrl();
            $company->save();
            $companies[] = $company;
        }

        foreach ($companies as $company) {
            $offices = [];
            for ($j = 0; $j < rand(2, 3); $j++) {
                $office = new Office();
                $office->name = $faker->city() . ' - ' . $faker->word();
                $office->address = $faker->streetAddress();
                $office->city = $faker->city();
                $office->zip_code = $faker->postcode();
                $office->country = $faker->country();
                $office->email = $faker->email();
                $office->phone = $faker->phoneNumber();
                $office->company_id = $company->id;
                $office->save();
                $offices[] = $office;
            }

            $company->head_office_id = $offices[0]->id;
            $company->save();

            for ($k = 0; $k < rand(8, 12); $k++) {
                $employee = new Employee();
                $employee->first_name = $faker->firstName();
                $employee->last_name = $faker->lastName();
                $employee->email = $faker->email();
                $employee->phone = $faker->phoneNumber();
                $employee->job_title = $faker->jobTitle();
                $employee->office_id = $offices[array_rand($offices)]->id;
                $employee->save();
            }
        }

        $output->writeln('Database populated successfully!');
        return 0;
    }
}
