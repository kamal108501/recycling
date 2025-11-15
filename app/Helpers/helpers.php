<?php

use App\Site;
use App\UserAction;
use App\LS_UserAction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Vinkla\Hashids\Facades\Hashids;

/* PRINT DATA */

function printData($data, $break = true)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if ($break) {
        die;
    }
}

/* GET CURRENT DATETIME */

function currentDT()
{
    return date('Y-m-d H:i:s');
}

/* GET CURRENT LOGIN USER */
function currentUser()
{
    return Session::get('user.userid');
}

function currentUserName()
{
    return Session::get('user.full_name');
}

function getStartDT($date, $timezone = 'IST')
{
    $setTime =  $timezone === 'IST' ? '00:00:00' : '18:30:00';
    return $date . ' ' . $setTime;
}

function getEndDT($date, $timezone = 'IST')
{
    $setTime =  $timezone === 'IST' ? '23:59:59' : '18:29:59';
    return $date . ' ' . $setTime;
}

/* VALIDATE INPUT */

function validateInput($input)
{
    $input = strip_tags($input);
    return $input = trim($input);
}

/* GET PAGE TITLE */

function getPageTitle()
{
    $title = '';

    if (SEG3()) {
        if (SEG3() == 'edit') {
            $title = ucwords(SEG3() . ' ' . getModuleName(SEG1()));
        } else {
            $title .= ucfirst(getModuleName(SEG2()));
        }
    } else if (SEG2()) {
        if (SEG2() == 'create') {
            $title = ucwords(SEG2() . ' ' . getModuleName(SEG1()));
        } else {
            $title .= ucfirst(getModuleName(SEG2()));
        }
    } else {
        $title .= ucfirst(getModuleName(SEG1()));
    }

    if (isset($sub_page_name))
        $title .= " : " . $sub_page_name;

    return $title;
}

/* GET MODULE NAME */

