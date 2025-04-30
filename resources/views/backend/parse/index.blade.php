@extends('layouts.backend.main')

@section('title', 'Главная | Парсер')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Парсер</h4>
                    </div>
                </div>
            </div>
            <div class="profile-foreground position-relative"
                 style="
                        margin-top: -1.5rem !important;
                        margin-right: -1.5rem !important;
                        margin-left: -1.5rem !important;
                     ">
                <div class="profile-wid-bg">
                    {{--                        <img src="{{asset('assets/images/profile-bg.jpg')}}" alt="" class="profile-wid-img" />--}}
                </div>

                <div class="pt-4 mb-lg-3 pb-lg-4 px-4">
                    <div class="col">
                        @include('components.backend.message')
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="card shadow">
                                <div class="card body">
                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_rooms_and_renters_from_excel_file')}}" ><b>Парсить помещения и арендаторов из excel файла</b></a>
                                </div>
                            </div>
                        </div>
                    </div>
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.create_old_tables_in_new_base')}}" ><b>Создать старые таблицы в новой базе</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос пользователей в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_users_from_old_base_to_text_file')}}" ><b>Парсить пользователей из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_users_from_text_file_to_new_base')}}" ><b>Парсить пользователей из текстового файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос помещений в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_rooms_from_old_base_to_text_file')}}" ><b>Парсить помещения из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_rooms_from_text_file_to_new_base')}}" ><b>Парсить помещения из текстового файла в новую базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_daily_checking_rooms_from_text_file_to_new_base')}}" ><b>Парсить помещения с ежедневным обходом из текстового файла в новую базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_rooms_and_renters_from_text_file_to_new_base')}}" ><b>Парсить помещения из арендаторов из текстового файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос оборудования в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipments_from_old_base_to_text_file')}}" ><b>Парсить оборудование из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipments_from_text_file_to_new_base')}}" ><b>Парсить оборудование из текстового файла в новую базу</b></a>--}}
{{--                                  </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос актов выполненных работ в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_avrs_from_old_base_to_text_file', [0, 5000])}}" ><b>Парсить акты 0-5000 из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_avrs_from_old_base_to_text_file', [5000, 5000])}}" ><b>Парсить акты 5000-10000 из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_avrs_from_old_base_to_text_file', [10000, 5000])}}" ><b>Парсить акты 10000-15000 из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_avrs_from_text_file_to_sql_file', [0, 5000])}}" ><b>Парсить акты 0-5000 из текстового файла в sql файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_avrs_from_text_file_to_sql_file', [5000, 5000])}}" ><b>Парсить акты 5000-10000 из текстового файла в sql файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_avrs_from_text_file_to_sql_file', [10000, 5000])}}" ><b>Парсить акты 10000-15000 из текстового файла в sql файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_avrs_from_sql_file_to_new_base', [0, 5000])}}" ><b>Парсить акты 0-5000 из sql файла в новую базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_avrs_from_sql_file_to_new_base', [5000, 5000])}}" ><b>Парсить акты 5000-10000 из sql файла в новую базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_avrs_from_sql_file_to_new_base', [10000, 5000])}}" ><b>Парсить акты 10000-15000 из sql файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос складов в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_spare_part_storages_from_old_base_to_text_file')}}" ><b>Парсить склады из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_spare_part_storages_from_text_file_to_new_base')}}" ><b>Парсить склады из текстового файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос заявок в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_applications_from_old_base_to_text_file')}}" ><b>Парсить заявки из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_applications_from_text_file_to_new_base')}}" ><b>Парсить заявки из текстового файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос ремонта в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_repairs_from_old_base_to_text_file')}}" ><b>Парсить ремонт из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_repairs_from_text_file_to_new_base')}}" ><b>Парсить ремонт из текстового файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос заказов запчастей в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_spare_part_orders_from_old_base_to_text_file')}}" ><b>Парсить заказы из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_spare_part_orders_from_text_file_to_new_base')}}" ><b>Парсить заказы из текстового файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос счетчиков в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_counters_from_old_base_to_text_file')}}" ><b>Парсить счетчики из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_counter_counts_from_old_base_to_text_file')}}" ><b>Парсить показания счетчиков из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_counters_from_text_file_to_new_base')}}" ><b>Парсить счетчики из text файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос чеклистов в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_hvac_supply_machines_from_old_base_to_text_file')}}" ><b>Парсить чеклисты приточных установок из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_hvac_supply_machines_from_text_file_to_new_base')}}" ><b>Парсить чеклисты приточных установок из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_hvac_extract_machines_from_old_base_to_text_file')}}" ><b>Парсить чеклисты вытяжных установок из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_hvac_extract_machines_from_text_file_to_new_base')}}" ><b>Парсить чеклисты вытяжных установок из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_hvac_room_climate_from_old_base_to_text_file')}}" ><b>Парсить чеклисты климата в помещении из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_hvac_room_climate_from_text_file_to_new_base')}}" ><b>Парсить чеклисты климата в помещении из текстового файла в базу</b></a>--}}

