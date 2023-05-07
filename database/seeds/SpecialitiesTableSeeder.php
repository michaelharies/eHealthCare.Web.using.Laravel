<?php
/*
 * File name: SpecialitiesTableSeeder.php
 * Last modified: 2021.03.02 at 14:35:42
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

use Illuminate\Database\Seeder;

class SpecialitiesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        DB::table('specialities')->delete();

        DB::table('specialities')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'name' => 'Allergists',
                    'color' => '#ff9f43',
                    'description' => '<p>Diagnosis and treatment of allergic and immunologic ailments, manage and prevent immune system disorders such as autoimmune diseases and immunodeficiency diseases.</p>',
                    'order' => 1,
                    'featured' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2021-01-19 17:01:35',
                    'updated_at' => '2021-01-31 14:19:56',
                ),
            1 =>
                array(
                    'id' => 2,
                    'name' => 'Oncologists',
                    'color' => '#0abde3',
                    'description' => '<p>All types of neoplasms, including benign, potentially malignant, malignant (cancer) and canerous growths. This includes leukaemia and mesothelioma.<br></p>',
                    'order' => 2,
                    'featured' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2021-01-19 18:05:00',
                    'updated_at' => '2021-01-31 13:35:11',
                ),
            2 =>
                array(
                    'id' => 3,
                    'name' => 'Ophthalmologists',
                    'color' => '#ee5253',
                    'description' => '<p>Diseases of the eye and normal eye development and function.</p>',
                    'order' => 3,
                    'featured' => 1,
                    'parent_id' => NULL,
                    'created_at' => '2021-01-31 13:37:04',
                    'updated_at' => '2021-02-02 00:33:10',
                ),
            3 =>
                array(
                    'id' => 4,
                    'name' => 'Neurologists',
                    'color' => '#10ac84',
                    'description' => '<p>Dementias, transmissible spongiform encephalopathies, Parkinson’s disease, neurodegenerative diseases, Alzheimer’s disease, epilepsy, multiple sclerosis, and studies of the normal brain and nervous system.</p>',
                    'order' => 4,
                    'featured' => 0,
                    'parent_id' => NULL,
                    'created_at' => '2021-01-31 13:38:37',
                    'updated_at' => '2021-02-23 14:37:09',
                ),
            4 =>
                array(
                    'id' => 5,
                    'name' => 'Hematologists',
                    'color' => '#5f27cd',
                    'description' => '<p>Haematological diseases, anaemia, clotting (including thromboses and venous embolisms) and normal development and function of platelets and erythrocytes</p>',
                    'order' => 5,
                    'featured' => 0,
                    'parent_id' => NULL,
                    'created_at' => '2021-01-31 13:42:02',
                    'updated_at' => '2021-01-31 13:42:02',
                ),
            5 =>
                array(
                    'id' => 6,
                    'name' => 'Dental Surgeons',
                    'color' => '#e3875f',
                    'description' => '<p>Prevention, and treatment of diseases and conditions of the oral cavity. The dentists supporting team aids in providing oral health services.</p>',
                    'order' => 6,
                    'featured' => 0,
                    'parent_id' => NULL,
                    'created_at' => '2021-01-31 13:43:20',
                    'updated_at' => '2021-01-31 14:55:51',
                ),
            6 =>
                array(
                    'id' => 7,
                    'name' => 'Cardiovascular',
                    'color' => '#6ae35f',
                    'description' => '<p>Coronary heart disease, diseases of the vasculature and circulation including the lymphatic system, and normal development and function of the cardiovascular system.</p>',
                    'order' => 1,
                    'featured' => 0,
                    'parent_id' => 5,
                    'created_at' => '2021-01-31 14:46:15',
                    'updated_at' => '2021-01-31 14:46:30',
                ),
            7 =>
                array(
                    'id' => 8,
                    'name' => 'Otolaryngologist',
                    'color' => '#545c5c',
                    'description' => '<p>Management of diseases and disorders of the ear, nose, throat, and related bodily structures.<br></p>',
                    'order' => 2,
                    'featured' => 0,
                    'parent_id' => 5,
                    'created_at' => '2021-01-31 14:47:23',
                    'updated_at' => '2021-01-31 14:47:23',
                ),
            8 =>
                array(
                    'id' => 9,
                    'name' => 'Veterinarian',
                    'color' => '#82bbf5',
                    'description' => '<p>Care for the health of animals and work to improve public health. They diagnose, treat, and research medical conditions and diseases of pets, livestock, and other animals.<br></p>',
                    'order' => 1,
                    'featured' => 0,
                    'parent_id' => 1,
                    'created_at' => '2021-01-31 14:49:40',
                    'updated_at' => '2021-01-31 14:49:40',
                ),
        ));


    }
}