function getModuleName($uri)
{
    switch ($uri) {
        case "parkings":
            return "Site Parkings";
        case "missing_charts":
            return "Missing Charts";
        case "fuel_charts":
            return "Fuel Charts";
        case "equ_monitor":
            return "Equ Monitor";
        case "company_dashboard":
            return "Company Dashboard";
        case "driver_dashboard":
            return "Driver Dashboard";
        case "driver_reporting":
            return "Driver Reporting";
        case "preventive_maintenance":
            return "Preventive Maintenance";
        case "preventive_overview":
            return "Preventive Overview";
        case "driver_performance":
            return "Driver Performance";
        case "chart_permission":
            return "Chart Permission";
        case "pm_manual_data":
            return "PM Manual Data";
        case "pm_sap_data":
            return "PM SAP Dta";
        case "driver_attendance":
            return "Driver Attendance";
        case "leave_status":
            return "Leave Status";
        case "master_employees":
            return "SAP SF";
        case "dms_reports":
            return "DMS Reports";
        case "ibutton_lists":
            return "IButton Lists";
        case "attendance_emails":
            return "Attendance Emails";
        case "alert_emails":
            return "Alert Emails";
        case "alert_messages":
            return "Alert Messages";
        case "voice_alerts":
            return "Voice Alerts";
        case "dpr_data_logs":
            return "DPR Data Logs";
        case "tag_assignment":
            return "Tag Assignment";
        case "drop_confirmation":
            return "Drop Confirmation";
        case "site_dpr_report":
            return "Site DPR Report";
        case "live_location":
            return "Live Location";
        case "idle_driver_reporting":
            return "Idle Driver Reporting";
        case "oms_duty_records":
            return "OMS Duty Records";
        case "oms_performance":
            return "OMS Performance";
        case "oms_daily_performance":
            return "OMS Daily Performance";
        case "oms_internal_performance":
            return "OMS Internal Performance";
        case "oms_dashboard":
            return "OMS Dashboard";
        case "fuelalert_justification":
            return "Fuel Alert Justification";
        case "geofence_justification":
            return "Geofence Justification";
        case "overspeed_justification":
            return "Overspeed Alerts";
        case "faulty_meter":
            return "Faulty Meter";
        case "badaverage_justification":
            return "Bad Average Justification";
        case "fuelalert_dashboard":
            return "Fuel Alert Dashboard";
        case "overspeed_dashboard":
            return "Overspeed Dashboard";
        case "geofence_dashboard":
            return "Geofence Dashboard";
        case "faulty_meter_dashboard":
            return "Faulty Meter Dashboard";
        case "badaverage_dashboard":
            return "Bad Average Dashboard";
        case "fdms_dashboard":
            return "FDMS MIS Dashboard";
        case "fdms_dashboard_v3":
            return "FDMS MIS Dashboard";
        case "alert_reason":
            return "Alert Reason";
        case "equ_assignment":
            return "Equipment Assignment";
        case "main_disconnect_justification":
            return "Main Disconnect Justification";
        case "main_disconnect_dashboard":
            return "Main Disconnect Dashboard";
        case "group_assignment":
            return "Group Assignment";
        case "cluster_master":
            return "Cluster Master";
        case "geofence_process":
            return "Geofence Process";
        case "unauthorized_access_vehicle":
            return "Unauthorized Access Vehicle";
        case "daily_monitoring":
            return "Daily Monitoring";
        case "flms_inspection":
            return "Flms Inspection";
        case "dpr_reports":
            return "DPR Reports";
        case "battery_theft":
            return "Battery Theft";
        case "ticket_system":
            return "Ticket System";
        case "ticket_issue_type":
            return "Ticket Issue Type";
        case "ticket_system":
            return "Ticket System";
        case "ticket_sub_issue_type":
            return "Ticket Sub Issue Type";
        case "manpower_type":
            return "Manpower Type";
        case "sm_cron_status":
            return "Cron Status";
        case "hire_equip_excess_fuel":
            return "HIRE Equip Excess Fuel";
        case "fuel_sensor_report":
            return "Fuel Sensor Report";
        case "inactive_vehicle":
            return "Inactive Vehicle";
        case "operator_status_process":
            return "Operator Status Process";
        case "unlicensed_equipment_driven":
            return "Ope. Skill Mismatch";
        case "attendance_files":
            return "Attendance Files";
        case "weigh_bridges":
            return "Weigh Bridges";
        case "loading_sources":
            return "Loading Sources";
        case "ws_companies":
            return "Welspun Companies";
        case "ws_sites":
            return "Welspun Sites";
        case "ws_users":
            return "Welspun Users";
        case "ws_dashboard":
            return "Welspun Dashboard";
        case "chainages":
            return "Chainage Master";
        case "unloading_points":
            return "Unloading Points";
        case "endpoints":
            return "Endpoints";
        case "challan_data":
            return "Challan Data";
        case "challan_api_logs":
            return "Challan API Logs";
        case "ws_reports":
            return "WS Reports";
        case "ws_vehicles":
            return "WS Vehicles";
        case "lastpoint_overview":
            return "Lastpoint Overview";
        case "port_master":
            return "Port Master";
        case "oms_utilization":
            return "OMS Utilization";
        case "fleet_utilization":
            return "Fleet Utilization";
        case "asset_type_utilization":
            return "Asset Type Utilization";
        case "operator_requirements":
            return "Operator requirements";
        case "fdms_reports":
            return "FDMS Reports";
        case "safety_process":
            return "Safety Process";
        case "occasion_wise_images":
            return "Occasion Wise Images";
        case "dashboard_status":
            return "Dashboard Status";
        case "safety_activities":
            return "Safety Activities";
        case "dgset_monitoring":
            return "D.G Set Monitoring";
        case "dgset_monitoring_report":
            return "D.G Set Monitoring Report";
        case "cm_insurance":
            return "Insurance";
        case "registration":
            return "Registration";
        case "fitness":
            return "Fitness";
        case "permit":
            return "Permit";
        case "tax":
            return "Tax";
        case "puc":
            return "PUC";
        case "explosive_license";
            return "Explosive License";
        case "calibration ":
            return "Calibration ";
        case "third_party_inspection":
            return "Third Party Inspection";
        case "driver_license":
            return "Driver License";
        case "landing_pages":
            return "Landing Pages";
        case "alert_notifications":
            return "Alert Notifications";
        case "fuel_sensor_report_v1":
            return "Fuel Sensor Report V1";
        case "dataport_master":
            return "Data Port Master";
        case "smooth_fuel_events":
            return "Smooth Fuel Events";
        case "cm_dashboard":
            return "Compliance Monitoring Dashboard";
        case "safety_dashboard":
            return "Safety Dashboard";
        case "fleet_management":
            return "Fleet Management";
        case "app_download":
            return "APP Download";
        case "safety_process_users":
            return "Safety Process Users";
        case "rc_data":
            return "RC Data";
        case "rc_api_logs":
            return "RC API Logs";
        case "user_action_logs":
            return "User Action Logs";
        case "rca_actions":
            return "RCA Actions";
        case "rca_tracking":
            return "RCA Tracking";
        case "idea_category":
            return "Idea Category";
        case "idea_generation":
            return "Idea Generation";
        case "fuel_excessive_summary":
            return "Fuel Excessive Summary";
        case "sap_transactions":
            return "SAP Transactions";
        case "smart_running_overview":
            return "Smart Running Overview";
        case "smart_running_comparison":
            return "Smart Running Comparison";
        case "user_action_logs":
            return "User Action Logs";
        case "permissions":
            return "Permissions";
        case "roles":
            return "Roles";
        case "users":
            return "Users";
        case "shifts":
            return "Shifts";
        case "work_location":
            return "Work Location";
        case "activities":
            return "Activities";
        case "department":
            return "Department";
        case "used_for":
            return "Used For";
        case "logsheet_data":
            return "Logsheet Data";
        case "compliance_violation":
            return "Compliance Violation";
        case "equipment_overview":
            return "Equipment Overview";
        case "fleet_overview":
            return "Fleet Overview";
        case "update_compliances":
            return "Update Compliances";
        case "tracking_locations":
            return "Tracking Locations";
        case "tracking_data":
            return "Tracking Data";
        case "fdms_transaction_details":
            return "FDMS Transaction Details";
        case "fleet_utilization_me":
            return "Fleet Utilization ME";
        case "fleet_overview_status":
            return "Fleet Overview Status";
        case "fleet_utilization_data":
            return "Fleet Utilization Data";
        case "pending_logs":
            return "Pending Logs";
        case "fuel_data_management":
            return "Fuel Data Management";
        case "site_employees":
            return "Site Employees";
        case "tracking_cloner":
            return "Tracking Cloner";
        case "measuring_points":
            return "Measuring Points";
        case "sms_logs":
            return "SMS Logs";
        case "email_logs":
            return "Email Logs";
        default:
            return $uri;
    }
}

