<?php

namespace App\Http\Controllers\Backend\Parse;

use App\Http\Controllers\Controller;
use App\Models\Avrs\Avr;
use App\Models\Avrs\AvrEquipment;
use App\Models\Avrs\AvrExecutor;
use App\Models\Avrs\AvrWork;
use App\Models\Axes\Axe;
use App\Models\Brands\Brand;
use App\Models\Buildings\Building;
use App\Models\Checklists\ChecklistAirDiffuser;
use App\Models\Checklists\ChecklistAirDuct;
use App\Models\Checklists\ChecklistAirExtract;
use App\Models\Checklists\ChecklistAirSupply;
use App\Models\Checklists\ChecklistBalk;
use App\Models\Counters\CounterCount;
use App\Models\Counters\CounterType;
use App\Models\Counters\Tariff;
use App\Models\Counters\TrkRoomCounter;
use App\Models\DocCommunications\DocCommunication;
use App\Models\EquipmentParameters\EquipmentParameter;
use App\Models\Equipments\EquipmentName;
use App\Models\EquipmentSpareParts\EquipmentSparePart;
use App\Models\EquipmentStatuses\EquipmentStatus;
use App\Models\EquipmentUsers\EquipmentUser;
use App\Models\EquipmentWorkPeriods\EquipmentWorkPeriod;
use App\Models\Executables\Executable;
use App\Models\Floors\Floor;
use App\Models\OperationApplications\OperationApplication;
use App\Models\Orders\Order;
use App\Models\Orders\OrderSparePart;
use App\Models\Orders\OrderStatus;
use App\Models\Organizations\Organization;
use App\Models\ParameterNames\ParameterName;
use App\Models\RenterTrkRoomBrands\RenterTrkRoomBrand;
use App\Models\RoomPurposes\RoomPurpose;
use App\Models\Rooms\Room;
use App\Models\SpareParts\SparePartName;
use App\Models\StoreHouses\StoreHouseName;
use App\Models\Systems\System;
use App\Models\Towns\Town;
use App\Models\TrkEquipments\TrkEquipment;
use App\Models\TrkRoomClimates\TrkRoomClimate;
use App\Models\TrkRoomRepairs\TrkRoomRepair;
use App\Models\TrkRooms\TrkRoom;
use App\Models\Trks\Trk;
use App\Models\TrkStoreHouses\TrkStoreHouse;
use App\Models\User;
use App\Models\UserDivisions\UserDivision;
use App\Models\WorkNames\WorkName;
use App\Services\Parse\GetDataFromStringService;
use App\Services\Parse\PunctuationMarkService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new User([
            'name'     => $row[0],
            'email'    => $row[1],
            'password' => Hash::make($row[2]),
        ]);
    }
}

class ParseController extends Controller
{
    public const OLD_BASE_FOLDER = 'old_base/';
    public const NEW_BASE_FOLDER = 'new_base/';
    public const SEEDER_TEXT_FILE_FOLDER = 'text_files/';
    public const AVR_TEXT_FILES_FOLDER = 'avr/';

    public const USER_SEEDER_TEXT_FILE_NAME = 'user.php';

    public const ROOM_SEEDER_TEXT_FILE_NAME = 'trk_room.php';
    public const DAILY_CHECKING_ROOM_SEEDER_TEXT_FILE_NAME = 'trk_room_daily_check.php';
    public const ROOM_RENTER_SEEDER_TEXT_FILE_NAME = 'trk_room_renter.php';
    public const TRK_ROOM_RENTERS_SEEDER_TEXT_FILE_NAME = 'trk_room_renters_from_hvac.php';

    public const EQUIPMENT_SEEDER_TEXT_FILE_NAME = 'trk_room_equipment.php';

    public const SPARE_PART_STORAGE_SEEDER_TEXT_FILE_NAME = 'spare_part_storage.php';

    public const REPAIR_SEEDER_TEXT_FILE_NAME = 'repair.php';

    public const SPARE_PART_ORDER_SEEDER_TEXT_FILE_NAME = 'spare_part_order.php';

    public const AVR_TEXT_FILE = 'avr';
    public const AVR_SEEDER_SQL_FILE = 'avrs';
    public const AVR_WORKS_SEEDER_SQL_FILE = 'avr_works';
    public const AVR_EQUIPMENTS_SEEDER_SQL_FILE = 'avr_equipments';
    public const AVR_USERS_SEEDER_SQL_FILE = 'avr_users';
    public const SQL_FILE_EXT = '.sql';
    public const PHP_FILE_EXT = '.php';

    public const COUNTERS_TEXT_FILE_NAME = 'counter.php';
    public const COUNTER_COUNTS_TEXT_FILE_NAME = 'counter_count.php';

    public const CHECKLIST_SUPPLY_AIR_SEEDER_TEXT_FILE_NAME = 'checklist_supply_air.php';
    public const CHECKLIST_EXTRACT_AIR_SEEDER_TEXT_FILE_NAME = 'checklist_extract_air.php';
    public const CHECKLIST_ROOM_CLIMATE_SEEDER_TEXT_FILE_NAME = 'checklist_room_climate.php';

    public const CONDITION_CHECKLIST_SEEDER_TEXT_FILE_NAME = 'condition_checklist.php';
    public const BALK = 'Балка';
    public const FANCOIL = 'Фанкойл';
    public const CONDITIONER = 'Кондиционер';
    public const EXTRACT_DIFFUSER = 'Вытяжка на диффузорах';
    public const EXTRACT_CHANNEL = 'Вытяжка в канале';
    public const SUPPLY_DIFFUSER = 'Приток на диффузорах';
    public const SUPPLY_CHANNEL = 'Приток в канале';

    public const EQUIPMENT_USERS_SEEDER_TEXT_FILE_NAME = 'equipment_user.php';

    public const EQUIPMENT_BEARINGS_SEEDER_TEXT_FILE_NAME = 'equipment_bearing.php';
    public const EQUIPMENT_AIR_FILTERS_SEEDER_TEXT_FILE_NAME = 'equipment_air_filter.php';
    public const EQUIPMENT_DRIVEBELTS_SEEDER_TEXT_FILE_NAME = 'equipment_drivebelt.php';
    public const EQUIPMENT_ELECTRO_MOTORS_SEEDER_TEXT_FILE_NAME = 'equipment_electro_motors.php';
    public const EQUIPMENT_PULLIES_SEEDER_TEXT_FILE_NAME = 'equipment_pulley.php';
    public const EQUIPMENT_TAPERBASHES_SEEDER_TEXT_FILE_NAME = 'equipment_taperbash.php';
    public const EQUIPMENT_PUMPS_SEEDER_TEXT_FILE_NAME = 'equipment_pump.php';
    public const EQUIPMENT_VALVE_ACTUATORS_SEEDER_TEXT_FILE_NAME = 'equipment_valve_actuator.php';

    public const EQUIPMENT_RATES_SEEDER_TEXT_FILE_NAME = 'equipment_rate.php';
    public const SUPPLY_AIR_RATE_PASSPORT = 'Расход воздуха на притоке паспортный';
    public const SUPPLY_AIR_RATE_FACT = 'Расход воздуха на притоке фактический';
    public const EXTRACT_AIR_RATE_PASSPORT = 'Расход воздуха на вытяжке паспортный';
    public const EXTRACT_AIR_RATE_FACT = 'Расход воздуха на вытяжке фактический';
    public const HOT_WATER_RATE_PASSPORT = 'Расход ГВС паспортный';
    public const HOT_WATER_RATE_FACT = 'Расход ГВС фактический';
    public const COLD_WATER_RATE_PASSPORT = 'Расход ХВС паспортный';
    public const COLD_WATER_RATE_FACT = 'Расход ХВС фактический';

    public const OLD_OLD_BASE_FOLDER = 'old_old_base/';
    public const CLOSED_APPLICATION_TEXT_FILES_FOLDER = 'closed_applications/';
    public const CLOSED_APPLICATION_TEXT_FILE = 'closed_application';

    public const NEW_BASE_CLOSED_APPLICATIONS_FOLDER = 'new_base/closed_applications/';

    public const CLOSED_APPLICATIONS_SEEDER_SQL_FILE = 'closed_applications';
    public const CLOSED_APPLICATIONS_WORKS_SEEDER_SQL_FILE = 'closed_application_works';
    public const CLOSED_APPLICATIONS_EQUIPMENTS_SEEDER_SQL_FILE = 'closed_application_equipments';
    public const CLOSED_APPLICATIONS_USERS_SEEDER_SQL_FILE = 'closed_application_users';

    public const APPLICATION_TEXT_FILE_NAME = 'applications.php';

    public function __construct
    (
        GetDataFromStringService $getDataFromStringService,
        PunctuationMarkService   $punctuationMarkService
    )
    {
        $this->middleware(['auth', 'verified', 'profiled', 'not_blocked']);
        $this->getDataFromStringService = $getDataFromStringService;
        $this->punctuationMarkService = $punctuationMarkService;
    }

    public function index(): Response
    {
        return response()->view('backend.parse.index', []
        );
    }

    public function create(): RedirectResponse
    {
        return redirect()->back()->with('success', 'Парсинг закончен');
    }