{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос потребителей оборудования (equipment_users) в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_users_from_old_base_to_text_file')}}" ><b>Парсить потребителей из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_users_from_text_file_to_new_base')}}" ><b>Парсить потребителей из текстового файла в базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос чеклистов балок, фанкойлов, кондиционеров, диффузоров, воздуховодов в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_condition_checklists_from_old_base_to_text_file')}}" ><b>Парсить из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_balk_checklists_from_text_file_to_new_base')}}" ><b>Парсить чеклисты балок из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_diffuser_checklists_from_text_file_to_new_base')}}" ><b>Парсить чеклисты диффузоров из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_duct_checklists_from_text_file_to_new_base')}}" ><b>Парсить чеклисты воздуховодов из текстового файла в базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос запчастей из которых состоит оборудование в новую базу из старой--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_bearings_from_old_base_to_text_file')}}" ><b>Парсить подшипники из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_bearings_from_text_file_to_new_base')}}" ><b>Парсить подшипники из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_air_filters_from_old_base_to_text_file')}}" ><b>Парсить воздушные фильтры из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_air_filters_from_text_file_to_new_base')}}" ><b>Парсить воздушные фильтры из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_drivebelts_from_old_base_to_text_file')}}" ><b>Парсить приводные ремни из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_drivebelts_from_text_file_to_new_base')}}" ><b>Парсить приводные ремни из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_electro_motors_from_old_base_to_text_file')}}" ><b>Парсить электродвигатели из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_electro_motors_from_text_file_to_new_base')}}" ><b>Парсить электродвигатели из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_pulleis_from_old_base_to_text_file')}}" ><b>Парсить шкивы из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_pulleis_from_text_file_to_new_base')}}" ><b>Парсить шкивы из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_taperbashes_from_old_base_to_text_file')}}" ><b>Парсить тапербаши из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_taperbashes_from_text_file_to_new_base')}}" ><b>Парсить тапербаши из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_pumps_from_old_base_to_text_file')}}" ><b>Парсить насосы оборудования из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_pumps_from_text_file_to_new_base')}}" ><b>Парсить насосы оборудования из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_valve_actuators_from_old_base_to_text_file')}}" ><b>Парсить приводы оборудования из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_valve_actuators_from_text_file_to_new_base')}}" ><b>Парсить приводы оборудования из текстового файла в базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_equipment_rates_from_old_base_to_text_file')}}" ><b>Парсить расходы оборудования из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_equipment_rates_from_text_file_to_new_base')}}" ><b>Парсить расходы оборудования из текстового файла в базу</b></a>--}}

{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row mt-5">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Таблицы первой версии сайта создать в старой базе--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.create_old_old_tables_in_new_base')}}" ><b>Создать в новой базе таблицы первой версии сайта</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Перенос актов выполненных работ в новую базу из древней базы первой версии--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_closed_applications_from_old_old_base_to_text_file', [0, 5000])}}" ><b>Парсить акты 0-5000 из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_closed_applications_from_old_old_base_to_text_file', [5000, 5000])}}" ><b>Парсить акты 5000-10000 из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_closed_applications_from_old_old_base_to_text_file', [10000, 5000])}}" ><b>Парсить акты 10000-15000 из старой базы в текстовый файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_closed_applications_from_text_file_to_sql_file', [0, 5000])}}" ><b>Парсить акты 0-5000 из текстового файла в sql файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_closed_applications_from_text_file_to_sql_file', [5000, 5000])}}" ><b>Парсить акты 5000-10000 из текстового файла в sql файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.parse_closed_applications_from_text_file_to_sql_file', [10000, 5000])}}" ><b>Парсить акты 10000-15000 из текстового файла в sql файл</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_closed_applications_from_sql_file_to_new_base', [0, 5000])}}" ><b>Парсить акты 0-5000 из sql файла в новую базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_closed_applications_from_sql_file_to_new_base', [5000, 5000])}}" ><b>Парсить акты 5000-10000 из sql файла в новую базу</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.parse_closed_applications_from_sql_file_to_new_base', [10000, 5000])}}" ><b>Парсить акты 10000-15000 из sql файла в новую базу</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Старые таблицы в новой базе--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.delete_old_tables_from_new_base')}}" ><b>Удалить старые таблицы из новой базы</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Древние таблицы в новой базе--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-danger" href="{{route('parse.delete_old_old_tables_from_new_base')}}" ><b>Удалить древние таблицы из новой базы</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Исправление актов балок на Форт Тауэр (ТО 4 --> ТО 5)--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_balk_avrs_from_to_4_to_to_5')}}" ><b>Исправить акты балок Форт Тауэр</b></a>--}}
{{--                                  </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Настройка АВР--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.add_equipment_period_works_to_4_in_to_5_avrs')}}" ><b>В акты с ТО 5 добавить ТО 4</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.add_equipment_period_works_to_4_and_to_5_in_to_6_avrs')}}" ><b>В акты с ТО 6 добавить ТО 4 и ТО 5</b></a>--}}
{{--                                    </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Замена типов работ--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_all_work_type_like_to_4_in_avrs')}}" ><b>Замена всех вхождений ТО4 в актах</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_all_work_type_like_extract_air_filters_change_in_avrs')}}" ><b>Замена всех вхождений Замена фильтров вытяжки в актах</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_all_work_type_like_supply_air_filters_change_in_avrs')}}" ><b>Замена всех вхождений Замена панельных фильтров притока в актах</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row">--}}
{{--                        <div class="col">--}}
{{--                            <div class="card shadow">--}}
{{--                                <div class="card-header">--}}
{{--                                    Настройка тех. мероприятий--}}
{{--                                </div>--}}
{{--                                <div class="card-body">--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_equipment_period_works_air_filters')}}" ><b>Автонастройка тех. мероприятий по замене фильтров</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_equipment_period_works_drive_belts')}}" ><b>Автонастройка тех. мероприятий по замене приводных ремней</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_equipment_period_works_to_4')}}" ><b>Автонастройка тех. мероприятий по ТО 4</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_equipment_period_works_to_5')}}" ><b>Автонастройка тех. мероприятий по ТО 5</b></a>--}}
{{--                                    <br><br>--}}
{{--                                    <a class="btn btn-sm btn-warning" href="{{route('parse.set_equipment_period_works_to_6')}}" ><b>Автонастройка тех. мероприятий по ТО 6</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