/* GET SEMENT 1 */

function SEG1()
{
    return request()->segment(1);
}

/* GET SEMENT 2 */

function SEG2()
{
    return request()->segment(2);
}

/* GET SEMENT 3 */

function SEG3()
{
    return request()->segment(3);
}

/* GET TIME BETWEEN TWO DATES 3 */

function timeBetween($startDate, $endDate, $format = 1)
{
    list($date, $time) = explode(' ', $endDate);
    $startdate = explode("-", $date);
    $starttime = explode(":", $time);

    list($date, $time) = explode(' ', $startDate);
    $enddate = explode("-", $date);
    $endtime = explode(":", $time);

    $secondsDifference = mktime(
        $endtime[0],
        $endtime[1],
        $endtime[2],
        $enddate[1],
        $enddate[2],
        $enddate[0]
    ) - mktime(
        $starttime[0],
        $starttime[1],
        $starttime[2],
        $startdate[1],
        $startdate[2],
        $startdate[0]
    );

    switch ($format) {
        // Difference in Minutes
        case 1:
            return floor($secondsDifference / 60);
            // Difference in Hours
        case 2:
            return floor($secondsDifference / 60 / 60);
            // Difference in Days
        case 3:
            return floor($secondsDifference / 60 / 60 / 24);
            // Difference in Weeks
        case 4:
            return floor($secondsDifference / 60 / 60 / 24 / 7);
            // Difference in Months
        case 5:
            return floor($secondsDifference / 60 / 60 / 24 / 7 / 4);
            // Difference in Years
        default:
            return floor($secondsDifference / 365 / 60 / 60 / 24);
    }
}

/* GET SECONDS TO HOUR MINUTES 3 */

function sec2HourMinute($seconds)
{

    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds / 60) % 60);
    //$seconds = $seconds % 60;
    $HourMinute = "";
    if ($hours > 0)
        $HourMinute .= "$hours Hours ";
    if ($minutes > 0)
        $HourMinute .= "$minutes Minutes ";

    return $HourMinute;
}

function getLastSQL($break = true)
{
    $queries = DB::getQueryLog();
    $last_query = 'No query found.';
    if ($queries) {
        $last_query = end($queries);
        $last_query = bindDataToQuery($last_query);
    }
    echo $last_query;
    if ($break) {
        die;
    }
}

function getCronLastSQL($break = true)
{
    $queries = DB::connection('mysql_api')->getQueryLog();
    $last_query = 'No query found.';
    if ($queries) {
        $last_query = end($queries);
        $last_query = bindDataToQuery($last_query);
    }
    echo $last_query;
    if ($break) {
        die;
    }
}

