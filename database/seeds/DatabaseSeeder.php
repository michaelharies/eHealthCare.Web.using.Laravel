<?php
/*
 * File name: DatabaseSeeder.php
 * Last modified: 2021.09.16 at 12:29:38
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(ModelHasPermissionsTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
        $this->call(CustomFieldsTableSeeder::class);
        $this->call(CustomFieldValuesTableSeeder::class);
        $this->call(AppSettingsTableSeeder::class);
        $this->call(SpecialitiesTableSeeder::class);
        $this->call(FaqCategoriesTableSeeder::class);
        $this->call(AppointmentStatusesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);

        $this->call(AddressesTableSeeder::class);
        $this->call(TaxesTableSeeder::class);
        $this->call(ClinicLevelsTableSeeder::class);
        $this->call(ClinicsTableSeeder::class);
        $this->call(PatientsTableSeeder::class);
        $this->call(DoctorsTableSeeder::class);
        $this->call(GalleriesTableSeeder::class);
        $this->call(DoctorReviewsTableSeeder::class);
        $this->call(ClinicReviewsTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(FaqsTableSeeder::class);
        $this->call(FavoritesTableSeeder::class);
        $this->call(AwardsTableSeeder::class);
        $this->call(AvailabilityHoursTableSeeder::class);
        $this->call(ExperiencesTableSeeder::class);
        $this->call(PaymentMethodsTableSeeder::class);
        $this->call(PaymentStatusesTableSeeder::class);

        $this->call(MediaTableSeeder::class);
        $this->call(UploadsTableSeeder::class);
        $this->call(ClinicsPayoutsTableSeeder::class);
        $this->call(DoctorSpecialitiesTableSeeder::class);
        $this->call(SlidesTableSeeder::class);
        $this->call(CustomPagesTableSeeder::class);
        $this->call(WalletsTableSeeder::class);
        $this->call(WalletTransactionsTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


    }
}
