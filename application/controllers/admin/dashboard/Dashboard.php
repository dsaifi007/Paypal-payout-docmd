<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Application(DOCMD) Dashboard class
 *
 * This class is extend the MY_Controller , which is defined in core folder
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Libraries
 * @author      Chrominfotech Team
 * @link        https://www.chromeinfotech.net/company/about-us.html
 * @Descritpiton - This  class will show everything of entire admin panel stat,graph
  link,docotor,users,etc.
 */
class Dashboard extends MY_Controller {

    protected $data = [];
    protected $parent_dir = "admin/dashboard";
    protected $view_name = "dashboard";
    protected $flag = "";
    protected $total_record = "";
    protected $admin_session_data = array();

    public function __construct() {
        parent::__construct();

        $this->user_not_loggedin();
        $this->load->model("admin/dashboard/dashboard_model", "dashboard");
    }

    public function index($id = null) {

        $this->data["items"] = $this->dashboard->get_statistic_view_model();
        $this->data['view'] = $this->parent_dir . "/" . $this->view_name;
        //$this->user_doctor_graph($id); 
        if ($this->input->cookie("email")) {
            
            $this->load->helper('cookie');
            $this->load->model("admin/login/admin_model", "admin_model");
            $response = $this->admin_model->check_email_password(["email" => get_cookie("email"), "password" => get_cookie("password")]);
            if ($response) {
                $this->admin_session_data = [
                    "email" => get_cookie("email"),
                    "name" => $response['name'],
                    "logged_in" => TRUE
                ];
                $this->session->set_userdata($this->admin_session_data);
                redirect("dashboard", 'refresh');
            }
            
        }
        $this->draw_graph($id);
        $this->displayview($this->data);
    }

    public function user_doctor_graph($id) {
        include("fusioncharts.php");
        $result = $this->dashboard->users_doctors_graph_model($id);
        //dd($result);
        $this->data['active'] = $id;
        if ($result) {
            if ($id == 2) {

                $total = "Projected Revenue(USD)";
            } elseif ($id == 1) {
                $total = "Total No Of Providers";
            } elseif ($id == 3 || $id == 4 || $id == 5 || $id = 6) {
                $total = "Total No Of Appointment";
            } else {
                $total = "Total No Of Users";
            }

            $arrData = array(
                "chart" => array(
                    "caption" => "DOCMD",
                    //"subCaption" => "Total No Of Users Last Year",
                    "xAxisname" => "Month",
                    "yAxisName" => $total,
                    "numberPrefix" => "",
                    "bgColor" => "#ffffff",
                    "paletteColors" => "#0075c2",
                    "legendItemFontColor" => "#666666",
                    "theme" => "zune",
                    "decimals" => "2"
                //"showToolTip"=>"0"
                )
            );

            // creating array for categories object

            $categoryArray = array();
            $dataseries1 = array();


            // pushing category array values
            //while ($row = mysqli_fetch_array($result)) {
            foreach ($result as $k => $row) {
                array_push($categoryArray, array(
                    "label" => $row["category"],
                ));
                array_push($dataseries1, array(
                    "value" => $row["value1"],
                ));
            }

            $arrData["categories"] = array(
                array(
                    "category" => $categoryArray
                )
            );

            // creating dataset object

            $arrData["dataset"] = array(
                array(
                    "seriesName" => $total,
                    "data" => $dataseries1
                )
            );
            //dd($arrData);
            /* JSON Encode the data to retrieve the string containing the JSON representation of the data in the array. */
            $jsonEncodedData = json_encode($arrData);

            // chart object

            $msChart = new FusionCharts("mscombi2d", "chart1", "600", "350", "chart-container", "json", $jsonEncodedData);

            // Render the chart

            $msChart->render();
        } else {
            echo "no data found";
        }
    }

    public function user_doctor_graph1($idd = null) {
        $this->flag = $this->input->post();
        $this->dashboard->users_doctors_graph_model($this->flag['id']);
        echo json_encode(["message" => "success"]);
    }

    public function draw_graph($id) {
        //include("fusioncharts1.php");
        $result = $this->dashboard->users_doctors_graph_model($id);
        // https://www.fusioncharts.com/explore/chart-gallery/column-bar-charts/simple-column
        //dd($this->data["items"]);
        $this->total_record = $this->data["items"]['users'];

        $this->data['active'] = $id;
        if ($result) {
            if ($id == 2) {
                $total = "Projected Revenue(USD)";
                $this->total_record = "$" . $this->data["items"]['earning'];
            } elseif ($id == 1) {
                $total = "Total No Of Providers";
                $this->total_record = $this->data["items"]['doctors'];
            } elseif ($id == 3 || $id == 4 || $id == 5 || $id == 6) {
                $total = "Appointments";
                $this->total_record = $this->data["items"]['total_appointment'];
            } else {
                $total = "Total No Of Users";
                $this->total_record = $this->data["items"]['users'];
            }

            $this->data['chart_data'] = [
                "chart" => [
                    "caption" => $total,
                    "subcaption" => "Stats",
                    "xaxisname" => "Month",
                    "yaxisname" => "Stats(number)",
                    "numbersuffix" => "",
                    "theme" => "fint",
                    "showPlotBorder" => "0",
                //"palettecolors"=> "#3399FF",
                ],
                "data" => $result
            ];

            //echo json_encode($chart);die;
            //$this->load->view("admin/dashboard/chart", $this->data);
        }
    }

}

?>