function bindDataToQuery($queryItem)
{
    $query = $queryItem['query'];
    $bindings = $queryItem['bindings'];
    $arr = explode('?', $query);
    $res = '';
    foreach ($arr as $idx => $ele) {
        if ($idx < count($arr) - 1) {
            $res = $res . $ele . "'" . $bindings[$idx] . "'";
        }
    }
    $res = $res . $arr[count($arr) - 1];
    return $res;
}

function getdiff($endtime, $starttime, $dt = 1)
{
    $endtime = strtotime($endtime);
    $starttime = strtotime($starttime);
    $timediff = $endtime - $starttime;
    if ($dt == 0) {
        $days = gmdate("z", $timediff);
        return $days . "," . gmdate("H:i:s", $timediff);
    } else {
        return gmdate("H:i:s", $timediff);
    }
}

function time_to_sec($time)
{
    $hours = substr($time, 0, -6);
    $minutes = substr($time, -5, 2);
    $seconds = substr($time, -2);

    return $hours * 3600 + $minutes * 60 + $seconds;
}

function time_to_sec1($time)
{

    list($hours, $minutes, $seconds) = explode(":", $time);
    return $hours * 3600 + $minutes * 60 + $seconds;
}

function addtime($oldPlayTime, $PlayTimeToAdd, $dt = 1)
{
    $pieces = explode(':', $oldPlayTime);
    $hours = $pieces[0];
    //$hours=str_replace("00","12",$hours);
    $minutes = $pieces[1];
    $seconds = $pieces[2];
    $oldPlayTime = $hours . ":" . $minutes . ":" . $seconds;

    $pieces = explode(':', $PlayTimeToAdd);
    $hours = $pieces[0];
    //$hours=str_replace("00","12",$hours);
    $minutes = $pieces[1];
    $seconds = $pieces[2];

    $str = $minutes . " minute " . $seconds . " second";
    $str = "01/01/2000 " . $oldPlayTime . "+ " . $hours . " hour " . $minutes . " minute " . $seconds . " second";

    if (($timestamp = strtotime($str)) === false) {
        echo "error ";
        return false;
    } else {
        if ($dt) {
            $sum = date('H:i:s', $timestamp);
            $pieces = explode(':', $sum);
            $hours = $pieces[0];
            //$hours=str_replace("12","00",$hours);
            $minutes = $pieces[1];
            $seconds = $pieces[2];
            $sum = $hours . ":" . $minutes . ":" . $seconds;
            return $sum;
        } else {
            $sum = date('H:i:s', $timestamp);
            $pieces = explode(':', $sum);
            $hours = $pieces[0];
            //$hours=str_replace("12","00",$hours);
            $minutes = $pieces[1];
            $seconds = $pieces[2];
            $sum = $hours . ":" . $minutes . ":" . $seconds;
            return date('z', $timestamp) . "," . $sum;
        }
    }
}

function AddTimeToStr($aElapsedTimes)
{

    $totalHours = 0;
    $totalMinutes = 0;
    $totalSeconds = 0;

    foreach ($aElapsedTimes as $time) {
        $timeParts = explode(":", $time);
        $h = $timeParts[0];
        $m = $timeParts[1];
        $s = $timeParts[2];
        $totalHours += $h;
        $totalMinutes += $m;
        $totalSeconds += $s;
    }

    $additionalMinutes = floor($totalSeconds / 60);
    $seconds = $totalSeconds % 60;
    $totalMinutes = $totalMinutes + $additionalMinutes;

    $additionalHours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    $hours = $totalHours + $additionalHours;

    $strSeconds = strval($seconds);
    $strMinutes = strval($minutes);

    if ($seconds < 10) {
        $strSeconds = "0" . $seconds;
    }

    if ($minutes < 10) {
        $strMinutes = "0" . $minutes;
    }

    $strHours = strval($hours);
    if ($hours < 10) {
        $strHours = "0" . $hours;
    }

    return ($strHours . ":" . $strMinutes . ":" . $strSeconds);
}

function getISTDate()
{
    date_default_timezone_set('Asia/Kolkata');
    return date('Y-m-d');
}

function getISTTime()
{
    date_default_timezone_set('Asia/Kolkata');
    return time();
}

function getISTDateTime()
{
    date_default_timezone_set('Asia/Kolkata');
    return date('Y-m-d H:i:s');
}

function getDatesFromRange($start, $end)
{
    $dates = array($start);
    while (end($dates) < $end) {
        $dates[] = date('Y-m-d', strtotime(end($dates) . ' +1 day'));
    }
    return $dates;
}

