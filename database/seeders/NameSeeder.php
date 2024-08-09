<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('name')->insert([
            ['name_id' => 1, 'first_name' => 'LOUISSE KAYE', 'middle_init' => 'P.', 'last_name' => 'ABA'],
            ['name_id' => 2, 'first_name' => 'Joanne Rose', 'middle_init' => 'O.', 'last_name' => 'ALVAREZ'],
            ['name_id' => 3, 'first_name' => 'Joy Christine', 'middle_init' => 'V.', 'last_name' => 'ASIS'],
            ['name_id' => 4, 'first_name' => 'Gerald', 'middle_init' => 'C.', 'last_name' => 'BACLAYON'],
            ['name_id' => 5, 'first_name' => 'Anshawer', 'middle_init' => 'D.', 'last_name' => 'BARA-ACAL'],
            ['name_id' => 6, 'first_name' => 'Brenz Ryan', 'middle_init' => 'B.', 'last_name' => 'BAUTISTA'],
            ['name_id' => 7, 'first_name' => 'Cyril', 'middle_init' => 'B.', 'last_name' => 'BICHARA'],
            ['name_id' => 8, 'first_name' => 'Lea Therese', 'middle_init' => 'N.', 'last_name' => 'BONDAD'],
            ['name_id' => 9, 'first_name' => 'Lemuel', 'middle_init' => 'C.', 'last_name' => 'CABAHIT'],
            ['name_id' => 10, 'first_name' => 'Almira Mae', 'middle_init' => 'C.', 'last_name' => 'CAINGLET'],
            ['name_id' => 11, 'first_name' => 'Lazaro IV', 'middle_init' => 'F.', 'last_name' => 'CAJEGAS'],
            ['name_id' => 12, 'first_name' => 'Gay', 'middle_init' => 'P.', 'last_name' => 'CALLANTA'],
            ['name_id' => 13, 'first_name' => 'Krissette Grace', 'middle_init' => 'F.', 'last_name' => 'CAMPILAN'],
            ['name_id' => 14, 'first_name' => 'Ai-Let', 'middle_init' => 'R.', 'last_name' => 'CASTRO'],
            ['name_id' => 15, 'first_name' => 'Liberty', 'middle_init' => 'B.', 'last_name' => 'DAITIA'],
            ['name_id' => 16, 'first_name' => 'Mary Jossel', 'middle_init' => 'H.', 'last_name' => 'DISPO'],
            ['name_id' => 17, 'first_name' => 'Elvert', 'middle_init' => 'L.', 'last_name' => 'ELUDO'],
            ['name_id' => 18, 'first_name' => 'Dominic', 'middle_init' => 'O.', 'last_name' => 'ESCOBAL'],
            ['name_id' => 19, 'first_name' => 'Daryl', 'middle_init' => 'D.', 'last_name' => 'FACIOL'],
            ['name_id' => 20, 'first_name' => 'Rodante', 'middle_init' => 'B.', 'last_name' => 'FELINA'],
            ['name_id' => 21, 'first_name' => 'Janice', 'middle_init' => 'B.', 'last_name' => 'FUROG'],
            ['name_id' => 22, 'first_name' => 'Rey', 'middle_init' => 'E.', 'last_name' => 'GAMAYON'],
            ['name_id' => 23, 'first_name' => 'Dulce', 'middle_init' => 'A.', 'last_name' => 'GUALBERTO'],
            ['name_id' => 24, 'first_name' => 'Janeth Faye', 'middle_init' => 'Z.', 'last_name' => 'HADMAN'],
            ['name_id' => 25, 'first_name' => 'Jed Emerson', 'middle_init' => 'D.', 'last_name' => 'JAO-JAO'],
            ['name_id' => 26, 'first_name' => 'Jovelyn', 'middle_init' => 'M.', 'last_name' => 'JAYME'],
            ['name_id' => 27, 'first_name' => 'Herman', 'middle_init' => 'C.', 'last_name' => 'JUANICO'],
            ['name_id' => 28, 'first_name' => 'Sheena Mae', 'middle_init' => 'O.', 'last_name' => 'LANTACA'],
            ['name_id' => 29, 'first_name' => 'Ralph John', 'middle_init' => '', 'last_name' => 'LIGAS'],
            ['name_id' => 30, 'first_name' => 'Lariza Amor', 'middle_init' => 'R.', 'last_name' => 'LUCERO'],
            ['name_id' => 31, 'first_name' => 'Neil', 'middle_init' => 'P.', 'last_name' => 'MAATA'],
            ['name_id' => 32, 'first_name' => 'Jenisse Dowell', 'middle_init' => 'N.', 'last_name' => 'MEDEL'],
            ['name_id' => 33, 'first_name' => 'Rogin Aron', 'middle_init' => 'B.', 'last_name' => 'MONTALAN'],
            ['name_id' => 34, 'first_name' => 'Maria Raniela', 'middle_init' => 'P.', 'last_name' => 'ORTEZA'],
            ['name_id' => 35, 'first_name' => 'May Lara Bea', 'middle_init' => 'A.', 'last_name' => 'PAULIN'],
            ['name_id' => 36, 'first_name' => 'Gaudencio Jr.', 'middle_init' => 'L.', 'last_name' => 'PAULMA'],
            ['name_id' => 37, 'first_name' => 'Kayshe Joy', 'middle_init' => 'F.', 'last_name' => 'PELINGON'],
            ['name_id' => 38, 'first_name' => 'June Ray', 'middle_init' => 'P.', 'last_name' => 'PENASO'],
            ['name_id' => 39, 'first_name' => 'James', 'middle_init' => 'C.', 'last_name' => 'PERATER'],
            ['name_id' => 40, 'first_name' => 'Raymund', 'middle_init' => 'M.', 'last_name' => 'POSTRANO'],
            ['name_id' => 41, 'first_name' => 'RITZIELAINE ROSSE', 'middle_init' => 'V.', 'last_name' => 'SALES'],
            ['name_id' => 42, 'first_name' => 'Daphne Niccole', 'middle_init' => 'G.', 'last_name' => 'SEROJALES'],
            ['name_id' => 43, 'first_name' => 'Maruel', 'middle_init' => 'C.', 'last_name' => 'SILVERIO'],
            ['name_id' => 44, 'first_name' => 'Jay', 'middle_init' => 'A.', 'last_name' => 'SIMEON'],
            ['name_id' => 45, 'first_name' => 'Osin Jr.', 'middle_init' => 'A.', 'last_name' => 'SINSUAT'],
            ['name_id' => 46, 'first_name' => 'Gladys', 'middle_init' => 'P.', 'last_name' => 'TUPAZ'],
            ['name_id' => 47, 'first_name' => 'VIRGINIA', 'middle_init' => 'R.', 'last_name' => 'VERDEJO'],
            ['name_id' => 48, 'first_name' => 'ALVIN', 'middle_init' => 'M.', 'last_name' => 'VILLANUEVA'],
            ['name_id' => 49, 'first_name' => 'Ruel', 'middle_init' => 'A.', 'last_name' => 'ABRIGONDO'],
            ['name_id' => 50, 'first_name' => 'Joriel Jan', 'middle_init' => 'M.', 'last_name' => 'AGOD'],
            ['name_id' => 51, 'first_name' => 'Jepee', 'middle_init' => 'L.', 'last_name' => 'ALAUD'],
            ['name_id' => 52, 'first_name' => 'Jay Ralph', 'middle_init' => 'E.', 'last_name' => 'ALVARINA'],
            ['name_id' => 53, 'first_name' => 'Niño Anthony', 'middle_init' => 'A.', 'last_name' => 'ASINO'],
            ['name_id' => 54, 'first_name' => 'Jowyn', 'middle_init' => 'O.', 'last_name' => 'AWITAN'],
            ['name_id' => 55, 'first_name' => 'Coleen', 'middle_init' => 'M.', 'last_name' => 'BALAIS'],
            ['name_id' => 56, 'first_name' => 'Reiner', 'middle_init' => 'P.', 'last_name' => 'BALDOMERO'],
            ['name_id' => 57, 'first_name' => 'Edgar', 'middle_init' => 'G.', 'last_name' => 'BINLOT'],
            ['name_id' => 58, 'first_name' => 'Jeaneth Ann', 'middle_init' => 'C.', 'last_name' => 'BONAVENTE'],
            ['name_id' => 59, 'first_name' => 'Earl Ven Kirby', 'middle_init' => 'M.', 'last_name' => 'BUDIONGAN'],
            ['name_id' => 60, 'first_name' => 'Ayanni Diane', 'middle_init' => 'C.', 'last_name' => 'CALE'],
            ['name_id' => 61, 'first_name' => 'Hannah Bennett Althea', 'middle_init' => '', 'last_name' => 'CANUBIDA'],
            ['name_id' => 62, 'first_name' => 'Rubelen', 'middle_init' => 'M.', 'last_name' => 'DADULA'],
            ['name_id' => 63, 'first_name' => 'Elaiza Ruth', 'middle_init' => 'D.', 'last_name' => 'DIAMOS'],
            ['name_id' => 64, 'first_name' => 'Mary Ashley', 'middle_init' => 'S.', 'last_name' => 'GADRINDAB'],
            ['name_id' => 65, 'first_name' => 'Lowie', 'middle_init' => 'M.', 'last_name' => 'GONZALES'],
            ['name_id' => 66, 'first_name' => 'Joven Clar', 'middle_init' => 'Z.', 'last_name' => 'GRANADA'],
            ['name_id' => 67, 'first_name' => 'Lou Marie France', 'middle_init' => 'G.', 'last_name' => 'LADAO'],
            ['name_id' => 68, 'first_name' => 'Marlyn', 'middle_init' => 'B.', 'last_name' => 'MACALE'],
            ['name_id' => 69, 'first_name' => 'Marcelo', 'middle_init' => 'M.', 'last_name' => 'MACALE'],
            ['name_id' => 70, 'first_name' => 'Muhammad', 'middle_init' => 'D.', 'last_name' => 'MAMACOTAO'],
            ['name_id' => 71, 'first_name' => 'Reginald', 'middle_init' => 'L.', 'last_name' => 'MEJIO'],
            ['name_id' => 72, 'first_name' => 'Maetrita', 'middle_init' => 'B.', 'last_name' => 'MIRAL'],
            ['name_id' => 73, 'first_name' => 'Reynald Jay', 'middle_init' => 'P.', 'last_name' => 'PELLEJERA'],
            ['name_id' => 74, 'first_name' => 'Joanna Rhee', 'middle_init' => 'S.', 'last_name' => 'REYES'],
            ['name_id' => 75, 'first_name' => 'Fred', 'middle_init' => 'P.', 'last_name' => 'TARAN'],
            ['name_id' => 76, 'first_name' => 'Admin', 'middle_init' => 'Super', 'last_name' => 'User'],
        ]);
    }
}