    protected function create_old_tables_in_new_base()
    {
        try {

            $old_base_folder = ParseController::OLD_BASE_FOLDER;

            File::delete($old_base_folder . 'u331692824_xvo3_database.sql');
            File::delete($old_base_folder . 'u331692824_xvo3_extra.sql');
            File::delete($old_base_folder . 'u331692824_xvo3_table_engine_type.sql');
            File::delete($old_base_folder . 'u331692824_xvo3_table_migration.sql');

            $files = array_diff(scandir($old_base_folder), array('.', '..'));

            foreach ($files as $file) {

                $table_full_name = str_replace('u331692824_xvo3', 'old', $file);

                $table_name = substr($table_full_name, 0, -4);

                Schema::dropIfExists($table_name);

                $file_content = file_get_contents($old_base_folder . $file);

                $new_file_content = Str::replace('CREATE TABLE `', 'CREATE TABLE `old_', $file_content);

                $new_file_content = Str::replace('INSERT INTO `', 'INSERT INTO `old_', $new_file_content);

                $new_file_content = Str::replace("'0000-00-00'", 'NULL', $new_file_content);

                $new_file_content = Str::replace("date NOT NULL", 'date', $new_file_content);

                DB::unprepared($new_file_content);
            }

            return redirect()->back()->with('success', 'Старые таблицы с префиксом old_ созданы');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    protected function create_old_old_tables_in_new_base()
    {
        try {

            $old_old_base_folder = ParseController::OLD_OLD_BASE_FOLDER;

            $files = array_diff(scandir($old_old_base_folder), array('.', '..'));

            foreach ($files as $file) {
                $table_name = substr($file, 0, -4);

                Schema::dropIfExists($table_name);

                DB::unprepared(file_get_contents($old_old_base_folder . $file));
            }

            return redirect()->back()->with('success', 'Старые таблицы с префиксом old_old_ созданы');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    protected function parse_avrs_from_sql_file_to_new_base(string $skip, string $take)
    {
        try {

            DB::unprepared(file_get_contents(ParseController::NEW_BASE_FOLDER . ParseController::AVR_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT));
            DB::unprepared(file_get_contents(ParseController::NEW_BASE_FOLDER . ParseController::AVR_WORKS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT));
            DB::unprepared(file_get_contents(ParseController::NEW_BASE_FOLDER . ParseController::AVR_EQUIPMENTS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT));
            DB::unprepared(file_get_contents(ParseController::NEW_BASE_FOLDER . ParseController::AVR_USERS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT));

            $to = $skip + $take;

            return redirect()->back()->with('success', 'Акты выполненных работ ' . $skip . '-' . $to . ' из sql файла записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    protected function parse_closed_applications_from_sql_file_to_new_base(string $skip, string $take)
    {
        try {

            DB::unprepared(file_get_contents(ParseController::NEW_BASE_CLOSED_APPLICATIONS_FOLDER . ParseController::CLOSED_APPLICATIONS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT));
            DB::unprepared(file_get_contents(ParseController::NEW_BASE_CLOSED_APPLICATIONS_FOLDER . ParseController::CLOSED_APPLICATIONS_WORKS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT));
            DB::unprepared(file_get_contents(ParseController::NEW_BASE_CLOSED_APPLICATIONS_FOLDER . ParseController::CLOSED_APPLICATIONS_EQUIPMENTS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT));
            DB::unprepared(file_get_contents(ParseController::NEW_BASE_CLOSED_APPLICATIONS_FOLDER . ParseController::CLOSED_APPLICATIONS_USERS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT));

            return redirect()->back()->with('success', 'Акты выполненных работ ' . $skip . '-' . $take . ' из sql файла записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    // parse from old tables to text files
    protected function parse_spare_part_storages_from_old_base_to_text_file()
    {
        try {
            $spare_part_storages = DB::table('old_storage')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($spare_part_storages as $spare_part_storage) {
                $trk = DB::table('old_trk')->where('id', $spare_part_storage->trk_id)->pluck('name')->first();
                $storage_name = DB::table('old_storage_name')->where('id', $spare_part_storage->storage_name_id)->pluck('name')->first();
                $spare_part_name = DB::table('old_spare_part')->where('id', $spare_part_storage->spare_part_id)->pluck('name')->first();
                $spare_part_model = DB::table('old_spare_part_model')->where('id', $spare_part_storage->spare_part_model_id)->pluck('name')->first();
                $spare_part_condition = DB::table('old_spare_part_condition')->where('id', $spare_part_storage->spare_part_condition_id)->pluck('name')->first();
                $storage_operation = DB::table('old_storage_operation')->where('id', $spare_part_storage->storage_operation_id)->pluck('name')->first();
                $amount = $spare_part_storage->amount;
                $unit_measure = DB::table('old_unit_measure')->where('id', $spare_part_storage->unit_measure_id)->pluck('name')->first();
                $balance = $spare_part_storage->balance;
                $required_minimum = $spare_part_storage->requed_minimum;
                $require_order_amount = $spare_part_storage->requed_order_amount;
                $contractor = DB::table('old_contractor')->where('id', $spare_part_storage->contractor_id)->pluck('name')->first();
                $comment = $spare_part_storage->comment;

                $data_string .= "[ 'trk' => '$trk',";
                $data_string .= " 'storage_name' => '$storage_name',";
                $data_string .= " 'spare_part_name' => '$spare_part_name',";
                $data_string .= " 'spare_part_model' => '$spare_part_model',";
                $data_string .= " 'spare_part_condition' => '$spare_part_condition',";
                $data_string .= " 'storage_operation' => '$storage_operation',";
                $data_string .= " 'amount' => '$amount',";
                $data_string .= " 'unit_measure' => '$unit_measure',";
                $data_string .= " 'balance' => '$balance',";
                $data_string .= " 'required_minimum' => '$required_minimum',";
                $data_string .= " 'require_order_amount' => '$require_order_amount',";
                $data_string .= " 'contractor' => '$contractor',";
                $data_string .= " 'comment' => '$comment',";
                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $spare_part_storage_seeder_text_file_name = ParseController::SPARE_PART_STORAGE_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $spare_part_storage_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Склады в текстовый файл сохранены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось сохранить склады в текстовый файл');

        }
    }

    protected function parse_counters_from_old_base_to_text_file()
    {

        try {
            $counters = DB::table('old_counter')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($counters as $counter) {

                $trk = DB::table('old_trk')->where('id', $counter->trk_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $counter->floor_id)->pluck('name')->first();
                $brand = DB::table('old_renter_brand')->where('id', $counter->renter_brand_id)->pluck('name')->first();
                $tariff = DB::table('old_counter_tariff')->where('id', $counter->counter_tariff_id)->pluck('name')->first();
                $counter_type = DB::table('old_counter_type')->where('id', $counter->counter_type_id)->pluck('name')->first();
                $user = DB::table('old_user')->where('id', $counter->user_id)->pluck('surname')->first();

                if ($brand == 'O\'stin') $brand = 'O`stin';

                $data_string .= "[ 'trk' => '$trk',";
                $data_string .= " 'old_id' => '$counter->id',";
                $data_string .= " 'floor' => '$floor',";
                $data_string .= " 'brand' => '$brand',";
                $data_string .= " 'organization' => '$counter->legal_entity',";
                $data_string .= " 'using_purposes' => '$counter->purpose',";
                $data_string .= " 'installation_date' => '$counter->installation_date',";
                $data_string .= " 'date' => '$counter->date',";
                $data_string .= " 'type' => '$counter_type',";
                $data_string .= " 'coefficient' => '$counter->transformation_ratio',";
                $data_string .= " 'counter_number' => '$counter->number',";
                $data_string .= " 'tariff' => '$tariff',";
                $data_string .= " 'comment' => '$counter->installation_date',";
                $data_string .= " 'user' => '$user',";
                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $counter_seeder_text_file_name = ParseController::COUNTERS_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $counter_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Счетчики в текстовый файл сохранены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось сохранить счетчики в текстовый файл');

        }
    }

    protected function parse_counter_counts_from_old_base_to_text_file()
    {

        try {
            $counter_counts = DB::table('old_counter_rate')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($counter_counts as $counter_count) {

                $trk = DB::table('old_trk')->where('id', $counter_count->trk_id)->pluck('name')->first();
                $brand = DB::table('old_renter_brand')->where('id', $counter_count->renter_brand_id)->pluck('name')->first();
                $user = DB::table('old_user')->where('id', $counter_count->user_id)->pluck('surname')->first();
                $tariff = DB::table('old_counter_tariff')->where('id', $counter_count->counter_tariff_id)->pluck('name')->first();
                $counter_type = DB::table('old_counter_type')->where('id', $counter_count->counter_type_id)->pluck('name')->first();

                if ($brand == 'O\'stin') $brand = 'O`stin';

                $data_string .= "[ 'trk' => '$trk',";
                $data_string .= " 'counter_id' => '$counter_count->counter_id',";
                $data_string .= " 'brand' => '$brand',";
                $data_string .= " 'counter_tariff' => '$tariff',";
                $data_string .= " 'counter_type' => '$counter_type',";
                $data_string .= " 'day_rate' => '$counter_count->day_rate',";
                $data_string .= " 'period_begin_rate' => '$counter_count->period_begin_rate',";
                $data_string .= " 'period_finish_rate' => '$counter_count->period_finish_rate',";
                $data_string .= " 'transformation_ratio' => '$counter_count->transformation_ratio',";
                $data_string .= " 'result' => '$counter_count->result',";
                $data_string .= " 'comment' => '$counter_count->comment',";
                $data_string .= " 'date' => '$counter_count->date',";
                $data_string .= " 'user' => '$user',";
                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $counter_count_seeder_text_file_name = ParseController::COUNTER_COUNTS_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $counter_count_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Показания счетчиков в текстовый файл сохранены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось сохранить показания счетчиков в текстовый файл');

        }
    }

    protected function parse_avrs_from_old_base_to_text_file(string $skip, string $take)
    {
        try {

            ini_set('max_execution_time', 540);

            $avrs = DB::table('old_avr')->skip($skip)->take($take)->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($avrs as $avr) {
                $trk = DB::table('old_trk')->where('id', $avr->trk_id)->pluck('name')->first();
                $building = DB::table('old_building')->where('id', $avr->building_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $avr->floor_id)->pluck('name')->first();

                $room = DB::table('old_room')->where('id', $avr->room_id)->pluck('name')->first();

                $system = DB::table('old_system')->where('id', $avr->system_id)->pluck('name')->first();
                $division = DB::table('old_contractor')->where('id', $avr->contractor_id)->pluck('name')->first();
                $user = DB::table('old_user')->where('id', $avr->user_id)->pluck('surname')->first();

                $description = $avr->description;
                $remark = $avr->remark;
                $recommendation = $avr->recommendation;

                while (Str::contains($room, "'")) {
                    $room = Str::replace("'", '`', $room);
                }

                while (Str::contains($description, "'")) {
                    $description = Str::replace("'", '`', $description);
                }

                while (Str::contains($remark, "'")) {
                    $remark = Str::replace("'", '`', $remark);
                }

                while (Str::contains($recommendation, "'")) {
                    $recommendation = Str::replace("'", '`', $recommendation);
                }

                if (Str::contains($trk, "Золотой Вавилон (Ясенево)")) {
                    $trk = 'FORT Ясенево';
                }

                if (
                    $trk == 'Европолис (Лесная)'
                    || $building == 'Неизвестно'
                    || $building == 'Все блоки/зоны'
                ) {
                    $building = 'Блок 1';
                }

                if ($building == 'ПН1(Корпус 1)') {
                    $building = 'ПН1 (корпус 1)';
                }

                if ($building == 'ПН1(Корпус 2)') {
                    $building = 'ПН1 (корпус 2)';
                }

                $data_string .= "[ 'date' => '$avr->date',";
                $data_string .= " 'id' => '$avr->id',";
                $data_string .= " 'real_date' => '$avr->real_date',";
                $data_string .= " 'trk' => '$trk',";
                $data_string .= " 'building' => '$building',";
                $data_string .= " 'floor' => '$floor',";
                $data_string .= " 'room' => '$room',";
                $data_string .= " 'system' => '$system',";
                $data_string .= " 'description' => '$description',";
                $data_string .= " 'remark' => '$remark',";
                $data_string .= " 'recommendation' => '$recommendation',";
                $data_string .= " 'division' => '$division',";
                $data_string .= " 'user' => '$user'";
                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::AVR_TEXT_FILES_FOLDER;
            $avr_seeder_text_file_name = ParseController::AVR_TEXT_FILE . '_' . $skip . '_' . $take . ParseController::PHP_FILE_EXT;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $avr_seeder_text_file_name, $data_string);

            $from = $skip;
            $to = $skip + $take;

            return redirect()->back()->with('success', 'Акты выполненных работ ' . $from . '-' . $to . ' в текстовый файл сохранены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось сохранить акты выполненных работ в текстовый файл');
        }
    }

    protected function parse_closed_applications_from_old_old_base_to_text_file(string $skip, string $take)
    {
        try {

            ini_set('max_execution_time', 540);

            $avrs = DB::table('old_old_closed_application')->skip($skip)->take($take)->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($avrs as $avr) {

                $trk = DB::table('old_old_trk')->where('id', $avr->trk_id)->pluck('trk_name')->first();
                $building = DB::table('old_old_building')->where('id', $avr->building_id)->pluck('name')->first();
                $system = DB::table('old_old_equipment_type')->where('id', $avr->equipment_type_id)->pluck('name')->first();

                $user1 = null;
                $user2 = null;
                $user3 = null;
                $user4 = null;

                if ($avr->xvo_person_id1 != '') {
                    $user1 = DB::table('old_old_user')->where('surname', 'LIKE', $avr->xvo_person_id1 . '%')->pluck('surname')->first();
                }

                if ($avr->xvo_person_id2 != '') {
                    $user2 = DB::table('old_old_user')->where('surname', 'LIKE', $avr->xvo_person_id2 . '%')->pluck('surname')->first();
                }

                if ($avr->xvo_person_id3 != '') {
                    $user3 = DB::table('old_old_user')->where('surname', 'LIKE', $avr->xvo_person_id3 . '%')->pluck('surname')->first();
                }

                if ($avr->xvo_person_id4 != '') {
                    $user4 = DB::table('old_old_user')->where('surname', 'LIKE', $avr->xvo_person_id4 . '%')->pluck('surname')->first();
                }

                $description = $avr->description;
                $remark = $avr->remark;
                $recommendation = $avr->recommendation;

                while (Str::contains($description, "'")) {
                    $description = Str::replace("'", '`', $description);
                }

                while (Str::contains($remark, "'")) {
                    $remark = Str::replace("'", '`', $remark);
                }

                while (Str::contains($recommendation, "'")) {
                    $recommendation = Str::replace("'", '`', $recommendation);
                }

                $data_string .= "[ 'date' => '$avr->date',";
                $data_string .= " 'id' => '$avr->id',";
                $data_string .= " 'trk' => '$trk',";
                $data_string .= " 'building' => '$building',";
                $data_string .= " 'system' => '$system',";
                $data_string .= " 'equipment' => '$avr->equipment_name_id',";
                $data_string .= " 'description' => '$description',";
                $data_string .= " 'remark' => '$remark',";
                $data_string .= " 'recommendation' => '$recommendation',";
                $data_string .= " 'user' => '$avr->xvo_person_id',";
                $data_string .= " 'user1' => '$user1',";
                $data_string .= " 'user2' => '$user2',";
                $data_string .= " 'user3' => '$user3',";
                $data_string .= " 'user4' => '$user4'";

                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CLOSED_APPLICATION_TEXT_FILES_FOLDER;
            $avr_seeder_text_file_name = ParseController::CLOSED_APPLICATION_TEXT_FILE . '_' . $skip . '_' . $take . ParseController::PHP_FILE_EXT;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $avr_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Акты выполненных работ из базы первой версии ' . $skip . '-' . $take . ' в текстовый файл сохранены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e->getMessage());

        }

    }

    protected function parse_repairs_from_old_base_to_text_file()
    {

        try {
            $repairs = DB::table('old_repair')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($repairs as $repair) {
                $trk = DB::table('old_trk')->where('id', $repair->trk_id)->pluck('name')->first();
                $building = DB::table('old_building')->where('id', $repair->building_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $repair->floor_id)->pluck('name')->first();

                $room = DB::table('old_room')->where('id', $repair->room_id)->pluck('name')->first();

                $system = DB::table('old_system')->where('id', $repair->system_id)->pluck('name')->first();
                $division = DB::table('old_contractor')->where('id', $repair->contractor_id)->pluck('name')->first();
                $user = DB::table('old_user')->where('id', $repair->user_id)->pluck('surname')->first();

                $equipment_name = DB::table('old_equipment_name')->where('id', $repair->equipment_id)->first();
                $repair_status = DB::table('old_repair_status')->where('id', $repair->repair_status_id)->first();

                $comment = $repair->comment;

                if (Str::contains($comment, "'")) {
                    $comment = Str::replace("'", '`', $comment);
                }

                $data_string .= "[ 'plan_at' => '$repair->plan_date',";
                $data_string .= " 'executed_at' => '$repair->close_date',";
                $data_string .= " 'created_at' => '$repair->date',";
                $data_string .= " 'id' => '$repair->id',";
                $data_string .= " 'trk' => '$trk',";
                $data_string .= " 'building' => '$building',";
                $data_string .= " 'floor' => '$floor',";
                $data_string .= " 'room' => '$room',";
                $data_string .= " 'system' => '$system',";
                $data_string .= " 'equipment' => '$equipment_name->name',";
                $data_string .= " 'comment' => '$comment',";
                $data_string .= " 'division' => '$division',";
                $data_string .= " 'user' => '$user',";
                $data_string .= " 'status' => '$repair_status->name'";
                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $repair_seeder_text_file_name = ParseController::REPAIR_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $repair_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Ремонт записан в текстовый файл');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось записать ремонт в текстовый файл');

        }

    }

    protected function parse_applications_from_old_base_to_text_file()
    {

        try {
            $applications = DB::table('old_application')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($applications as $application) {

                $trk = DB::table('old_trk')->where('id', $application->trk_id)->pluck('name')->first();
                $division = DB::table('old_contractor')->where('id', $application->contractor_id)->pluck('name')->first();
                $user = DB::table('old_user')->where('id', $application->user_id)->pluck('surname')->first();
                $application_status = DB::table('old_application_status')->where('id', $application->application_status_id)->first();
                $room = DB::table('old_room')->where('id', $application->room_id)->pluck('name')->first();
                $equipment_name = DB::table('old_equipment_name')->where('id', $application->equipment_id)->pluck('name')->first();
                $problem = DB::table('old_problem')->where('id', $application->problem_id)->pluck('name')->first();

                $received_user = null;
                $received_date = null;

                if(!empty($application->received_user_id))
                {
                    $received_user =  DB::table('old_user')->where('id', $application->received_user_id)->pluck('surname')->first();
                    $received_date = $application->received_date;
                }

                $comment = $application->comment;

                if (Str::contains($comment, "'")) {
                    $comment = Str::replace("'", '`', $comment);
                }

                if(
                    ($application_status->name == 'новая'
                        || $application_status->name == 'запланирован ремонт'
                        || $application_status->name == 'в обработке (диагностика)'
                    )
                    && $division != 'Пожарная безопасность' && $division != 'Служба эксплуатации ТРК')
                {

                    $data_string .= "[ 'date' => '$application->date',";
                    $data_string .= " 'id' => '$application->id',";
                    $data_string .= " 'trk' => '$trk',";
                    $data_string .= " 'comment' => '$room, $equipment_name, $problem, $comment',";
                    $data_string .= " 'division' => '$division',";
                    $data_string .= " 'user' => '$user',";
                    $data_string .= " 'received_user' => '$received_user',";
                    $data_string .= " 'received_date' => '$received_date',";
                    $data_string .= " 'status' => '$application_status->name'";
                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $application_seeder_text_file_name = ParseController::APPLICATION_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $application_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Заявки записаны в текстовый файл');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось записать заявки в текстовый файл');

        }

    }

    protected function parse_spare_part_orders_from_old_base_to_text_file()
    {
        try {
            $orders = DB::table('old_order_spare_part')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($orders as $order) {
                $trk = DB::table('old_trk')->where('id', $order->trk_id)->pluck('name')->first();
                $room = DB::table('old_room')->where('id', $order->room_id)->pluck('name')->first();

                $system = DB::table('old_system')->where('id', $order->system_id)->pluck('name')->first();
                $division = DB::table('old_contractor')->where('id', $order->contractor_id)->pluck('name')->first();
                $user = DB::table('old_user')->where('id', $order->user_id)->pluck('surname')->first();

                $equipment_name = DB::table('old_equipment_name')->where('id', $order->equipment_id)->first();
                $spare_part = DB::table('old_spare_part')->where('id', $order->spare_part_id)->first();
                $order_status = DB::table('old_order_status')->where('id', $order->order_status_id)->first();
                $delivery_at = $order->delivery_date ?? null;
                $closed_at = $order->close_date ?? null;

                $comment = Str::limit($order->comment, 250);

                while (Str::contains($comment, "'")) {
                    $comment = Str::replace("'", '`', $comment);
                }

                if (!empty($spare_part->id)) {
                    $data_string .= "[ 'date' => '$order->date',";
                    $data_string .= " 'repair_id' => '$order->repair_id',";
                    $data_string .= " 'id' => '$order->id',";
                    $data_string .= " 'trk' => '$trk',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'system' => '$system',";
                    $data_string .= " 'equipment' => '$equipment_name->name',";
                    $data_string .= " 'spare_part' => '$spare_part->name',";
                    $data_string .= " 'order_status' => '$order_status->name',";
                    $data_string .= " 'is_urgency' => '$order->quickly_id',";
                    $data_string .= " 'account_number' => '$order->contract',";
                    $data_string .= " 'provider' => '$order->shipper',";
                    $data_string .= " 'comment' => '$comment',";
                    $data_string .= " 'delivery_at' => '$delivery_at',";
                    $data_string .= " 'closed_at' => '$closed_at',";
                    $data_string .= " 'division' => '$division',";
                    $data_string .= " 'user' => '$user',";
                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $spare_part_order_seeder_text_file_name = ParseController::SPARE_PART_ORDER_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $spare_part_order_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Заказы запчастей из старой базы в текстовый файл сохранены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Заказы запчастей из старой базы в текстовый файл сохранить не удалось');

        }

    }

    protected function parse_equipments_from_old_base_to_text_file()
    {
        try {
            $equipments = DB::table('old_equipment')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipments as $equipment) {
                $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();
                $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();
                $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                $axis = DB::table('old_axis')->where('id', $equipment->axis_id)->pluck('name')->first();

                $system = DB::table('old_system')->where('id', $equipment->system_id)->pluck('name')->first();
                $user = DB::table('old_user')->where('id', $equipment->user_id)->pluck('surname')->first();

                $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->first();

                if (!Str::contains($equipment_name->name, ['Удалить', 'удалить1', 'XM-1(удалить)', 'ПВ-03.2-ОФ(удалить)'])) {

                    if (Str::contains($room, "'")) {
                        $room = Str::replace("'", '`', $room);
                    }

                    if ($system == 'Кондиционирование' && $trk == 'Форт Тауэр') {
                        if (Str::contains($equipment_name->name, "CB-")) {
                            $equipment_name->name = $equipment_name->name . '/1';
                        }
                    }

                    if (Str::contains($trk, "Золотой Вавилон (Ясенево)")) {
                        $trk = 'FORT Ясенево';
                    }

                    if (
                        $trk == 'Европолис (Лесная)'
                        || $building == 'Неизвестно'
                        || $building == 'Все блоки/зоны'
                    ) {
                        $building = 'Блок 1';
                    }

                    if ($building == 'ПН1(Корпус 1)') {
                        $building = 'ПН1 (корпус 1)';
                    }

                    if ($building == 'ПН1(Корпус 2)') {
                        $building = 'ПН1 (корпус 2)';
                    }

                    $data_string .= "[ 'date' => '$equipment->date',";
                    $data_string .= " 'id' => '$equipment->id',";
                    $data_string .= " 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'system' => '$system',";
                    $data_string .= " 'equipment' => '$equipment_name->name',";
                    $data_string .= " 'axis' => '$axis',";
                    $data_string .= " 'comment' => '$equipment->comment',";
                    $data_string .= " 'user' => '$user',";
                    $data_string .= " ]," . PHP_EOL;
                }
            }

            $data_string .= '];' . PHP_EOL;

            $data_string = str_replace('14/5-15/Я-Я\'', '14/5-15/Я-Я`', $data_string);

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_seeder_text_file_name = ParseController::EQUIPMENT_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Оборудование из старой базы в текстовый файл сохранено');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Оборудование из старой базы в текстовый файл сохранить не удалось');

        }

    }


    protected function parse_rooms_from_old_base_to_text_file()
    {
        try {
            $trk_rooms_raw = DB::table('old_equipment')->get();

            $trk_rooms = $trk_rooms_raw->unique(function ($item) {
                return $item->trk_id . ' ' .
                    $item->building_id . ' ' .
                    $item->floor_id . ' ' .
                    $item->room_id . ' ' .
                    $item->system_id . ' ' .
                    $item->equipment_id;
            });

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($trk_rooms as $trk_room) {

                $trk = DB::table('old_trk')->where('id', $trk_room->trk_id)->pluck('name')->first();
                $building = DB::table('old_building')->where('id', $trk_room->building_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $trk_room->floor_id)->pluck('name')->first();
                $room = DB::table('old_room')->where('id', $trk_room->room_id)->pluck('name')->first();
                $system = DB::table('old_system')->where('id', $trk_room->system_id)->pluck('name')->first();
                $equipment_name = DB::table('old_equipment_name')->where('id', $trk_room->equipment_id)->pluck('name')->first();
                $axis = DB::table('old_axis')->where('id', $trk_room->axis_id)->pluck('name')->first();

                if (!Str::contains($equipment_name, ['Удалить', 'удалить1', 'XM-1(удалить)', 'ПВ-03.2-ОФ(удалить)'])) {

                    if ($system == 'Кондиционирование' && $trk == 'Форт Тауэр') {
                        if (!Str::contains($equipment_name, "/")) {

                            if (Str::contains($equipment_name, "CB-")) {
                                $equipment_name = $equipment_name . '/1';
                            }
                        }
                    }

                    if (Str::contains($trk, "Золотой Вавилон (Ясенево)")) {
                        $trk = 'FORT Ясенево';
                    }

                    if (
                        $trk == 'Европолис (Лесная)'
                        || $building == 'Неизвестно'
                        || $building == 'Все блоки/зоны'
                    ) {
                        $building = 'Блок 1';
                    }

                    if ($building == 'ПН1(Корпус 1)') {
                        $building = 'ПН1 (корпус 1)';
                    }

                    if ($building == 'ПН1(Корпус 2)') {
                        $building = 'ПН1 (корпус 2)';
                    }

                    while (Str::contains($axis, "'")) {
                        $axis = str_replace("'", '`', $axis);
                    }

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'system' => '$system',";
                    $data_string .= " 'axis' => '$axis',";
                    $data_string .= " 'equipment_name' => '$equipment_name'";
                    $data_string .= " ]," . PHP_EOL;
                }
            }

            $data_string .= '];' . PHP_EOL;

            $data_string = str_replace('McDonald\'s', 'McDonald`s', $data_string);
            $data_string = str_replace("'building' => 'Париж'", "'building' => 'Блок 1'", $data_string);
            $data_string = str_replace("'building' => 'Рим'", "'building' => 'Блок 1'", $data_string);
            $data_string = str_replace("'building' => 'Лондон'", "'building' => 'Блок 1'", $data_string);
            $data_string = str_replace("'building' => 'Барселона'", "'building' => 'Блок 1'", $data_string);

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $trk_room_seeder_text_file_name = ParseController::ROOM_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $trk_room_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Помещения из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $trk_room_seeder_text_file_name);

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }

    }

    protected function parse_hvac_supply_machines_from_old_base_to_text_file()
    {

        try {

            $hvac_supplies = DB::table('old_checklist_ventmachine_supply')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($hvac_supplies as $hvac_supply) {

                $avr = DB::table('old_avr')->where('id', $hvac_supply->avr_id)->first();

                $trk = DB::table('old_trk')->where('id', $avr->trk_id)->pluck('name')->first();

                if ($trk == 'Золотой Вавилон (Ясенево)') {
                    $trk = 'FORT Ясенево';
                }

                $building = DB::table('old_building')->where('id', $avr->building_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $avr->floor_id)->pluck('name')->first();
                $room = DB::table('old_room')->where('id', $avr->room_id)->pluck('name')->first();

                $new_trk = DB::table('trks')->where('name', $trk)->first();
                $new_building = DB::table('buildings')->where('name', $building)->first();

                if (empty($new_building->id)) {
                    $new_building = DB::table('buildings')->where('name', 'Блок 1')->first();
                }

                $new_floor = DB::table('floors')->where('name', $floor)->first();
                $new_room = DB::table('rooms')->where('name', $room)->first();

                $trk_room = DB::table('trk_rooms')
                    ->where('trk_id', $new_trk->id)
                    ->where('building_id', $new_building->id)
                    ->where('floor_id', $new_floor->id)
                    ->where('room_id', $new_room->id)
                    ->first();

                if (empty($trk_room->id)) {
                    $trk_room = TrkRoom::create([
                        'trk_id' => $new_trk->id,
                        'building_id' => $new_building->id,
                        'floor_id' => $new_floor->id,
                        'room_id' => $new_room->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $system = DB::table('old_system')->where('id', $avr->system_id)->pluck('name')->first();
                $equipment = DB::table('old_equipment')->where('id', $hvac_supply->equipment_id)->first();
                $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->first();

                $new_system = DB::table('systems')->where('name', $system)->first();
                $new_equipment_name = DB::table('equipment_names')->where('name', $equipment_name->name)->first();

                $trk_equipment = DB::table('trk_equipments')
                    ->where('trk_room_id', $trk_room->id)
                    ->where('system_id', $new_system->id)
                    ->where('equipment_name_id', $new_equipment_name->id)
                    ->first();

                if (empty($trk_equipment->id)) {
                    $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();

                    $trk_equipment = TrkEquipment::create([
                        'trk_room_id' => $trk_room->id,
                        'system_id' => $new_system->id,
                        'equipment_name_id' => $new_equipment_name->id,
                        'equipment_status_id' => $equipment_status->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $old_user = DB::table('old_user')->where('id', $hvac_supply->user_id)->first();

                $user = User::where('name', $old_user->surname)->first();

                $data_string .= "[ 'trk_equipment_id' => '$trk_equipment->id',";
                $data_string .= " 'date' => '$hvac_supply->date',";
                $data_string .= " 'trk_room_id' => '$trk_room->id',";
                $data_string .= " 'old_avr_id' => '$hvac_supply->avr_id',";
                $data_string .= " 'user_id' => '$user->id',";
                $data_string .= " 'outside_air_t' => '$hvac_supply->t_outside',";
                $data_string .= " 'setpoint_air_t' => '$hvac_supply->t_setpoint',";
                $data_string .= " 'supply_air_t' => '$hvac_supply->t_supply',";
                $data_string .= " 'supply_engine_t' => '$hvac_supply->t_engine',";
                $data_string .= " 'front_bearing_t' => '$hvac_supply->t_bearing',";

                $current = $this->punctuationMarkService->setFromVerticalLineToRightslash($hvac_supply->i);
                $fact_current = $this->getDataFromStringService->getFirstPartFromString($current);
                $passport_current = $this->getDataFromStringService->getSecondPartFromString($current);
                $data_string .= " 'supply_engine_actual_current' => '$fact_current',";
                $data_string .= " 'supply_engine_passport_current' => '$passport_current',";

                $frequency = $this->punctuationMarkService->setFromVerticalLineToRightslash($hvac_supply->hz);
                $fact_frequency = $this->getDataFromStringService->getFirstPartFromString($frequency);
                $passport_frequency = $this->getDataFromStringService->getSecondPartFromString($frequency);
                $data_string .= " 'supply_engine_actual_frequency' => '$fact_frequency',";
                $data_string .= " 'supply_engine_passport_frequency' => '$passport_frequency',";

                $data_string .= " 'supply_air_actual_rate' => '$hvac_supply->q',";

                $hot_water = $this->punctuationMarkService->setFromVerticalLineToRightslash($hvac_supply->t_hot_water);
                $hot_water_t = $this->punctuationMarkService->setFromBackslashToRightslash($hot_water);
                $inlet_hot_water_t = $this->getDataFromStringService->getFirstPartFromString($hot_water_t);
                $outlet_hot_water_t = $this->getDataFromStringService->getSecondPartFromString($hot_water_t);
                $data_string .= " 'hot_water_valve_open_percent' => '$hvac_supply->hot_water_valve',";
                $data_string .= " 'inlet_hot_water_t' => '$inlet_hot_water_t',";
                $data_string .= " 'outlet_hot_water_t' => '$outlet_hot_water_t',";

                $inlet_cold_water_t = $this->getDataFromStringService->getFirstPartFromString($hvac_supply->t_cold_water);
                $outlet_cold_water_t = $this->getDataFromStringService->getSecondPartFromString($hvac_supply->t_cold_water);
                $data_string .= " 'cold_water_valve_open_percent' => '$hvac_supply->cold_water_valve',";
                $data_string .= " 'inlet_cold_water_t' => '$inlet_cold_water_t',";
                $data_string .= " 'outlet_cold_water_t' => '$outlet_cold_water_t',";

                $supply_air_dumper_open_percent = $this->punctuationMarkService->setFromVerticalLineToRightslash($hvac_supply->air_valve);
                $supply_air_dumper_open_percent = $this->getDataFromStringService->getFirstPartFromString($supply_air_dumper_open_percent);
                $recycle_air_dumper_open_percent = $this->getDataFromStringService->getSecondPartFromString($supply_air_dumper_open_percent);
                $data_string .= " 'supply_air_dumper_open_percent' => '$supply_air_dumper_open_percent',";
                $data_string .= " 'recycle_air_dumper_open_percent' => '$recycle_air_dumper_open_percent',";

                $hot_water_pump_actual_current = $this->punctuationMarkService->setFromVerticalLineToRightslash($hvac_supply->i_water_pump);
                $hot_water_pump_actual_current = $this->getDataFromStringService->getFirstPartFromString($hot_water_pump_actual_current);
                $hot_water_pump_passport_current = $this->getDataFromStringService->getSecondPartFromString($hot_water_pump_actual_current);
                $data_string .= " 'hot_water_pump_actual_current' => '$hot_water_pump_actual_current',";
                $data_string .= " 'hot_water_pump_passport_current' => '$hot_water_pump_passport_current',";

                $glycol_pump_actual_current = $this->getDataFromStringService->getFirstPartFromString($hvac_supply->i_glycol_pump);
                $glycol_pump_passport_current = $this->getDataFromStringService->getSecondPartFromString($hvac_supply->i_glycol_pump);
                $data_string .= " 'glycol_pump_actual_current' => '$glycol_pump_actual_current',";
                $data_string .= " 'glycol_pump_passport_current' => '$glycol_pump_passport_current',";

                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $trk_checklist_supply_air_seeder_text_file_name = ParseController::CHECKLIST_SUPPLY_AIR_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $trk_checklist_supply_air_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Чеклисты притока из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $trk_checklist_supply_air_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_hvac_extract_machines_from_old_base_to_text_file()
    {

        try {

            $hvac_extracts = DB::table('old_checklist_ventmachine_extract')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($hvac_extracts as $hvac_extract) {


                $avr = DB::table('old_avr')->where('id', $hvac_extract->avr_id)->first();

                $trk = DB::table('old_trk')->where('id', $avr->trk_id)->pluck('name')->first();

                if ($trk == 'Золотой Вавилон (Ясенево)') {
                    $trk = 'FORT Ясенево';
                }

                $building = DB::table('old_building')->where('id', $avr->building_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $avr->floor_id)->pluck('name')->first();
                $room = DB::table('old_room')->where('id', $avr->room_id)->pluck('name')->first();

                $new_trk = DB::table('trks')->where('name', $trk)->first();
                $new_building = DB::table('buildings')->where('name', $building)->first();

                if (empty($new_building->id)) {
                    $new_building = DB::table('buildings')->where('name', 'Блок 1')->first();
                }
                $new_floor = DB::table('floors')->where('name', $floor)->first();
                $new_room = DB::table('rooms')->where('name', $room)->first();

                $trk_room = DB::table('trk_rooms')
                    ->where('trk_id', $new_trk->id)
                    ->where('building_id', $new_building->id)
                    ->where('floor_id', $new_floor->id)
                    ->where('room_id', $new_room->id)
                    ->first();

                if (empty($trk_room->id)) {
                    $trk_room = TrkRoom::create([
                        'trk_id' => $new_trk->id,
                        'building_id' => $new_building->id,
                        'floor_id' => $new_floor->id,
                        'room_id' => $new_room->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $system = DB::table('old_system')->where('id', $avr->system_id)->pluck('name')->first();
                $equipment = DB::table('old_equipment')->where('id', $hvac_extract->equipment_id)->first();
                $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->first();

                $new_system = DB::table('systems')->where('name', $system)->first();
                $new_equipment_name = DB::table('equipment_names')->where('name', $equipment_name->name)->first();

                $trk_equipment = DB::table('trk_equipments')
                    ->where('trk_room_id', $trk_room->id)
                    ->where('system_id', $new_system->id)
                    ->where('equipment_name_id', $new_equipment_name->id)
                    ->first();

                if (empty($trk_equipment->id)) {
                    $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();

                    $trk_equipment = TrkEquipment::create([
                        'trk_room_id' => $trk_room->id,
                        'system_id' => $new_system->id,
                        'equipment_name_id' => $new_equipment_name->id,
                        'equipment_status_id' => $equipment_status->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $old_user = DB::table('old_user')->where('id', $hvac_extract->user_id)->first();

                $user = User::where('name', $old_user->surname)->first();

                $data_string .= "[ 'trk_equipment_id' => '$trk_equipment->id',";
                $data_string .= " 'date' => '$hvac_extract->date',";
                $data_string .= " 'old_avr_id' => '$hvac_extract->avr_id',";
                $data_string .= " 'trk_room_id' => '$trk_room->id',";
                $data_string .= " 'user_id' => '$user->id',";
                $data_string .= " 'extract_air_t' => '$hvac_extract->t_extract',";
                $data_string .= " 'extract_engine_t' => '$hvac_extract->t_engine',";
                $data_string .= " 'front_bearing_t' => '$hvac_extract->t_bearing',";

                $current = $this->punctuationMarkService->setFromVerticalLineToRightslash($hvac_extract->i);
                $fact_current = $this->getDataFromStringService->getFirstPartFromString($current);
                $passport_current = $this->getDataFromStringService->getSecondPartFromString($current);
                $data_string .= " 'extract_engine_actual_current' => '$fact_current',";
                $data_string .= " 'extract_engine_passport_current' => '$passport_current',";

                $frequency = $this->punctuationMarkService->setFromVerticalLineToRightslash($hvac_extract->hz);
                $fact_frequency = $this->getDataFromStringService->getFirstPartFromString($frequency);
                $passport_frequency = $this->getDataFromStringService->getSecondPartFromString($frequency);
                $data_string .= " 'extract_engine_actual_frequency' => '$fact_frequency',";
                $data_string .= " 'extract_engine_passport_frequency' => '$passport_frequency',";

                $data_string .= " 'extract_air_actual_rate' => '$hvac_extract->q',";

                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $trk_checklist_extract_air_seeder_text_file_name = ParseController::CHECKLIST_EXTRACT_AIR_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $trk_checklist_extract_air_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Чеклисты вытяжки из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $trk_checklist_extract_air_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_hvac_room_climate_from_old_base_to_text_file()
    {

        try {

            $hvac_room_climates = DB::table('old_hvac_climate')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($hvac_room_climates as $hvac_room_climate) {

                $old_user = DB::table('old_user')->where('id', $hvac_room_climate->user_id)->first();
                $user = User::where('name', $old_user->surname)->first();

                $hvac = DB::table('old_hvac')->where('id', $hvac_room_climate->hvac_id)->first();

                $trk = DB::table('old_trk')->where('id', $hvac->trk_id)->pluck('name')->first();
                $building = DB::table('old_building')->where('id', $hvac->building_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $hvac->floor_id)->pluck('name')->first();
                $room = DB::table('old_room')->where('id', $hvac->room_id)->pluck('name')->first();

                $new_trk = Trk::where('name', $trk)->first();
                $new_building = Building::where('name', $building)->first();
                $new_floor = Floor::where('name', $floor)->first();
                $new_room = Room::where('name', $room)->first();

                if (empty($new_room->id)) {
                    $new_room = Room::create([
                        'name' => $room,
                        'author_id' => $user->id,
                        'last_editor_id' => $user->id,
                    ]);
                }

                $trk_room = TrkRoom::where('trk_id', $new_trk->id)
                    ->where('building_id', $new_building->id)
                    ->where('floor_id', $new_floor->id)
                    ->where('room_id', $new_room->id)
                    ->first();


                if (empty($trk_room->id)) {
                    $trk_room = TrkRoom::create([
                        'trk_id' => $new_trk->id,
                        'building_id' => $new_building->id,
                        'floor_id' => $new_floor->id,
                        'room_id' => $new_room->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $data_string .= "[ 'trk_room_id' => '$trk_room->id',";
                $data_string .= " 'date' => '$hvac_room_climate->date',";
                $data_string .= " 'user_id' => '$user->id',";

                $data_string .= " 't_outside' => '$hvac_room_climate->t_outside',";
                $data_string .= " 't_inside' => '$hvac_room_climate->t_inside',";
                $data_string .= " 'h_inside' => '$hvac_room_climate->h_inside',";
                $data_string .= " 't_supply_air' => '$hvac_room_climate->t_supply',";
                $data_string .= " 'q_supply_air_total' => '$hvac_room_climate->q_supply',";
                $data_string .= " 'q_extract_air_total' => '$hvac_room_climate->q_extract',";
                $data_string .= " 'comment' => '$hvac_room_climate->comment',";

                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $trk_checklist_room_climate_seeder_text_file_name = ParseController::CHECKLIST_ROOM_CLIMATE_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $trk_checklist_room_climate_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Чеклисты климата в помещении из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $trk_checklist_room_climate_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_bearings_from_old_base_to_text_file()
    {
        try {

            $equipment_bearings = DB::table('old_equipment_bearing')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_bearings as $equipment_bearing) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_bearing->equipment_id)->first();
                $bearing = DB::table('old_bearing_type')->where('id', $equipment_bearing->bearing_type_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();

                    if ($trk == 'Золотой Вавилон (Ясенево)') {
                        $trk = 'FORT Ясенево';
                    }

                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'bearing' => '$bearing->name',";
                    $data_string .= " 'supply_fan' => '$equipment_bearing->supply_fan',";
                    $data_string .= " 'supply_engine' => '$equipment_bearing->supply_engine',";
                    $data_string .= " 'extract_fan' => '$equipment_bearing->extract_fan',";
                    $data_string .= " 'extract_engine' => '$equipment_bearing->extract_engine',";
                    $data_string .= " 'amount' => '$equipment_bearing->amount',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_bearings_seeder_text_file_name = ParseController::EQUIPMENT_BEARINGS_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_bearings_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Подшипники оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_bearings_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_air_filters_from_old_base_to_text_file()
    {
        try {

            $equipment_air_filters = DB::table('old_equipment_airfilter')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_air_filters as $equipment_air_filter) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_air_filter->equipment_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();

                    if ($trk == 'Золотой Вавилон (Ясенево)') {
                        $trk = 'FORT Ясенево';
                    }

                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $air_filter_cleaning_class = DB::table('old_airfilter_cleaning_class')->where('id', $equipment_air_filter->airfilter_cleaning_class_id)->first();
                    $air_filter_frame_thickness = DB::table('old_airfilter_frame_thickness')->where('id', $equipment_air_filter->airfilter_frame_thickness_id)->first();
                    $air_filter_pocket_length = DB::table('old_airfilter_pocket_length')->where('id', $equipment_air_filter->airfilter_pocket_length_id)->first();
                    $air_filter_size = DB::table('old_airfilter_size')->where('id', $equipment_air_filter->airfilter_size_id)->first();
                    $air_filter_type = DB::table('old_airfilter_type')->where('id', $equipment_air_filter->airfilter_type_id)->first();

                    $filter = $air_filter_size->name . 'x' . $air_filter_frame_thickness->name . 'x' . $equipment_air_filter->pockets_amount . 'x' . $air_filter_pocket_length->name . 'x' . $air_filter_cleaning_class->name;

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'filter' => '$filter',";
                    $data_string .= " 'type' => '$air_filter_type->name',";
                    $data_string .= " 'supply' => '$equipment_air_filter->supply',";
                    $data_string .= " 'extract' => '$equipment_air_filter->extract',";
                    $data_string .= " 'recuperator' => '$equipment_air_filter->recuperator',";
                    $data_string .= " 'amount' => '$equipment_air_filter->amount',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_air_filters_seeder_text_file_name = ParseController::EQUIPMENT_AIR_FILTERS_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_air_filters_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Фильтры оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_air_filters_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_drivebelts_from_old_base_to_text_file()
    {
        try {

            $equipment_drivebelts = DB::table('old_equipment_drivebelt')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_drivebelts as $equipment_drivebelt) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_drivebelt->equipment_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();

                    if ($trk == 'Золотой Вавилон (Ясенево)') {
                        $trk = 'FORT Ясенево';
                    }

                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $drivebelt = DB::table('old_drivebelt_type')->where('id', $equipment_drivebelt->drivebelt_type_id)->first();

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'drivebelt' => '$drivebelt->name',";
                    $data_string .= " 'supply' => '$equipment_drivebelt->supply',";
                    $data_string .= " 'extract' => '$equipment_drivebelt->extract',";
                    $data_string .= " 'recuperator' => '$equipment_drivebelt->recuperator',";
                    $data_string .= " 'amount' => '$equipment_drivebelt->amount',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_drivebelts_seeder_text_file_name = ParseController::EQUIPMENT_DRIVEBELTS_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_drivebelts_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Приводные ремни оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_drivebelts_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_electro_motors_from_old_base_to_text_file()
    {
        try {

            $equipment_electro_motors = DB::table('old_equipment_electro_motor')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_electro_motors as $equipment_electro_motor) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_electro_motor->equipment_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();

                    if ($trk == 'Золотой Вавилон (Ясенево)') {
                        $trk = 'FORT Ясенево';
                    }

                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $electro_motor = DB::table('old_electro_motor_type')->where('id', $equipment_electro_motor->electro_motor_type_id)->first();

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'electro_motor' => '$electro_motor->name',";
                    $data_string .= " 'supply' => '$equipment_electro_motor->supply_motor',";
                    $data_string .= " 'extract' => '$equipment_electro_motor->extract_motor',";
                    $data_string .= " 'recuperator' => '$equipment_electro_motor->recuperator_motor',";
                    $data_string .= " 'amount' => '$equipment_electro_motor->amount',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_electro_motors_seeder_text_file_name = ParseController::EQUIPMENT_ELECTRO_MOTORS_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_electro_motors_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Электродвигатели оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_electro_motors_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_pulleis_from_old_base_to_text_file()
    {
        try {

            $equipment_pulleis = DB::table('old_equipment_pulley')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_pulleis as $equipment_pulley) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_pulley->equipment_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();

                    if ($trk == 'Золотой Вавилон (Ясенево)') {
                        $trk = 'FORT Ясенево';
                    }

                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $pulley = DB::table('old_pulley_type')->where('id', $equipment_pulley->pulley_type_id)->first();

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'pulley' => '$pulley->name',";
                    $data_string .= " 'supply_fan' => '$equipment_pulley->supply_fan',";
                    $data_string .= " 'supply_engine' => '$equipment_pulley->supply_engine',";
                    $data_string .= " 'extract_fan' => '$equipment_pulley->extract_fan',";
                    $data_string .= " 'extract_engine' => '$equipment_pulley->extract_engine',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_pulleis_seeder_text_file_name = ParseController::EQUIPMENT_PULLIES_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_pulleis_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Шкивы оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_pulleis_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_taperbashes_from_old_base_to_text_file()
    {
        try {

            $equipment_taperbashes = DB::table('old_equipment_taperbash')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_taperbashes as $equipment_taperbash) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_taperbash->equipment_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();

                    if ($trk == 'Золотой Вавилон (Ясенево)') {
                        $trk = 'FORT Ясенево';
                    }

                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $taperbash = DB::table('old_taperbash_type')->where('id', $equipment_taperbash->taperbash_type_id)->first();

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'taperbash' => '$taperbash->name',";
                    $data_string .= " 'supply_fan' => '$equipment_taperbash->supply_fan',";
                    $data_string .= " 'supply_engine' => '$equipment_taperbash->supply_engine',";
                    $data_string .= " 'extract_fan' => '$equipment_taperbash->extract_fan',";
                    $data_string .= " 'extract_engine' => '$equipment_taperbash->extract_engine',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_taperbashes_seeder_text_file_name = ParseController::EQUIPMENT_TAPERBASHES_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_taperbashes_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Тапербаши из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_taperbashes_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_pumps_from_old_base_to_text_file()
    {
        try {

            $equipment_pumps = DB::table('old_equipment_pump')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_pumps as $equipment_pump) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_pump->equipment_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();

                    if ($trk == 'Золотой Вавилон (Ясенево)') {
                        $trk = 'FORT Ясенево';
                    }

                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $pump = DB::table('old_pump_type')->where('id', $equipment_pump->pump_type_id)->first();

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'pump' => '$pump->name',";
                    $data_string .= " 'hot_water' => '$equipment_pump->hot_water',";
                    $data_string .= " 'glycol' => '$equipment_pump->glycol',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_pumps_seeder_text_file_name = ParseController::EQUIPMENT_PUMPS_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_pumps_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Насосы оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_pumps_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_valve_actuators_from_old_base_to_text_file()
    {
        try {

            $equipment_valve_actuators = DB::table('old_equipment_valve_actuator')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_valve_actuators as $equipment_valve_actuator) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_valve_actuator->equipment_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();
                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $valve_actuator = DB::table('old_pump_type')->where('id', $equipment_valve_actuator->valve_actuator_type_id)->first();

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'actuator' => '$valve_actuator->name',";
                    $data_string .= " 'hot_water' => '$equipment_valve_actuator->hot_water',";
                    $data_string .= " 'cold_water' => '$equipment_valve_actuator->cold_water',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_valve_actuators_seeder_text_file_name = ParseController::EQUIPMENT_VALVE_ACTUATORS_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_valve_actuators_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Насосы оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_valve_actuators_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_rates_from_old_base_to_text_file()
    {
        try {

            $equipment_rates = DB::table('old_equipment_rate')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($equipment_rates as $equipment_rate) {

                $equipment = DB::table('old_equipment')->where('id', $equipment_rate->equipment_id)->first();

                if (!empty($equipment->id)) {
                    $equipment_name = DB::table('old_equipment_name')->where('id', $equipment->equipment_id)->pluck('name')->first();
                    $trk = DB::table('old_trk')->where('id', $equipment->trk_id)->pluck('name')->first();

                    if ($trk == 'Золотой Вавилон (Ясенево)') {
                        $trk = 'FORT Ясенево';
                    }

                    $building = DB::table('old_building')->where('id', $equipment->building_id)->pluck('name')->first();
                    $floor = DB::table('old_floor')->where('id', $equipment->floor_id)->pluck('name')->first();
                    $room = DB::table('old_room')->where('id', $equipment->room_id)->pluck('name')->first();

                    $data_string .= "[ 'trk' => '$trk',";
                    $data_string .= " 'building' => '$building',";
                    $data_string .= " 'floor' => '$floor',";
                    $data_string .= " 'room' => '$room',";
                    $data_string .= " 'equipment' => '$equipment_name',";

                    $data_string .= " 'supply_air_rate' => '$equipment_rate->supply_air_rate',";
                    $data_string .= " 'project_supply_air_rate' => '$equipment_rate->project_supply_air_rate',";
                    $data_string .= " 'extract_air_rate' => '$equipment_rate->extract_air_rate',";
                    $data_string .= " 'project_extract_air_rate' => '$equipment_rate->project_extract_air_rate',";
                    $data_string .= " 'supply_hot_water_rate' => '$equipment_rate->supply_hot_water_rate',";
                    $data_string .= " 'project_supply_hot_water_rate' => '$equipment_rate->project_supply_hot_water_rate',";
                    $data_string .= " 'supply_cold_water_rate' => '$equipment_rate->supply_cold_water_rate',";
                    $data_string .= " 'project_supply_cold_water_rate' => '$equipment_rate->project_supply_cold_water_rate',";

                    $data_string .= " ]," . PHP_EOL;
                }

            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_rates_seeder_text_file_name = ParseController::EQUIPMENT_RATES_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_rates_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Расходы оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_rates_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_condition_checklists_from_old_base_to_text_file()
    {
        try {

            $condition_checklists = DB::table('old_checklist_hvac_ventilation')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($condition_checklists as $condition_checklist) {

                $old_user = DB::table('old_user')->where('id', $condition_checklist->user_id)->first();
                $user = User::where('name', $old_user->surname)->first();

                $checklist_type = DB::table('old_checklist_type')->where('id', $condition_checklist->checklist_type_id)->pluck('name')->first();

                $data_string .= "[ 'hvac_id' => '$condition_checklist->hvac_id',";
                $data_string .= " 'date' => '$condition_checklist->date',";
                $data_string .= " 'user' => '$user->name',";

                $data_string .= " 'old_checklist_id' => '$condition_checklist->checklist_id',";
                $data_string .= " 'checklist_type' => '$checklist_type',";
                $data_string .= " 'number' => '$condition_checklist->number',";
                $data_string .= " 'standard_size' => '$condition_checklist->standard_size',";
                $data_string .= " 'cross_size' => '$condition_checklist->cross_size',";
                $data_string .= " 'air_speed' => '$condition_checklist->air_speed',";
                $data_string .= " 'air_rate' => '$condition_checklist->air_rate',";
                $data_string .= " 'air_pressure' => '$condition_checklist->air_pressure',";
                $data_string .= " 'air_temperature' => '$condition_checklist->air_temperature',";
                $data_string .= " 'air_temperature_output' => '$condition_checklist->air_temperature_output',";
                $data_string .= " 'air_valve_setting' => '$condition_checklist->air_valve_setting',";
                $data_string .= " 't_cold_water_input' => '$condition_checklist->t_cold_water_input',";
                $data_string .= " 't_cold_water_output' => '$condition_checklist->t_cold_water_output',";
                $data_string .= " 'cold_water_valve_setting' => '$condition_checklist->cold_water_valve_setting',";
                $data_string .= " 'p_delta_cold_water' => '$condition_checklist->p_delta_cold_water',";
                $data_string .= " 'cold_water_rate' => '$condition_checklist->cold_water_rate',";

                $data_string .= " 'comment' => '$condition_checklist->comment',";

                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $condition_checklist_seeder_text_file_name = ParseController::CONDITION_CHECKLIST_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $condition_checklist_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Чеклисты балок/фанкойлов/кондиционеров/диффузоров/воздуховод из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $condition_checklist_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_equipment_users_from_old_base_to_text_file()
    {

        try {

            $hvac_equipments = DB::table('old_hvac_equipment')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($hvac_equipments as $hvac_equipment) {

                $hvac = DB::table('old_hvac')->where('id', $hvac_equipment->hvac_id)->first();

                $trk = DB::table('old_trk')->where('id', $hvac->trk_id)->pluck('name')->first();
                $building = DB::table('old_building')->where('id', $hvac->building_id)->pluck('name')->first();
                $floor = DB::table('old_floor')->where('id', $hvac->floor_id)->pluck('name')->first();
                $room = DB::table('old_room')->where('id', $hvac->room_id)->pluck('name')->first();
                $system = DB::table('old_system')->where('id', $hvac_equipment->system_id)->pluck('name')->first();

                $old_trk_equipment = DB::table('old_equipment')
                    ->where('id', $hvac_equipment->equipment_id)->first();

                $equipment_name = DB::table('old_equipment_name')->where('id', $old_trk_equipment->equipment_id)->pluck('name')->first();

                if (Str::contains($equipment_name, 'CB-')
                    && !Str::contains($equipment_name, '/')) {
                    $equipment_name .= '/1';
                }

                $new_trk = Trk::where('name', $trk)->first();
                $new_building = Building::where('name', $building)->first();

                if (empty($new_building->id)) {
                    $new_building = DB::table('buildings')->where('name', 'Блок 1')->first();
                }
                $new_floor = Floor::where('name', $floor)->first();
                $new_room = Room::where('name', $room)->first();

                if (empty($new_floor->id)) {
                    $new_floor = Floor::create([
                        'name' => $floor,
                        'alias' => Str::slug($floor),
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                if (empty($new_room->id)) {
                    $new_room = Room::create([
                        'name' => $room,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $trk_room = TrkRoom::where('trk_id', $new_trk->id)
                    ->where('building_id', $new_building->id)
                    ->where('floor_id', $new_floor->id)
                    ->where('room_id', $new_room->id)
                    ->first();


                if (empty($trk_room->id)) {
                    $trk_room = TrkRoom::create([
                        'trk_id' => $new_trk->id,
                        'building_id' => $new_building->id,
                        'floor_id' => $new_floor->id,
                        'room_id' => $new_room->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $data_string .= "[ 'trk_room_id' => '$trk_room->id',";
                $data_string .= " 'equipment_name' => '$equipment_name',";
                $data_string .= " 'system' => '$system',";
                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $equipment_users_seeder_text_file_name = ParseController::EQUIPMENT_USERS_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $equipment_users_seeder_text_file_name, $data_string);

            return redirect()->back()->with('success', 'Потребители оборудования из старой базы записаны в текстовый файл ' . $seeder_text_files_folder . $equipment_users_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    protected function parse_users_from_old_base_to_text_file()
    {

        try {
            $users = DB::table('old_user')->get();

            $data_string = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

            foreach ($users as $user) {

                $division = DB::table('old_contractor')
                    ->where('id', $user->contractor_id)
                    ->first();

                if(
                    $division->name != "ХВО"
                    && $division->name != "АСУ"
                    && $division->name != "Служба эксплуатации ТРК"
                )
                {
                    $division->name = '';
                }

                $data_string .= "[ 'name' => '$user->surname',";
                $data_string .= " 'old_id' => '$user->id',";
                $data_string .= " 'email' => '$user->email',";
                $data_string .= " 'password' => '$user->password_hash',";
                $data_string .= " 'division' => '$division->name'";
                $data_string .= " ]," . PHP_EOL;
            }

            $data_string .= '];' . PHP_EOL;

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $user_seeder_text_file_name = ParseController::USER_SEEDER_TEXT_FILE_NAME;

            if (!is_dir($seeder_text_files_folder)) mkdir($seeder_text_files_folder);
            file_put_contents($seeder_text_files_folder . $user_seeder_text_file_name, $data_string);
            return redirect()->back()->with('success', 'Создан файл со старыми пользователями: ' . $seeder_text_files_folder . $user_seeder_text_file_name);

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }
    }


    // from text file to new base
    protected function parse_rooms_from_text_file_to_new_base()
    {
        try {
            ini_set('max_execution_time', 180);

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $trk_room_seeder_text_file_name = ParseController::ROOM_SEEDER_TEXT_FILE_NAME;

            $trk_rooms = include $seeder_text_files_folder . $trk_room_seeder_text_file_name;

            foreach ($trk_rooms as $trk_room) {
                $trk = Trk::where('name', $trk_room['trk'])->first();

                if (empty($trk->id)) {
                    $trk = Trk::create([
                        'name' => $trk_room['trk'],
                        'alias' => Str::slug($trk_room['trk']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $building = Building::where('name', $trk_room['building'])->first();

                if (empty($building->id)) {
                    $building = Building::create([
                        'name' => $trk_room['building'],
                        'alias' => Str::slug($trk_room['building']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $floor = Floor::where('name', $trk_room['floor'])->first();

                if (empty($floor->id)) {
                    $floor = Floor::create([
                        'name' => $trk_room['floor'],
                        'alias' => Str::slug($trk_room['floor']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $room = Room::where('name', $trk_room['room'])->first();

                if (empty($room->id)) {
                    $room = Room::create([
                        'name' => $trk_room['room'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $system = System::where('name', $trk_room['system'])->first();

                if (empty($system->id)) {
                    $system = System::create([
                        'name' => $trk_room['system'],
                        'alias' => Str::slug($trk_room['system']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $axis = Axe::where('name', $trk_room['axis'])->first();

                if (empty($axis->id)) {
                    $axis = Axe::create([
                        'name' => $trk_room['axis'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $equipment_name = EquipmentName::where('name', $trk_room['equipment_name'])->first();

                if (empty($equipment_name->id)) {
                    $equipment_name = EquipmentName::create([
                        'name' => $trk_room['equipment_name'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }


                $room_purpose = RoomPurpose::where('name', 'Техническое')->first();
                $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();


                $old_trk_room = TrkRoom::where('trk_id', $trk->id)
                    ->where('building_id', $building->id)
                    ->where('floor_id', $floor->id)
                    ->where('room_purpose_id', $room_purpose->id)
                    ->where('room_id', $room->id)
                    ->first();

                if (empty($old_trk_room->id)) {
                    try {
                        $old_trk_room = TrkRoom::create([
                            'trk_id' => $trk->id,
                            'building_id' => $building->id,
                            'floor_id' => $floor->id,
                            'room_id' => $room->id,
                            'room_purpose_id' => $room_purpose->id,
                            'author_id' => 14,
                            'last_editor_id' => 14
                        ]);
                    } catch (\Exception $e) {
                        Log::error($e);
                        redirect()->back()->with('error', $e);
                    }
                }

                if (Str::contains($equipment_name->name, "П")
                    || Str::contains($equipment_name->name, "AHU-")
                    || Str::contains($equipment_name->name, "ВУ")
                    || Str::contains($equipment_name->name, "Вентиляция")
                    && $system->name == System::CHILLER
                ) {
                    $system = System::where('name', System::AIR_RECYCLE)->first();
                }

                if (Str::contains($equipment_name->name, "фанк")
                    || Str::contains($equipment_name->name, "Фанк")
                    || Str::contains($equipment_name->name, "сплит")
                    || Str::contains($equipment_name->name, "VRF")
                    && $system->name == System::CHILLER
                ) {
                    $system = System::where('name', System::AIR_CONDITION)->first();
                }

                $old_trk_equipment = TrkEquipment::where('trk_room_id', $old_trk_room->id)
                    ->where('system_id', $system->id)
                    ->where('equipment_name_id', $equipment_name->id)
                    ->where('equipment_status_id', $equipment_status->id)
                    ->first();

                if (empty($old_trk_equipment->id)) {

                    try {

                        $new_trk_room_equipment = TrkEquipment::create([
                            'trk_room_id' => $old_trk_room->id,
                            'system_id' => $system->id,
                            'equipment_name_id' => $equipment_name->id,
                            'equipment_status_id' => $equipment_status->id,
                            'axis_id' => $axis->id,
                            'author_id' => 14,
                            'last_editor_id' => 14
                        ]);

                        if (
                            System::AIR_CONDITION == $system->name
                            && $floor->name != Floor::ROOF
                            && $floor->name != Floor::ROOF_PLUS
                        ) {
                            if (
                                !EquipmentUser::where('trk_room_id', $new_trk_room_equipment->trk_room_id)
                                    ->where('equipment_id', $new_trk_room_equipment->id)
                                    ->exists()
                            ) {
                                EquipmentUser::create([
                                    'trk_room_id' => $new_trk_room_equipment->trk_room_id,
                                    'equipment_id' => $new_trk_room_equipment->id,
                                    'author_id' => Auth::id(),
                                    'last_editor_id' => Auth::id(),
                                ]);
                            }
                        }
                    } catch (\Exception $e) {

                        Log::error($e);
                        redirect()->back()->with('error', $e);

                    }
                }

            }
            return redirect()->back()->with('success', 'Помещения из файла ' . $seeder_text_files_folder . $trk_room_seeder_text_file_name . ' перенесены в новую базу');

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }


    }

    protected function parse_users_from_text_file_to_new_base()
    {
        try {
            ini_set('max_execution_time', 180);

            $seeder_text_files_folder = ParseController::SEEDER_TEXT_FILE_FOLDER;
            $user_seeder_text_file_name = ParseController::USER_SEEDER_TEXT_FILE_NAME;

            $users = include $seeder_text_files_folder . $user_seeder_text_file_name;

            $organization = Organization::where('name', 'Fort Group')->first();
            $town = Town::where('name', 'Санкт-Петербург')->first();

            foreach ($users as $user) {

                $old_user = User::where('name', $user['name'])
                    ->first();

                if (empty($old_user->id)) {

                    try {

                        $division = UserDivision::where('name', 'like', $user['division'])->first();

                        $new_user = User::create([
                            'name' => $user['name'],
                            'email' => $user['email'],
                            'password' => $user['password'],
                            'town_id' => $town->id,
                            'organization_id' => $organization->id,
                            'user_division_id' => $division->id ?? null,
                            'email_verified_at' => now(),
                            'is_blocked' => 0,
                            'author_id' => 14,
                            'last_editor_id' => 14
                        ]);

                        $new_user->assignRole('reader');

                    } catch (\Exception $e) {
                        Log::error($e);
                    }
                }

            }
            return redirect()->back()->with('success', 'Старые пользователи из файла ' . $seeder_text_files_folder . $user_seeder_text_file_name . ' записаны в новую базу');

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }


    }

    protected function parse_spare_part_storages_from_text_file_to_new_base()
    {
        try {
            ini_set('max_execution_time', 180);

            $spare_part_storages = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::SPARE_PART_STORAGE_SEEDER_TEXT_FILE_NAME;

            foreach ($spare_part_storages as $spare_part_storage) {
                $trk = Trk::where('name', $spare_part_storage['trk'])->first();

                if (empty($trk->id)) {
                    $trk = Trk::create([
                        'name' => $spare_part_storage['trk'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $storage_name = StoreHouseName::where('name', $spare_part_storage['storage_name'])->first();

                if (empty($storage_name->id)) {
                    $storage_name = StoreHouseName::create([
                        'name' => $spare_part_storage['storage_name'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $spare_part_name = SparePartName::where('name', $spare_part_storage['spare_part_name'])->first();

                if (empty($spare_part_name->id)) {
                    $spare_part_name = SparePartName::create([
                        'name' => $spare_part_storage['spare_part_name'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $division = UserDivision::where('name', $spare_part_storage['contractor'])->first();

                $old_spare_part_storage = TrkStoreHouse::where('trk_id', $trk->id)
                    ->where('store_house_name_id', $storage_name->id)
                    ->where('spare_part_name_id', $spare_part_name->id)
                    ->where('user_division_id', $division->id)
                    ->where('spare_part_model', $spare_part_storage['spare_part_model'])
                    ->first();

                if (empty($old_spare_part_storage->id)) {
                    $old_spare_part_storage = TrkStoreHouse::create([
                        'trk_id' => $trk->id,
                        'store_house_name_id' => $storage_name->id,
                        'spare_part_name_id' => $spare_part_name->id,
                        'user_division_id' => $division->id,
                        'spare_part_model' => $spare_part_storage['spare_part_model'],
                        'value' => $spare_part_storage['amount'],
                        'min_required_value' => $spare_part_storage['required_minimum'],
                        'comment' => $spare_part_storage['comment'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

            }

            return redirect()->back()->with('success', 'Склады из файла в новую базу сохранены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось сохранить склады из файла в новую базу');

        }

    }

    protected function parse_spare_part_orders_from_text_file_to_new_base()
    {
        try {
            ini_set('max_execution_time', 180);

            $spare_part_orders = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::SPARE_PART_ORDER_SEEDER_TEXT_FILE_NAME;

            foreach ($spare_part_orders as $order) {

                if ($order['trk'] == 'Золотой Вавилон (Ясенево)') {
                    $order['trk'] = 'FORT Ясенево';
                }

                $trk = Trk::where('name', $order['trk'])->first();
                $room = Room::where('name', $order['room'])->first();

                if ($order['system'] == 'Газовая котельная') {
                    $order['system'] = 'Газовое оборудование';
                }

                $system = System::where('name', $order['system'])->first();

                $equipment = EquipmentName::where('name', $order['equipment'])->first();
                $spare_part = SparePartName::where('name', $order['spare_part'])->first();
                $division = UserDivision::where('name', $order['division'])->first();
                $user = User::where('name', $order['user'])->first();
                $order_status = OrderStatus::where('name', $order['order_status'])->first();

                if (empty($spare_part->id)) {
                    $spare_part = SparePartName::create([
                        'name' => $order['spare_part'],
                        'author_id' => $user->id,
                        'last_editor_id' => 14
                    ]);
                }

                if (empty($equipment->id)) {
                    $equipment = EquipmentName::create([
                        'name' => $order['equipment'],
                        'author_id' => $user->id,
                        'last_editor_id' => 14
                    ]);
                }

                if (empty($room->id)) {
                    $room = Room::create([
                        'name' => $order['room'],
                        'author_id' => $user->id,
                        'last_editor_id' => 14
                    ]);
                }

                $comment = Str::limit($order['comment'], 250);

                while (Str::contains($comment, "'")) {
                    $comment = Str::replace("'", '`', $comment);
                }

                $new_order = Order::create([
                    'trk_id' => $trk->id,
                    'system_id' => $system->id,
                    'room_name_id' => $room->id,
                    'equipment_name_id' => $equipment->id,
                    'order_status_id' => $order_status->id,
                    'user_division_id' => $division->id,
                    'is_urgency' => $order['is_urgency'] == 1 ? 0 : 1,
                    'comment' => $comment,
                    'account_number' => $order['account_number'],
                    'provider' => $order['provider'],
                    'delivery_at' => $order['delivery_at'] != '' ? $order['delivery_at'] : null,
                    'closed_at' => $order['closed_at'] != '' ? $order['closed_at'] : null,
                    'author_id' => $user->id,
                    'last_editor_id' => 14
                ]);

                OrderSparePart::create([
                    'order_id' => $new_order->id,
                    'spare_part_name_id' => $spare_part->id,
                    'value' => 1,
                    'author_id' => $user->id,
                    'last_editor_id' => 14
                ]);

            }

            return redirect()->back()->with('success', 'Заказы запчастей записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось записать заказы в новую базу');

        }
    }

    protected function parse_closed_applications_from_text_file_to_sql_file(string $skip, string $take)
    {
        try {
            ini_set('max_execution_time', 900);

            $new_base_folder = ParseController::NEW_BASE_CLOSED_APPLICATIONS_FOLDER;
            $avr_sql_file = ParseController::CLOSED_APPLICATIONS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT;
            $avr_equipment_sql_file = ParseController::CLOSED_APPLICATIONS_EQUIPMENTS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT;
            $avr_user_sql_file = ParseController::CLOSED_APPLICATIONS_USERS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT;
            $avr_work_sql_file = ParseController::CLOSED_APPLICATIONS_WORKS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT;

            if (file_exists($new_base_folder . $avr_sql_file)) {
                unlink($new_base_folder . $avr_sql_file);
            }

            if (file_exists($new_base_folder . $avr_equipment_sql_file)) {
                unlink($new_base_folder . $avr_equipment_sql_file);
            }

            if (file_exists($new_base_folder . $avr_user_sql_file)) {
                unlink($new_base_folder . $avr_user_sql_file);
            }

            if (file_exists($new_base_folder . $avr_work_sql_file)) {
                unlink($new_base_folder . $avr_work_sql_file);
            }

            $avrs = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CLOSED_APPLICATION_TEXT_FILES_FOLDER . ParseController::CLOSED_APPLICATION_TEXT_FILE . '_' . $skip . '_' . $take . ParseController::PHP_FILE_EXT;

            if (!is_dir($new_base_folder)) mkdir($new_base_folder);

            $data_string = "INSERT INTO `avrs` (`id`, `trk_room_id`, `system_id`, `date`, `comment`, `sort_order`, `author_id`, `last_editor_id`, `destroyer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES " . PHP_EOL;
            sleep(1);
            file_put_contents($new_base_folder . $avr_sql_file, $data_string, FILE_APPEND);
            $data_string = null;

            $avr_equipment_data_string = "INSERT INTO `avr_equipments` (`id`, `trk_equipment_id`, `avr_id`, `comment`, `sort_order`, `author_id`, `last_editor_id`, `destroyer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES " . PHP_EOL;
            sleep(1);
            file_put_contents($new_base_folder . $avr_equipment_sql_file, $avr_equipment_data_string, FILE_APPEND);
            $avr_equipment_data_string = null;

            $avr_executor_data_string = "INSERT INTO `avr_executors` (`id`, `avr_id`, `user_id`, `comment`, `sort_order`, `author_id`, `last_editor_id`, `destroyer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES " . PHP_EOL;
            sleep(1);
            file_put_contents($new_base_folder . $avr_user_sql_file, $avr_executor_data_string, FILE_APPEND);
            $avr_executor_data_string = null;

            $avr_work_data_string = "INSERT INTO `avr_works` (`id`, `avr_id`, `trk_equipment_id`, `work_name_id`, `description`, `comment`, `sort_order`, `author_id`, `last_editor_id`, `destroyer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES " . PHP_EOL;
            sleep(1);
            file_put_contents($new_base_folder . $avr_work_sql_file, $avr_work_data_string, FILE_APPEND);
            $avr_work_data_string = null;

            foreach ($avrs as $avr) {

                $equipment_name_id = EquipmentName::where('name', $avr['equipment'])->pluck('id')->first();

                if (!empty($equipment_name_id) && $avr['trk'] != 'Деловой Петербург') {

                    $trk = Trk::where('name', $avr['trk'])->first();
                    $building = Building::where('name', $avr['building'])->first();
                    $system = System::where('name', $avr['system'])->first();

                    if (empty($system->id)) {
                        $system = System::where('name', 'Вентиляция')->first();
                    }

                    if (empty($trk->id)) {
                        dd($avr['trk']);
                    }

                    $trk_room_ids = TrkRoom::where('trk_id', $trk->id)
                        ->where('building_id', $building->id)
                        ->pluck('id')
                        ->toArray();


                    $trk_room_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name_id)
                        ->where('system_id', $system->id)
                        ->first();

                    if (!empty($trk_room_equipment->id)) {
                        $user1 = null;
                        $user2 = null;
                        $user3 = null;
                        $user4 = null;

                        if ($avr['user1'] != '') {
                            $user1 = User::where('name', $avr['user1'])->first();

                            if (empty($user1->id)) {
                                $email = DB::table('old_old_user')->where('surname', $avr['user1'])->pluck('email')->first();

                                if (empty($email)) {
                                    dd('empty email ' . $avr['user1']);
                                }

                                $old_user = User::where('email', $email)->first();

                                if (empty($old_user->id)) {

                                    $user1 = User::create([
                                        'name' => $avr['user1'],
                                        'email' => $email,
                                        'password' => '$2y$10$9D1f4iD9DMlz4BCqg3UZrePbcgdOEVtqO7C6wu3pPu.vH7ug8K7ni',
                                        'is_blocked' => 1
                                    ]);
                                }
                            }
                        }

                        if ($avr['user2'] != '') {
                            $user2 = User::where('name', $avr['user2'])->first();

                            if (empty($user2->id)) {
                                $email = DB::table('old_old_user')->where('surname', $avr['user2'])->pluck('email')->first();

                                if (empty($email)) {
                                    dd('empty email ' . $avr['user2']);
                                }

                                $old_user = User::where('email', $email)->first();

                                if (empty($old_user->id)) {
                                    $user2 = User::create([
                                        'name' => $avr['user2'],
                                        'email' => $email,
                                        'password' => '$2y$10$9D1f4iD9DMlz4BCqg3UZrePbcgdOEVtqO7C6wu3pPu.vH7ug8K7ni',
                                        'is_blocked' => 1
                                    ]);
                                }
                            }
                        }

                        if ($avr['user3'] != '') {
                            $user3 = User::where('name', $avr['user3'])->first();

                            if (empty($user3->id)) {
                                $email = DB::table('old_old_user')->where('surname', $avr['user3'])->pluck('email')->first();

                                if (empty($email)) {
                                    dd('empty email ' . $avr['user3']);
                                }

                                $old_user = User::where('email', $email)->first();

                                if (empty($old_user->id)) {

                                    $user3 = User::create([
                                        'name' => $avr['user3'],
                                        'email' => $email,
                                        'password' => '$2y$10$9D1f4iD9DMlz4BCqg3UZrePbcgdOEVtqO7C6wu3pPu.vH7ug8K7ni',
                                        'is_blocked' => 1
                                    ]);
                                }
                            }
                        }

                        if ($avr['user4'] != '') {
                            $user4 = User::where('name', $avr['user4'])->first();

                            if (empty($user4->id)) {
                                $email = DB::table('old_old_user')->where('surname', $avr['user4'])->pluck('email')->first();

                                if (empty($email)) {
                                    dd('empty email ' . $avr['user4']);
                                }

                                $old_user = User::where('email', $email)->first();

                                if (empty($old_user->id)) {

                                    $user4 = User::create([
                                        'name' => $avr['user4'],
                                        'email' => $email,
                                        'password' => '$2y$10$9D1f4iD9DMlz4BCqg3UZrePbcgdOEVtqO7C6wu3pPu.vH7ug8K7ni',
                                        'is_blocked' => 1
                                    ]);
                                }
                            }
                        }

                        $avr_id = Str::uuid();
                        $date = $avr['date'];
                        $date_now = now();

                        $exists_avr = Avr::where('date', $date)
                            ->where('trk_room_id', $trk_room_equipment->trk_room->id)
                            ->where('system_id', $system->id)
                            ->first();

                        if (empty($exists_avr->id)) {
                            $trk_room_id = $trk_room_equipment->trk_room->id;

                            $data_string = "('$avr_id', '$trk_room_id', '$system->id', '$date', NULL, 1, 14,	14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;

                            if (!empty($user1)) {
                                $id = Str::uuid();
                                $avr_executor_data_string .= "( '$id', '$avr_id', '$user1->id', NULL, 1, 14,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;
                            }

                            if (!empty($user2)) {
                                $id = Str::uuid();
                                $avr_executor_data_string .= "( '$id', '$avr_id', '$user2->id', NULL, 1, 14,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;
                            }

                            if (!empty($user3)) {
                                $id = Str::uuid();
                                $avr_executor_data_string .= "( '$id', '$avr_id', '$user3->id', NULL, 1, 14,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;
                            }

                            if (!empty($user4)) {
                                $id = Str::uuid();
                                $avr_executor_data_string .= "( '$id', '$avr_id', '$user4->id', NULL, 1, 14,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;
                            }

                            $id = Str::uuid();
                            $avr_equipment_data_string .= "( '$id', '$trk_room_equipment->id', '$avr_id', NULL, 1, 14,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;

                            $avr_work_id = Str::uuid();

                            $work_name = WorkName::where('name', 'ТО 4')->first();

                            $avr_description = $avr['description'];
                            $avr_work_data_string .= "( '$avr_work_id', '$avr_id', '$trk_room_equipment->id', '$work_name->id', '$avr_description', NULL, 1, 14,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;

                        }

                    }

                }

                file_put_contents($new_base_folder . $avr_sql_file, $data_string, FILE_APPEND);
                file_put_contents($new_base_folder . $avr_equipment_sql_file, $avr_equipment_data_string, FILE_APPEND);
                file_put_contents($new_base_folder . $avr_work_sql_file, $avr_work_data_string, FILE_APPEND);
                file_put_contents($new_base_folder . $avr_user_sql_file, $avr_executor_data_string, FILE_APPEND);

                $data_string = null;
                $avr_work_data_string = null;
                $avr_equipment_data_string = null;
                $avr_executor_data_string = null;
            }

            $data = file_get_contents($new_base_folder . $avr_sql_file);
            $new_data = substr($data, 0, -3);
            $new_data .= ';';
            file_put_contents($new_base_folder . $avr_sql_file, $new_data);

            $avr_equipment_data = file_get_contents($new_base_folder . $avr_equipment_sql_file);
            $new_data = substr($avr_equipment_data, 0, -3);
            $new_data .= ';';
            file_put_contents($new_base_folder . $avr_equipment_sql_file, $new_data);

            $avr_user_data = file_get_contents($new_base_folder . $avr_user_sql_file);
            $new_data = substr($avr_user_data, 0, -3);
            $new_data .= ';';
            file_put_contents($new_base_folder . $avr_user_sql_file, $new_data);

            $avr_work_data = file_get_contents($new_base_folder . $avr_work_sql_file);
            $new_data = substr($avr_work_data, 0, -3);
            $new_data .= ';';
            file_put_contents($new_base_folder . $avr_work_sql_file, $new_data);

            return redirect()->back()->with('success', 'Акты выполненных ' . $skip . '-' . $take . ' работ из текстового файла в sql файл записаны');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось записать акты выполненных работ из текстового файла в sql файл');

        }
    }

    protected function parse_avrs_from_text_file_to_sql_file(string $skip, string $take)
    {
        try {

            ini_set('max_execution_time', 900);

            $new_base_folder = ParseController::NEW_BASE_FOLDER;
            $avr_sql_file = ParseController::AVR_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT;
            $avr_equipment_sql_file = ParseController::AVR_EQUIPMENTS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT;
            $avr_user_sql_file = ParseController::AVR_USERS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT;
            $avr_work_sql_file = ParseController::AVR_WORKS_SEEDER_SQL_FILE . '_' . $skip . '_' . $take . ParseController::SQL_FILE_EXT;

            if (file_exists($new_base_folder . $avr_sql_file)) {
                unlink($new_base_folder . $avr_sql_file);
            }

            if (file_exists($new_base_folder . $avr_equipment_sql_file)) {
                unlink($new_base_folder . $avr_equipment_sql_file);
            }

            if (file_exists($new_base_folder . $avr_user_sql_file)) {
                unlink($new_base_folder . $avr_user_sql_file);
            }

            if (file_exists($new_base_folder . $avr_work_sql_file)) {
                unlink($new_base_folder . $avr_work_sql_file);
            }

            $avrs = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::AVR_TEXT_FILES_FOLDER . ParseController::AVR_TEXT_FILE . '_' . $skip . '_' . $take . ParseController::PHP_FILE_EXT;

            if (!is_dir($new_base_folder)) mkdir($new_base_folder);

            $data_string = "INSERT INTO `avrs` (`id`, `trk_room_id`, `system_id`, `date`, `comment`, `sort_order`, `author_id`, `last_editor_id`, `destroyer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES " . PHP_EOL;
            sleep(1);
            file_put_contents($new_base_folder . $avr_sql_file, $data_string, FILE_APPEND);

            $avr_equipment_data_string = "INSERT INTO `avr_equipments` (`id`, `trk_equipment_id`, `avr_id`, `comment`, `sort_order`, `author_id`, `last_editor_id`, `destroyer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES " . PHP_EOL;
            sleep(1);
            file_put_contents($new_base_folder . $avr_equipment_sql_file, $avr_equipment_data_string, FILE_APPEND);
            $avr_equipment_data_string = null;

            $avr_executor_data_string = "INSERT INTO `avr_executors` (`id`, `avr_id`, `user_id`, `comment`, `sort_order`, `author_id`, `last_editor_id`, `destroyer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES " . PHP_EOL;
            sleep(1);
            file_put_contents($new_base_folder . $avr_user_sql_file, $avr_executor_data_string, FILE_APPEND);
            $avr_executor_data_string = null;

            $avr_work_data_string = "INSERT INTO `avr_works` (`id`, `avr_id`, `trk_equipment_id`, `work_name_id`, `description`, `comment`, `sort_order`, `author_id`, `last_editor_id`, `destroyer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES " . PHP_EOL;
            sleep(1);
            file_put_contents($new_base_folder . $avr_work_sql_file, $avr_work_data_string, FILE_APPEND);
            $avr_work_data_string = null;

            foreach ($avrs as $avr) {

                if ($avr['trk'] == 'Золотой Вавилон (Ясенево)') {
                    $avr['trk'] = 'FORT Ясенево';
                }

                $trk = Trk::where('name', $avr['trk'])->first();
                $building = Building::where('name', $avr['building'])->first();
                $floor = Floor::where('name', $avr['floor'])->first();
                $room = Room::where('name', $avr['room'])->first();
                $system = System::where('name', $avr['system'])->first();

                if (empty($floor->id)) {
                    $floor = Floor::create([
                        'name' => $avr['floor'],
                        'alias' => Str::slug($avr['building']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                if (empty($room->id)) {
                    $room = Room::create([
                        'name' => $avr['room'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $trk_room_id = TrkRoom::where('trk_id', $trk->id)
                    ->where('building_id', $building->id)
                    ->where('floor_id', $floor->id)
                    ->where('room_id', $room->id)
                    ->first();

                $user = User::where('name', $avr['user'])->first();

                $room_purpose = RoomPurpose::where('name', 'Техническое')->first();

                if (empty($trk_room_id->id)) {
                    $trk_room_id = TrkRoom::create([
                        'trk_id' => $trk->id,
                        'building_id' => $building->id,
                        'floor_id' => $floor->id,
                        'room_id' => $room->id,
                        'room_purpose_id' => $room_purpose->id,
                        'author_id' => $user->id,
                        'last_editor_id' => 14
                    ]);
                }

                $avr_users = DB::table('old_avr_user')->where('avr_id', $avr['id'])->get();
                $avr_equipments = DB::table('old_avr_equipment')->where('avr_id', $avr['id'])->get();

                if (!empty($trk_room_id->id)) {
                    try {

                        $avr_id = Str::uuid();
                        $date = $avr['date'];
                        $date_now = now();

                        $data_string = "('$avr_id', '$trk_room_id->id', '$system->id', '$date', NULL, 1, $user->id,	14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;

                        foreach ($avr_users as $avr_user) {

                            $old_user = DB::table('old_user')->where('id', $avr_user->user_id)->first();
                            $new_user = User::where('name', $old_user->surname)->first();

                            $id = Str::uuid();
                            $avr_executor_data_string .= "( '$id', '$avr_id', '$new_user->id', NULL, 1, $user->id,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;

                        }

                        foreach ($avr_equipments as $avr_equipment) {

                            $old_equipment_name = DB::table('old_equipment_name')->where('id', $avr_equipment->equipment_id)->first();

                            $equipment_name = $old_equipment_name->name;

                            if (Str::contains($equipment_name, 'CB-')
                                && !Str::contains($equipment_name, '/')) {
                                $equipment_name .= '/1';
                            }

                            $new_equipment = EquipmentName::where('name', $equipment_name)->first();

                            if (empty($new_equipment->id)) {
                                $new_equipment = EquipmentName::create([
                                    'name' => $equipment_name,
                                    'author_id' => 14,
                                    'last_editor_id' => 14
                                ]);
                            }

                            if (Str::contains($new_equipment->name, "П")
                                || Str::contains($new_equipment->name, "AHU-")
                                || Str::contains($new_equipment->name, "ВУ")
                                || Str::contains($new_equipment->name, "Вентиляция")
                                && $system->name == System::CHILLER
                            ) {
                                $system = System::where('name', System::AIR_RECYCLE)->first();
                            }

                            if (Str::contains($new_equipment->name, "фанк")
                                || Str::contains($new_equipment->name, "Фанк")
                                || Str::contains($new_equipment->name, "сплит")
                                || Str::contains($new_equipment->name, "VRF")
                                && $system->name == System::CHILLER
                            ) {
                                $system = System::where('name', System::AIR_CONDITION)->first();
                            }

                            $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room_id->id)
                                ->where('system_id', $system->id)
                                ->where('equipment_name_id', $new_equipment->id)
                                ->first();

                            $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();

                            if (empty($trk_equipment->id)) {
                                $trk_equipment = TrkEquipment::create([
                                    'trk_room_id' => $trk_room_id->id,
                                    'system_id' => $system->id,
                                    'equipment_name_id' => $new_equipment->id,
                                    'equipment_status_id' => $equipment_status->id,
                                    'author_id' => $user->id,
                                    'last_editor_id' => 14
                                ]);
                            }

                            $id = Str::uuid();
                            $avr_equipment_data_string .= "( '$id', '$trk_equipment->id', '$avr_id', NULL, 1, $user->id,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;

                            $avr_works = DB::table('old_avr_work_type')
                                ->where('avr_id', $avr['id'])
                                ->get();

                            $avr_description_write_flag = true;

                            foreach ($avr_works as $avr_work) {

                                $old_work = DB::table('old_work_type')->where('id', $avr_work->work_type_id)->first();

                                $old_work->name = preg_replace('| +|', ' ', $old_work->name);

                                $work_name = WorkName::where('name', $old_work->name)->first();

                                if (empty($work_name->id)) {

                                    $work_name = WorkName::create([
                                        'name' => $old_work->name,
                                        'author_id' => 14,
                                        'last_editor_id' => 14
                                    ]);
                                }

                                $avr_work_id = Str::uuid();

                                if ($avr_description_write_flag) {

                                    $avr_description = $avr['description'];
                                    $avr_work_data_string .= "( '$avr_work_id', '$avr_id', '$trk_equipment->id', '$work_name->id', '$avr_description', NULL, 1, $user->id,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;
                                    $avr_description_write_flag = false;

                                } else {

                                    $avr_work_data_string .= "( '$avr_work_id', '$avr_id', '$trk_equipment->id', '$work_name->id', NULL, NULL, 1, $user->id,  14,	NULL, '$date_now', '$date_now', NULL)," . PHP_EOL;
                                }
                            }
                        }

                        file_put_contents($new_base_folder . $avr_sql_file, $data_string, FILE_APPEND);
                        file_put_contents($new_base_folder . $avr_equipment_sql_file, $avr_equipment_data_string, FILE_APPEND);
                        file_put_contents($new_base_folder . $avr_user_sql_file, $avr_executor_data_string, FILE_APPEND);
                        file_put_contents($new_base_folder . $avr_work_sql_file, $avr_work_data_string, FILE_APPEND);

                        $avr_executor_data_string = null;
                        $avr_work_data_string = null;
                        $avr_equipment_data_string = null;

                    } catch (\Exception $e) {
                        Log::error($e);
                    }
                }
            }

            $data = file_get_contents($new_base_folder . $avr_sql_file);
            $new_data = substr($data, 0, -3);
            $new_data .= ';';
            file_put_contents($new_base_folder . $avr_sql_file, $new_data);

            $avr_equipment_data = file_get_contents($new_base_folder . $avr_equipment_sql_file);
            $new_data = substr($avr_equipment_data, 0, -3);
            $new_data .= ';';
            file_put_contents($new_base_folder . $avr_equipment_sql_file, $new_data);

            $avr_user_data = file_get_contents($new_base_folder . $avr_user_sql_file);
            $new_data = substr($avr_user_data, 0, -3);
            $new_data .= ';';
            file_put_contents($new_base_folder . $avr_user_sql_file, $new_data);

            $avr_work_data = file_get_contents($new_base_folder . $avr_work_sql_file);
            $new_data = substr($avr_work_data, 0, -3);
            $new_data .= ';';
            file_put_contents($new_base_folder . $avr_work_sql_file, $new_data);

            $from = $skip;
            $to = $skip + $take;

            return redirect()->back()->with('success', 'Акты выполненных ' . $from . '-' . $to . ' работ из текстового файла в sql файл записаны');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось записать акты выполненных работ из текстового файла в sql файл');

        }
    }

    protected function parse_repairs_from_text_file_to_new_base()
    {
        try {

            ini_set('max_execution_time', 900);

            $repairs = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::REPAIR_SEEDER_TEXT_FILE_NAME;

            foreach ($repairs as $repair) {

                if ($repair['trk'] == 'Золотой Вавилон (Ясенево)') {
                    $repair['trk'] = 'FORT Ясенево';
                }

                $trk = Trk::where('name', $repair['trk'])->first();

                $building = Building::where('name', $repair['building'])->first();

                if (empty($building->id)) {
                    $building = Building::where('name', 'Блок 1')->first();
                }

                $floor = Floor::where('name', $repair['floor'])->first();
                $room = Room::where('name', $repair['room'])->first();

                if ($repair['system'] == 'Газовая котельная') {
                    $repair['system'] = 'Газовое оборудование';
                }


                $system = System::where('name', $repair['system'])->first();
                $division = UserDivision::where('name', $repair['division'])->first();

                if (empty($floor->id)) {
                    $floor = Floor::create([
                        'name' => $repair['floor'],
                        'alias' => Str::slug($repair['building']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                if (empty($room->id)) {
                    $room = Room::create([
                        'name' => $repair['room'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $trk_room_id = TrkRoom::where('trk_id', $trk->id)
                    ->where('building_id', $building->id)
                    ->where('floor_id', $floor->id)
                    ->where('room_id', $room->id)
                    ->first();

                $user = User::where('name', $repair['user'])->first();

                $room_purpose = RoomPurpose::where('name', 'Техническое')->first();

                if (empty($trk_room_id->id)) {
                    $trk_room_id = TrkRoom::create([
                        'trk_id' => $trk->id,
                        'building_id' => $building->id,
                        'floor_id' => $floor->id,
                        'room_id' => $room->id,
                        'room_purpose_id' => $room_purpose->id,
                        'author_id' => $user->id,
                        'last_editor_id' => 14
                    ]);
                }


                if (!empty($trk_room_id->id)) {

                    $avr = DB::table('old_avr')->where('repair_id', $repair['id'])->first();

                    $new_equipment = EquipmentName::where('name', $repair['equipment'])->first();

                    if (empty($new_equipment->id)) {
                        $new_equipment = EquipmentName::create([
                            'name' => $repair['equipment'],
                            'author_id' => 14,
                            'last_editor_id' => 14
                        ]);
                    }

                    $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room_id->id)
                        ->where('system_id', $system->id)
                        ->where('equipment_name_id', $new_equipment->id)
                        ->first();

                    $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();

                    if (empty($trk_equipment->id)) {
                        $trk_equipment = TrkEquipment::create([
                            'trk_room_id' => $trk_room_id->id,
                            'system_id' => $system->id,
                            'equipment_name_id' => $new_equipment->id,
                            'equipment_status_id' => $equipment_status->id,
                            'author_id' => $user->id,
                            'last_editor_id' => 14
                        ]);
                    }

                    TrkRoomRepair::create([
                        'trk_room_id' => $trk_room_id->id,
                        'equipment_id' => $trk_equipment->id,
                        'user_division_id' => $division->id,
                        'description' => $repair['comment'],
                        'executed_at' => $repair['executed_at'] == '' ? null : $repair['executed_at'],
                        'deadline_at' => $repair['plan_at'] == '' ? null : $repair['plan_at'],
                        'executed_result' => $avr->description ?? null,
                        'done_progress' => $repair['executed_at'] == '' ? 0 : 100,
                        'system_id' => $system->id,
                        'author_id' => $user->id,
                        'last_editor_id' => 14
                    ]);


                }

            }
            return redirect()->back()->with('success', 'Ремонт записан в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось записать ремонт в новую базу');

        }
    }

    protected function parse_applications_from_text_file_to_new_base()
    {
        try {

            ini_set('max_execution_time', 900);

            $applications = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::APPLICATION_TEXT_FILE_NAME;

            foreach ($applications as $application) {

                if($application['status'] == 'новая') {

                    if ($application['trk'] == 'Золотой Вавилон (Ясенево)') {
                        $application['trk'] = 'FORT Ясенево';
                    }

                    $trk = Trk::where('name', $application['trk'])->first();

                    $division = UserDivision::where('name', $application['division'])->first();

                    if(empty($division->id))
                    {
                        dd('empty division ' . $application['division']);
                    }

                    $user = User::where('name', $application['user'])->first();

                    $old_application = OperationApplication::where('trk_id', $trk->id)
                        ->where('division_id', $division->id)
                        ->where('trouble_description', $application['comment'])
                        ->where('author_id', $user->id)
                        ->first();

                    if (empty($old_application->id)) {

                        $new_application = OperationApplication::create([
                            'trk_id' => $trk->id,
                            'division_id' => $division->id,
                            'trouble_description' => $application['comment'],
                            'done_percents' => 0,
                            'author_id' => $user->id,
                            'last_editor_id' => 14,
                            'created_at' => $application['date'],
                        ]);
                    }
                }

                if($application['status'] == 'в обработке (диагностика)') {

                    if ($application['trk'] == 'Золотой Вавилон (Ясенево)') {
                        $application['trk'] = 'FORT Ясенево';
                    }

                    $trk = Trk::where('name', $application['trk'])->first();

                    $division = UserDivision::where('name', $application['division'])->first();

                    if(empty($division->id))
                    {
                        dd('empty division ' . $application['division']);
                    }

                    $user = User::where('name', $application['user'])->first();
                    $received_user = User::where('name', $application['received_user'])->first();

                    $old_application = OperationApplication::where('trk_id', $trk->id)
                        ->where('division_id', $division->id)
                        ->where('trouble_description', $application['comment'])
                        ->where('author_id', $user->id)
                        ->first();

                    if (empty($old_application->id)) {

                        $new_application = OperationApplication::create([
                            'trk_id' => $trk->id,
                            'division_id' => $division->id,
                            'done_percents' => 0,
                            'trouble_description' => $application['comment'],
                            'author_id' => $user->id,
                            'last_editor_id' => $user->id,
                            'created_at' => $application['date'],
                        ]);

                        $new_application->update([
                            'done_percents' => 10,
                            'result_description' => 'Посмотрел заявку. Принял в обработку.',
                            'last_editor_id' => $received_user->id,
                        ]);

                        Executable::create([
                           'executor_id' => $received_user->id,
                           'executable_id' => $new_application->id,
                           'executable_type' => OperationApplication::class,
                            'created_at' => $application['received_date'],
                        ]);

                    }
                }

            }
            return redirect()->back()->with('success', 'Заявки записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось записать заявки в новую базу');

        }
    }

    protected function parse_rooms_and_renters_from_text_file_to_new_base()
    {

        try {
            $trk_rooms = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::ROOM_RENTER_SEEDER_TEXT_FILE_NAME;

            foreach ($trk_rooms as $trk_room) {

                $trk = Trk::where('name', $trk_room['trk'])->first();

                $building = Building::where('name', $trk_room['building'])->first();

                $user_id = Auth::id();

                if (isset($trk_room['user'])) {
                    $user_id = User::where('name', $trk_room['user'])->pluck('id')->first();
                }

                if (empty($building->id)) {
                    $building = Building::create([
                        'name' => $trk_room['building'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $floor = Floor::where('name', $trk_room['floor'])->first();

                if (empty($floor->id)) {
                    $floor = Floor::create([
                        'name' => $trk_room['floor'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $room = Room::where('name', $trk_room['room'])->first();

                if (empty($room->id)) {

                    $room = Room::create([
                        'name' => $trk_room['room'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);

                }

                if ($trk && $building && $floor && $room) {

                    $new_trk_room = TrkRoom::where('trk_id', $trk->id)
                        ->where('building_id', $building->id)
                        ->where('floor_id', $floor->id)
                        ->where('room_id', $room->id)
                        ->first();

                    if (empty($new_trk_room->id)) {

                        $room_purpose_id = RoomPurpose::where('name', 'Коммерческое')->pluck('id')->first();

                        $new_trk_room = TrkRoom::create([
                            'trk_id' => $trk->id,
                            'building_id' => $building->id,
                            'floor_id' => $floor->id,
                            'room_id' => $room->id,
                            'square' => $trk_room['square'],
                            'room_purpose_id' => $room_purpose_id,
                            'author_id' => $user_id,
                            'last_editor_id' => Auth::id(),
                        ]);

                    }

                    if (isset($trk_room['brand'])) {

                        $brand = Brand::where('name', $trk_room['brand'])->first();

                        if (empty($brand->id)) {
                            $brand = Brand::create([
                                'name' => $trk_room['brand'],
                                'author_id' => $user_id,
                                'last_editor_id' => 14
                            ]);
                        }

                        $organization = Organization::where('name', $trk_room['organization'])->first();

                        if (empty($organization->id)) {
                            $organization = Organization::create([
                                'name' => $trk_room['organization'],
                                'author_id' => $user_id,
                                'last_editor_id' => 14
                            ]);
                        }

                        $trk_room_renter = RenterTrkRoomBrand::where('trk_room_id', $new_trk_room->id)
                            ->where('brand_id', $trk_room['brand'])
                            ->where('organization_id', $trk_room['organization'])
                            ->first();

                        if (empty($trk_room_renter->id)) {

                            RenterTrkRoomBrand::create([
                                'trk_room_id' => $new_trk_room->id,
                                'brand_id' => $brand->id,
                                'organization_id' => $organization->id,
                                'author_id' => $user_id,
                                'last_editor_id' => Auth::id(),
                            ]);

                        }
                    }
                }
            }

            return redirect()->back()->with('success', 'Помещения из арендаторов записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Помещения из арендаторов не записаны в новую базу, ошибка');

        }
    }

    public function parse_daily_checking_rooms_from_text_file_to_new_base()
    {

        try {
            $trk_rooms = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::DAILY_CHECKING_ROOM_SEEDER_TEXT_FILE_NAME;

            foreach ($trk_rooms as $trk_room) {

                $trk = Trk::where('name', $trk_room['trk'])->first();

                if (empty($trk->id)) {
                    $trk = Trk::create([
                        'name' => $trk_room['trk'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $building = Building::where('name', $trk_room['building'])->first();

                if (empty($building->id)) {
                    $building = Building::create([
                        'name' => $trk_room['building'],
                        'alias' => Str::slug($trk_room['building']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $floor = Floor::where('name', $trk_room['floor'])->first();

                if (empty($floor->id)) {
                    $floor = Floor::create([
                        'name' => $trk_room['floor'],
                        'alias' => Str::slug($trk_room['floor']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $room = Room::where('name', $trk_room['room'])->first();

                if (empty($room->id)) {
                    $room = Room::create([
                        'name' => $trk_room['room'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $room_purpose = RoomPurpose::where('name', 'Техническое')->first();

                if (
                    !TrkRoom::where('trk_id', $trk->id)
                        ->where('building_id', $building->id)
                        ->where('floor_id', $floor->id)
                        ->where('room_purpose_id', $room_purpose->id)
                        ->where('room_id', $room->id)
                        ->exists()
                ) {
                    try {
                        TrkRoom::create([
                            'trk_id' => $trk->id,
                            'building_id' => $building->id,
                            'floor_id' => $floor->id,
                            'room_id' => $room->id,
                            'room_purpose_id' => $room_purpose->id,
                            'need_daily_checking' => 1,
                            'author_id' => 14,
                            'last_editor_id' => 14
                        ]);
                    } catch (\Exception $e) {
                        Log::error($e);
                        Log::info('Уже существует: ' . $trk->name . ', ' . $building->name . ', '
                            . $floor->name . ', ' . $room->name);
                        return redirect()->back()->with('error', $e);
                    }
                }

            }
            return redirect()->back()->with('success', 'Помещения с ежедневным обходом из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::DAILY_CHECKING_ROOM_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', $e);
        }

    }

    public function parse_counters_from_text_file_to_new_base()
    {
        try {
            $trk_counters = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::COUNTERS_TEXT_FILE_NAME;
            $counter_counts = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::COUNTER_COUNTS_TEXT_FILE_NAME;

            foreach ($trk_counters as $trk_counter) {

                $trk = Trk::where('name', $trk_counter['trk'])->first();
                $floor = Floor::where('name', $trk_counter['floor'])->first();
                $brand = Brand::where('name', $trk_counter['brand'])->first();
                $organization = Organization::where('name', $trk_counter['organization'])->first();
                $user = User::where('name', $trk_counter['user'])->first();

                if (empty($brand->id)) {
                    $brand = Brand::create([
                        'name' => $trk_counter['brand'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                if (empty($organization->id)) {
                    $organization = Organization::create([
                        'name' => $trk_counter['organization'],
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                $tariff_name_id = Tariff::where('name', $trk_counter['tariff'])->first();
                $counter_type_id = CounterType::where('name', $trk_counter['type'])->first();

                if (empty($floor->id)) {
                    $floor = Floor::create([
                        'name' => $trk_counter['floor'],
                        'alias' => Str::slug($trk_counter['floor']),
                        'author_id' => 14,
                        'last_editor_id' => 14
                    ]);
                }

                try {

                    $trk_room_counter = TrkRoomCounter::where('trk_id', $trk->id)
                        ->where('floor_id', $floor->id)
                        ->where('brand_id', $brand->id)
                        ->where('organization_id', $organization->id)
                        ->where('number', $trk_counter['counter_number'])
                        ->first();

                    if (empty($trk_room_counter->id)) {
                        $trk_room_counter = TrkRoomCounter::create([
                            'trk_id' => $trk->id,
                            'floor_id' => $floor->id,
                            'brand_id' => $brand->id,
                            'organization_id' => $organization->id,
                            'tariff_name_id' => $tariff_name_id->id,
                            'counter_type_id' => $counter_type_id->id,
                            'number' => $trk_counter['counter_number'],
                            'coefficient' => $trk_counter['coefficient'],
                            'mounted_at' => $trk_counter['date'],
                            'using_purpose' => $trk_counter['using_purposes'],
                            'comment' => $trk_counter['comment'],
                            'author_id' => $user->id,
                            'last_editor_id' => $user->id,
                        ]);

                    }

                    $counts = [];

                    foreach ($counter_counts as $counter_count) {
                        if ($counter_count['counter_id'] == $trk_counter['old_id']) {
                            $counts[] = $counter_count;
                        }
                    }

                    foreach ($counts as $count) {

                        $user = User::where('name', $count['user'])->first();

                        CounterCount::create([
                            'trk_room_counter_id' => $trk_room_counter->id,
                            'tariff' => $count['day_rate'],
                            'date' => $count['date'],
                            'count' => $count['period_finish_rate'],
                            'comment' => $count['comment'],
                            'author_id' => $user->id,
                            'last_editor_id' => $user->id,
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e);

                }

            }
            return redirect()->back()->with('success', 'Счетчики из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::COUNTERS_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_hvac_supply_machines_from_text_file_to_new_base()
    {
        try {
            $supply_air_checklists = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CHECKLIST_SUPPLY_AIR_SEEDER_TEXT_FILE_NAME;

            $system = System::where('name', System::AIR_RECYCLE)->first();
            $work_TO_4 = WorkName::where('name', WorkName::TO_4)->first();
            $work_complete_checklist = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

            foreach ($supply_air_checklists as $supply_air_checklist) {

                try {

                    if (!ChecklistAirSupply::where('created_at', $supply_air_checklist['date'] . ' 12:00:00')
                        ->where('trk_equipment_id', $supply_air_checklist['trk_equipment_id'])
                        ->exists()) {

                        $hot_water_pump_actual_current = $this->punctuationMarkService->setFromCommaToDot($supply_air_checklist['hot_water_pump_actual_current']);
                        $hot_water_pump_passport_current = $this->punctuationMarkService->setFromCommaToDot($supply_air_checklist['hot_water_pump_passport_current']);

                        $supply_engine_actual_current = $this->punctuationMarkService->setFromCommaToDot($supply_air_checklist['supply_engine_actual_current']);
                        $supply_engine_passport_current = $this->punctuationMarkService->setFromCommaToDot($supply_air_checklist['supply_engine_passport_current']);

                        $inlet_cold_water_t = $this->punctuationMarkService->setFromDashToNothing($supply_air_checklist['inlet_cold_water_t']);

                        $supply_air_dumper_open_percent = $this->punctuationMarkService->setFromDashToNothing($supply_air_checklist['supply_air_dumper_open_percent']);

                        $front_bearing_t = $this->getDataFromStringService->cutString($supply_air_checklist['front_bearing_t'], 2);

                        $checklist_air_supply = ChecklistAirSupply::create([
                            'trk_equipment_id' => $supply_air_checklist['trk_equipment_id'],
                            'outside_air_t' => $supply_air_checklist['outside_air_t'] == '' ? null : $supply_air_checklist['outside_air_t'],
                            'setpoint_air_t' => $supply_air_checklist['setpoint_air_t'] == '' ? null : $supply_air_checklist['setpoint_air_t'],
                            'supply_air_t' => $supply_air_checklist['supply_air_t'] == '' ? null : $supply_air_checklist['supply_air_t'],
                            'supply_engine_t' => $supply_air_checklist['supply_engine_t'] == '' ? null : $supply_air_checklist['supply_engine_t'],
                            'front_bearing_t' => $front_bearing_t == '' ? null : $front_bearing_t,
                            'supply_engine_actual_current' => $supply_engine_actual_current == '' ? null : $supply_engine_actual_current,
                            'supply_engine_passport_current' => $supply_engine_passport_current == '' ? null : $supply_engine_passport_current,
                            'supply_engine_actual_frequency' => $supply_air_checklist['supply_engine_actual_frequency'] == '' ? null : $supply_air_checklist['supply_engine_actual_frequency'],
                            'supply_engine_passport_frequency' => $supply_air_checklist['supply_engine_passport_frequency'] == '' ? null : $supply_air_checklist['supply_engine_passport_frequency'],
                            'supply_air_actual_rate' => $supply_air_checklist['supply_air_actual_rate'] == '' ? null : $supply_air_checklist['supply_air_actual_rate'],
                            'hot_water_valve_open_percent' => $supply_air_checklist['hot_water_valve_open_percent'] == '' ? null : $supply_air_checklist['hot_water_valve_open_percent'],
                            'inlet_hot_water_t' => $supply_air_checklist['inlet_hot_water_t'] == '' ? null : $supply_air_checklist['inlet_hot_water_t'],
                            'outlet_hot_water_t' => $supply_air_checklist['outlet_hot_water_t'] == '' ? null : $supply_air_checklist['outlet_hot_water_t'],
                            'cold_water_valve_open_percent' => $supply_air_checklist['cold_water_valve_open_percent'] == '' ? null : $supply_air_checklist['cold_water_valve_open_percent'],
                            'inlet_cold_water_t' => $inlet_cold_water_t == '' ? null : $inlet_cold_water_t,
                            'outlet_cold_water_t' => $supply_air_checklist['outlet_cold_water_t'] == '' ? null : $supply_air_checklist['outlet_cold_water_t'],
                            'supply_air_dumper_open_percent' => $supply_air_dumper_open_percent == '' ? null : $supply_air_dumper_open_percent,
                            'recycle_air_dumper_open_percent' => $supply_air_checklist['recycle_air_dumper_open_percent'] == '' ? null : $supply_air_checklist['recycle_air_dumper_open_percent'],
                            'hot_water_pump_actual_current' => $hot_water_pump_actual_current == '' ? null : $hot_water_pump_actual_current,
                            'hot_water_pump_passport_current' => $hot_water_pump_passport_current == '' ? null : $hot_water_pump_passport_current,
                            'glycol_pump_actual_current' => $supply_air_checklist['glycol_pump_actual_current'] == '' ? null : $supply_air_checklist['glycol_pump_actual_current'],
                            'glycol_pump_passport_current' => $supply_air_checklist['glycol_pump_passport_current'] == '' ? null : $supply_air_checklist['glycol_pump_passport_current'],
                            'created_at' => $supply_air_checklist['date'] . ' 12:00:00',
                            'author_id' => $supply_air_checklist['user_id'],
                            'last_editor_id' => Auth::id(),
                        ]);

                        $avr = Avr::where('date', $supply_air_checklist['date'])
                            ->where('trk_room_id', $supply_air_checklist['trk_room_id'])
                            ->where('system_id', $system->id)
                            ->first();

                        if (empty($avr->id)) {
                            $avr = Avr::create([
                                'trk_room_id' => $supply_air_checklist['trk_room_id'],
                                'system_id' => $system->id,
                                'date' => $supply_air_checklist['date'],
                                'author_id' => $supply_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr_equipment = AvrEquipment::where('avr_id', $avr->id)
                            ->where('trk_equipment_id', $supply_air_checklist['trk_equipment_id'])
                            ->first();

                        if (empty($avr_equipment->id)) {
                            $avr_equipment = AvrEquipment::create([
                                'trk_equipment_id' => $supply_air_checklist['trk_equipment_id'],
                                'avr_id' => $avr->id,
                                'author_id' => $supply_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr_works = AvrWork::where('avr_id', $avr->id)->where('trk_equipment_id', $supply_air_checklist['trk_equipment_id'])->get();

                        if (count($avr_works) == 0) {

                            AvrWork::create([
                                'avr_id' => $avr->id,
                                'work_name_id' => $work_TO_4->id,
                                'description' => null,
                                'author_id' => $supply_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                                'trk_equipment_id' => $supply_air_checklist['trk_equipment_id'],
                            ]);

                            AvrWork::create([
                                'avr_id' => $avr->id,
                                'work_name_id' => $work_complete_checklist->id,
                                'description' => null,
                                'author_id' => $supply_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                                'trk_equipment_id' => $supply_air_checklist['trk_equipment_id'],
                            ]);
                        }

                        $avr_executors = AvrExecutor::where('avr_id', $avr->id)->get();

                        if (count($avr_executors) == 0) {

                            AvrExecutor::create([
                                'avr_id' => $avr->id,
                                'user_id' => $supply_air_checklist['user_id'],
                                'author_id' => $supply_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        DocCommunication::create([
                            'from_id' => $checklist_air_supply->id,
                            'from_type' => get_class($checklist_air_supply),
                            'to_id' => $avr->id,
                            'to_type' => get_class($avr),
                            'author_id' => $supply_air_checklist['user_id'],
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());
                }
            }

            return redirect()->back()->with('success', 'Чеклисты приточных установок из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CHECKLIST_SUPPLY_AIR_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);
        }
    }

    public function parse_hvac_extract_machines_from_text_file_to_new_base()
    {

        try {
            $extract_air_checklists = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CHECKLIST_EXTRACT_AIR_SEEDER_TEXT_FILE_NAME;

            $system = System::where('name', System::AIR_RECYCLE)->first();
            $work_TO_4 = WorkName::where('name', WorkName::TO_4)->first();
            $work_complete_checklist = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

            foreach ($extract_air_checklists as $extract_air_checklist) {

                try {

                    if (!ChecklistAirExtract::where('created_at', $extract_air_checklist['date'] . ' 12:00:00')
                        ->where('trk_equipment_id', $extract_air_checklist['trk_equipment_id'])
                        ->exists()) {

                        $extract_engine_actual_current = $this->punctuationMarkService->setFromCommaToDot($extract_air_checklist['extract_engine_actual_current']);
                        $extract_engine_passport_current = $this->punctuationMarkService->setFromCommaToDot($extract_air_checklist['extract_engine_passport_current']);

                        $front_bearing_t = $this->getDataFromStringService->cutString($extract_air_checklist['front_bearing_t'], 2);

                        $checklist_air_extract = ChecklistAirExtract::create([
                            'trk_equipment_id' => $extract_air_checklist['trk_equipment_id'],
                            'extract_air_t' => $extract_air_checklist['extract_air_t'] == '' ? null : $extract_air_checklist['extract_air_t'],
                            'extract_engine_t' => $extract_air_checklist['extract_engine_t'] == '' ? null : $extract_air_checklist['extract_engine_t'],
                            'front_bearing_t' => $front_bearing_t == '' ? null : $front_bearing_t,
                            'extract_engine_actual_current' => $extract_engine_actual_current == '' ? null : $extract_engine_actual_current,
                            'extract_engine_passport_current' => $extract_engine_passport_current == '' ? null : $extract_engine_passport_current,
                            'extract_engine_actual_frequency' => $extract_air_checklist['extract_engine_actual_frequency'] == '' ? null : $extract_air_checklist['extract_engine_actual_frequency'],
                            'extract_engine_passport_frequency' => $extract_air_checklist['extract_engine_passport_frequency'] == '' ? null : $extract_air_checklist['extract_engine_passport_frequency'],
                            'extract_air_actual_rate' => $extract_air_checklist['extract_air_actual_rate'] == '' ? null : $extract_air_checklist['extract_air_actual_rate'],
                            'created_at' => $extract_air_checklist['date'] . ' 12:00:00',
                            'author_id' => $extract_air_checklist['user_id'],
                            'last_editor_id' => Auth::id(),
                        ]);

                        $avr = Avr::where('date', $extract_air_checklist['date'])
                            ->where('trk_room_id', $extract_air_checklist['trk_room_id'])
                            ->where('system_id', $system->id)
                            ->first();

                        if (empty($avr->id)) {
                            $avr = Avr::create([
                                'trk_room_id' => $extract_air_checklist['trk_room_id'],
                                'system_id' => $system->id,
                                'date' => $extract_air_checklist['date'],
                                'author_id' => $extract_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr_equipment = AvrEquipment::where('avr_id', $avr->id)
                            ->where('trk_equipment_id', $extract_air_checklist['trk_equipment_id'])
                            ->first();

                        if (empty($avr_equipment->id)) {
                            $avr_equipment = AvrEquipment::create([
                                'trk_equipment_id' => $extract_air_checklist['trk_equipment_id'],
                                'avr_id' => $avr->id,
                                'author_id' => $extract_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr_works = AvrWork::where('avr_id', $avr->id)->where('trk_equipment_id', $extract_air_checklist['trk_equipment_id'])->get();

                        if (count($avr_works) == 0) {

                            AvrWork::create([
                                'avr_id' => $avr->id,
                                'work_name_id' => $work_TO_4->id,
                                'description' => null,
                                'author_id' => $extract_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                                'trk_equipment_id' => $extract_air_checklist['trk_equipment_id'],
                            ]);

                            AvrWork::create([
                                'avr_id' => $avr->id,
                                'work_name_id' => $work_complete_checklist->id,
                                'description' => null,
                                'author_id' => $extract_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                                'trk_equipment_id' => $extract_air_checklist['trk_equipment_id'],
                            ]);
                        }

                        $avr_executors = AvrExecutor::where('avr_id', $avr->id)->get();

                        if (count($avr_executors) == 0) {

                            AvrExecutor::create([
                                'avr_id' => $avr->id,
                                'user_id' => $extract_air_checklist['user_id'],
                                'author_id' => $extract_air_checklist['user_id'],
                                'last_editor_id' => Auth::id(),
                            ]);

                        }

                        DocCommunication::create([
                            'from_id' => $checklist_air_extract->id,
                            'from_type' => get_class($checklist_air_extract),
                            'to_id' => $avr->id,
                            'to_type' => get_class($avr),
                            'author_id' => $extract_air_checklist['user_id'],
                            'last_editor_id' => Auth::id(),
                        ]);

                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Чеклисты вытяжных установок из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CHECKLIST_EXTRACT_AIR_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_hvac_room_climate_from_text_file_to_new_base()
    {

        try {
            $room_climate_checklists = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CHECKLIST_ROOM_CLIMATE_SEEDER_TEXT_FILE_NAME;

            foreach ($room_climate_checklists as $room_climate_checklist) {

                try {

                    if (!TrkRoomClimate::where('created_at', $room_climate_checklist['date'] . ' 12:00:00')
                        ->where('trk_room_id', $room_climate_checklist['trk_room_id'])
                        ->exists()) {

                        TrkRoomClimate::create([
                            'trk_room_id' => $room_climate_checklist['trk_room_id'],
                            't_outside' => $room_climate_checklist['t_outside'] == '' ? null : $room_climate_checklist['t_outside'],
                            't_inside' => $room_climate_checklist['t_inside'] == '' ? null : $room_climate_checklist['t_inside'],
                            'h_inside' => $room_climate_checklist['h_inside'] == '' ? null : $room_climate_checklist['h_inside'],
                            't_supply_air' => $room_climate_checklist['t_supply_air'] == '' ? null : $room_climate_checklist['t_supply_air'],
                            'q_supply_air_total' => $room_climate_checklist['q_supply_air_total'] == '' ? null : $room_climate_checklist['q_supply_air_total'],
                            'q_extract_air_total' => $room_climate_checklist['q_extract_air_total'] == '' ? null : $room_climate_checklist['q_extract_air_total'],
                            'comment' => $room_climate_checklist['comment'] == '' ? null : $room_climate_checklist['comment'],
                            'created_at' => $room_climate_checklist['date'] . ' 12:00:00',
                            'author_id' => $room_climate_checklist['user_id'],
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Чеклисты климата в помещении из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CHECKLIST_ROOM_CLIMATE_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }


    public function parse_equipments_from_text_file_to_new_base()
    {

        try {
            $equipments = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_SEEDER_TEXT_FILE_NAME;

            foreach ($equipments as $equipment) {

                $trk = Trk::where('name', $equipment['trk'])->first();

                $system = System::where('name', $equipment['system'])->first();

                if (empty($system->id)) {

                    $system = System::create([
                        'name' => $equipment['system'],
                        'author_id' => 14,
                        'last_editor_id' => 14,
                    ]);

                }

                $building = Building::where('name', $equipment['building'])->first();

                if (empty($building->id)) {
                    $building = Building::create([
                        'name' => $equipment['building'],
                        'author_id' => 14,
                        'last_editor_id' => 14,
                    ]);
                }

                $floor = Floor::where('name', $equipment['floor'])->first();

                if (empty($floor->id)) {
                    $floor = Floor::create([
                        'name' => $equipment['floor'],
                        'author_id' => 14,
                        'last_editor_id' => 14,
                    ]);
                }

                $axis = Axe::where('name', $equipment['axis'])->first();

                if (empty($axis->id) && $equipment['axis'] != '') {
                    $axis = Axe::create([
                        'name' => $equipment['axis'],
                        'author_id' => 14,
                        'last_editor_id' => 14,
                    ]);
                }

                $room = Room::where('name', $equipment['room'])->first();

                if (empty($room->id)) {

                    $room = Room::create([
                        'name' => $equipment['room'],
                        'author_id' => 14,
                        'last_editor_id' => 14,
                    ]);

                }

                $equipment_name = EquipmentName::where('name', $equipment['equipment'])->first();

                if (empty($equipment_name->id)) {

                    $equipment_name = EquipmentName::create([
                        'name' => $equipment['equipment'],
                        'author_id' => 14,
                        'last_editor_id' => 14,
                    ]);

                }


                if ($trk && $building && $floor && $room) {
                    $new_trk_room = TrkRoom::where('trk_id', $trk->id)
                        ->where('building_id', $building->id)
                        ->where('floor_id', $floor->id)
                        ->where('room_id', $room->id)
                        ->first();

                    if (empty($new_trk_room->id)) {

                        $room_purpose_id = RoomPurpose::where('name', 'Техническое')->pluck('id')->first();

                        $new_trk_room = TrkRoom::create([
                            'trk_id' => $trk->id,
                            'building_id' => $building->id,
                            'floor_id' => $floor->id,
                            'room_id' => $room->id,
                            'square' => $trk_room['square'] ?? '0.00',
                            'room_purpose_id' => $room_purpose_id,
                            'author_id' => 14,
                            'last_editor_id' => 14,
                        ]);
                    }

                    if (isset($equipment['equipment'])) {

                        $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();

                        if (Str::contains($equipment_name->name, "П")
                            || Str::contains($equipment_name->name, "AHU-")
                            || Str::contains($equipment_name->name, "ВУ")
                            || Str::contains($equipment_name->name, "Вентиляция")
                            && $system->name == System::CHILLER
                        ) {
                            $system = System::where('name', System::AIR_RECYCLE)->first();
                        }

                        if (Str::contains($equipment_name->name, "фанк")
                            || Str::contains($equipment_name->name, "Фанк")
                            || Str::contains($equipment_name->name, "сплит")
                            || Str::contains($equipment_name->name, "VRF")
                            && $system->name == System::CHILLER
                        ) {
                            $system = System::where('name', System::AIR_CONDITION)->first();
                        }

                        if (
                            $system
                            && $equipment_name
                            && !TrkEquipment::where('trk_room_id', $new_trk_room->id)
                                ->where('equipment_name_id', $equipment_name->id)
                                ->where('system_id', $system->id)
                                ->exists()
                        ) {

                            $new_trk_room_equipment = TrkEquipment::create([
                                'trk_room_id' => $new_trk_room->id,
                                'system_id' => $system->id,
                                'equipment_name_id' => $equipment_name->id,
                                'equipment_status_id' => $equipment_status->id,
                                'axis_id' => $axis->id ?? null,
                                'comment' => Str::limit($equipment['comment'], 250),
                                'author_id' => 14,
                                'last_editor_id' => 14,
                            ]);

                            if (
                                System::AIR_CONDITION == $system->name
                                && $floor->name != Floor::ROOF
                                && $floor->name != Floor::ROOF_PLUS
                            ) {
                                if (
                                    !EquipmentUser::where('trk_room_id', $new_trk_room_equipment->trk_room_id)
                                        ->where('equipment_id', $new_trk_room_equipment->id)
                                        ->exists()
                                ) {
                                    EquipmentUser::create([
                                        'trk_room_id' => $new_trk_room_equipment->trk_room_id,
                                        'equipment_id' => $new_trk_room_equipment->id,
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            return redirect()->back()->with('success', 'Оборудование из текстового файла в новую базу сохранено');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', 'Не удалось сохранить оборудование из текстового файла в новую базу');

        }

    }

    public function parse_equipment_users_from_text_file_to_new_base()
    {
        ini_set('max_execution_time', 540);

        try {

            $equipment_users = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_USERS_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_users as $equipment_user) {

                try {

                    $system = System::where('name', $equipment_user['system'])->first();
                    $equipment_name = EquipmentName::where('name', $equipment_user['equipment_name'])->first();
                    $trk_room = TrkRoom::find($equipment_user['trk_room_id']);

                    if (empty($equipment_name->id)) {

                        $equipment_name = EquipmentName::create([
                            'name' => $equipment_user['equipment_name'],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                    $trk_equipment = TrkEquipment::where('trk_room_id', $trk_room->id)
                        ->where('system_id', $system->id)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    if(!empty($trk_equipment->id)) {

                        $equipment_user = EquipmentUser::firstOrNew([
                            'trk_room_id' => $trk_room->id,
                            'equipment_id' => $trk_equipment->id
                        ]);

                        $equipment_user->trk_room_id = $trk_room->id;
                        $equipment_user->equipment_id = $trk_equipment->id;
                        $equipment_user->author_id = Auth::id();
                        $equipment_user->last_editor_id = Auth::id();

                        $equipment_user->save();
                    }
                    // TODO парсер, ко всем кондиционерам привязать помещения где они висят
                    // кроме уровня Кровля (там в основном стоят наружные блоки)
                    // кроме помещения Отсутствует
                    // помещения пользователи для кондиционеров,
                    // если кондиционер висит в помещении, то это помещение пользователь кондиционера
//                    $system = System::where('name', System::AIR_CONDITION)->first();
//
//                    $floor_name_ids = Floor::where('name', 'like', 'Кровля' . '%')->pluck('id')->toArray();
//
//                    $room_name_ids = Room::where('name', 'like', 'Отсутствует' . '%')
//                        ->orWhere('name', 'like', 'Кровля' . '%')
//                        ->pluck('id')
//                        ->toArray();
//
//                    $trk_room_ids = TrkRoom::whereNotIn('floor_id', $floor_name_ids)
//                        ->whereNotIn('room_id', $room_name_ids)
//                        ->pluck('id')
//                        ->toArray();
//
//                    $trk_equipments = TrkEquipment::where('system_id', $system->id)
//                        ->whereNotIn('trk_room_id', $trk_room_ids)
//                        ->get();
//
//                    foreach($trk_equipments as $trk_equipment)
//                    {
//                        $equipment_user = EquipmentUser::where('trk_room_id', $trk_equipment->trk_room_id)
//                            ->where('equipment_id', $trk_equipment->id)
//                            ->first();
//
//                        if(empty($equipment_user->id))
//                        {
//                            EquipmentUser::create([
//                                'trk_room_id' => $trk_equipment->trk_room_id,
//                                'equipment_id' => $trk_equipment->id,
//                                'author_id' => Auth::id(),
//                                'last_editor_id' => Auth::id(),
//                            ]);
//                        }
//
//                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());
                }
            }

            return redirect()->back()->with('success', 'Потребители оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_USERS_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_balk_checklists_from_text_file_to_new_base()
    {
        try {

            ini_set('max_execution_time', 540);

            $balk_checklists = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CONDITION_CHECKLIST_SEEDER_TEXT_FILE_NAME;

            $system = System::where('name', System::AIR_CONDITION)->first();
            $work_TO_4 = WorkName::where('name', WorkName::TO_4)->first();
            $work_TO_5 = WorkName::where('name', WorkName::TO_5)->first();
            $work_complete_checklist = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

            $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();

            foreach ($balk_checklists as $balk_checklist) {

                try {

                    if ($balk_checklist['checklist_type'] == ParseController::BALK) {

                        $user = User::where('name', $balk_checklist['user'])->first();

                        $hvac = DB::table('old_hvac')->where('id', $balk_checklist['hvac_id'])->first();
                        $room = DB::table('old_room')->where('id', $hvac->room_id)->first();
                        $trk = DB::table('old_trk')->where('id', $hvac->trk_id)->first();
                        $building = DB::table('old_building')->where('id', $hvac->building_id)->first();
                        $floor = DB::table('old_floor')->where('id', $hvac->floor_id)->first();

                        $trk = Trk::where('name', $trk->name)->first();
                        $room = Room::where('name', $room->name)->first();
                        $building = Building::where('name', $building->name)->first();
                        $floor = Floor::where('name', $floor->name)->first();

                        $trk_room = TrkRoom::where('trk_id', $trk->id)
                            ->where('building_id', $building->id)
                            ->where('floor_id', $floor->id)
                            ->where('room_id', $room->id)
                            ->first();

                        $number = $balk_checklist['number'] == '' ? 1 : $balk_checklist['number'];

                        $name = 'CB-' . $room->name . '/' . $number;

                        $balk_name = EquipmentName::where('name', $name)->first();

                        if (empty($balk_name->id)) {

                            $balk_name = EquipmentName::create([
                                'name' => $name,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $balk = TrkEquipment::where('trk_room_id', $trk_room->id)
                            ->where('system_id', $system->id)
                            ->where('equipment_name_id', $balk_name->id)
                            ->first();

                        if (empty($balk->id)) {

                            $balk = TrkEquipment::create([
                                'trk_room_id' => $trk_room->id,
                                'system_id' => $system->id,
                                'equipment_name_id' => $balk_name->id,
                                'equipment_status_id' => $equipment_status->id,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        if (!EquipmentUser::where('trk_room_id', $trk_room->id)
                            ->where('equipment_id', $balk->id)
                            ->exists())
                        {

                            EquipmentUser::create([
                                'trk_room_id' => $trk_room->id,
                                'equipment_id' => $balk->id,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr = Avr::where('date', $balk_checklist['date'])
                            ->where('trk_room_id', $trk_room->id)
                            ->where('system_id', $system->id)
                            ->first();

                        if (
                            !empty($avr->id)
                            && !AvrEquipment::where('trk_equipment_id', $balk->id)
                                ->where('avr_id', $avr->id)
                                ->exists()
                        ) {
                            AvrEquipment::create([
                                'trk_equipment_id' => $balk->id,
                                'avr_id' => $avr->id,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        if (
                            !empty($avr->id)
                            && !AvrWork::where('trk_equipment_id', $balk->id)
                                ->where('avr_id', $avr->id)
                                ->exists()
                        )
                        {
                            $works = AvrWork::where('avr_id', $avr->id)->get();

                            foreach ($works as $work) {

                                if (
                                    !AvrWork::where('trk_equipment_id', $balk->id)
                                    ->where('avr_id', $avr->id)
                                    ->where('work_name_id', $work->work_name_id)
                                    ->exists()
                                )
                                {
                                    AvrWork::create([
                                        'trk_equipment_id' => $balk->id,
                                        'avr_id' => $avr->id,
                                        'work_name_id' => $work->work_name_id,
                                        'author_id' => Auth::id(),
                                        'last_editor_id' => Auth::id(),
                                    ]);
                                }
                            }
                        }

                        $cold_water_outlet_temperature = $balk_checklist['t_cold_water_output'] == 157 ? 15 : $balk_checklist['t_cold_water_output'];
                        $cold_water_outlet_temperature = $cold_water_outlet_temperature == 115 ? 15 : $cold_water_outlet_temperature;
                        $cold_water_outlet_temperature = $cold_water_outlet_temperature == 9999 ? 15 : $cold_water_outlet_temperature;
                        $cold_water_outlet_temperature = $cold_water_outlet_temperature == 166 ? 15 : $cold_water_outlet_temperature;
                        $cold_water_outlet_temperature = $cold_water_outlet_temperature == 162 ? 15 : $cold_water_outlet_temperature;
                        $cold_water_outlet_temperature = $cold_water_outlet_temperature == '' ? null : $cold_water_outlet_temperature;

                        $cold_water_inlet_temperature = $balk_checklist['t_cold_water_input'] == 116 ? 16 : $balk_checklist['t_cold_water_input'];
                        $cold_water_inlet_temperature = $cold_water_inlet_temperature == '' ? null : $cold_water_inlet_temperature;

                        if(!ChecklistBalk::where('trk_room_id', $trk_room->id)
                            ->where('trk_equipment_id', $balk->id)
                            ->where('balk_number', $balk_checklist['number'] == '' ? 1 : $balk_checklist['number'])
                            ->where('created_at', $balk_checklist['date'] . ' 12:00:00')
                            ->exists()
                        )
                        {
                            $checklist_balk = ChecklistBalk::create([
                                'trk_room_id' => $trk_room->id,
                                'trk_equipment_id' => $balk->id,
                                'balk_number' => $balk_checklist['number'] == '' ? 1 : $balk_checklist['number'],
                                'balk_size_type' => $balk_checklist['standard_size'] == '' ? 300 : $balk_checklist['standard_size'],
                                'air_speed' => $balk_checklist['air_speed'] == '' ? null : $balk_checklist['air_speed'],
                                'air_flow_rate' => $balk_checklist['air_rate'] == '' ? null : $balk_checklist['air_rate'],
                                'air_pressure' => $balk_checklist['air_pressure'] == '' ? null : $balk_checklist['air_pressure'],
                                'air_inlet_temperature' => $balk_checklist['air_temperature'] == '' ? null : $balk_checklist['air_temperature'],
                                'air_outlet_temperature' => $balk_checklist['air_temperature_output'] == '' ? null : $balk_checklist['air_temperature_output'],
                                'air_flap' => $balk_checklist['air_valve_setting'] == '' ? null : $balk_checklist['air_valve_setting'],
                                'cold_water_inlet_temperature' => $cold_water_inlet_temperature,
                                'cold_water_outlet_temperature' => $cold_water_outlet_temperature,
                                'cold_water_pressure_drop' => $balk_checklist['p_delta_cold_water'] == '' ? null : $balk_checklist['p_delta_cold_water'],
                                'cold_water_valve' => $balk_checklist['cold_water_valve_setting'] == '' ? null : $balk_checklist['cold_water_valve_setting'],
                                'cold_water_rate' => $balk_checklist['cold_water_rate'] == '' ? null : $balk_checklist['cold_water_rate'],
                                'comment' => $balk_checklist['comment'],
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                                'created_at' => $balk_checklist['date'] . ' 12:00:00',
                            ]);

                            $avr = Avr::where('date', $balk_checklist['date'])
                                ->where('trk_room_id', $checklist_balk->trk_room_id)
                                ->where('system_id', $system->id)
                                ->first();

                            if (empty($avr->id)) {

                                $avr = Avr::create([
                                    'trk_room_id' => $checklist_balk->trk_room_id,
                                    'system_id' => $system->id,
                                    'date' => $balk_checklist['date'],
                                    'author_id' => $user->id,
                                    'last_editor_id' => Auth::id(),
                                ]);
                            }

                            $avr_equipment = AvrEquipment::where('avr_id', $avr->id)
                                ->where('trk_equipment_id', $checklist_balk->trk_equipment_id)
                                ->first();

                            if (empty($avr_equipment->id)) {

                                $avr_equipment = AvrEquipment::create([
                                    'trk_equipment_id' => $checklist_balk->trk_equipment_id,
                                    'avr_id' => $avr->id,
                                    'author_id' => $user->id,
                                    'last_editor_id' => Auth::id(),
                                ]);
                            }

                            $TO_5_is_present = false;

                            if (Str::contains($balk_checklist['comment'], 'ТО 5') || Str::contains($balk_checklist['comment'], 'ТО5')) {
                                $TO_5_is_present = true;
                            }

                            $new_avr_work_checklist_complete = AvrWork::where('avr_id', $avr->id)
                                ->where('work_name_id', $work_complete_checklist->id)
                                ->where('trk_equipment_id', $checklist_balk->trk_equipment_id)
                                ->first();

                            if (empty($new_avr_work_checklist_complete->id)) {

                                AvrWork::create([
                                    'avr_id' => $avr->id,
                                    'work_name_id' => $work_complete_checklist->id,
                                    'description' => null,
                                    'author_id' => $user->id,
                                    'last_editor_id' => Auth::id(),
                                    'trk_equipment_id' => $checklist_balk->trk_equipment_id,
                                ]);
                            }

                            $new_avr_work_to_4 = AvrWork::where('avr_id', $avr->id)
                                ->where('work_name_id', $work_TO_4->id)
                                ->where('trk_equipment_id', $checklist_balk->trk_equipment_id)
                                ->first();

                            if (empty($new_avr_work_to_4->id)) {

                                AvrWork::create([
                                    'avr_id' => $avr->id,
                                    'work_name_id' => $work_TO_4->id,
                                    'description' => null,
                                    'author_id' => $user->id,
                                    'last_editor_id' => Auth::id(),
                                    'trk_equipment_id' => $checklist_balk->trk_equipment_id,
                                ]);

                                if ($TO_5_is_present) {

                                    AvrWork::create([
                                        'avr_id' => $avr->id,
                                        'work_name_id' => $work_TO_5->id,
                                        'description' => null,
                                        'author_id' => $user->id,
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $checklist_balk->trk_equipment_id,
                                    ]);

                                }

                            }

                            $new_avr_work_to_5 = AvrWork::where('avr_id', $avr->id)
                                ->where('work_name_id', $work_TO_5->id)
                                ->where('trk_equipment_id', $checklist_balk->trk_equipment_id)
                                ->first();

                            if (empty($new_avr_work_to_5->id)) {

                                if ($TO_5_is_present) {

                                    AvrWork::create([
                                        'avr_id' => $avr->id,
                                        'work_name_id' => $work_TO_5->id,
                                        'description' => null,
                                        'author_id' => $user->id,
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $checklist_balk->trk_equipment_id,
                                    ]);

                                    if(!AvrWork::where('avr_id', $avr->id)
                                        ->where('work_name_id', $work_TO_4->id)
                                        ->where('trk_equipment_id', $checklist_balk->trk_equipment_id)
                                        ->exists())
                                    {
                                    AvrWork::create([
                                        'avr_id' => $avr->id,
                                        'work_name_id' => $work_TO_4->id,
                                        'description' => null,
                                        'author_id' => $user->id,
                                        'last_editor_id' => Auth::id(),
                                        'trk_equipment_id' => $checklist_balk->trk_equipment_id,
                                    ]);
                                    }

                                }
                            }

                            $avr_executors = AvrExecutor::where('avr_id', $avr->id)->get();

                            if (count($avr_executors) == 0) {

                                AvrExecutor::create([
                                    'avr_id' => $avr->id,
                                    'user_id' => $user->id,
                                    'author_id' => $user->id,
                                    'last_editor_id' => Auth::id(),
                                ]);

                            }

                            DocCommunication::create([
                                'from_id' => $checklist_balk->id,
                                'from_type' => get_class($checklist_balk),
                                'to_id' => $avr->id,
                                'to_type' => get_class($avr),
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error');

                }
            }

            return redirect()->back()->with('success', 'Чеклисты балок из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CONDITION_CHECKLIST_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_diffuser_checklists_from_text_file_to_new_base()
    {
        try {

            $diffuser_checklists = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CONDITION_CHECKLIST_SEEDER_TEXT_FILE_NAME;

            $system = System::where('name', System::AIR_RECYCLE)->first();
            $work_TO_4 = WorkName::where('name', WorkName::TO_4)->first();
            $work_complete_checklist = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

            $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();

            foreach ($diffuser_checklists as $diffuser_checklist) {

                try {

                    if (
                        $diffuser_checklist['checklist_type'] == ParseController::EXTRACT_DIFFUSER
                        || $diffuser_checklist['checklist_type'] == ParseController::SUPPLY_DIFFUSER
                    ) {

                        $air_direction_type = $diffuser_checklist['checklist_type'] == ParseController::SUPPLY_DIFFUSER ? 0 : 1;

                        $user = User::where('name', $diffuser_checklist['user'])->first();

                        $avr_checklist = DB::table('old_avr_checklist')
                            ->where('checklist_id', $diffuser_checklist['old_checklist_id'])
                            ->where('checklist_type_id', 1)
                            ->first();

                        $avr = DB::table('old_avr_equipment')
                            ->where('avr_id', $avr_checklist->avr_id)
                            ->first();

                        $name = DB::table('old_equipment_name')->where('id', $avr->equipment_id)->first();
                        $equipment_name = EquipmentName::where('name', $name->name)->first();

                        $hvac = DB::table('old_hvac')->where('id', $diffuser_checklist['hvac_id'])->first();

                        $trk = DB::table('old_trk')->where('id', $hvac->trk_id)->first();
                        $building = DB::table('old_building')->where('id', $hvac->building_id)->first();
                        $floor = DB::table('old_floor')->where('id', $hvac->floor_id)->first();
                        $room = DB::table('old_room')->where('id', $hvac->room_id)->first();

                        $new_trk = Trk::where('name', $trk->name)->first();
                        $new_building = Building::where('name', $building->name)->first();
                        $new_floor = Floor::where('name', $floor->name)->first();
                        $new_room = Room::where('name', $room->name)->first();

                        if (empty($new_room->id)) {

                            $new_room = Room::create([
                                'name' => $room->name,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                        $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                            ->where('system_id', $system->id)
                            ->where('equipment_name_id', $equipment_name->id)
                            ->first();

                        $trk_room = TrkRoom::where('trk_id', $new_trk->id)
                            ->where('building_id', $new_building->id)
                            ->where('floor_id', $new_floor->id)
                            ->where('room_id', $new_room->id)
                            ->first();

                        if (empty($trk_room->id)) {

                            $trk_room = TrkRoom::create([
                                'trk_id' => $new_trk->id,
                                'building_id' => $new_building->id,
                                'floor_id' => $new_floor->id,
                                'room_id' => $new_room->id,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $checklist_diffuser = ChecklistAirDiffuser::create([
                            'trk_room_id' => $trk_room->id,
                            'trk_equipment_id' => $trk_equipment->id,
                            'air_direction_type' => $air_direction_type,
                            'measuring_point_number' => $diffuser_checklist['number'] == '' ? 1 : $diffuser_checklist['number'],
                            'length_or_diameter' => null,
                            'width' => null,
                            'diffuser_cross_sectional_area' => $diffuser_checklist['cross_size'] == '' ? null : $diffuser_checklist['cross_size'],
                            'air_speed' => $diffuser_checklist['air_speed'] == '' ? null : $diffuser_checklist['air_speed'],
                            'estimated_coefficient' => 1,
                            'air_flow_rate' => $diffuser_checklist['air_rate'] == '' ? null : $diffuser_checklist['air_rate'],
                            'air_pressure' => $diffuser_checklist['air_pressure'] == '' ? null : $diffuser_checklist['air_pressure'],
                            'air_temperature' => $diffuser_checklist['air_temperature'] == '' ? null : $diffuser_checklist['air_temperature'],
                            'air_throttling_valve' => $diffuser_checklist['air_valve_setting'] == '' ? null : $diffuser_checklist['air_valve_setting'],
                            'comment' => $diffuser_checklist['comment'],
                            'author_id' => $user->id,
                            'last_editor_id' => Auth::id(),
                            'created_at' => $diffuser_checklist['date'] . ' 12:00:00',
                        ]);

                        $avr = Avr::where('date', $diffuser_checklist['date'])
                            ->where('trk_room_id', $checklist_diffuser->trk_room_id)
                            ->where('system_id', $system->id)
                            ->first();

                        if (empty($avr->id)) {

                            $avr = Avr::create([
                                'trk_room_id' => $checklist_diffuser->trk_room_id,
                                'system_id' => $system->id,
                                'date' => $diffuser_checklist['date'],
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr_equipment = AvrEquipment::where('avr_id', $avr->id)
                            ->where('trk_equipment_id', $checklist_diffuser->trk_equipment_id)
                            ->first();

                        if (empty($avr_equipment->id)) {
                            $avr_equipment = AvrEquipment::create([
                                'trk_equipment_id' => $checklist_diffuser->trk_equipment_id,
                                'avr_id' => $avr->id,
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr_works = AvrWork::where('avr_id', $avr->id)->where('trk_equipment_id', $checklist_diffuser->trk_equipment_id)->get();

                        if (count($avr_works) == 0) {

                            AvrWork::create([
                                'avr_id' => $avr->id,
                                'work_name_id' => $work_TO_4->id,
                                'description' => null,
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                                'trk_equipment_id' => $checklist_diffuser->trk_equipment_id,
                            ]);

                            AvrWork::create([
                                'avr_id' => $avr->id,
                                'work_name_id' => $work_complete_checklist->id,
                                'description' => null,
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                                'trk_equipment_id' => $checklist_diffuser->trk_equipment_id,
                            ]);
                        }

                        $avr_executors = AvrExecutor::where('avr_id', $avr->id)->get();

                        if (count($avr_executors) == 0) {

                            AvrExecutor::create([
                                'avr_id' => $avr->id,
                                'user_id' => $user->id,
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                            ]);

                        }

                        DocCommunication::create([
                            'from_id' => $checklist_diffuser->id,
                            'from_type' => get_class($checklist_diffuser),
                            'to_id' => $avr->id,
                            'to_type' => get_class($avr),
                            'author_id' => $user->id,
                            'last_editor_id' => Auth::id(),
                        ]);

                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e);

                }

            }
            return redirect()->back()->with('success', 'Чеклисты диффузоров из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CONDITION_CHECKLIST_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_duct_checklists_from_text_file_to_new_base()
    {
        try {

            $duct_checklists = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CONDITION_CHECKLIST_SEEDER_TEXT_FILE_NAME;

            $system = System::where('name', System::AIR_RECYCLE)->first();
            $work_TO_4 = WorkName::where('name', WorkName::TO_4)->first();
            $work_complete_checklist = WorkName::where('name', WorkName::COMPLETE_CHECKLIST)->first();

            $equipment_status = EquipmentStatus::where('name', 'В эксплуатации')->first();

            foreach ($duct_checklists as $duct_checklist) {

                try {

                    if (
                        $duct_checklist['checklist_type'] == ParseController::EXTRACT_CHANNEL
                        || $duct_checklist['checklist_type'] == ParseController::SUPPLY_CHANNEL
                    ) {

                        $air_direction_type = $duct_checklist['checklist_type'] == ParseController::SUPPLY_DIFFUSER ? 0 : 1;

                        $user = User::where('name', $duct_checklist['user'])->first();

                        $avr_checklist = DB::table('old_avr_checklist')
                            ->where('checklist_id', $duct_checklist['old_checklist_id'])
                            ->where('checklist_type_id', 1)
                            ->first();

                        $avr = DB::table('old_avr_equipment')
                            ->where('avr_id', $avr_checklist->avr_id)
                            ->first();

                        $name = DB::table('old_equipment_name')->where('id', $avr->equipment_id)->first();
                        $equipment_name = EquipmentName::where('name', $name->name)->first();

                        $hvac = DB::table('old_hvac')->where('id', $duct_checklist['hvac_id'])->first();

                        $trk = DB::table('old_trk')->where('id', $hvac->trk_id)->first();
                        $building = DB::table('old_building')->where('id', $hvac->building_id)->first();
                        $floor = DB::table('old_floor')->where('id', $hvac->floor_id)->first();
                        $room = DB::table('old_room')->where('id', $hvac->room_id)->first();

                        $new_trk = Trk::where('name', $trk->name)->first();
                        $new_building = Building::where('name', $building->name)->first();
                        $new_floor = Floor::where('name', $floor->name)->first();
                        $new_room = Room::where('name', $room->name)->first();

                        if (empty($new_room->id)) {
                            $new_room = Room::create([
                                'name' => $room->name,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                        $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                            ->where('system_id', $system->id)
                            ->where('equipment_name_id', $equipment_name->id)
                            ->first();

                        $trk_room = TrkRoom::where('trk_id', $new_trk->id)
                            ->where('building_id', $new_building->id)
                            ->where('floor_id', $new_floor->id)
                            ->where('room_id', $new_room->id)
                            ->first();

                        if (empty($trk_room->id)) {
                            $trk_room = TrkRoom::create([
                                'trk_id' => $new_trk->id,
                                'building_id' => $new_building->id,
                                'floor_id' => $new_floor->id,
                                'room_id' => $new_room->id,
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $checklist_air_duct = ChecklistAirDuct::create([
                            'trk_room_id' => $trk_room->id,
                            'trk_equipment_id' => $trk_equipment->id,
                            'air_direction_type' => $air_direction_type,
                            'measuring_point_number' => $duct_checklist['number'] == '' ? 1 : $duct_checklist['number'],
                            'length_or_diameter' => null,
                            'width' => null,
                            'duct_cross_sectional_area' => $duct_checklist['cross_size'] == '' ? null : $duct_checklist['cross_size'],
                            'air_speed' => $duct_checklist['air_speed'] == '' ? null : $duct_checklist['air_speed'],
                            'air_flow_rate' => $duct_checklist['air_rate'] == '' ? null : $duct_checklist['air_rate'],
                            'air_pressure' => $duct_checklist['air_pressure'] == '' ? null : $duct_checklist['air_pressure'],
                            'air_temperature' => $duct_checklist['air_temperature'] == '' ? null : $duct_checklist['air_temperature'],
                            'air_throttling_valve' => $duct_checklist['air_valve_setting'] == '' ? null : $duct_checklist['air_valve_setting'],
                            'comment' => $duct_checklist['comment'],
                            'author_id' => $user->id,
                            'last_editor_id' => Auth::id(),
                            'created_at' => $duct_checklist['date'] . ' 12:00:00',
                        ]);

                        $avr = Avr::where('date', $checklist_air_duct['date'])
                            ->where('trk_room_id', $checklist_air_duct->trk_room_id)
                            ->where('system_id', $system->id)
                            ->first();

                        if (empty($avr->id)) {
                            $avr = Avr::create([
                                'trk_room_id' => $checklist_air_duct->trk_room_id,
                                'system_id' => $system->id,
                                'date' => $duct_checklist['date'],
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr_equipment = AvrEquipment::where('avr_id', $avr->id)
                            ->where('trk_equipment_id', $checklist_air_duct->trk_equipment_id)
                            ->first();

                        if (empty($avr_equipment->id)) {
                            $avr_equipment = AvrEquipment::create([
                                'trk_equipment_id' => $checklist_air_duct->trk_equipment_id,
                                'avr_id' => $avr->id,
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                            ]);
                        }

                        $avr_works = AvrWork::where('avr_id', $avr->id)->where('trk_equipment_id', $checklist_air_duct->trk_equipment_id)->get();

                        if (count($avr_works) == 0) {

                            AvrWork::create([
                                'avr_id' => $avr->id,
                                'work_name_id' => $work_TO_4->id,
                                'description' => null,
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                                'trk_equipment_id' => $checklist_air_duct->trk_equipment_id,
                            ]);

                            AvrWork::create([
                                'avr_id' => $avr->id,
                                'work_name_id' => $work_complete_checklist->id,
                                'description' => null,
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                                'trk_equipment_id' => $checklist_air_duct->trk_equipment_id,
                            ]);
                        }

                        $avr_executors = AvrExecutor::where('avr_id', $avr->id)->get();

                        if (count($avr_executors) == 0) {

                            AvrExecutor::create([
                                'avr_id' => $avr->id,
                                'user_id' => $user->id,
                                'author_id' => $user->id,
                                'last_editor_id' => Auth::id(),
                            ]);

                        }

                        DocCommunication::create([
                            'from_id' => $checklist_air_duct->id,
                            'from_type' => get_class($checklist_air_duct),
                            'to_id' => $avr->id,
                            'to_type' => get_class($avr),
                            'author_id' => $user->id,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e);
                }
            }

            return redirect()->back()->with('success', 'Чеклисты воздуховодов из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::CONDITION_CHECKLIST_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_bearings_from_text_file_to_new_base()
    {
        try {

            $equipment_bearings = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_BEARINGS_SEEDER_TEXT_FILE_NAME;

            $system = System::where('name', 'Вентиляция')->first();

            $spare_part_name = SparePartName::where('name', 'Подшипник')->first();

            foreach ($equipment_bearings as $equipment_bearing) {

                try {

                    $new_trk = Trk::where('name', $equipment_bearing['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_bearing['equipment'])->first();

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('system_id', $system->id)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    $comment = null;

                    if ($equipment_bearing['supply_fan']) {
                        $comment = 'Вентилятор притока';
                    }

                    if ($equipment_bearing['supply_engine']) {
                        $comment = 'Двигатель притока';
                    }

                    if ($equipment_bearing['extract_fan']) {
                        $comment = 'Вентилятор вытяжки';
                    }

                    if ($equipment_bearing['extract_engine']) {
                        $comment = 'Двигатель вытяжки';
                    }

                    if (!EquipmentSparePart::where('equipment_id', $trk_equipment->id)
                        ->where('spare_part_id', $spare_part_name->id)
                        ->where('model', $equipment_bearing['bearing'])
                        ->where('value', $equipment_bearing['amount'])
                        ->where('comment', $comment)
                        ->exists()
                    ) {
                        EquipmentSparePart::create([
                            'equipment_id' => $trk_equipment->id,
                            'spare_part_id' => $spare_part_name->id,
                            'model' => $equipment_bearing['bearing'],
                            'value' => $equipment_bearing['amount'],
                            'comment' => $comment,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Подшипники оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_BEARINGS_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_air_filters_from_text_file_to_new_base()
    {
        try {

            $equipment_air_filters = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_AIR_FILTERS_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_air_filters as $equipment_air_filter) {

                try {

                    $spare_part_name = SparePartName::where('name', 'Фильтр воздушный ' . $equipment_air_filter['type'])->first();

                    if (empty($spare_part_name->id)) {
                        $spare_part_name = SparePartName::create([
                            'name' => 'Фильтр воздушный ' . $equipment_air_filter['type'],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                    $new_trk = Trk::where('name', $equipment_air_filter['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_air_filter['equipment'])->first();

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    $comment = null;

                    if ($equipment_air_filter['supply']) {
                        $comment = 'приток';
                    }

                    if ($equipment_air_filter['extract']) {
                        $comment = 'вытяжка';
                    }

                    if ($equipment_air_filter['recuperator']) {
                        $comment = 'рекуператор';
                    }

                    if (!EquipmentSparePart::where('equipment_id', $trk_equipment->id)
                        ->where('spare_part_id', $spare_part_name->id)
                        ->where('model', $equipment_air_filter['filter'])
                        ->where('value', $equipment_air_filter['amount'])
                        ->where('comment', $comment)
                        ->exists()
                    ) {
                        EquipmentSparePart::create([
                            'equipment_id' => $trk_equipment->id,
                            'spare_part_id' => $spare_part_name->id,
                            'model' => $equipment_air_filter['filter'],
                            'value' => $equipment_air_filter['amount'],
                            'comment' => $comment,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Фильтры оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_AIR_FILTERS_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_drivebelts_from_text_file_to_new_base()
    {
        try {

            $equipment_air_filters = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_DRIVEBELTS_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_air_filters as $equipment_air_filter) {

                try {

                    $spare_part_name = SparePartName::where('name', 'Ремень приводной')->first();

                    $new_trk = Trk::where('name', $equipment_air_filter['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_air_filter['equipment'])->first();

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    $comment = null;

                    if ($equipment_air_filter['supply']) {
                        $comment = 'приток';
                    }

                    if ($equipment_air_filter['extract']) {
                        $comment = 'вытяжка';
                    }

                    if ($equipment_air_filter['recuperator']) {
                        $comment = 'рекуператор';
                    }

                    if (!EquipmentSparePart::where('equipment_id', $trk_equipment->id)
                        ->where('spare_part_id', $spare_part_name->id)
                        ->where('model', $equipment_air_filter['drivebelt'])
                        ->where('value', $equipment_air_filter['amount'])
                        ->where('comment', $comment)
                        ->exists()
                    ) {
                        EquipmentSparePart::create([
                            'equipment_id' => $trk_equipment->id,
                            'spare_part_id' => $spare_part_name->id,
                            'model' => $equipment_air_filter['drivebelt'],
                            'value' => $equipment_air_filter['amount'],
                            'comment' => $comment,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Приводные ремни оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_DRIVEBELTS_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_electro_motors_from_text_file_to_new_base()
    {
        try {

            $equipment_electro_motors = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_ELECTRO_MOTORS_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_electro_motors as $equipment_electro_motor) {

                try {

                    $spare_part_name = SparePartName::where('name', 'Электродвигатель')->first();

                    $new_trk = Trk::where('name', $equipment_electro_motor['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_electro_motor['equipment'])->first();

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    $comment = null;

                    if ($equipment_electro_motor['supply']) {
                        $comment = 'приток';
                    }

                    if ($equipment_electro_motor['extract']) {
                        $comment = 'вытяжка';
                    }

                    if ($equipment_electro_motor['recuperator']) {
                        $comment = 'рекуператор';
                    }

                    if (!EquipmentSparePart::where('equipment_id', $trk_equipment->id)
                        ->where('spare_part_id', $spare_part_name->id)
                        ->where('model', $equipment_electro_motor['electro_motor'])
                        ->where('value', $equipment_electro_motor['amount'])
                        ->where('comment', $comment)
                        ->exists()
                    ) {
                        EquipmentSparePart::create([
                            'equipment_id' => $trk_equipment->id,
                            'spare_part_id' => $spare_part_name->id,
                            'model' => $equipment_electro_motor['electro_motor'],
                            'value' => $equipment_electro_motor['amount'],
                            'comment' => $comment,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Электродвигатели оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_ELECTRO_MOTORS_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_pulleis_from_text_file_to_new_base()
    {
        try {

            $equipment_pullies = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_PULLIES_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_pullies as $equipment_pulley) {

                try {

                    $spare_part_name = SparePartName::where('name', 'Шкив')->first();

                    $new_trk = Trk::where('name', $equipment_pulley['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_pulley['equipment'])->first();

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    $comment = null;

                    if ($equipment_pulley['supply_fan']) {
                        $comment = 'Вентилятор притока';
                    }

                    if ($equipment_pulley['extract_fan']) {
                        $comment = 'Вентилятор вытяжки';
                    }

                    if ($equipment_pulley['supply_engine']) {
                        $comment = 'Двигатель притока';
                    }

                    if ($equipment_pulley['extract_engine']) {
                        $comment = 'Двигатель вытяжки';
                    }

                    if (!EquipmentSparePart::where('equipment_id', $trk_equipment->id)
                        ->where('spare_part_id', $spare_part_name->id)
                        ->where('model', $equipment_pulley['pulley'])
                        ->where('value', 1)
                        ->where('comment', $comment)
                        ->exists()
                    ) {
                        EquipmentSparePart::create([
                            'equipment_id' => $trk_equipment->id,
                            'spare_part_id' => $spare_part_name->id,
                            'model' => $equipment_pulley['pulley'],
                            'value' => 1,
                            'comment' => $comment,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Шкивы оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_PULLIES_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_taperbashes_from_text_file_to_new_base()
    {
        try {

            $equipment_pumpes = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_TAPERBASHES_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_pumpes as $equipment_pump) {

                try {

                    $spare_part_name = SparePartName::where('name', 'Тапербуш')->first();

                    $new_trk = Trk::where('name', $equipment_pump['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_pump['equipment'])->first();

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    $comment = null;

                    if ($equipment_pump['supply_fan']) {
                        $comment = 'Вентилятор притока';
                    }

                    if ($equipment_pump['extract_fan']) {
                        $comment = 'Вентилятор вытяжки';
                    }

                    if ($equipment_pump['supply_engine']) {
                        $comment = 'Двигатель притока';
                    }

                    if ($equipment_pump['extract_engine']) {
                        $comment = 'Двигатель вытяжки';
                    }

                    if (!EquipmentSparePart::where('equipment_id', $trk_equipment->id)
                        ->where('spare_part_id', $spare_part_name->id)
                        ->where('model', $equipment_pump['taperbash'])
                        ->where('value', 1)
                        ->where('comment', $comment)
                        ->exists()
                    ) {
                        EquipmentSparePart::create([
                            'equipment_id' => $trk_equipment->id,
                            'spare_part_id' => $spare_part_name->id,
                            'model' => $equipment_pump['taperbash'],
                            'value' => 1,
                            'comment' => $comment,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Тапербаши оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_TAPERBASHES_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_pumps_from_text_file_to_new_base()
    {
        try {

            $equipment_pumps = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_PUMPS_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_pumps as $equipment_pump) {

                try {

                    $spare_part_name = SparePartName::where('name', 'Насос')->first();

                    $new_trk = Trk::where('name', $equipment_pump['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_pump['equipment'])->first();

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    $comment = null;

                    if ($equipment_pump['hot_water']) {
                        $comment = 'вода';
                    }

                    if ($equipment_pump['glycol']) {
                        $comment = 'гликоль';
                    }

                    if (!EquipmentSparePart::where('equipment_id', $trk_equipment->id)
                        ->where('spare_part_id', $spare_part_name->id)
                        ->where('model', $equipment_pump['pump'])
                        ->where('value', 1)
                        ->where('comment', $comment)
                        ->exists()
                    ) {
                        EquipmentSparePart::create([
                            'equipment_id' => $trk_equipment->id,
                            'spare_part_id' => $spare_part_name->id,
                            'model' => $equipment_pump['pump'],
                            'value' => 1,
                            'comment' => $comment,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Насосы оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_PUMPS_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_valve_actuators_from_text_file_to_new_base()
    {
        try {

            $equipment_valve_actuators = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_VALVE_ACTUATORS_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_valve_actuators as $equipment_valve_actuator) {

                try {

                    $spare_part_name = SparePartName::where('name', 'Привод клапана трехходового')->first();

                    $new_trk = Trk::where('name', $equipment_valve_actuator['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_valve_actuator['equipment'])->first();

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();

                    $comment = null;

                    if ($equipment_valve_actuator['hot_water']) {
                        $comment = 'гвс';
                    }

                    if ($equipment_valve_actuator['cold_water']) {
                        $comment = 'хвс';
                    }

                    if (!EquipmentSparePart::where('equipment_id', $trk_equipment->id)
                        ->where('spare_part_id', $spare_part_name->id)
                        ->where('model', $equipment_valve_actuator['actuator'])
                        ->where('value', 1)
                        ->where('comment', $comment)
                        ->exists()
                    ) {
                        EquipmentSparePart::create([
                            'equipment_id' => $trk_equipment->id,
                            'spare_part_id' => $spare_part_name->id,
                            'model' => $equipment_valve_actuator['actuator'],
                            'value' => 1,
                            'comment' => $comment,
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Приводы оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_VALVE_ACTUATORS_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_equipment_rates_from_text_file_to_new_base()
    {
        try {

            $equipment_rates = include ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_RATES_SEEDER_TEXT_FILE_NAME;

            foreach ($equipment_rates as $equipment_rate) {

                try {

                    $new_trk = Trk::where('name', $equipment_rate['trk'])->first();

                    $trk_room_ids = TrkRoom::where('trk_id', $new_trk->id)->pluck('id')->toArray();

                    $equipment_name = EquipmentName::where('name', $equipment_rate['equipment'])->first();

                    if (empty($equipment_name->id)) {
                        $equipment_name = EquipmentName::create([
                            'name' => $equipment_rate['equipment'],
                            'author_id' => Auth::id(),
                            'last_editor_id' => Auth::id(),
                        ]);
                    }

                    $trk_equipment = TrkEquipment::whereIn('trk_room_id', $trk_room_ids)
                        ->where('equipment_name_id', $equipment_name->id)
                        ->first();


                    if ($equipment_rate['supply_air_rate'] > 0 && !empty($trk_equipment->id)) {
                        $parameter_name = ParameterName::where('name', ParseController::SUPPLY_AIR_RATE_FACT)->first();

                        if (!EquipmentParameter::where('equipment_id', $trk_equipment->id)
                            ->where('parameter_name_id', $parameter_name->id)
                            ->where('value', $equipment_rate['supply_air_rate'])
                            ->exists()
                        ) {
                            EquipmentParameter::create([
                                'equipment_id' => $trk_equipment->id,
                                'parameter_name_id' => $parameter_name->id,
                                'value' => $equipment_rate['supply_air_rate'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                    if ($equipment_rate['project_supply_air_rate'] > 0 && !empty($trk_equipment->id)) {
                        $parameter_name = ParameterName::where('name', ParseController::SUPPLY_AIR_RATE_PASSPORT)->first();

                        if (!EquipmentParameter::where('equipment_id', $trk_equipment->id)
                            ->where('parameter_name_id', $parameter_name->id)
                            ->where('value', $equipment_rate['project_supply_air_rate'])
                            ->exists()
                        ) {
                            EquipmentParameter::create([
                                'equipment_id' => $trk_equipment->id,
                                'parameter_name_id' => $parameter_name->id,
                                'value' => $equipment_rate['project_supply_air_rate'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                    if ($equipment_rate['extract_air_rate'] > 0 && !empty($trk_equipment->id)) {
                        $parameter_name = ParameterName::where('name', ParseController::EXTRACT_AIR_RATE_FACT)->first();

                        if (!EquipmentParameter::where('equipment_id', $trk_equipment->id)
                            ->where('parameter_name_id', $parameter_name->id)
                            ->where('value', $equipment_rate['extract_air_rate'])
                            ->exists()
                        ) {
                            EquipmentParameter::create([
                                'equipment_id' => $trk_equipment->id,
                                'parameter_name_id' => $parameter_name->id,
                                'value' => $equipment_rate['extract_air_rate'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                    if ($equipment_rate['project_extract_air_rate'] > 0 && !empty($trk_equipment->id)) {
                        $parameter_name = ParameterName::where('name', ParseController::EXTRACT_AIR_RATE_PASSPORT)->first();

                        if (!EquipmentParameter::where('equipment_id', $trk_equipment->id)
                            ->where('parameter_name_id', $parameter_name->id)
                            ->where('value', $equipment_rate['project_extract_air_rate'])
                            ->exists()
                        ) {
                            EquipmentParameter::create([
                                'equipment_id' => $trk_equipment->id,
                                'parameter_name_id' => $parameter_name->id,
                                'value' => $equipment_rate['project_extract_air_rate'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                    if ($equipment_rate['supply_hot_water_rate'] > 0 && !empty($trk_equipment->id)) {
                        $parameter_name = ParameterName::where('name', ParseController::HOT_WATER_RATE_FACT)->first();

                        if (!EquipmentParameter::where('equipment_id', $trk_equipment->id)
                            ->where('parameter_name_id', $parameter_name->id)
                            ->where('value', $equipment_rate['supply_hot_water_rate'])
                            ->exists()
                        ) {
                            EquipmentParameter::create([
                                'equipment_id' => $trk_equipment->id,
                                'parameter_name_id' => $parameter_name->id,
                                'value' => $equipment_rate['supply_hot_water_rate'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                    if ($equipment_rate['project_supply_hot_water_rate'] > 0 && !empty($trk_equipment->id)) {
                        $parameter_name = ParameterName::where('name', ParseController::HOT_WATER_RATE_PASSPORT)->first();

                        if (!EquipmentParameter::where('equipment_id', $trk_equipment->id)
                            ->where('parameter_name_id', $parameter_name->id)
                            ->where('value', $equipment_rate['project_supply_hot_water_rate'])
                            ->exists()
                        ) {
                            EquipmentParameter::create([
                                'equipment_id' => $trk_equipment->id,
                                'parameter_name_id' => $parameter_name->id,
                                'value' => $equipment_rate['project_supply_hot_water_rate'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                    if ($equipment_rate['supply_cold_water_rate'] > 0 && !empty($trk_equipment->id)) {
                        $parameter_name = ParameterName::where('name', ParseController::COLD_WATER_RATE_FACT)->first();

                        if (!EquipmentParameter::where('equipment_id', $trk_equipment->id)
                            ->where('parameter_name_id', $parameter_name->id)
                            ->where('value', $equipment_rate['supply_cold_water_rate'])
                            ->exists()
                        ) {
                            EquipmentParameter::create([
                                'equipment_id' => $trk_equipment->id,
                                'parameter_name_id' => $parameter_name->id,
                                'value' => $equipment_rate['supply_cold_water_rate'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                    if ($equipment_rate['project_supply_cold_water_rate'] > 0 && !empty($trk_equipment->id)) {
                        $parameter_name = ParameterName::where('name', ParseController::COLD_WATER_RATE_PASSPORT)->first();

                        if (!EquipmentParameter::where('equipment_id', $trk_equipment->id)
                            ->where('parameter_name_id', $parameter_name->id)
                            ->where('value', $equipment_rate['project_supply_cold_water_rate'])
                            ->exists()
                        ) {
                            EquipmentParameter::create([
                                'equipment_id' => $trk_equipment->id,
                                'parameter_name_id' => $parameter_name->id,
                                'value' => $equipment_rate['project_supply_cold_water_rate'],
                                'author_id' => Auth::id(),
                                'last_editor_id' => Auth::id(),
                            ]);
                        }
                    }

                } catch (\Exception $e) {

                    Log::error($e);
                    return redirect()->back()->with('error', $e->getMessage());

                }

            }
            return redirect()->back()->with('success', 'Расходы оборудования из файла ' . ParseController::SEEDER_TEXT_FILE_FOLDER . ParseController::EQUIPMENT_RATES_SEEDER_TEXT_FILE_NAME . ' записаны в новую базу');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function delete_old_tables_from_new_base()
    {
        try {

            $old_base_folder = ParseController::OLD_BASE_FOLDER;

            $files = array_diff(scandir($old_base_folder), array('.', '..'));

            foreach ($files as $file) {

                $table_full_name = str_replace('u331692824_xvo3_table', 'old', $file);

                $table_name = substr($table_full_name, 0, -4);

                Schema::dropIfExists($table_name);
            }

            return redirect()->back()->with('success', 'Старые таблицы с префиксом old_ удалены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function delete_old_old_tables_from_new_base()
    {
        try {

            $old_base_folder = ParseController::OLD_OLD_BASE_FOLDER;

            $files = array_diff(scandir($old_base_folder), array('.', '..'));

            foreach ($files as $file) {

                $table_name = substr($file, 0, -4);

                Schema::dropIfExists($table_name);

            }

            return redirect()->back()->with('success', 'Старые таблицы с префиксом old_old_ удалены');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_equipment_period_works_air_filters()
    {
        try {

            $work_name_ids = WorkName::where('name', 'like', 'замен' . '%' . 'фильтр' . '%')
                ->whereNot('name', 'like', '%' . 'ТО-4' . '%')
                ->whereNot('name', 'like', '%' . 'ТО4' . '%')
                ->whereNot('name', 'like', '%' . 'осушит' . '%')
                ->whereNot('name', 'like', '%' . 'холод' . '%')
                ->whereNot('name', 'like', '%' . 'труб' . '%')
                ->whereNot('name', 'like', '%' . 'теплообмен' . '%')
                ->whereNot('name', 'like', '%' . 'поверхност' . '%')
                ->whereNot('name', 'like', '%' . 'проверка' . '%')
                ->whereNot('name', 'like', '%' . 'сетчат' . '%')
                ->whereNot('name', 'like', '%' . 'нитки' . '%')
                ->pluck('id')
                ->toArray();

            $avr_works = AvrWork::whereIn('work_name_id', $work_name_ids)
                ->join('avrs', 'avrs.id', '=', 'avr_works.avr_id')
                ->orderBy('avrs.date', 'desc')
                ->get();

            foreach ($avr_works as $avr_work) {

                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $avr_work->trk_equipment_id)
                    ->where('work_name_id', $avr_work->work_name_id)
                    ->first();

                $avr = Avr::where('id', $avr_work->avr_id)->orderBy('date', 'desc')->first();

                if (empty($equipment_period_work->id)) {

                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $next_to_be_at = $next_to_be_at->addDays(182);

                    EquipmentWorkPeriod::create([
                        'equipment_id' => $avr_work->trk_equipment_id,
                        'work_name_id' => $avr_work->work_name_id,
                        'repeat_days' => 182,
                        'last_was_at' => $avr->date,
                        'next_to_be_at' => $next_to_be_at,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                } else {

                    $avr_date = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $last_work_date = Carbon::createFromFormat('Y-m-d', $equipment_period_work->last_was_at);

                    if ($avr_date->gt($last_work_date)) {
                        $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                        $next_to_be_at = $next_to_be_at->addDays(182);

                        $equipment_period_work->update([
                            'last_was_at' => $avr->date,
                            'next_to_be_at' => $next_to_be_at,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Автонастройка тех. мероприятий по замене фильтров закончена');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_equipment_period_works_drive_belts()
    {
        try {

            $work_name_ids = WorkName::where('name', 'like', 'замен' . '%' . 'ремн' . '%')
                ->whereNot('name', 'like', '%' . 'проверк' . '%')
                ->whereNot('name', 'like', '%' . 'не требов' . '%')
                ->pluck('id')
                ->toArray();

            $avr_works = AvrWork::whereIn('work_name_id', $work_name_ids)->get();

            $avr_works = AvrWork::whereIn('work_name_id', $work_name_ids)
                ->join('avrs', 'avrs.id', '=', 'avr_works.avr_id')
                ->orderBy('avrs.date', 'desc')
                ->get();

            foreach ($avr_works as $avr_work) {

                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $avr_work->trk_equipment_id)
                    ->where('work_name_id', $avr_work->work_name_id)
                    ->first();

                $avr = Avr::where('id', $avr_work->avr_id)->orderBy('date', 'desc')->first();

                if (empty($equipment_period_work->id)) {

                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $next_to_be_at = $next_to_be_at->addDays(1095);

                    EquipmentWorkPeriod::create([
                        'equipment_id' => $avr_work->trk_equipment_id,
                        'work_name_id' => $avr_work->work_name_id,
                        'repeat_days' => 1095,
                        'last_was_at' => $avr->date,
                        'next_to_be_at' => $next_to_be_at,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                } else {

                    $avr_date = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $last_work_date = Carbon::createFromFormat('Y-m-d', $equipment_period_work->last_was_at);

                    if ($avr_date->gt($last_work_date)) {
                        $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                        $next_to_be_at = $next_to_be_at->addDays(1095);

                        $equipment_period_work->update([
                            'last_was_at' => $avr->date,
                            'next_to_be_at' => $next_to_be_at,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Автонастройка тех. мероприятий по замене ремней закончена');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_equipment_period_works_to_4()
    {
        try {

            $work_name_ids = WorkName::where('name', 'like', '%' . 'ТО 4' . '%')
                ->orWhere('name', 'like', '%' . 'ТО-4' . '%')
                ->orWhere('name', 'like', '%' . 'ТО4' . '%')
                ->whereNot('name', 'like', '%' . 'замен' . '%')
                ->pluck('id')
                ->toArray();

            $avr_works = AvrWork::whereIn('work_name_id', $work_name_ids)
                ->join('avrs', 'avrs.id', '=', 'avr_works.avr_id')
                ->orderBy('avrs.date', 'desc')
                ->get();

            foreach ($avr_works as $avr_work) {

                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $avr_work->trk_equipment_id)
                    ->where('work_name_id', $avr_work->work_name_id)
                    ->first();

                $avr = Avr::where('id', $avr_work->avr_id)->orderBy('date', 'desc')->first();

                if (empty($equipment_period_work->id)) {

                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $next_to_be_at = $next_to_be_at->addDays(92);

                    EquipmentWorkPeriod::create([
                        'equipment_id' => $avr_work->trk_equipment_id,
                        'work_name_id' => $avr_work->work_name_id,
                        'repeat_days' => 92,
                        'last_was_at' => $avr->date,
                        'next_to_be_at' => $next_to_be_at,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                } else {

                    $avr_date = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $last_work_date = Carbon::createFromFormat('Y-m-d', $equipment_period_work->last_was_at);

                    if ($avr_date->gt($last_work_date)) {
                        $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                        $next_to_be_at = $next_to_be_at->addDays(92);

                        $equipment_period_work->update([
                            'last_was_at' => $avr->date,
                            'next_to_be_at' => $next_to_be_at,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Автонастройка тех. мероприятий по ТО 4 закончена');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_equipment_period_works_to_5()
    {
        try {

            $work_name_ids = WorkName::where('name', 'like', '%' . 'ТО 5' . '%')
                ->orWhere('name', 'like', '%' . 'ТО-5' . '%')
                ->orWhere('name', 'like', '%' . 'ТО5' . '%')
                ->pluck('id')
                ->toArray();

            $avr_works = AvrWork::whereIn('work_name_id', $work_name_ids)
                ->join('avrs', 'avrs.id', '=', 'avr_works.avr_id')
                ->orderBy('avrs.date', 'desc')
                ->get();

            foreach ($avr_works as $avr_work) {

                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $avr_work->trk_equipment_id)
                    ->where('work_name_id', $avr_work->work_name_id)
                    ->first();

                $avr = Avr::where('id', $avr_work->avr_id)->orderBy('date', 'desc')->first();

                if (empty($equipment_period_work->id)) {

                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $next_to_be_at = $next_to_be_at->addDays(183);

                    EquipmentWorkPeriod::create([
                        'equipment_id' => $avr_work->trk_equipment_id,
                        'work_name_id' => $avr_work->work_name_id,
                        'repeat_days' => 183,
                        'last_was_at' => $avr->date,
                        'next_to_be_at' => $next_to_be_at,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                } else {

                    $avr_date = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $last_work_date = Carbon::createFromFormat('Y-m-d', $equipment_period_work->last_was_at);

                    if ($avr_date->gt($last_work_date)) {
                        $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                        $next_to_be_at = $next_to_be_at->addDays(183);

                        $equipment_period_work->update([
                            'last_was_at' => $avr->date,
                            'next_to_be_at' => $next_to_be_at,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Автонастройка тех. мероприятий по ТО 5 закончена');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_equipment_period_works_to_6()
    {
        try {

            $work_name_ids = WorkName::where('name', 'like', '%' . 'ТО 6' . '%')
                ->orWhere('name', 'like', '%' . 'ТО-6' . '%')
                ->orWhere('name', 'like', '%' . 'ТО6' . '%')
                ->pluck('id')
                ->toArray();

            $avr_works = AvrWork::whereIn('work_name_id', $work_name_ids)
                ->join('avrs', 'avrs.id', '=', 'avr_works.avr_id')
                ->orderBy('avrs.date', 'desc')
                ->get();

            foreach ($avr_works as $avr_work) {

                $equipment_period_work = EquipmentWorkPeriod::where('equipment_id', $avr_work->trk_equipment_id)
                    ->where('work_name_id', $avr_work->work_name_id)
                    ->first();

                $avr = Avr::where('id', $avr_work->avr_id)->orderBy('date', 'desc')->first();

                if (empty($equipment_period_work->id)) {

                    $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $next_to_be_at = $next_to_be_at->addDays(365);

                    EquipmentWorkPeriod::create([
                        'equipment_id' => $avr_work->trk_equipment_id,
                        'work_name_id' => $avr_work->work_name_id,
                        'repeat_days' => 365,
                        'last_was_at' => $avr->date,
                        'next_to_be_at' => $next_to_be_at,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);

                } else {

                    $avr_date = Carbon::createFromFormat('Y-m-d', $avr->date);
                    $last_work_date = Carbon::createFromFormat('Y-m-d', $equipment_period_work->last_was_at);

                    if ($avr_date->gt($last_work_date)) {
                        $next_to_be_at = Carbon::createFromFormat('Y-m-d', $avr->date);
                        $next_to_be_at = $next_to_be_at->addDays(365);

                        $equipment_period_work->update([
                            'last_was_at' => $avr->date,
                            'next_to_be_at' => $next_to_be_at,
                            'last_editor_id' => Auth::id(),
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Автонастройка тех. мероприятий по ТО 6 закончена');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_balk_avrs_from_to_4_to_to_5()
    {

        try {

            $equipment_name_ids = EquipmentName::where('name', 'like', 'CB-' . '%')->pluck('id')->toArray();
            $equipment_name_ids = implode("', '", $equipment_name_ids);

            $system = System::where('name', System::AIR_CONDITION)->first();

            $work_TO_5 = WorkName::where('name', WorkName::TO_5)->first();
            $work_TO_4 = WorkName::where('name', WorkName::TO_4)->first();

            $balks = TrkEquipment::select('*')->whereRaw("equipment_name_id in ('$equipment_name_ids')")
                ->where('system_id', $system->id)
                ->get();

            foreach ($balks as $balk) {
                $avr_works = AvrWork::where('trk_equipment_id', $balk->id)
                    ->where(function ($q) {
                        $q->where('description', 'like', '%' . 'ТО 5' . '%')
                            ->orWhere('description', 'like', '%' . 'ТО5' . '%');
                    })
                    ->get();

                foreach ($avr_works as $avr_work) {
                    $avr_ids = Avr::where('id', $avr_work->avr_id)->pluck('id')->toArray();

                    $avr_works_to = AvrWork::whereIn('avr_id', $avr_ids)->get();

                    foreach ($avr_works_to as $avr_work_to) {
                        if ($avr_work_to->work_name_id == $work_TO_4->id) {
                            $exists_avr_work = AvrWork::where('work_name_id', $work_TO_5->id)
                                ->where('trk_equipment_id', $avr_work_to->trk_equipment_id)
                                ->where('avr_id', $avr_work_to->avr_id)
                                ->first();

                            if (empty($exists_avr_work->id)) {
                                $avr_work_to->update([
                                    'work_name_id' => $work_TO_5->id,
                                    'last_editor_id' => Auth::id(),
                                ]);
                            }
                        }
                    }
                }

            }

            return redirect()->back()->with('success', 'Исправление актов балок Форт Тауэр закончено.');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function add_equipment_period_works_to_4_in_to_5_avrs()
    {

        try {

            $work_to_4 = WorkName::where('name', 'ТО 4')->first();
            $works_to_5 = WorkName::where('name', 'like', '%' . 'ТО 5' . '%')
                ->orWhere('name', 'like', '%' . 'ТО5' . '%')
                ->orWhere('name', 'like', '%' . 'ТО-5' . '%')
                ->get();

            foreach ($works_to_5 as $to_5) {
                $avr_works = AvrWork::where('work_name_id', $to_5->id)->get();

                foreach ($avr_works as $avr_work) {

                    $old_work_to_4 = AvrWork::where('avr_id', $avr_work->avr_id)
                        ->where('trk_equipment_id', $avr_work->trk_equipment_id)
                        ->where('work_name_id', $work_to_4->id)
                        ->first();

                    if (empty($old_work_to_4->id)) {
                        $new_avr_work = AvrWork::create([
                            'avr_id' => $avr_work->avr_id,
                            'trk_equipment_id' => $avr_work->trk_equipment_id,
                            'work_name_id' => $work_to_4->id,
                            'author_id' => $avr_work->author_id,
                            'last_editor_id' => Auth::id(),
                        ]);

                    }
                }
            }

            return redirect()->back()->with('success', 'Добавление ТО 4 в акты с ТО 5 закончено.');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function add_equipment_period_works_to_4_and_to_5_in_to_6_avrs()
    {

        try {

            $work_to_4 = WorkName::where('name', 'ТО 4')->first();
            $work_to_5 = WorkName::where('name', 'ТО 5')->first();
            $works_to_6 = WorkName::where('name', 'like', '%' . 'ТО 6' . '%')
                ->orWhere('name', 'like', '%' . 'ТО6' . '%')
                ->orWhere('name', 'like', '%' . 'ТО-6' . '%')
                ->get();

            foreach ($works_to_6 as $to_6) {
                $avr_works = AvrWork::where('work_name_id', $to_6->id)->get();

                foreach ($avr_works as $avr_work) {

                    $old_work_to_4 = AvrWork::where('avr_id', $avr_work->avr_id)
                        ->where('trk_equipment_id', $avr_work->trk_equipment_id)
                        ->where('work_name_id', $work_to_4->id)
                        ->first();

                    if (empty($old_work_to_4->id)) {
                        $new_avr_work = AvrWork::create([
                            'avr_id' => $avr_work->avr_id,
                            'trk_equipment_id' => $avr_work->trk_equipment_id,
                            'work_name_id' => $work_to_4->id,
                            'author_id' => $avr_work->author_id,
                            'last_editor_id' => Auth::id(),
                        ]);

                    }

                    $old_work_to_5 = AvrWork::where('avr_id', $avr_work->avr_id)
                        ->where('trk_equipment_id', $avr_work->trk_equipment_id)
                        ->where('work_name_id', $work_to_5->id)
                        ->first();

                    if (empty($old_work_to_5->id)) {
                        $new_avr_work = AvrWork::create([
                            'avr_id' => $avr_work->avr_id,
                            'trk_equipment_id' => $avr_work->trk_equipment_id,
                            'work_name_id' => $work_to_5->id,
                            'author_id' => $avr_work->author_id,
                            'last_editor_id' => Auth::id(),
                        ]);

                    }
                }
            }

            return redirect()->back()->with('success', 'Добавление ТО 4 и ТО 5 в акты с ТО 6 закончено.');

        } catch (\Exception $e) {

            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_all_work_type_like_to_4_in_avrs()
    {

        $work_names = [
            'Выполнение работ в рамках ТО-4',
            'Выполнение работ в рамках ТО4',
            'Выполнено работы в рамках ТО4',
            'Выполнено ТО-4',
            'Выполнены работы в рамках ТО4',
            'Квартальное техническое обслуживание ТО-4 согласно регламента',
            'Проведение работ в рамках ТО4',
            'Проведено квартальное ТО4',
            'Проведены работы в рамках ТО 4',
            'Произведен осмотр и диагностика оборудования ТО 4',
            'произведено то4',
            'ТО-4',
        ];

        $new_work_name = 'ТО 4';

        try {

            DB::beginTransaction();

            $new_work = WorkName::where('name', 'like', $new_work_name)->first();

            foreach($work_names as $work_name)
            {
                $work = WorkName::where('name', 'like', $work_name)->first();

                if(!empty($work->id))
                {

                    $avr_works = AvrWork::where('work_name_id', $work->id)->get();

                    foreach ($avr_works as $avr_work) {

                        $new_avr_work = AvrWork::where('work_name_id', $new_work->id)
                            ->where('trk_equipment_id', $avr_work->trk_equipment_id)
                            ->where('avr_id', $avr_work->avr_id)
                            ->first();

                        if ($new_avr_work === null) {

                            $new_avr_work = AvrWork::create([
                                'avr_id' => $avr_work->avr_id,
                                'trk_equipment_id' => $avr_work->trk_equipment_id,
                                'description' => $avr_work->description,
                                'author_id' => $avr_work->author_id,
                                'last_editor_id' => Auth::id(),
                                'work_name_id' => $new_work->id,
                            ]);
                        }

                        $avr_work->delete();
                    }
                    $work->delete();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Замена всех вхождений ТО4 в актах закончено');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_all_work_type_like_extract_air_filters_change_in_avrs()
    {

        $work_names = [
            'Замена воздушных фильтров вытяжки',
            'Замена карманных воздушных фильтров вытяжки',
            'Замена кассетных воздушных фильтров вытяжки',
            'Замена панельных воздушных фильтров вытяжки',
            'Сухая чистка воздушных фильтров вытяжки',
            'Замена воздушного панельного фильтра вытяжки',
            'Сухая чистка воздушного фильтра вытяжки',
        ];

        $new_work_name = 'Замена фильтров вытяжки';

        try {

            DB::beginTransaction();

            $new_work = WorkName::where('name', 'like', $new_work_name)->first();

            foreach($work_names as $work_name)
            {
                $work = WorkName::where('name', 'like', $work_name)->first();

                if(!empty($work->id))
                {

                    $avr_works = AvrWork::where('work_name_id', $work->id)->get();

                    foreach ($avr_works as $avr_work) {

                        $new_avr_work = AvrWork::where('work_name_id', $new_work->id)
                            ->where('trk_equipment_id', $avr_work->trk_equipment_id)
                            ->where('avr_id', $avr_work->avr_id)
                            ->first();

                        if ($new_avr_work === null) {

                            $new_avr_work = AvrWork::create([
                                'avr_id' => $avr_work->avr_id,
                                'trk_equipment_id' => $avr_work->trk_equipment_id,
                                'description' => $avr_work->description,
                                'author_id' => $avr_work->author_id,
                                'last_editor_id' => Auth::id(),
                                'work_name_id' => $new_work->id,
                            ]);
                        }

                        $avr_work->delete();
                    }
                    $work->delete();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Замена всех вхождений Замена фильтров вытяжки в актах закончено');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function set_all_work_type_like_supply_air_filters_change_in_avrs()
    {

        $work_names = [
            'Замена воздушного панельного фильтра',
            'Замена воздушного панельного фильтра притока',
            'Замена воздушных панельных фильтров притока',
            'Замена панельных воздушных фильтров притока',
            'Замена панельных фильтров притока',
            'Замена воздушногофильтра притока',
            'Замена воздушных фильтров грубой очистки притока',
            'Замена фильтров притока',
            'Замена фильтрующего материала на притоке',
            'Сухая чистка воздушного фильтра притока',
            'Сухая чистка воздушного фильтра притока пылесосом',
            'Сухая чистка воздушных кассетных фильтров притока',
            'Сухая чистка воздушных кассетных фильтров притока пылесосом',
            'Сухая чистка воздушных фильтров притока',
            'Сухая чистка воздушных фильтров притока пылесосом',
        ];

        $new_work_name = 'Замена фильтров приток №1';

        try {

            DB::beginTransaction();

            $new_work = WorkName::where('name', 'like', $new_work_name)->first();

            foreach($work_names as $work_name)
            {
                $work = WorkName::where('name', 'like', $work_name)->first();

                if(!empty($work->id))
                {

                    $avr_works = AvrWork::where('work_name_id', $work->id)->get();

                    foreach ($avr_works as $avr_work) {

                        $new_avr_work = AvrWork::where('work_name_id', $new_work->id)
                            ->where('trk_equipment_id', $avr_work->trk_equipment_id)
                            ->where('avr_id', $avr_work->avr_id)
                            ->first();

                        if ($new_avr_work === null) {

                            $new_avr_work = AvrWork::create([
                                'avr_id' => $avr_work->avr_id,
                                'trk_equipment_id' => $avr_work->trk_equipment_id,
                                'description' => $avr_work->description,
                                'author_id' => $avr_work->author_id,
                                'last_editor_id' => Auth::id(),
                                'work_name_id' => $new_work->id,
                            ]);
                        }

                        $avr_work->delete();
                    }
                    $work->delete();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Замена всех вхождений Замена панельных фильтров притока в актах закончено');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);
            return redirect()->back()->with('error', $e);

        }
    }

    public function parse_rooms_and_renters_from_excel_file()
    {
        $filename = 'trk_rooms_and_renters.xlsx';
        $lines = Excel::toArray(null, $filename);

        try {

            DB::beginTransaction();

        foreach($lines as $line)
        {
            foreach($line as $value)
            {
                $trk_name = $value[0];
                $building_name = $value[1];
                $floor_name = $value[2];
                $room_name = $value[3];
                $brand_name = $value[4];
                $organization_name = $value[5];

                $room = Room::where('name', $room_name)->first();

                if(empty($room->id))
                {
                    $room = Room::create([
                        'name' => $room_name,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $brand = Brand::where('name', 'like', $brand_name)->first();

                if(empty($brand->id))
                {
                    $brand = Brand::create([
                        'name' => $brand_name,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $organization = Organization::where('name', 'like', '%' . $organization_name . '%')->first();

                if(empty($organization->id))
                {
                    $organization = Organization::create([
                        'name' => $organization_name,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $trk = Trk::where('name', $trk_name)->first();
                $building = Building::where('name', $building_name)->first();
                $floor = Floor::where('name', $floor_name)->first();

                $trk_room = TrkRoom::where('trk_id', $trk->id)
                    ->where('building_id', $building->id)
                    ->where('floor_id', $floor->id)
                    ->where('room_id', $room->id)
                    ->first();

                $room_purpose = RoomPurpose::where('name', 'Коммерческое')->first();

                if(empty($trk_room->id))
                {
                    $trk_room = TrkRoom::create([
                        'trk_id' => $trk->id,
                        'building_id' => $building->id,
                        'floor_id' => $floor->id,
                        'room_id' => $room->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                        'room_purpose_id' => $room_purpose->id,
                        'need_daily_checking' => 0,
                    ]);

                } else {

                    $trk_room->update([
                        'room_purpose_id' => $room_purpose->id,
                        'need_daily_checking' => 0,
                        'last_editor_id' => Auth::id(),
                    ]);
                }

                $renter = RenterTrkRoomBrand::where('trk_room_id', $trk_room->id)
                    ->where('brand_id', $brand->id)
                    ->where('organization_id', $organization->id)
                    ->first();

                if(empty($renter->id))
                {
                    $renter = RenterTrkRoomBrand::create([
                        'trk_room_id' => $trk_room->id,
                        'brand_id' => $brand->id,
                        'organization_id' => $organization->id,
                        'author_id' => Auth::id(),
                        'last_editor_id' => Auth::id(),
                    ]);
                }

            }
         }

            DB::commit();

        return redirect()->back()->with('success', 'Помещения и арендаторы на ТРК ' . $trk->name . ' созданы');

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error($e);

            return redirect()->back()->with('error', $e);

        }

    }
}