function getFinancialMonths($exp)
{
    $date_range_arr = [];
    foreach ($exp as $key => $year) {
        if ($key == 1) {
            for ($j = 0; $j < 3; $j++) {
                if (strtotime(date('Y-m')) > strtotime($year . "-" . $j)) {
                    $date_range_arr[] = date("Y-m", strtotime($year . "-" . $j . "+1 month"));
                }
            }
        } else {
            for ($i = 3; $i <= 11; $i++) {
                if (strtotime(date('Y-m')) > strtotime($year . "-" . $i)) {
                    $date_range_arr[] = date("Y-m", strtotime($year . "-" . $i . "+1 month"));
                }
            }
        }
    }

    return $date_range_arr;
}

function utf8ize($mixed)
{
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}

function endRequest($status, $responseCode, $responseMessage, $data = '')
{
    $response = array();

    $response['responseStatus'] = $status;
    $response['responseCode'] = $responseCode;
    $response['responseMessage'] = $responseMessage;
    if ($status == true && $data) {
        $response['data'] = $data;
    }

    return response()->json($response);
}

function groupBy($key, $array)
{
    $result = array();

    foreach ($array as $val) {
        if (array_key_exists($key, $val)) {
            $result[$val[$key]][] = $val;
        } else {
            $result[""][] = $val;
        }
    }

    return $result;
}

/* function to formate money in india */

// function moneyFormatIndia($num)
// {
//     $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::DECIMAL);
//     $amount = $fmt->format($num);
//     return $amount;
// }

function moneyFormatIndia($num)
{
    $num = (float)$num;
    $num = number_format($num, 2, '.', '');

    $parts = explode('.', $num);
    $integerPart = $parts[0];
    $decimalPart = isset($parts[1]) ? $parts[1] : '00';

    $lastThree = substr($integerPart, -3);
    $rest = substr($integerPart, 0, -3);

    if (strlen($rest) > 0) {
        $lastThree = ',' . $lastThree;
    }

    $integerPart = preg_replace('/(\d)(?=(\d{2})+(?!\d))/', '$1,', $rest);
    $integerPart .= $lastThree;

    return $integerPart . '.' . $decimalPart;
}

/** send sms * */
/* SEND SMS TO USER */

function sendSMS($to = '', $text = '', $template_id = '')
{
    $stream_options = array(
        'http' => array(
            'method' => 'GET',
        ),
    );
    $context = stream_context_create($stream_options);
    $auth = 'D!~2037Fj917cZIac';
    $senderid = 'MOBAIN';
    $entity_id = '1101446250000017498';
    $text = urlencode($text);
    file_get_contents('https://global.datagenit.com/API/sms-api.php?auth=' . $auth . '&msisdn=' . $to . '&senderid=' . $senderid . '&message=' . $text . '&entity_id=' . $entity_id . '&template_id=' . $template_id, null, $context);
}

function removeArrayElement($data)
{
    if ($data) {
        if (($key = array_search("all", $data)) !== false) {
            unset($data[$key]);
        }
    }

    return $data;
}

function formatStatusColumn($row, $statusBtn)
{
    $returnVal = '';

    if ($statusBtn) {
        if (isset($row->status) && $row->status == 0) {
            $returnVal .= "<a data-target='#status-modal' title='Change Status' onclick='javascript:setStatusModel(`" . $row->id . "`," . 1 . ")' data-toggle='modal' class='btn-danger btn-sm cursor-pointer'>Inactive</a>&nbsp;";
        } else if (isset($row->is_active) && $row->is_active == 0) {
            $returnVal .= "<a data-target='#status-modal' title='Change Status' onclick='javascript:setStatusModel(`" . $row->id . "`," . 1 . ")' data-toggle='modal' class='btn-danger btn-sm cursor-pointer'>Inactive</a>&nbsp;";
        } else {
            $returnVal .= "<a data-target='#status-modal' title='Change Status' onclick='javascript:setStatusModel(`" . $row->id . "`," . 0 . ")' data-toggle='modal' class='btn-success btn-sm cursor-pointer'>Active</a>&nbsp;";
        }
    }

    return $returnVal;
}

function getBtnHtml($row, $module, $is_admin, $editBtn, $deleteBtn)
{
    $originalModule = $module;
    $actionBtn = '';
    if ($module == 'site_employees' || $module == 'users' || $module == 'ws_users') {
        $id = $row->userid;
    } else if ($module == 'ls-users') {
        $id = $row->user_id;
        $module = 'users';
    } else if ($module == 'tracking_locations') {
        $id = $row->location_id;
        $module = 'tracking_locations';
    } else if ($module == 'logsheet_data') {
        $id = $row->logsheet_id;
        $module = 'logsheet_data';
    } else {
        $id = $row->id;
    }

    if ($is_admin || $editBtn) {
        $actionBtn .= '<a href="' . $module . '/' . $id . '/edit" class="edit btn btn-success btn-sm">
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                       </a>&nbsp;';
    }
    if ($is_admin || $deleteBtn) {
        $actionBtn .= '<a href="javascript:void(0);
        " id="delete-product" onclick="deleteConfirmation(`' . $id . '`)" class="delete btn btn-danger btn-sm">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                       </a>&nbsp;';
    }
    if (($module == 'users' || $module == 'ws_users' || $module == 'cluster_master') && $originalModule != 'ls-users') {
        $actionBtn .= '<a class="edit btn btn-info btn-sm cursor-pointer" data-target="#site-modal" onclick="javascript:getSiteRecord(`' . $id . '`);" data-toggle="modal" title="View Sites">
                        <i class="fa fa-eye" aria-hidden="true"></i>
                       </a>&nbsp;';
    }

    return $actionBtn;
}

function getISTToUTCDate($date)
{
    return date('Y-m-d H:i:s', strtotime($date . '-330 minutes'));
}

function getUTCToISTDate($date, $format = 'Y-m-d H:i:s')
{
    if ($date != '')
        return date($format, strtotime($date . '+330 minutes'));
    else
        return '';
}

function changeDTFormat($date, $format = 'd/m/Y H:i A')
{
    if ($date != '')
        return date($format, strtotime($date));
    else
        return '';
}

function checkPermissions($url_slug, $btn = false)
{
    $permissions = Session::get('permissions');

    if ($btn) {
        if (!in_array($url_slug, $permissions)) {
            return false;
        }
        return true;
    } else {
        if (!in_array($url_slug, $permissions)) {
            return abort(404);
        }
    }
}
//FIND AVG STATUS
function calculateAvgSts($munit = '', $min_tolerance = 0, $max_tolerance = 0, $cal_average = 0)
{
    if ($cal_average > 0) {
        if ($munit == 'Hours') {
            if ($cal_average < $min_tolerance) {
                $avg_status = "POSITIVE";
            } else if ($cal_average > $max_tolerance) {
                $avg_status = "NEGATIVE";
            } else {
                $avg_status = "OK";
            }
        } else {
            if ($cal_average < $min_tolerance) {
                $avg_status = "NEGATIVE";
            } else if ($cal_average > $max_tolerance) {
                $avg_status = "POSITIVE";
            } else {
                $avg_status = "OK";
            }
        }
    } else {
        $avg_status = "ERROR";
    }
    return $avg_status;
}

function toleranceRange($equipment_average = 0, $perc = 0)
{
    if ($perc) {
        $array['min_tolerance'] = round($equipment_average - ($equipment_average * ($perc / 100)), 2);
        $array['max_tolerance'] = round($equipment_average + ($equipment_average * ($perc / 100)), 2);
        return $array;
    }
}

function convertddmmyytoMysql($date)
{
    $new_date = '';
    if (trim($date) == '')
        return $new_date;

    list($dd, $mm, $yy) = explode('/', $date);
    if (intval($mm) > 0 && intval($dd) > 0 && intval($yy) > 0) {
        $new_date = date('Y-m-d', mktime(0, 0, 0, $mm, $dd, $yy));
    }

    return $new_date;
}

function convertmmddyytoMysql($date)
{
    $new_date = '';
    if (trim($date) == '')
        return $new_date;

    list($mm, $dd, $yy) = explode('/', $date);
    if (intval($mm) > 0 && intval($dd) > 0 && intval($yy) > 0) {
        $new_date = date('Y-m-d', mktime(0, 0, 0, $mm, $dd, $yy));
    }

    return $new_date;
}

function secToHR($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds / 60) % 60);
    $seconds = $seconds % 60;

    $hours = $hours < 10 ? "0" . $hours : $hours;
    $minutes = $minutes < 10 ? "0" . $minutes : $minutes;
    $seconds = $seconds < 10 ? "0" . $seconds : $seconds;
    return "$hours:$minutes:$seconds";
}

function time_to_float($time)
{

    $timeArr = explode(":", $time);

    return $timeArr[0] + round(($timeArr[1] / 60), 2);
}
function getDates($first, $last, $step = '+1 day', $output_format = 'd-m-Y')
{
    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);
    while ($current <= $last) {
        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }
    return $dates;
}

/*
 * @category WEBSITE
 * @author Original Author <ksanghavi@moba.de>
 * @author Another Author <ksanghavi@moba.de>
 * @copyright MOBA
 * @comment  GET DATE RANGE
 * @date 2021-01-01
 */

function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d')
{

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while ($current <= $last) {
        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}


function getWeeksArray($startDate, $endDate)
{
    $firstDayOfMonth = strtotime($startDate);
    $lastDayOfMonth = strtotime($endDate);

    $weeksArray = [];

    $currentDay = $firstDayOfMonth;
    while ($currentDay <= $lastDayOfMonth) {
        $weekStartDate = date('Y-m-d', $currentDay);
        $weekEndDate = date('Y-m-d', strtotime('next Saturday', $currentDay));

        $weeksArray[] = array(
            'start_date' => $weekStartDate,
            'end_date' => $weekEndDate
        );

        $currentDay = strtotime('next Sunday', $currentDay);
    }

    return $weeksArray;
}

function findDaysBetweenDates($sdate, $edate)
{
    $start = strtotime($sdate);
    $end = strtotime($edate);

    return ceil(abs($end - $start) / 86400);
}

function timeToSeconds($timeString)
{
    list($hours, $minutes, $seconds) = explode(':', $timeString);
    return $hours * 3600 + $minutes * 60 + $seconds;
}

function secondToHours($totalSeconds)
{
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $seconds = $totalSeconds % 60;

    return $hours . ':' . $minutes . ':' . $seconds;
}

function getReportingManagerIds($userId)
{
    $managerIds = [$userId];
    $nextIds = [$userId];

    while (!empty($nextIds)) {

        $nextIds = DB::table('users_master')
            ->whereIn('admin_id', $nextIds)
            ->pluck('userid')
            ->all();

        $nextIds = array_diff($nextIds, $managerIds);

        if (empty($nextIds)) {
            break;
        }

        $managerIds = array_merge($managerIds, $nextIds);
        $managerIds = array_unique($managerIds);
    }

    return $managerIds;
}


function getReportingManagerIdsV1($userId)
{
    $managerIds = [$userId];
    $nextId = $userId;

    do {
        $nextId = DB::table('users_master')
            ->where('userid', $nextId)
            ->value('admin_id');

        if ($nextId) {
            $managerIds[] = $nextId;
        }
    } while ($nextId);

    return $managerIds;
}

/*
    * @category WEBSITE
    * @author Original Author <ksanghavi@moba.de>
    * @author Another Author <ksanghavi@moba.de>
    * @copyright MOBA
    * @comment  COMMON FUNCTION TO CREATE IMAGE URL
    * @date 2024-02-28
*/

function createFullImagePath($folder_path, $image_name)
{
    $img_path = '';
    if ($image_name == '')
        return $img_path;

    $siteURL = url('/');
    $filePath = base_path() . '/public/' . $folder_path . '/' . $image_name;

    // if (File::exists($filePath)) {
    $img_path = $siteURL . '/' . $folder_path . '/' . $image_name;
    // }

    return $img_path;
}

function createFullImagePathForAPI($folder_path, $image_name)
{
    $img_path = '';
    if ($image_name == '')
        return $img_path;

    $siteURL = url('/');
    $filePath = base_path() . '/public/' . $folder_path . '/' . $image_name;

    if (File::exists($filePath)) {
        $img_path = $siteURL . '/' . $folder_path . '/' . $image_name;
    }

    return $img_path;
}

function checkDateTime($date, $format = 'd-m-Y')
{
    if ($date == '')
        return '';

    if ($format == 'datetime')
        $format_val = 'd/m/Y H:i A';
    else
        $format_val = 'd-m-Y';

    return "<span class='custom-sort'>" . $date . "</span>" . date($format_val, strtotime($date));
}

function convertDateAndTime($datetime)
{
    if ($datetime == '')
        return '';

    return "<span class='custom-sort'>" . $datetime . "</span>" . date('d/m/Y h:i A', strtotime($datetime));
}

function mapSiteName($user_id, $site_name)
{
    if ($user_id == 587) {
        switch ($site_name) {
            case 'ARGA':
                return 'Site 01';
            case 'DHMA':
                return 'Site 02';
            case 'ENKI':
                return 'Site 03';
            case 'SHMA':
                return 'Site 04';
            default:
                return $site_name;
        }
    }

    return $site_name;
}

function mapSiteOriginalName($user_id, $site_name)
{
    if ($user_id == 587) {
        switch ($site_name) {
            case 'Site 01':
                return 'ARGA';
            case 'Site 02':
                return 'DHMA';
            case 'Site 03':
                return 'ENKI';
            case 'Site 04':
                return 'SHMA';
            default:
                return $site_name;
        }
    }

    return $site_name;
}

function storeUserActions($actionArray)
{
    $logged_user_name = currentUserName();
    $action = $actionArray['action'];
    $module = $actionArray['module_name'];

    $site_id = $actionArray['site_id'] ?? 0;
    $equ_id = $actionArray['equ_id'] ?? 0;
    $method = $actionArray['method'];
    $table_name = $actionArray['table_name'] ?? '';
    $table_id = $actionArray['table_id'] ?? 0;
    $old_value = $actionArray['old_value'] ?? 0;
    $new_value = $actionArray['new_value'] ?? 0;

    $message = '';

    switch ($action) {
        case "login":
        case "logout":
            $message = $logged_user_name . " " . ($action === "login" ? "logged in" : "logged out") . " at " . currentDT();
            break;

        case "create":
        case "update":
            $message = $logged_user_name . ($action === "create" ? " created " : " updated ") . $module;
            break;

        case "destroy":
            $message = $logged_user_name . " " . $action . " " . $module . " ID " . $actionArray['record_id'] . " at " . currentDT();
            break;

        case "updateStatus":
            $message = $logged_user_name . " " . $action . " at " . currentDT() . " on " . $module . " ID " . $actionArray['record_id'];
            break;
    }

    $userActionArray = [
        'site_id'      => $site_id,
        'equ_id'       => $equ_id,
        'module_name'  => $module,
        'action'       => $action,
        'type'         => 'web',
        'text'         => $message,
        'created_by'   => currentUser(),
        'created_date' => currentDT(),
        'method'       => $method,
        'table_name'   => $table_name,
        'table_id'     => $table_id,
        'old_value'    => $old_value,
        'new_value'    => $new_value,
    ];

    UserAction::create($userActionArray);
    return true;
}

function storeLSUserActions($actionArray)
{
    $logged_user_name = currentUserName();
    $action = $actionArray['action'];
    $module = $actionArray['module_name'];

    $site_id = $actionArray['site_id'] ?? 0;
    $equ_id = $actionArray['equ_id'] ?? 0;
    $method = $actionArray['method'];
    $table_name = $actionArray['table_name'] ?? '';
    $table_id = $actionArray['table_id'] ?? 0;
    $old_value = $actionArray['old_value'] ?? 0;
    $new_value = $actionArray['new_value'] ?? 0;

    $message = '';

    switch ($action) {
        case "login":
        case "logout":
            $message = $logged_user_name . " " . ($action === "login" ? "logged in" : "logged out") . " at " . currentDT();
            break;

        case "create":
        case "update":
            $message = $logged_user_name . ($action === "create" ? " created " : " updated ") . $module;
            break;

        case "destroy":
            $message = $logged_user_name . " " . $action . " " . $module . " ID " . $actionArray['record_id'] . " at " . currentDT();
            break;

        case "updateStatus":
            $message = $logged_user_name . " " . $action . " at " . currentDT() . " on " . $module . " ID " . $actionArray['record_id'];
            break;
    }

    $userActionArray = [
        'site_id'      => $site_id,
        'equ_id'       => $equ_id,
        'module_name'  => $module,
        'action'       => $action,
        'type'         => 'web',
        'text'         => $message,
        'created_by'   => currentUser(),
        'created_date' => currentDT(),
        'method'       => $method,
        'table_name'   => $table_name,
        'table_id'     => $table_id,
        'old_value'    => $old_value,
        'new_value'    => $new_value,
    ];

    LS_UserAction::create($userActionArray);
    return true;
}

function actionLogValues($data, $oldValues, $request)
{
    $newValues = [];

    if ($request->id) {
        foreach ($oldValues as $key => $oldValue) {
            if ($data->$key != $oldValue) {
                $newValues[$key] = $data->$key;
            }
        }
    } else {
        $newValues = $data->toArray();
    }
    return $newValues;
}



/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return round($angle * $earthRadius, 2);
}

function encode($id)
{
    return Hashids::encode($id);
}

function decode($encoded)
{
    return Hashids::decode($encoded)[0] ?? null;
}
